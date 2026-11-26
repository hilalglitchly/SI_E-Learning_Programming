<?php

namespace App\Http\Controllers;

use App\Models\Langganan;
use App\Models\Notifikasi;
use App\Models\PaketLangganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LanggananController extends Controller
{
    // shortcut: Midtrans HTTP calls live in this controller; extract a gateway client if a second provider or payment flow is added.
    private function midtransServerKey(): string
    {
        return trim((string) config('services.midtrans.server_key', ''));
    }

    private function midtransClientKey(): string
    {
        return trim((string) config('services.midtrans.client_key', ''));
    }

    private function midtransIsProd(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    private function midtransConfigured(): bool
    {
        return $this->midtransServerKey() !== '' && $this->midtransClientKey() !== '';
    }

    private function snapBaseUrl(): string
    {
        return $this->midtransIsProd()
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    private function coreApiBaseUrl(): string
    {
        return $this->midtransIsProd()
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    public function index()
    {
        $user = auth()->user();
        if (!$user->isMurid()) {
            return redirect()->route('dashboard');
        }

        $activeSub = $user->activeSubscription();
        $paketList = PaketLangganan::where('is_active', true)->orderBy('harga', 'asc')->get();
        $riwayat = Langganan::with('paket')
            ->where('id_user', $user->id_user)
            ->orderByDesc('id_langganan')
            ->get();
        $pendingOrder = $riwayat->first(fn($r) => $r->status === 'pending');

        return view('langganan.index', [
            'activeSub' => $activeSub,
            'paketList' => $paketList,
            'riwayat' => $riwayat,
            'pendingOrder' => $pendingOrder,
            'midtransConfigured' => $this->midtransConfigured(),
            'midtransClientKey' => $this->midtransClientKey(),
            'midtransIsProd' => $this->midtransIsProd(),
            'snapJsUrl' => $this->snapBaseUrl() . '/snap/snap.js',
            'autoPayInvoice' => session('auto_pay_invoice', ''),
            'pesan' => session('pesan', ''),
            'tipe_pesan' => session('tipe_pesan', ''),
        ]);
    }

    public function checkout(Request $request)
    {
        $user = auth()->user();
        if (!$user->isMurid()) {
            abort(403, 'Akses ditolak.');
        }

        if (!$this->midtransConfigured()) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Payment gateway Midtrans belum dikonfigurasi. Atur MIDTRANS_SERVER_KEY dan MIDTRANS_CLIENT_KEY di .env.')
                ->with('tipe_pesan', 'error');
        }

        $validator = Validator::make($request->all(), [
            'id_paket' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Gagal: Pilih paket langganan yang valid.')
                ->with('tipe_pesan', 'error');
        }

        $paket = PaketLangganan::where('id_paket', (int) $request->input('id_paket'))
            ->where('is_active', true)
            ->first();

        if (!$paket) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Gagal: Paket langganan tidak tersedia.')
                ->with('tipe_pesan', 'error');
        }

        $order = DB::transaction(function () use ($user, $paket) {
            $alreadyPending = Langganan::where('id_user', $user->id_user)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();

            if ($alreadyPending) {
                return null;
            }

            $invoice = 'SUB-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            return Langganan::create([
                'kode_invoice' => $invoice,
                'id_user' => $user->id_user,
                'id_paket' => $paket->id_paket,
                'nominal' => $paket->harga, // Always snapshot server-side price, never trust client input
                'status' => 'pending',
                'gateway_status' => 'pending',
                'created_at' => now(),
            ]);
        });

        if (!$order) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Anda masih memiliki transaksi yang menunggu pembayaran. Selesaikan atau batalkan terlebih dahulu.')
                ->with('tipe_pesan', 'error');
        }

        $murid = $user->murid;
        $customerEmail = ($murid && filter_var($murid->email, FILTER_VALIDATE_EMAIL))
            ? $murid->email
            : ($user->username . '@student.local');

        $payload = [
            'transaction_details' => [
                'order_id' => $order->kode_invoice,
                'gross_amount' => (int) $order->nominal,
            ],
            'item_details' => [[
                'id' => 'PAKET-' . $paket->id_paket,
                'price' => (int) $order->nominal,
                'quantity' => 1,
                'name' => mb_substr($paket->nama_paket, 0, 50),
            ]],
            'customer_details' => [
                'first_name' => mb_substr($murid->nama_murid ?? $user->username, 0, 50),
                'email' => $customerEmail,
            ],
            'callbacks' => [
                'finish' => route('langganan.index'),
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->midtransServerKey(), '')
                ->acceptJson()
                ->timeout(15)
                ->post($this->snapBaseUrl() . '/snap/v1/transactions', $payload);
        } catch (\Throwable $e) {
            $order->delete();
            Log::warning('Midtrans Snap request failed', ['invoice' => $order->kode_invoice, 'error' => $e->getMessage()]);

            return redirect()->route('langganan.index')
                ->with('pesan', 'Gagal terhubung ke Midtrans. Periksa koneksi internet dan konfigurasi API key.')
                ->with('tipe_pesan', 'error');
        }

        $snapToken = trim((string) $response->json('token', ''));
        $redirectUrl = trim((string) $response->json('redirect_url', ''));

        if (!$response->successful() || $snapToken === '') {
            $order->delete();
            Log::warning('Midtrans Snap non-2xx response', [
                'invoice' => $order->kode_invoice,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return redirect()->route('langganan.index')
                ->with('pesan', 'Midtrans menolak pembuatan transaksi (HTTP ' . $response->status() . '). Periksa MIDTRANS_SERVER_KEY.')
                ->with('tipe_pesan', 'error');
        }

        $order->update([
            'snap_token' => $snapToken,
            'payment_url' => $redirectUrl !== '' ? $redirectUrl : null,
        ]);

        return redirect()->route('langganan.index')
            ->with('auto_pay_invoice', $order->kode_invoice)
            ->with('pesan', "Transaksi {$order->kode_invoice} siap dibayar melalui Midtrans.")
            ->with('tipe_pesan', 'success');
    }

    public function webhook(Request $request)
    {
        $serverKey = $this->midtransServerKey();
        if ($serverKey === '') {
            return response()->json(['message' => 'Payment gateway not configured'], 503);
        }

        $orderId = (string) $request->input('order_id', '');
        $statusCode = (string) $request->input('status_code', '');
        $grossAmount = (string) $request->input('gross_amount', '');
        $signatureKey = (string) $request->input('signature_key', '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return response()->json(['message' => 'Incomplete payload'], 400);
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($expectedSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Langganan::where('kode_invoice', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $outcome = $this->applyMidtransStatus($order, $request->all());
        if ($outcome === 'amount_mismatch') {
            Log::warning('Midtrans webhook gross_amount mismatch', [
                'invoice' => $orderId,
                'expected' => $order->nominal,
                'received' => $grossAmount,
            ]);

            return response()->json(['message' => 'Amount mismatch'], 400);
        }

        return response()->json(['message' => 'OK', 'status' => $outcome]);
    }

    public function syncStatus($id)
    {
        $user = auth()->user();
        $order = Langganan::findOrFail((int) $id);

        if (!$user->isMurid() || (int) $order->id_user !== (int) $user->id_user) {
            abort(403, 'Akses ditolak.');
        }

        [$ok, $message] = $this->fetchAndSyncOrder($order);

        return redirect()->route('langganan.index')
            ->with('pesan', $message)
            ->with('tipe_pesan', $ok ? 'success' : 'error');
    }

    public function cancel($id)
    {
        $user = auth()->user();
        $order = Langganan::findOrFail((int) $id);

        if (!$user->isMurid() || (int) $order->id_user !== (int) $user->id_user) {
            abort(403, 'Akses ditolak.');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Hanya transaksi berstatus menunggu pembayaran yang dapat dibatalkan.')
                ->with('tipe_pesan', 'error');
        }

        if ($this->midtransConfigured()) {
            try {
                $res = Http::withBasicAuth($this->midtransServerKey(), '')
                    ->acceptJson()
                    ->timeout(10)
                    ->post($this->coreApiBaseUrl() . '/v2/' . urlencode($order->kode_invoice) . '/cancel');

                // HTTP 412 from Midtrans means the transaction already settled/changed state; sync instead of cancelling
                if ($res->status() === 412 || (string) $res->json('status_code') === '412') {
                    [$ok, $message] = $this->fetchAndSyncOrder($order);

                    return redirect()->route('langganan.index')
                        ->with('pesan', $message)
                        ->with('tipe_pesan', $ok ? 'success' : 'error');
                }
            } catch (\Throwable $e) {
                Log::info('Midtrans cancel best-effort skipped', ['invoice' => $order->kode_invoice, 'error' => $e->getMessage()]);
            }
        }

        DB::transaction(function () use ($order) {
            $locked = Langganan::where('id_langganan', $order->id_langganan)->lockForUpdate()->first();
            if ($locked && $locked->status === 'pending') {
                $locked->update([
                    'status' => 'cancelled',
                    'gateway_status' => 'cancel',
                    'catatan_admin' => 'Dibatalkan oleh pengguna.',
                ]);
            }
        });

        return redirect()->route('langganan.index')
            ->with('pesan', "Transaksi {$order->kode_invoice} dibatalkan.")
            ->with('tipe_pesan', 'success');
    }

    public function adminIndex()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $orders = Langganan::with(['user.murid', 'paket'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('id_langganan')
            ->get();

        $pendingCount = $orders->where('status', 'pending')->count();
        $activeCount = $orders->filter(fn($o) => $o->isCurrentlyActive())->count();
        $totalRevenue = $orders->where('status', 'active')->sum('nominal');

        return view('admin.langganan', [
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'totalRevenue' => $totalRevenue,
            'midtransConfigured' => $this->midtransConfigured(),
            'midtransIsProd' => $this->midtransIsProd(),
            'pesan' => session('pesan', ''),
            'tipe_pesan' => session('tipe_pesan', ''),
        ]);
    }

    public function adminSyncStatus($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $order = Langganan::findOrFail((int) $id);
        [$ok, $message] = $this->fetchAndSyncOrder($order);

        return redirect()->route('admin.langganan.index')
            ->with('pesan', $message)
            ->with('tipe_pesan', $ok ? 'success' : 'error');
    }

    private function fetchAndSyncOrder(Langganan $order): array
    {
        if (!$this->midtransConfigured()) {
            return [false, 'Midtrans belum dikonfigurasi.'];
        }

        try {
            $response = Http::withBasicAuth($this->midtransServerKey(), '')
                ->acceptJson()
                ->timeout(15)
                ->get($this->coreApiBaseUrl() . '/v2/' . urlencode($order->kode_invoice) . '/status');
        } catch (\Throwable $e) {
            Log::warning('Midtrans status check failed', ['invoice' => $order->kode_invoice, 'error' => $e->getMessage()]);

            return [false, 'Gagal memeriksa status ke Midtrans. Silakan coba lagi.'];
        }

        $data = $response->json();
        if (!is_array($data)) {
            return [false, 'Respon tidak valid dari Midtrans.'];
        }

        if ((string) ($data['status_code'] ?? '') === '404') {
            return [false, "Transaksi {$order->kode_invoice} belum dipilih metode pembayarannya di Midtrans. Klik Bayar Sekarang untuk melanjutkan."];
        }

        if (!$response->successful() || empty($data['transaction_status'])) {
            return [false, 'Gagal mengambil status transaksi dari Midtrans (HTTP ' . $response->status() . ').'];
        }

        $outcome = $this->applyMidtransStatus($order, $data);

        return match ($outcome) {
            'active' => [true, "Pembayaran {$order->kode_invoice} terkonfirmasi lunas! Langganan Pro aktif."],
            'pending' => [true, "Transaksi {$order->kode_invoice} masih menunggu penyelesaian pembayaran di Midtrans."],
            'expired', 'cancelled', 'failed' => [false, "Transaksi {$order->kode_invoice} berstatus {$outcome} di Midtrans."],
            'refunded' => [false, "Transaksi {$order->kode_invoice} telah di-refund di Midtrans."],
            'amount_mismatch' => [false, 'Nominal pembayaran tidak sesuai dengan tagihan sistem.'],
            default => [true, "Status transaksi {$order->kode_invoice}: {$outcome}."],
        };
    }

    private function applyMidtransStatus(Langganan $order, array $payload): string
    {
        $txStatus = strtolower((string) ($payload['transaction_status'] ?? ''));
        $fraudStatus = strtolower((string) ($payload['fraud_status'] ?? 'accept'));
        $paymentType = mb_substr(trim((string) ($payload['payment_type'] ?? '')), 0, 40) ?: null;
        $txId = mb_substr(trim((string) ($payload['transaction_id'] ?? '')), 0, 100) ?: null;
        $grossAmountRaw = $payload['gross_amount'] ?? null;

        $isPaid = $txStatus === 'settlement' || ($txStatus === 'capture' && $fraudStatus === 'accept');
        $isPending = $txStatus === 'pending' || ($txStatus === 'capture' && $fraudStatus === 'challenge');
        $isRefunded = in_array($txStatus, ['refund', 'partial_refund', 'chargeback'], true);

        $closedStatus = match ($txStatus) {
            'expire' => 'expired',
            'cancel' => 'cancelled',
            'deny', 'failure' => 'failed',
            default => null,
        };

        return DB::transaction(function () use ($order, $txStatus, $paymentType, $txId, $grossAmountRaw, $isPaid, $isPending, $isRefunded, $closedStatus) {
            $locked = Langganan::with('paket')
                ->where('id_langganan', $order->id_langganan)
                ->lockForUpdate()
                ->first();

            if (!$locked) {
                return 'missing';
            }

            if ($isPaid) {
                if ($grossAmountRaw !== null && (int) round((float) $grossAmountRaw) !== (int) $locked->nominal) {
                    return 'amount_mismatch';
                }

                if ($locked->status === 'active') {
                    return 'active';
                }

                $existingActive = Langganan::where('id_user', $locked->id_user)
                    ->where('status', 'active')
                    ->where('tgl_berakhir', '>', now())
                    ->orderByDesc('tgl_berakhir')
                    ->lockForUpdate()
                    ->first();

                // Stack renewal period if student already has remaining active Pro time
                $start = $existingActive ? $existingActive->tgl_berakhir->copy() : now();
                $durasi = max(1, (int) ($locked->paket->durasi_hari ?? 30));
                $end = $start->copy()->addDays($durasi);

                $locked->update([
                    'status' => 'active',
                    'tgl_mulai' => $start,
                    'tgl_berakhir' => $end,
                    'payment_type' => $paymentType ?? $locked->payment_type,
                    'gateway_transaction_id' => $txId ?? $locked->gateway_transaction_id,
                    'gateway_status' => $txStatus,
                    'catatan_admin' => 'Lunas via Midtrans' . ($paymentType ? ' (' . strtoupper($paymentType) . ')' : ''),
                    'verified_at' => now(),
                ]);

                Notifikasi::create([
                    'id_user' => $locked->id_user,
                    'judul' => 'Langganan Pro Aktif!',
                    'pesan' => "Pembayaran {$locked->kode_invoice} berhasil. Akses Pro aktif hingga " . $end->format('d M Y H:i') . '.',
                    'link' => route('langganan.index', [], false),
                ]);

                return 'active';
            }

            if ($isRefunded) {
                $locked->update([
                    'status' => 'refunded',
                    'tgl_berakhir' => now(),
                    'payment_type' => $paymentType ?? $locked->payment_type,
                    'gateway_transaction_id' => $txId ?? $locked->gateway_transaction_id,
                    'gateway_status' => $txStatus,
                    'catatan_admin' => 'Transaksi di-refund melalui Midtrans.',
                ]);

                return 'refunded';
            }

            // Never downgrade an already settled subscription from delayed pending/expire callbacks
            if ($locked->status === 'active') {
                return 'active';
            }

            if ($isPending) {
                $locked->update([
                    'payment_type' => $paymentType ?? $locked->payment_type,
                    'gateway_transaction_id' => $txId ?? $locked->gateway_transaction_id,
                    'gateway_status' => $txStatus,
                ]);

                return 'pending';
            }

            if ($closedStatus !== null) {
                $wasPending = $locked->status === 'pending';
                $locked->update([
                    'status' => $closedStatus,
                    'payment_type' => $paymentType ?? $locked->payment_type,
                    'gateway_transaction_id' => $txId ?? $locked->gateway_transaction_id,
                    'gateway_status' => $txStatus,
                    'catatan_admin' => 'Status Midtrans: ' . $txStatus,
                ]);

                if ($wasPending) {
                    Notifikasi::create([
                        'id_user' => $locked->id_user,
                        'judul' => 'Pembayaran Pro Tidak Berhasil',
                        'pesan' => "Transaksi {$locked->kode_invoice} berstatus {$closedStatus}. Anda dapat membuat transaksi baru.",
                        'link' => route('langganan.index', [], false),
                    ]);
                }

                return $closedStatus;
            }

            return $locked->status;
        });
    }
}
