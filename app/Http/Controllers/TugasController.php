<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Tugas;
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
            $premiumFilter = $user->hasActiveSubscription() ? '' : 'AND k.is_premium = 0';
            $rows = DB::select("
                SELECT t.*, k.nama_kelas 
                FROM tb_tugas t
                JOIN tb_kelas k ON t.id_kelas = k.id_kelas
                JOIN tb_peserta_kelas pk ON k.id_kelas = pk.id_kelas
                JOIN tb_murid m ON pk.nim = m.nim
                WHERE m.id_user = :id_user
                {$premiumFilter}
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
        if (!$user->isPengajar() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Pengajar.');
        }

        $id_tugas = (int) $request->query('id_tugas');
        if ($id_tugas <= 0) {
            abort(404, 'ID Tugas tidak ditemukan.');
        }

        $tugasModel = Tugas::with('kelas')->find($id_tugas);
        if (!$tugasModel || !$tugasModel->kelas) {
            abort(404, 'Data tugas tidak ditemukan.');
        }

        if (!$tugasModel->kelas->canManage($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin menilai tugas kelas ini.');
        }

        $infoRow = DB::selectOne("
            SELECT t.*, k.nama_kelas 
            FROM tb_tugas t 
            JOIN tb_kelas k ON t.id_kelas = k.id_kelas 
            WHERE t.id_tugas = :id_tugas
        ", ['id_tugas' => $id_tugas]);

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
        if (!$user->isPengajar() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Pengajar.');
        }

        $id_tugas = (int) ($request->query('id_tugas') ?? $request->input('id_tugas'));
        $id_submission = (int) $request->input('id_submission');
        $nilaiRaw = $request->input('nilai');
        $catatan = trim((string) $request->input('catatan'));

        $tugasModel = Tugas::with('kelas')->findOrFail($id_tugas);
        if (!$tugasModel->kelas || !$tugasModel->kelas->canManage($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin menilai tugas kelas ini.');
        }

        $submission = Submission::where('id_submission', $id_submission)
            ->where('id_tugas', $tugasModel->id_tugas)
            ->firstOrFail();

        $nilai = null;
        if ($nilaiRaw !== '' && $nilaiRaw !== null) {
            if (!is_numeric($nilaiRaw) || (int) $nilaiRaw < 0 || (int) $nilaiRaw > 100) {
                return redirect()->route('penilaian.index', ['id_tugas' => $id_tugas])
                    ->with('pesan', 'Gagal: Nilai harus berupa angka di rentang 0 hingga 100!')
                    ->with('tipe_pesan', 'error');
            }
            $nilai = (int) $nilaiRaw;
        }

        $submission->update([
            'nilai' => $nilai,
            'catatan' => $catatan,
        ]);

        return redirect()->route('penilaian.index', ['id_tugas' => $id_tugas])
            ->with('pesan', 'Nilai dan feedback berhasil disimpan!')
            ->with('tipe_pesan', 'success');
    }
}
