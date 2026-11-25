<?php

namespace App\Http\Controllers;

use App\Models\Langganan;
use App\Models\Notifikasi;
use App\Models\PaketLangganan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LanggananController extends Controller
{
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
        $hasPending = $riwayat->contains(fn($r) => $r->status === 'pending');

        return view('langganan.index', [
            'activeSub' => $activeSub,
            'paketList' => $paketList,
            'riwayat' => $riwayat,
            'hasPending' => $hasPending,
            'payment' => config('app.payment'),
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

        $validator = Validator::make($request->all(), [
            'id_paket' => ['required', 'integer'],
            'bukti_bayar' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Gagal: Pilih paket valid dan unggah bukti transfer (JPG/PNG/PDF, maks. 5 MB).')
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

        $file = $request->file('bukti_bayar');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Gagal: Ekstensi bukti transfer hanya boleh JPG, PNG, atau PDF.')
                ->with('tipe_pesan', 'error');
        }

        $safeName = 'bukti_' . time() . '_' . Str::random(16) . '.' . $ext;
        $storedPath = Storage::disk('local')->putFileAs('private/bukti_bayar', $file, $safeName);
        if (!$storedPath) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Gagal menyimpan berkas bukti transfer. Silakan coba lagi.')
                ->with('tipe_pesan', 'error');
        }

        $created = DB::transaction(function () use ($user, $paket, $safeName) {
            $alreadyPending = Langganan::where('id_user', $user->id_user)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists();

            if ($alreadyPending) {
                return null;
            }

            $invoice = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            return Langganan::create([
                'kode_invoice' => $invoice,
                'id_user' => $user->id_user,
                'id_paket' => $paket->id_paket,
                'nominal' => $paket->harga, // Always snapshot server-side price, never trust client input
                'status' => 'pending',
                'bukti_bayar' => $safeName,
                'created_at' => now(),
            ]);
        });

        if (!$created) {
            Storage::disk('local')->delete('private/bukti_bayar/' . $safeName);
            return redirect()->route('langganan.index')
                ->with('pesan', 'Anda masih memiliki pengajuan pembayaran yang menunggu verifikasi Admin.')
                ->with('tipe_pesan', 'error');
        }

        $admins = User::whereRaw('LOWER(role) = ?', ['admin'])->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id_user,
                'judul' => 'Verifikasi Pembayaran Pro',
                'pesan' => "{$user->username} mengunggah bukti transfer {$paket->nama_paket} ({$created->kode_invoice}).",
                'link' => route('admin.langganan.index', [], false),
            ]);
        }

        return redirect()->route('langganan.index')
            ->with('pesan', "Bukti pembayaran ({$created->kode_invoice}) berhasil dikirim! Menunggu verifikasi Admin.")
            ->with('tipe_pesan', 'success');
    }

    public function adminIndex()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $orders = Langganan::with(['user.murid', 'paket', 'verifier'])
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
            'pesan' => session('pesan', ''),
            'tipe_pesan' => session('tipe_pesan', ''),
        ]);
    }

    public function approve(Request $request, $id)
    {
        $admin = auth()->user();
        if (!$admin->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $catatan = mb_substr(trim((string) $request->input('catatan_admin', '')), 0, 255);

        $result = DB::transaction(function () use ($id, $admin, $catatan) {
            $order = Langganan::with('paket')->where('id_langganan', (int) $id)->lockForUpdate()->firstOrFail();
            if ($order->status !== 'pending') {
                return ['ok' => false, 'order' => $order];
            }

            $existingActive = Langganan::where('id_user', $order->id_user)
                ->where('status', 'active')
                ->where('tgl_berakhir', '>', now())
                ->orderByDesc('tgl_berakhir')
                ->lockForUpdate()
                ->first();

            // Stack renewal period if student already has remaining active time
            $start = $existingActive ? $existingActive->tgl_berakhir->copy() : now();
            $durasi = max(1, (int) ($order->paket->durasi_hari ?? 30));
            $end = $start->copy()->addDays($durasi);

            $order->update([
                'status' => 'active',
                'tgl_mulai' => $start,
                'tgl_berakhir' => $end,
                'catatan_admin' => $catatan !== '' ? $catatan : 'Pembayaran diverifikasi.',
                'verified_by' => $admin->id_user,
                'verified_at' => now(),
            ]);

            Notifikasi::create([
                'id_user' => $order->id_user,
                'judul' => 'Langganan Pro Aktif!',
                'pesan' => "Invoice {$order->kode_invoice} disetujui. Akses Pro aktif hingga " . $end->format('d M Y H:i') . '.',
                'link' => route('langganan.index', [], false),
            ]);

            return ['ok' => true, 'order' => $order];
        });

        if (!$result['ok']) {
            return redirect()->route('admin.langganan.index')
                ->with('pesan', 'Transaksi ini sudah diproses sebelumnya.')
                ->with('tipe_pesan', 'error');
        }

        return redirect()->route('admin.langganan.index')
            ->with('pesan', "Invoice {$result['order']->kode_invoice} berhasil disetujui dan langganan Pro diaktifkan!")
            ->with('tipe_pesan', 'success');
    }

    public function reject(Request $request, $id)
    {
        $admin = auth()->user();
        if (!$admin->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $catatan = mb_substr(trim((string) $request->input('catatan_admin', '')), 0, 255);
        if ($catatan === '') {
            $catatan = 'Bukti transfer tidak valid atau nominal tidak sesuai.';
        }

        $result = DB::transaction(function () use ($id, $admin, $catatan) {
            $order = Langganan::where('id_langganan', (int) $id)->lockForUpdate()->firstOrFail();
            if ($order->status !== 'pending') {
                return ['ok' => false, 'order' => $order];
            }

            $order->update([
                'status' => 'rejected',
                'catatan_admin' => $catatan,
                'verified_by' => $admin->id_user,
                'verified_at' => now(),
            ]);

            Notifikasi::create([
                'id_user' => $order->id_user,
                'judul' => 'Pembayaran Pro Ditolak',
                'pesan' => "Invoice {$order->kode_invoice} ditolak: {$catatan}",
                'link' => route('langganan.index', [], false),
            ]);

            return ['ok' => true, 'order' => $order];
        });

        if (!$result['ok']) {
            return redirect()->route('admin.langganan.index')
                ->with('pesan', 'Transaksi ini sudah diproses sebelumnya.')
                ->with('tipe_pesan', 'error');
        }

        return redirect()->route('admin.langganan.index')
            ->with('pesan', "Invoice {$result['order']->kode_invoice} ditolak.")
            ->with('tipe_pesan', 'success');
    }

    public function downloadBukti($id)
    {
        $order = Langganan::findOrFail((int) $id);
        $user = auth()->user();

        if (!$user->isAdmin() && (int) $order->id_user !== (int) $user->id_user) {
            abort(403, 'Akses ditolak.');
        }

        $safeName = basename((string) $order->bukti_bayar);
        $relPath = 'private/bukti_bayar/' . $safeName;
        if ($safeName === '' || !Storage::disk('local')->exists($relPath)) {
            abort(404, 'Berkas bukti bayar tidak ditemukan.');
        }

        return response()->download(Storage::disk('local')->path($relPath), $safeName, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
