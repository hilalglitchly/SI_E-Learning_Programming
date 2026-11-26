<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Langganan;
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

        $daftarKelas = [];
        $listPengajar = null;
        $listMurid = null;
        $listKelas = null;
        $allPengajarOptions = [];
        $totalPengajarCount = 0;
        $totalMuridCount = 0;
        $totalKelasCount = 0;
        $activeSubscription = null;
        $needsEmailVerification = ($user->isMurid() || $user->isPengajar()) && !$user->hasVerifiedEmail();
        $statKelas = 0;
        $statPengajar = 0;
        $statMurid = 0;
        $statSubmission = 0;
        $statProRevenue = 0;
        $statProAktif = 0;
        $statProPending = 0;
        $statProDitolak = 0;
        $rekapKelas = [];

        if (strtolower($role) === 'admin') {
            $listPengajar = DB::table('tb_pengajar as p')
                ->join('tb_user as u', 'p.id_user', '=', 'u.id_user')
                ->select('p.nidn', 'p.nama_pengajar', 'p.email', 'u.id_user', 'u.username')
                ->orderBy('p.nama_pengajar', 'asc')
                ->paginate(10, ['*'], 'pengajar_page')
                ->withQueryString();

            $listMurid = DB::table('tb_murid as m')
                ->join('tb_user as u', 'm.id_user', '=', 'u.id_user')
                ->select('m.nim', 'm.nama_murid', 'm.email', 'u.id_user', 'u.username')
                ->orderBy('m.nama_murid', 'asc')
                ->paginate(10, ['*'], 'murid_page')
                ->withQueryString();

            $listKelas = DB::table('tb_kelas as k')
                ->leftJoin('tb_pengajar as p', 'k.nidn', '=', 'p.nidn')
                ->select(
                    'k.id_kelas',
                    'k.nama_kelas',
                    'k.deskripsi',
                    'k.nidn',
                    'k.is_premium',
                    'p.nama_pengajar',
                    DB::raw('(SELECT COUNT(*) FROM tb_peserta_kelas pk WHERE pk.id_kelas = k.id_kelas) AS jumlah_murid')
                )
                ->orderBy('k.id_kelas', 'desc')
                ->paginate(10, ['*'], 'kelas_page')
                ->withQueryString();

            $allPengajarOptions = DB::table('tb_pengajar')
                ->select('nidn', 'nama_pengajar')
                ->orderBy('nama_pengajar', 'asc')
                ->get();

            $totalPengajarCount = $listPengajar->total();
            $totalMuridCount = $listMurid->total();
            $totalKelasCount = $listKelas->total();

        } elseif (strtolower($role) === 'pimpinan') {
            $statKelas = Kelas::count();
            $statPengajar = Pengajar::count();
            $statMurid = Murid::count();
            $statSubmission = Submission::count();
            $statProRevenue = (int) Langganan::where('status', 'active')->sum('nominal');
            $statProAktif = Langganan::where('status', 'active')->where('tgl_berakhir', '>', now())->count();
            $statProPending = Langganan::where('status', 'pending')->count();
            $statProDitolak = Langganan::whereIn('status', ['rejected', 'cancelled', 'expired', 'failed', 'refunded'])->count();

            $rows = DB::select("
                SELECT k.id_kelas, k.nama_kelas, k.deskripsi, k.is_premium, p.nama_pengajar,
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
            $activeSubscription = $user->activeSubscription();
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
            'listKelas',
            'allPengajarOptions',
            'totalPengajarCount',
            'totalMuridCount',
            'totalKelasCount',
            'activeSubscription',
            'needsEmailVerification',
            'statKelas',
            'statPengajar',
            'statMurid',
            'statSubmission',
            'statProRevenue',
            'statProAktif',
            'statProPending',
            'statProDitolak',
            'rekapKelas'
        ));
    }
}
