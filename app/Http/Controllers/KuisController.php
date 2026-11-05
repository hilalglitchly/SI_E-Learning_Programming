<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\NilaiKuis;
use App\Models\SoalKuis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuisController extends Controller
{
    public function manage(Request $request, $id_kuis = null)
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role !== 'Pengajar' && strtolower($role) !== 'pengajar' && strtolower($role) !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $id_kuis = $id_kuis ?? $request->query('id_kuis');
        if (empty($id_kuis)) {
            abort(404, 'ID Kuis tidak ditemukan.');
        }

        // Legacy support if ?delete_soal=ID is passed
        if ($request->has('delete_soal')) {
            SoalKuis::where('id_soal', $request->query('delete_soal'))
                ->where('id_kuis', $id_kuis)
                ->delete();
            return redirect()->route('kuis.manage', ['id_kuis' => $id_kuis, 'msg' => 'deleted']);
        }

        $kuisRow = DB::selectOne("
            SELECT ku.*, k.nama_kelas, k.id_kelas 
            FROM tb_kuis ku 
            JOIN tb_kelas k ON ku.id_kelas = k.id_kelas 
            WHERE ku.id_kuis = :id_kuis
        ", ['id_kuis' => $id_kuis]);

        if (!$kuisRow) {
            abort(404, 'Data kuis tidak ditemukan.');
        }
        $kuis = (array) $kuisRow;

        $daftarSoal = SoalKuis::where('id_kuis', $id_kuis)
            ->orderBy('id_soal', 'asc')
            ->get()
            ->map(fn($s) => $s->toArray())
            ->toArray();

        $pesan = session('pesan', '');
        if ($request->query('msg') === 'deleted') {
            $pesan = "Soal berhasil dihapus.";
        }

        return view('kuis.manage', compact('id_kuis', 'kuis', 'daftarSoal', 'pesan'));
    }

    public function storeSoal(Request $request, $id_kuis = null)
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role !== 'Pengajar' && strtolower($role) !== 'pengajar' && strtolower($role) !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $id_kuis = $id_kuis ?? $request->query('id_kuis') ?? $request->input('id_kuis');

        SoalKuis::create([
            'id_kuis' => $id_kuis,
            'pertanyaan' => trim((string) $request->input('pertanyaan')),
            'opsi_a' => trim((string) $request->input('opsi_a')),
            'opsi_b' => trim((string) $request->input('opsi_b')),
            'opsi_c' => trim((string) $request->input('opsi_c')),
            'opsi_d' => trim((string) $request->input('opsi_d')),
            'jawaban_benar' => $request->input('jawaban_benar'),
        ]);

        return redirect()->route('kuis.manage', ['id_kuis' => $id_kuis])
            ->with('pesan', 'Soal berhasil ditambahkan!');
    }

    public function deleteSoal(Request $request, $id_kuis, $id_soal)
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role !== 'Pengajar' && strtolower($role) !== 'pengajar' && strtolower($role) !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        SoalKuis::where('id_soal', $id_soal)
            ->where('id_kuis', $id_kuis)
            ->delete();

        return redirect()->route('kuis.manage', ['id_kuis' => $id_kuis, 'msg' => 'deleted']);
    }

    public function kerjakan(Request $request, $id_kuis = null)
    {
        $user = auth()->user();
        if (strtolower($user->role) !== 'murid') {
            return redirect()->route('dashboard');
        }

        $id_kuis = $id_kuis ?? $request->query('id_kuis');
        if (empty($id_kuis)) {
            abort(404, 'ID Kuis tidak ditemukan.');
        }

        $mhs = Murid::where('id_user', $user->id_user)->first();
        if (!$mhs) {
            abort(403, 'Data murid tidak ditemukan.');
        }
        $nim = $mhs->nim;

        $kuisRow = DB::selectOne("
            SELECT ku.*, k.nama_kelas 
            FROM tb_kuis ku 
            JOIN tb_kelas k ON ku.id_kelas = k.id_kelas 
            WHERE ku.id_kuis = :id_kuis
        ", ['id_kuis' => $id_kuis]);

        if (!$kuisRow) {
            abort(404, 'Kuis tidak ditemukan.');
        }
        $kuis = (array) $kuisRow;

        $sudahKerja = NilaiKuis::where('id_kuis', $id_kuis)->where('nim', $nim)->exists();
        if ($sudahKerja) {
            return redirect()->route('kelas.show', ['id' => $kuis['id_kelas']]);
        }

        $daftarSoal = SoalKuis::where('id_kuis', $id_kuis)
            ->orderBy('id_soal', 'asc')
            ->get()
            ->map(fn($s) => $s->toArray())
            ->toArray();

        return view('kuis.kerjakan', compact('id_kuis', 'kuis', 'daftarSoal'));
    }

    public function submit(Request $request, $id_kuis = null)
    {
        $user = auth()->user();
        if (strtolower($user->role) !== 'murid') {
            return redirect()->route('dashboard');
        }

        $id_kuis = $id_kuis ?? $request->query('id_kuis') ?? $request->input('id_kuis');

        $mhs = Murid::where('id_user', $user->id_user)->first();
        if (!$mhs) {
            abort(403, 'Data murid tidak ditemukan.');
        }
        $nim = $mhs->nim;

        $kuisRow = DB::selectOne("SELECT * FROM tb_kuis WHERE id_kuis = :id_kuis", ['id_kuis' => $id_kuis]);
        if (!$kuisRow) {
            abort(404, 'Kuis tidak ditemukan.');
        }

        $daftarSoal = SoalKuis::where('id_kuis', $id_kuis)->orderBy('id_soal', 'asc')->get();
        $benar = 0;
        $total_soal = $daftarSoal->count();

        foreach ($daftarSoal as $soal) {
            $id_soal = $soal->id_soal;
            $jawaban_murid = $request->input('soal_' . $id_soal, '');
            if ($jawaban_murid === $soal->jawaban_benar) {
                $benar++;
            }
        }

        $nilai_akhir = ($total_soal > 0) ? round(($benar / $total_soal) * 100) : 0;

        NilaiKuis::create([
            'id_kuis' => $id_kuis,
            'nim' => $nim,
            'nilai' => $nilai_akhir,
        ]);

        return redirect()->route('kelas.show', [
            'id' => $kuisRow->id_kelas,
            'msg' => 'kuis_selesai',
        ]);
    }
}
