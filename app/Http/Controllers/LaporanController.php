<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\Submission;
use App\Models\Tugas;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
        $username = $user->username;

        if (strtolower($role) !== 'admin' && strtolower($role) !== 'pimpinan') {
            return redirect()->route('dashboard');
        }

        $totalKelas = Kelas::count();
        $totalPengajar = Pengajar::count();
        $totalMurid = Murid::count();
        $totalTugas = Tugas::count();
        $totalSubmission = Submission::count();

        $rows = DB::select("
            SELECT 
                k.id_kelas,
                k.nama_kelas,
                k.deskripsi,
                p.nama_pengajar,
                p.nidn,
                (SELECT COUNT(*) FROM tb_peserta_kelas pk WHERE pk.id_kelas = k.id_kelas) AS jumlah_peserta,
                (SELECT COUNT(*) FROM tb_materi m WHERE m.id_kelas = k.id_kelas) AS jumlah_materi,
                (SELECT COUNT(*) FROM tb_tugas t WHERE t.id_kelas = k.id_kelas) AS jumlah_tugas,
                (SELECT COUNT(*) FROM tb_kuis ku WHERE ku.id_kelas = k.id_kelas) AS jumlah_kuis,
                (SELECT COUNT(*) FROM tb_submission s JOIN tb_tugas t2 ON s.id_tugas = t2.id_tugas WHERE t2.id_kelas = k.id_kelas) AS jumlah_submission,
                (SELECT ROUND(AVG(s2.nilai), 1) FROM tb_submission s2 JOIN tb_tugas t3 ON s2.id_tugas = t3.id_tugas WHERE t3.id_kelas = k.id_kelas AND s2.nilai IS NOT NULL) AS rata_nilai
            FROM tb_kelas k
            LEFT JOIN tb_pengajar p ON k.nidn = p.nidn
            ORDER BY k.id_kelas ASC
        ");
        $dataLaporan = array_map(fn($r) => (array) $r, $rows);

        return view('laporan.index', compact(
            'role',
            'username',
            'totalKelas',
            'totalPengajar',
            'totalMurid',
            'totalTugas',
            'totalSubmission',
            'dataLaporan'
        ));
    }
}
