<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Langganan;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Notifikasi;
use App\Models\Pengajar;
use App\Models\PesertaKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private string $serverKey = 'SB-Mid-server-TESTKEY123';
    private string $clientKey = 'SB-Mid-client-TESTKEY123';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.midtrans.server_key' => $this->serverKey,
            'services.midtrans.client_key' => $this->clientKey,
            'services.midtrans.is_production' => false,
        ]);
    }

    private function midtransSignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
    }

    public function test_full_midtrans_checkout_webhook_gating_and_expiry(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-abc123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/snap-token-abc123',
            ], 201),
        ]);

        $admin = User::create(['username' => 'adm_sub', 'password' => Hash::make('StrongPass123!'), 'role' => 'Admin']);
        $uDosen = User::create(['username' => 'dsn_sub', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '7001', 'nama_pengajar' => 'Dosen Pro', 'email' => 'd7001@test.local', 'id_user' => $uDosen->id_user]);

        $kelasGratis = Kelas::create(['nama_kelas' => 'Dasar HTML', 'deskripsi' => 'Gratis', 'nidn' => '7001', 'is_premium' => false]);
        $kelasPro = Kelas::create(['nama_kelas' => 'Cybersecurity Pro', 'deskripsi' => 'Premium', 'nidn' => '7001', 'is_premium' => true]);
        $materiPro = Materi::create(['id_kelas' => $kelasPro->id_kelas, 'judul_materi' => 'Modul Pro', 'deskripsi' => 'Isi', 'file_materi' => 'pro.pdf']);
        Storage::disk('local')->put('private/materi/pro.pdf', 'pdf-bytes');

        $u1 = User::create(['username' => 'mrd_sub1', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        $u2 = User::create(['username' => 'mrd_sub2', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '8001', 'nama_murid' => 'Murid 1', 'email' => 'm8001@test.local', 'id_user' => $u1->id_user]);
        Murid::create(['nim' => '8002', 'nama_murid' => 'Murid 2', 'email' => 'm8002@test.local', 'id_user' => $u2->id_user]);

        // 1. Free student can enroll in Gratis class, blocked from Premium class
        $this->actingAs($u1)->post(route('enroll.store'), ['id_kelas' => $kelasGratis->id_kelas])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($u1)->post(route('enroll.store'), ['id_kelas' => $kelasPro->id_kelas])
            ->assertRedirect(route('langganan.index'));
        $this->assertFalse(PesertaKelas::where('id_kelas', $kelasPro->id_kelas)->where('nim', '8001')->exists());

        // 2. Checkout ignores tampered nominal & requests Snap token using server-side plan price
        $this->actingAs($u1)->post(route('langganan.checkout'), [
            'id_paket' => 1, // Pro Bulanan = 49000
            'nominal' => 1,  // Tampered client value must be ignored
        ])->assertRedirect(route('langganan.index'));

        $order = Langganan::where('id_user', $u1->id_user)->firstOrFail();
        $this->assertSame(49000, $order->nominal);
        $this->assertSame('pending', $order->status);
        $this->assertSame('snap-token-abc123', $order->snap_token);

        Http::assertSent(function ($request) use ($order) {
            return $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
                && ($request['transaction_details']['order_id'] ?? null) === $order->kode_invoice
                && ($request['transaction_details']['gross_amount'] ?? null) === 49000;
        });

        // Duplicate pending submission is blocked
        $this->actingAs($u1)->post(route('langganan.checkout'), ['id_paket' => 2])
            ->assertRedirect(route('langganan.index'));
        $this->assertDatabaseCount('tb_langganan', 1);

        // 3. Forged webhook signature is rejected (403)
        $this->postJson(route('midtrans.webhook'), [
            'order_id' => $order->kode_invoice,
            'status_code' => '200',
            'gross_amount' => '49000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'forged-signature',
        ])->assertStatus(403);
        $this->assertSame('pending', $order->fresh()->status);

        // 4. Tampered gross_amount with valid signature is rejected (400)
        $this->postJson(route('midtrans.webhook'), [
            'order_id' => $order->kode_invoice,
            'status_code' => '200',
            'gross_amount' => '1000.00',
            'transaction_status' => 'settlement',
            'signature_key' => $this->midtransSignature($order->kode_invoice, '200', '1000.00'),
        ])->assertStatus(400);
        $this->assertSame('pending', $order->fresh()->status);

        // 5. Valid Midtrans settlement webhook activates Pro & is idempotent on replay
        $settlementPayload = [
            'order_id' => $order->kode_invoice,
            'status_code' => '200',
            'gross_amount' => '49000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
            'transaction_id' => 'mid-tx-999',
            'signature_key' => $this->midtransSignature($order->kode_invoice, '200', '49000.00'),
        ];

        $this->postJson(route('midtrans.webhook'), $settlementPayload)->assertOk();
        $order->refresh();
        $this->assertSame('active', $order->status);
        $this->assertSame('qris', $order->payment_type);
        $this->assertSame('mid-tx-999', $order->gateway_transaction_id);
        $firstExpiry = $order->tgl_berakhir->timestamp;

        // Replaying webhook must NOT extend expiry again
        $this->postJson(route('midtrans.webhook'), $settlementPayload)->assertOk();
        $this->assertSame($firstExpiry, $order->fresh()->tgl_berakhir->timestamp);
        $this->assertTrue(Notifikasi::where('id_user', $u1->id_user)->where('judul', 'Langganan Pro Aktif!')->exists());

        // 6. Active Pro student can enroll, open Premium class, and download Premium materi
        $this->actingAs($u1)->post(route('enroll.store'), ['id_kelas' => $kelasPro->id_kelas])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($u1)->get(route('kelas.show', ['id' => $kelasPro->id_kelas]))->assertStatus(200);
        $this->actingAs($u1)->get(route('files.materi', ['id' => $materiPro->id_materi]))->assertStatus(200);

        // 7. Admin dashboard lists transaction; non-admin blocked (403)
        $this->actingAs($u2)->get(route('admin.langganan.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('admin.langganan.index'))->assertStatus(200)->assertSee($order->kode_invoice);

        // 8. Once subscription expires, access to Premium class & file is revoked immediately
        $order->update(['tgl_berakhir' => now()->subMinute()]);
        $this->actingAs($u1)->get(route('kelas.show', ['id' => $kelasPro->id_kelas]))
            ->assertRedirect(route('langganan.index'));
        $this->actingAs($u1)->get(route('files.materi', ['id' => $materiPro->id_materi]))
            ->assertStatus(403);
    }

    public function test_sync_cancel_idor_and_webhook_expire(): void
    {
        $admin = User::create(['username' => 'adm_sync', 'password' => Hash::make('StrongPass123!'), 'role' => 'Admin']);
        $u1 = User::create(['username' => 'mrd_sync1', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        $u2 = User::create(['username' => 'mrd_sync2', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '8101', 'nama_murid' => 'Sync 1', 'email' => 's1@test.local', 'id_user' => $u1->id_user]);
        Murid::create(['nim' => '8102', 'nama_murid' => 'Sync 2', 'email' => 's2@test.local', 'id_user' => $u2->id_user]);

        $order = Langganan::create([
            'kode_invoice' => 'SUB-20261126-SYNC01',
            'id_user' => $u1->id_user,
            'id_paket' => 2,
            'nominal' => 490000,
            'status' => 'pending',
            'snap_token' => 'tok-sync',
            'created_at' => now(),
        ]);

        // IDOR: u2 cannot sync or cancel u1's order
        $this->actingAs($u2)->post(route('langganan.sync', $order->id_langganan))->assertStatus(403);
        $this->actingAs($u2)->post(route('langganan.cancel', $order->id_langganan))->assertStatus(403);

        // Admin sync settles order via Midtrans Core API
        Http::fake([
            'https://api.sandbox.midtrans.com/v2/SUB-20261126-SYNC01/status' => Http::response([
                'status_code' => '200',
                'order_id' => 'SUB-20261126-SYNC01',
                'gross_amount' => '490000.00',
                'transaction_status' => 'settlement',
                'payment_type' => 'bank_transfer',
                'transaction_id' => 'mid-va-123',
            ], 200),
            'https://api.sandbox.midtrans.com/v2/SUB-20261126-CANC02/cancel' => Http::response(['status_code' => '200'], 200),
        ]);

        $this->actingAs($admin)->post(route('admin.langganan.sync', $order->id_langganan))
            ->assertRedirect(route('admin.langganan.index'));
        $this->assertSame('active', $order->fresh()->status);
        $this->assertTrue($u1->fresh()->hasActiveSubscription());

        // Student can cancel own pending order
        $order2 = Langganan::create([
            'kode_invoice' => 'SUB-20261126-CANC02',
            'id_user' => $u2->id_user,
            'id_paket' => 1,
            'nominal' => 49000,
            'status' => 'pending',
            'snap_token' => 'tok-canc',
            'created_at' => now(),
        ]);

        $this->actingAs($u2)->post(route('langganan.cancel', $order2->id_langganan))
            ->assertRedirect(route('langganan.index'));
        $this->assertSame('cancelled', $order2->fresh()->status);

        // Webhook expire marks pending order expired & notifies student
        $order3 = Langganan::create([
            'kode_invoice' => 'SUB-20261126-EXP003',
            'id_user' => $u2->id_user,
            'id_paket' => 1,
            'nominal' => 49000,
            'status' => 'pending',
            'snap_token' => 'tok-exp',
            'created_at' => now(),
        ]);

        $this->postJson(route('midtrans.webhook'), [
            'order_id' => 'SUB-20261126-EXP003',
            'status_code' => '202',
            'gross_amount' => '49000.00',
            'transaction_status' => 'expire',
            'signature_key' => $this->midtransSignature('SUB-20261126-EXP003', '202', '49000.00'),
        ])->assertOk();

        $this->assertSame('expired', $order3->fresh()->status);
        $this->assertFalse($u2->fresh()->hasActiveSubscription());
        $this->assertTrue(Notifikasi::where('id_user', $u2->id_user)->where('judul', 'Pembayaran Pro Tidak Berhasil')->exists());
    }
}
