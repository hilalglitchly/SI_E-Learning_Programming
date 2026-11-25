<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Langganan;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Notifikasi;
use App\Models\Pengajar;
use App\Models\PesertaKelas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_subscription_checkout_verification_gating_and_expiry(): void
    {
        Storage::fake('local');

        $admin = User::create(['username' => 'adm_sub', 'password' => Hash::make('StrongPass123!'), 'role' => 'Admin']);
        $uDosen = User::create(['username' => 'dsn_sub', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '7001', 'nama_pengajar' => 'Dosen Pro', 'email' => 'd7001@test.local', 'id_user' => $uDosen->id_user]);

        $kelasGratis = Kelas::create(['nama_kelas' => 'Dasar HTML', 'deskripsi' => 'Gratis', 'nidn' => '7001', 'is_premium' => false]);
        $kelasPro = Kelas::create(['nama_kelas' => 'Cybersecurity Pro', 'deskripsi' => 'Premium', 'nidn' => '7001', 'is_premium' => true]);
        $materiPro = Materi::create(['id_kelas' => $kelasPro->id_kelas, 'judul_materi' => 'Modul Pro', 'deskripsi' => 'Isi', 'file_materi' => 'pro.pdf']);
        Tugas::create(['id_kelas' => $kelasPro->id_kelas, 'judul_tugas' => 'Tugas Rahasia Pro', 'deskripsi' => 'Konten Premium', 'deadline' => now()->addDays(5)]);
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

        // 2. Checkout ignores tampered nominal & stores proof privately
        $proof = UploadedFile::fake()->create('transfer.pdf', 120, 'application/pdf');
        $this->actingAs($u1)->post(route('langganan.checkout'), [
            'id_paket' => 1, // Pro Bulanan = 49000
            'nominal' => 1,  // Tampered client value must be ignored
            'bukti_bayar' => $proof,
        ])->assertRedirect(route('langganan.index'));

        $order = Langganan::where('id_user', $u1->id_user)->firstOrFail();
        $this->assertSame(49000, $order->nominal);
        $this->assertSame('pending', $order->status);
        Storage::disk('local')->assertExists('private/bukti_bayar/' . $order->bukti_bayar);
        $this->assertTrue(Notifikasi::where('id_user', $admin->id_user)->where('judul', 'Verifikasi Pembayaran Pro')->exists());

        // Duplicate pending submission is blocked
        $this->actingAs($u1)->post(route('langganan.checkout'), [
            'id_paket' => 2,
            'bukti_bayar' => UploadedFile::fake()->create('dup.pdf', 50, 'application/pdf'),
        ])->assertRedirect(route('langganan.index'));
        $this->assertDatabaseCount('tb_langganan', 1);

        // 3. Proof privacy & no-store cache headers: u2 blocked (403), u1 & admin allowed (200)
        $this->actingAs($u2)->get(route('files.bukti_bayar', $order->id_langganan))->assertStatus(403);
        $proofResp = $this->actingAs($u1)->get(route('files.bukti_bayar', $order->id_langganan))
            ->assertStatus(200)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('no-store', (string) $proofResp->headers->get('Cache-Control'));
        $this->actingAs($admin)->get(route('files.bukti_bayar', $order->id_langganan))->assertStatus(200);

        // 4. Non-admin cannot access admin payment verification or approve
        $this->actingAs($u1)->get(route('admin.langganan.index'))->assertStatus(403);
        $this->actingAs($u1)->post(route('admin.langganan.approve', $order->id_langganan))->assertStatus(403);

        // 5. Admin approves once; second approve is rejected (idempotent)
        $this->actingAs($admin)->get(route('admin.langganan.index'))->assertStatus(200)->assertSee($order->kode_invoice);
        $this->actingAs($admin)->post(route('admin.langganan.approve', $order->id_langganan), [
            'catatan_admin' => 'Mutasi BCA cocok',
        ])->assertRedirect(route('admin.langganan.index'));

        $order->refresh();
        $this->assertSame('active', $order->status);
        $firstExpiry = $order->tgl_berakhir->timestamp;

        // Second approve must NOT extend expiry again
        $this->actingAs($admin)->post(route('admin.langganan.approve', $order->id_langganan))
            ->assertRedirect(route('admin.langganan.index'));
        $this->assertSame($firstExpiry, $order->fresh()->tgl_berakhir->timestamp);

        // 6. Active Pro student can now enroll, open Premium class, see Premium tasks, and download Premium materi
        $this->actingAs($u1)->post(route('enroll.store'), ['id_kelas' => $kelasPro->id_kelas])
            ->assertRedirect(route('dashboard'));
        $this->actingAs($u1)->get(route('kelas.show', ['id' => $kelasPro->id_kelas]))->assertStatus(200);
        $this->actingAs($u1)->get(route('tugas.index'))->assertStatus(200)->assertSee('Tugas Rahasia Pro');
        $this->actingAs($u1)->get(route('files.materi', ['id' => $materiPro->id_materi]))->assertStatus(200);

        // 7. Once subscription expires, access to Premium class, global tasks, & file is revoked immediately
        $order->update(['tgl_berakhir' => now()->subMinute()]);
        $this->actingAs($u1)->get(route('kelas.show', ['id' => $kelasPro->id_kelas]))
            ->assertRedirect(route('langganan.index'));
        $this->actingAs($u1)->get(route('tugas.index'))->assertStatus(200)->assertDontSee('Tugas Rahasia Pro');
        $this->actingAs($u1)->get(route('files.materi', ['id' => $materiPro->id_materi]))
            ->assertStatus(403);

        // 8. Deleting user cleans up stored proof file
        $proofFile = 'private/bukti_bayar/' . $order->bukti_bayar;
        $this->actingAs($admin)->delete(route('admin.users.delete', $u1->id_user))
            ->assertRedirect(route('dashboard', ['msg' => 'deleted']));
        Storage::disk('local')->assertMissing($proofFile);
    }

    public function test_admin_rejection_notifies_student_and_keeps_premium_locked(): void
    {
        Storage::fake('local');

        $admin = User::create(['username' => 'adm_rej', 'password' => Hash::make('StrongPass123!'), 'role' => 'Admin']);
        $u1 = User::create(['username' => 'mrd_rej', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '8099', 'nama_murid' => 'Murid Rej', 'email' => 'm8099@test.local', 'id_user' => $u1->id_user]);

        $this->actingAs($u1)->post(route('langganan.checkout'), [
            'id_paket' => 2,
            'bukti_bayar' => UploadedFile::fake()->create('fake.pdf', 80, 'application/pdf'),
        ])->assertRedirect(route('langganan.index'));

        $order = Langganan::where('id_user', $u1->id_user)->firstOrFail();

        $this->actingAs($admin)->post(route('admin.langganan.reject', $order->id_langganan), [
            'catatan_admin' => 'Bukti buram',
        ])->assertRedirect(route('admin.langganan.index'));

        $this->assertSame('rejected', $order->fresh()->status);
        $this->assertFalse($u1->fresh()->hasActiveSubscription());
        $this->assertTrue(Notifikasi::where('id_user', $u1->id_user)->where('judul', 'Pembayaran Pro Ditolak')->exists());
    }
}
