<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TugasController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role;

        $daftarTugas = [];

        if (strtolower($role) === 'murid') {
            $rows = DB::select("
                SELECT t.*, k.nama_kelas 
                FROM tb_tugas t
                JOIN tb_kelas k ON t.id_kelas = k.id_kelas
                JOIN tb_peserta_kelas pk ON k.id_kelas = pk.id_kelas
                JOIN tb_murid m ON pk.nim = m.nim
                WHERE m.id_user = :id_user
                AND t.id_tugas NOT IN (
                    SELECT id_tugas FROM tb_submission WHERE nim = m.nim
                )
                ORDER BY t.deadline ASC
            ", ['id_user' => $id_user]);
            $daftarTugas = array_map(fn($r) => (array) $r, $rows);
        } elseif ($role === 'Pengajar' || strtolower($role) === 'pengajar' || strtolower($role) === 'admin') {
            $rows = DB::select("
                SELECT t.*, k.nama_kelas,
                       (SELECT COUNT(*) FROM tb_submission s WHERE s.id_tugas = t.id_tugas) as jumlah_kumpul
                FROM tb_tugas t
                JOIN tb_kelas k ON t.id_kelas = k.id_kelas
                LEFT JOIN tb_pengajar p ON k.nidn = p.nidn
                WHERE p.id_user = :id_user OR :role = 'admin'
                ORDER BY t.deadline DESC
            ", [
                'id_user' => $id_user,
                'role' => strtolower($role),
            ]);
            $daftarTugas = array_map(fn($r) => (array) $r, $rows);
        }

        return view('tugas.index', compact('role', 'daftarTugas'));
    }

    public function penilaian(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role !== 'Pengajar' && strtolower($role) !== 'pengajar' && strtolower($role) !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Pengajar.');
        }

        $id_tugas = $request->query('id_tugas');
        if (empty($id_tugas)) {
            abort(404, 'ID Tugas tidak ditemukan.');
        }

        $infoRow = DB::selectOne("
            SELECT t.*, k.nama_kelas 
            FROM tb_tugas t 
            JOIN tb_kelas k ON t.id_kelas = k.id_kelas 
            WHERE t.id_tugas = :id_tugas
        ", ['id_tugas' => $id_tugas]);

        if (!$infoRow) {
            abort(404, 'Data tugas tidak ditemukan.');
        }
        $infoTugas = (array) $infoRow;

        $subRows = DB::select("
            SELECT s.*, m.nama_murid, m.email 
            FROM tb_submission s
            JOIN tb_murid m ON s.nim = m.nim
            WHERE s.id_tugas = :id_tugas
            ORDER BY s.tgl_kumpul ASC
        ", ['id_tugas' => $id_tugas]);
        $daftarSubmission = array_map(fn($r) => (array) $r, $subRows);

        $pesan = session('pesan', '');
        $tipe_pesan = session('tipe_pesan', '');

        return view('penilaian.index', compact('id_tugas', 'infoTugas', 'daftarSubmission', 'pesan', 'tipe_pesan'));
    }

    public function storeNilai(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role !== 'Pengajar' && strtolower($role) !== 'pengajar' && strtolower($role) !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Pengajar.');
        }

        $id_tugas = $request->query('id_tugas') ?? $request->input('id_tugas');
        $id_submission = $request->input('id_submission');
        $nilai = $request->input('nilai');
        $catatan = trim((string) $request->input('catatan'));

        if ($nilai !== '' && $nilai !== null && ($nilai < 0 || $nilai > 100)) {
            return redirect()->route('penilaian.index', ['id_tugas' => $id_tugas])
                ->with('pesan', 'Gagal: Nilai harus berada di rentang 0 hingga 100!')
                ->with('tipe_pesan', 'error');
        }

        Submission::where('id_submission', $id_submission)->update([
            'nilai' => $nilai !== '' ? $nilai : null,
            'catatan' => $catatan,
        ]);

        return redirect()->route('penilaian.index', ['id_tugas' => $id_tugas])
            ->with('pesan', 'Nilai dan feedback berhasil disimpan!')
            ->with('tipe_pesan', 'success');
    }
}
