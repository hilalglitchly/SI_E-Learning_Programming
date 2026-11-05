<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role;
        $username = $user->username;

        // Legacy support if ?delete_user=ID is passed to dashboard
        if (strtolower($role) === 'admin' && $request->has('delete_user')) {
            $id_hapus = (int) $request->query('delete_user');
            if ($id_hapus !== (int) $id_user) {
                DB::table('tb_user')->where('id_user', $id_hapus)->delete();
                return redirect()->route('dashboard', ['msg' => 'deleted']);
            }
        }

        $daftarKelas = [];
        $listPengajar = [];
        $listMurid = [];
        $statKelas = 0;
        $statPengajar = 0;
        $statMurid = 0;
        $statSubmission = 0;
        $rekapKelas = [];

        if (strtolower($role) === 'admin') {
            $listPengajar = DB::select("
                SELECT p.nidn, p.nama_pengajar, p.email, u.id_user, u.username 
                FROM tb_pengajar p 
                JOIN tb_user u ON p.id_user = u.id_user 
                ORDER BY p.nama_pengajar ASC
            ");
            $listPengajar = array_map(fn($r) => (array) $r, $listPengajar);

            $listMurid = DB::select("
                SELECT m.nim, m.nama_murid, m.email, u.id_user, u.username 
                FROM tb_murid m 
                JOIN tb_user u ON m.id_user = u.id_user 
                ORDER BY m.nama_murid ASC
            ");
            $listMurid = array_map(fn($r) => (array) $r, $listMurid);

        } elseif (strtolower($role) === 'pimpinan') {
            $statKelas = Kelas::count();
            $statPengajar = Pengajar::count();
            $statMurid = Murid::count();
            $statSubmission = Submission::count();

            $rows = DB::select("
                SELECT k.id_kelas, k.nama_kelas, k.deskripsi, p.nama_pengajar,
                       (SELECT COUNT(*) FROM tb_peserta_kelas pk WHERE pk.id_kelas = k.id_kelas) AS jumlah_murid,
                       (SELECT COUNT(*) FROM tb_materi m WHERE m.id_kelas = k.id_kelas) AS jumlah_materi,
                       (SELECT COUNT(*) FROM tb_tugas t WHERE t.id_kelas = k.id_kelas) AS jumlah_tugas,
                       (SELECT COUNT(*) FROM tb_kuis ku WHERE ku.id_kelas = k.id_kelas) AS jumlah_kuis
                FROM tb_kelas k
                LEFT JOIN tb_pengajar p ON k.nidn = p.nidn
                ORDER BY k.id_kelas ASC
            ");
            $rekapKelas = array_map(fn($r) => (array) $r, $rows);

        } elseif ($role === 'Pengajar' || strtolower($role) === 'pengajar') {
            $rows = DB::select("
                SELECT k.* 
                FROM tb_kelas k
                JOIN tb_pengajar p ON k.nidn = p.nidn
                WHERE p.id_user = :id_user
            ", ['id_user' => $id_user]);
            $daftarKelas = array_map(fn($r) => (array) $r, $rows);

        } elseif (strtolower($role) === 'murid') {
            $rows = DB::select("
                SELECT k.*, p.nama_pengajar, m.nim,
                       (SELECT COUNT(*) FROM tb_tugas t WHERE t.id_kelas = k.id_kelas) as total_tugas_kelas,
                       (SELECT COUNT(DISTINCT s.id_tugas) FROM tb_submission s JOIN tb_tugas t ON s.id_tugas = t.id_tugas WHERE t.id_kelas = k.id_kelas AND s.nim = m.nim) as tugas_selesai,
                       (SELECT COUNT(*) FROM tb_kuis ku WHERE ku.id_kelas = k.id_kelas) as total_kuis_kelas,
                       (SELECT COUNT(DISTINCT nk.id_kuis) FROM tb_nilai_kuis nk JOIN tb_kuis ku ON nk.id_kuis = ku.id_kuis WHERE ku.id_kelas = k.id_kelas AND nk.nim = m.nim) as kuis_selesai
                FROM tb_peserta_kelas pk
                JOIN tb_kelas k ON pk.id_kelas = k.id_kelas
                LEFT JOIN tb_pengajar p ON k.nidn = p.nidn
                JOIN tb_murid m ON pk.nim = m.nim
                WHERE m.id_user = :id_user
            ", ['id_user' => $id_user]);
            $daftarKelas = array_map(fn($r) => (array) $r, $rows);
        }

        return view('dashboard', compact(
            'id_user',
            'role',
            'username',
            'daftarKelas',
            'listPengajar',
            'listMurid',
            'statKelas',
            'statPengajar',
            'statMurid',
            'statSubmission',
            'rekapKelas'
        ));
    }
}
