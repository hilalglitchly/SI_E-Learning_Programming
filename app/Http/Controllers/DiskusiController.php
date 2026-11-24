<?php

namespace App\Http\Controllers;

use App\Models\BalasanDiskusi;
use App\Models\Diskusi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiskusiController extends Controller
{
    public function show(Request $request, $id = null)
    {
        $id_diskusi = (int) ($id ?? $request->query('id'));
        if ($id_diskusi <= 0) {
            abort(404, 'ID Diskusi tidak ditemukan.');
        }

        $user = auth()->user();
        $role = $user->role;

        $diskusiModel = Diskusi::with('kelas')->find($id_diskusi);
        if (!$diskusiModel || !$diskusiModel->kelas) {
            abort(404, 'Topik diskusi tidak ditemukan.');
        }

        if (!$diskusiModel->kelas->canView($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin mengakses diskusi kelas ini.');
        }

        $diskusiRow = DB::selectOne("
            SELECT d.*, u.username, u.role 
            FROM tb_diskusi d 
            JOIN tb_user u ON d.id_user = u.id_user 
            WHERE d.id_diskusi = :id_diskusi
        ", ['id_diskusi' => $id_diskusi]);

        if (!$diskusiRow) {
            abort(404, 'Topik diskusi tidak ditemukan.');
        }
        $diskusi = (array) $diskusiRow;

        $balasanRows = DB::select("
            SELECT b.*, u.username, u.role 
            FROM tb_balasan_diskusi b 
            JOIN tb_user u ON b.id_user = u.id_user 
            WHERE b.id_diskusi = :id_diskusi 
            ORDER BY b.tgl_balasan ASC
        ", ['id_diskusi' => $id_diskusi]);
        $balasanList = array_map(fn($r) => (array) $r, $balasanRows);

        $pesan = session('pesan', '');
        if ($pesan === '' && $request->query('msg') === 'success') {
            $pesan = "Balasan berhasil dikirim!";
        }

        $id_user = $user->id_user;
        $canManageClass = $diskusiModel->kelas->canManage($user);

        return view('diskusi.show', compact('id_diskusi', 'id_user', 'role', 'canManageClass', 'diskusi', 'balasanList', 'pesan'));
    }

    public function storeBalasan(Request $request, $id = null)
    {
        $id_diskusi = (int) ($id ?? $request->query('id'));
        $user = auth()->user();
        $id_user = $user->id_user;
        $username = $user->username;

        $diskusiModel = Diskusi::with('kelas')->findOrFail($id_diskusi);
        if (!$diskusiModel->kelas || !$diskusiModel->kelas->canView($user) || $user->isPimpinan()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin membalas diskusi kelas ini.');
        }

        $isi_balasan = sanitize_rich_text(trim((string) $request->input('isi_balasan')));

        if (trim(strip_tags($isi_balasan)) !== '') {
            BalasanDiskusi::create([
                'id_diskusi' => $id_diskusi,
                'id_user' => $id_user,
                'isi_balasan' => $isi_balasan,
            ]);

            if ((int) $diskusiModel->id_user !== (int) $id_user) {
                $judul_notif = "Balasan Baru di Diskusi";
                $pesan_notif = "{$username} membalas topik Anda: \"{$diskusiModel->judul_diskusi}\"";
                $link_notif = route('diskusi.show', ['id' => $id_diskusi], false);

                Notifikasi::create([
                    'id_user' => $diskusiModel->id_user,
                    'judul' => $judul_notif,
                    'pesan' => $pesan_notif,
                    'link' => $link_notif,
                ]);
            }

            return redirect()->route('diskusi.show', ['id' => $id_diskusi, 'msg' => 'success']);
        }

        return redirect()->route('diskusi.show', ['id' => $id_diskusi]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $diskusi = Diskusi::with('kelas')->findOrFail((int) $id);
        if (!$diskusi->kelas || !$diskusi->kelas->canView($user)) {
            abort(403, 'Akses ditolak.');
        }
        if ((int) $diskusi->id_user !== (int) $user->id_user && !$diskusi->kelas->canManage($user)) {
            abort(403, 'Anda tidak memiliki izin mengedit topik ini.');
        }

        $judul = trim((string) $request->input('judul_diskusi'));
        $isi = sanitize_rich_text(trim((string) $request->input('isi_diskusi')));
        if ($judul === '' || mb_strlen($judul) > 150 || trim(strip_tags($isi)) === '') {
            return redirect()->route('diskusi.show', ['id' => $diskusi->id_diskusi])
                ->with('pesan', 'Gagal: Judul (maks. 150 karakter) dan isi diskusi wajib diisi.');
        }

        $diskusi->update([
            'judul_diskusi' => $judul,
            'isi_diskusi' => $isi,
        ]);

        return redirect()->route('diskusi.show', ['id' => $diskusi->id_diskusi])
            ->with('pesan', 'Topik diskusi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $diskusi = Diskusi::with('kelas')->findOrFail((int) $id);
        if (!$diskusi->kelas || !$diskusi->kelas->canView($user)) {
            abort(403, 'Akses ditolak.');
        }
        if ((int) $diskusi->id_user !== (int) $user->id_user && !$diskusi->kelas->canManage($user)) {
            abort(403, 'Anda tidak memiliki izin menghapus topik ini.');
        }

        $id_kelas = $diskusi->id_kelas;
        $diskusi->delete();

        return redirect()->route('kelas.show', ['id' => $id_kelas])
            ->with('pesan', 'Topik diskusi berhasil dihapus.');
    }

    public function updateBalasan(Request $request, $id, $id_balasan)
    {
        $user = auth()->user();
        $diskusi = Diskusi::with('kelas')->findOrFail((int) $id);
        if (!$diskusi->kelas || !$diskusi->kelas->canView($user)) {
            abort(403, 'Akses ditolak.');
        }

        $balasan = BalasanDiskusi::where('id_balasan', (int) $id_balasan)
            ->where('id_diskusi', $diskusi->id_diskusi)
            ->firstOrFail();

        if ((int) $balasan->id_user !== (int) $user->id_user && !$diskusi->kelas->canManage($user)) {
            abort(403, 'Anda tidak memiliki izin mengedit balasan ini.');
        }

        $isi = sanitize_rich_text(trim((string) $request->input('isi_balasan')));
        if (trim(strip_tags($isi)) === '') {
            return redirect()->route('diskusi.show', ['id' => $diskusi->id_diskusi])
                ->with('pesan', 'Gagal: Isi balasan tidak boleh kosong.');
        }

        $balasan->update(['isi_balasan' => $isi]);

        return redirect()->route('diskusi.show', ['id' => $diskusi->id_diskusi])
            ->with('pesan', 'Balasan berhasil diperbarui!');
    }

    public function deleteBalasan($id, $id_balasan)
    {
        $user = auth()->user();
        $diskusi = Diskusi::with('kelas')->findOrFail((int) $id);
        if (!$diskusi->kelas || !$diskusi->kelas->canView($user)) {
            abort(403, 'Akses ditolak.');
        }

        $balasan = BalasanDiskusi::where('id_balasan', (int) $id_balasan)
            ->where('id_diskusi', $diskusi->id_diskusi)
            ->firstOrFail();

        if ((int) $balasan->id_user !== (int) $user->id_user && !$diskusi->kelas->canManage($user)) {
            abort(403, 'Anda tidak memiliki izin menghapus balasan ini.');
        }

        $balasan->delete();

        return redirect()->route('diskusi.show', ['id' => $diskusi->id_diskusi])
            ->with('pesan', 'Balasan berhasil dihapus.');
    }
}

