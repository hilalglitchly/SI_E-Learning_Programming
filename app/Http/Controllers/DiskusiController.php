<?php

namespace App\Http\Controllers;

use App\Models\BalasanDiskusi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiskusiController extends Controller
{
    public function show(Request $request, $id = null)
    {
        $id_diskusi = $id ?? $request->query('id');
        if (empty($id_diskusi)) {
            abort(404, 'ID Diskusi tidak ditemukan.');
        }

        $user = auth()->user();
        $role = $user->role;

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

        $pesan = '';
        if ($request->query('msg') === 'success') {
            $pesan = "Balasan berhasil dikirim!";
        }

        return view('diskusi.show', compact('id_diskusi', 'role', 'diskusi', 'balasanList', 'pesan'));
    }

    public function storeBalasan(Request $request, $id = null)
    {
        $id_diskusi = $id ?? $request->query('id');
        $user = auth()->user();
        $id_user = $user->id_user;
        $username = $user->username;

        $isi_balasan = trim((string) $request->input('isi_balasan'));

        if ($isi_balasan !== '') {
            BalasanDiskusi::create([
                'id_diskusi' => $id_diskusi,
                'id_user' => $id_user,
                'isi_balasan' => $isi_balasan,
            ]);

            $authorRow = DB::selectOne("
                SELECT id_user, judul_diskusi FROM tb_diskusi WHERE id_diskusi = ?
            ", [$id_diskusi]);

            if ($authorRow && (int) $authorRow->id_user !== (int) $id_user) {
                $judul_notif = "Balasan Baru di Diskusi";
                $pesan_notif = "{$username} membalas topik Anda: \"{$authorRow->judul_diskusi}\"";
                $link_notif = route('diskusi.show', ['id' => $id_diskusi], false);

                Notifikasi::create([
                    'id_user' => $authorRow->id_user,
                    'judul' => $judul_notif,
                    'pesan' => $pesan_notif,
                    'link' => $link_notif,
                ]);
            }

            return redirect()->route('diskusi.show', ['id' => $id_diskusi, 'msg' => 'success']);
        }

        return redirect()->route('diskusi.show', ['id' => $id_diskusi]);
    }
}

