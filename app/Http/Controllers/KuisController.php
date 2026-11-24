<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
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
        if (!$user->isPengajar() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $id_kuis = (int) ($id_kuis ?? $request->query('id_kuis'));
        if ($id_kuis <= 0) {
            abort(404, 'ID Kuis tidak ditemukan.');
        }

        $kuisModel = Kuis::with('kelas')->find($id_kuis);
        if (!$kuisModel || !$kuisModel->kelas) {
            abort(404, 'Data kuis tidak ditemukan.');
        }

        if (!$kuisModel->kelas->canManage($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kuis kelas ini.');
        }

        $kuis = array_merge($kuisModel->toArray(), [
            'nama_kelas' => $kuisModel->kelas->nama_kelas,
            'id_kelas' => $kuisModel->kelas->id_kelas,
        ]);

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
        if (!$user->isPengajar() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $id_kuis = (int) ($id_kuis ?? $request->query('id_kuis') ?? $request->input('id_kuis'));
        $kuisModel = Kuis::with('kelas')->findOrFail($id_kuis);
        if (!$kuisModel->kelas || !$kuisModel->kelas->canManage($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kuis kelas ini.');
        }

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => ['required', 'string', 'max:255'],
            'opsi_b' => ['required', 'string', 'max:255'],
            'opsi_c' => ['required', 'string', 'max:255'],
            'opsi_d' => ['required', 'string', 'max:255'],
            'jawaban_benar' => ['required', 'in:A,B,C,D'],
        ]);

        SoalKuis::create([
            'id_kuis' => $id_kuis,
            'pertanyaan' => trim($validated['pertanyaan']),
            'opsi_a' => trim($validated['opsi_a']),
            'opsi_b' => trim($validated['opsi_b']),
            'opsi_c' => trim($validated['opsi_c']),
            'opsi_d' => trim($validated['opsi_d']),
            'jawaban_benar' => $validated['jawaban_benar'],
        ]);

        return redirect()->route('kuis.manage', ['id_kuis' => $id_kuis])
            ->with('pesan', 'Soal berhasil ditambahkan!');
    }

    public function updateSoal(Request $request, $id_kuis, $id_soal)
    {
        $user = auth()->user();
        if (!$user->isPengajar() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $kuisModel = Kuis::with('kelas')->findOrFail((int) $id_kuis);
        if (!$kuisModel->kelas || !$kuisModel->kelas->canManage($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kuis kelas ini.');
        }

        $soal = SoalKuis::where('id_soal', (int) $id_soal)
            ->where('id_kuis', $kuisModel->id_kuis)
            ->firstOrFail();

        $validated = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'opsi_a' => ['required', 'string', 'max:255'],
            'opsi_b' => ['required', 'string', 'max:255'],
            'opsi_c' => ['required', 'string', 'max:255'],
            'opsi_d' => ['required', 'string', 'max:255'],
            'jawaban_benar' => ['required', 'in:A,B,C,D'],
        ]);

        $soal->update([
            'pertanyaan' => trim($validated['pertanyaan']),
            'opsi_a' => trim($validated['opsi_a']),
            'opsi_b' => trim($validated['opsi_b']),
            'opsi_c' => trim($validated['opsi_c']),
            'opsi_d' => trim($validated['opsi_d']),
            'jawaban_benar' => $validated['jawaban_benar'],
        ]);

        return redirect()->route('kuis.manage', ['id_kuis' => $kuisModel->id_kuis])
            ->with('pesan', 'Soal berhasil diperbarui!');
    }

    public function deleteSoal(Request $request, $id_kuis, $id_soal)
    {
        $user = auth()->user();
        if (!$user->isPengajar() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $kuisModel = Kuis::with('kelas')->findOrFail((int) $id_kuis);
        if (!$kuisModel->kelas || !$kuisModel->kelas->canManage($user)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kuis kelas ini.');
        }

        SoalKuis::where('id_soal', (int) $id_soal)
            ->where('id_kuis', $kuisModel->id_kuis)
            ->delete();

        return redirect()->route('kuis.manage', ['id_kuis' => $kuisModel->id_kuis, 'msg' => 'deleted']);
    }

    public function kerjakan(Request $request, $id_kuis = null)
    {
        $user = auth()->user();
        if (!$user->isMurid()) {
            return redirect()->route('dashboard');
        }

        $id_kuis = (int) ($id_kuis ?? $request->query('id_kuis'));
        if ($id_kuis <= 0) {
            abort(404, 'ID Kuis tidak ditemukan.');
        }

        $mhs = Murid::where('id_user', $user->id_user)->first();
        if (!$mhs) {
            abort(403, 'Data murid tidak ditemukan.');
        }
        $nim = $mhs->nim;

        $kuisModel = Kuis::with('kelas')->find($id_kuis);
        if (!$kuisModel || !$kuisModel->kelas) {
            abort(404, 'Kuis tidak ditemukan.');
        }

        if (!$kuisModel->kelas->canView($user)) {
            abort(403, 'Akses ditolak. Anda tidak terdaftar di kelas ini.');
        }

        $kuis = array_merge($kuisModel->toArray(), [
            'nama_kelas' => $kuisModel->kelas->nama_kelas,
        ]);

        $sudahKerja = NilaiKuis::where('id_kuis', $id_kuis)->where('nim', $nim)->exists();
        if ($sudahKerja) {
            return redirect()->route('kelas.show', ['id' => $kuis['id_kelas']]);
        }

        $daftarSoal = SoalKuis::where('id_kuis', $id_kuis)
            ->orderBy('id_soal', 'asc')
            ->get()
            ->map(fn($s) => $s->toArray())
            ->toArray();

        $sessionKey = "kuis_started_at_{$id_kuis}_{$nim}";
        if (!session()->has($sessionKey)) {
            session()->put($sessionKey, now()->timestamp);
        }
        $elapsed = max(0, now()->timestamp - (int) session($sessionKey));
        $totalSeconds = max(60, (int) ($kuis['durasi_menit'] ?? 30) * 60);
        $remainingSeconds = max(0, $totalSeconds - $elapsed);

        return view('kuis.kerjakan', compact('id_kuis', 'kuis', 'daftarSoal', 'remainingSeconds'));
    }

    public function submit(Request $request, $id_kuis = null)
    {
        $user = auth()->user();
        if (!$user->isMurid()) {
            return redirect()->route('dashboard');
        }

        $id_kuis = (int) ($id_kuis ?? $request->query('id_kuis') ?? $request->input('id_kuis'));

        $mhs = Murid::where('id_user', $user->id_user)->first();
        if (!$mhs) {
            abort(403, 'Data murid tidak ditemukan.');
        }
        $nim = $mhs->nim;

        $kuisModel = Kuis::with('kelas')->findOrFail($id_kuis);
        if (!$kuisModel->kelas || !$kuisModel->kelas->canView($user)) {
            abort(403, 'Akses ditolak. Anda tidak terdaftar di kelas ini.');
        }

        $daftarSoal = SoalKuis::where('id_kuis', $id_kuis)->orderBy('id_soal', 'asc')->get();
        $benar = 0;
        $total_soal = $daftarSoal->count();

        foreach ($daftarSoal as $soal) {
            $id_soal = $soal->id_soal;
            $jawaban_murid = (string) $request->input('soal_' . $id_soal, '');
            if ($jawaban_murid === $soal->jawaban_benar) {
                $benar++;
            }
        }

        $nilai_akhir = ($total_soal > 0) ? round(($benar / $total_soal) * 100) : 0;

        $alreadySubmitted = DB::transaction(function () use ($id_kuis, $nim, $nilai_akhir) {
            $exists = NilaiKuis::where('id_kuis', $id_kuis)
                ->where('nim', $nim)
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                return true;
            }

            NilaiKuis::create([
                'id_kuis' => $id_kuis,
                'nim' => $nim,
                'nilai' => $nilai_akhir,
            ]);

            return false;
        });

        if ($alreadySubmitted) {
            return redirect()->route('kelas.show', ['id' => $kuisModel->id_kelas])
                ->with('pesan', 'Anda sudah pernah mengerjakan kuis ini.');
        }

        session()->forget("kuis_started_at_{$id_kuis}_{$nim}");

        return redirect()->route('kelas.show', [
            'id' => $kuisModel->id_kelas,
            'msg' => 'kuis_selesai',
        ]);
    }
}
