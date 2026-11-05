<?php

namespace App\Http\Controllers;

use App\Models\Diskusi;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\NilaiKuis;
use App\Models\PesertaKelas;
use App\Models\Submission;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function enrollIndex(Request $request)
    {
        $user = auth()->user();
        if (strtolower($user->role) !== 'murid') {
            return redirect()->route('dashboard');
        }

        $murid = Murid::where('id_user', $user->id_user)->first();
        if (!$murid) {
            abort(403, 'Data profil murid tidak ditemukan.');
        }

        $nim = $murid->nim;
        $pesan = session('pesan', '');

        $rows = DB::select("
            SELECT k.*, p.nama_pengajar 
            FROM tb_kelas k
            LEFT JOIN tb_pengajar p ON k.nidn = p.nidn
            WHERE k.id_kelas NOT IN (
                SELECT id_kelas FROM tb_peserta_kelas WHERE nim = :nim
            )
        ", ['nim' => $nim]);

        $kelasTersedia = array_map(fn($r) => (array) $r, $rows);

        return view('enroll', compact('kelasTersedia', 'pesan'));
    }

    public function enrollStore(Request $request)
    {
        $user = auth()->user();
        if (strtolower($user->role) !== 'murid') {
            return redirect()->route('dashboard');
        }

        $murid = Murid::where('id_user', $user->id_user)->first();
        if (!$murid) {
            abort(403, 'Data profil murid tidak ditemukan.');
        }

        $nim = $murid->nim;
        $id_kelas_ambil = $request->input('id_kelas');

        $exists = PesertaKelas::where('id_kelas', $id_kelas_ambil)
            ->where('nim', $nim)
            ->exists();

        if (!$exists) {
            PesertaKelas::create([
                'id_kelas' => $id_kelas_ambil,
                'nim' => $nim,
            ]);
            return redirect()->route('dashboard');
        }

        return redirect()->route('enroll.index')->with('pesan', 'Anda sudah terdaftar di kelas ini.');
    }

    public function show(Request $request, $id = null)
    {
        $id_kelas = $id ?? $request->query('id');
        if (empty($id_kelas)) {
            abort(404, 'ID Kelas tidak ditemukan.');
        }

        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role;
        $username = $user->username;

        $nim = '';
        if (strtolower($role) === 'murid') {
            $mhs = Murid::where('id_user', $id_user)->first();
            if ($mhs) {
                $nim = $mhs->nim;
                $terdaftar = PesertaKelas::where('id_kelas', $id_kelas)
                    ->where('nim', $nim)
                    ->exists();
                if (!$terdaftar) {
                    abort(403, 'Akses Ditolak. Anda tidak terdaftar di kelas ini.');
                }
            } else {
                abort(403, 'Data murid tidak ditemukan di database.');
            }
        }

        $kelasRow = DB::selectOne("
            SELECT k.*, p.nama_pengajar, p.email as email_pengajar 
            FROM tb_kelas k 
            LEFT JOIN tb_pengajar p ON k.nidn = p.nidn 
            WHERE k.id_kelas = :id_kelas
        ", ['id_kelas' => $id_kelas]);

        if (!$kelasRow) {
            abort(404, 'Kelas tidak ditemukan.');
        }
        $kelasInfo = (array) $kelasRow;

        $materiList = Materi::where('id_kelas', $id_kelas)
            ->orderBy('tgl_upload', 'desc')
            ->get()
            ->map(fn($m) => [
                'id_materi' => $m->id_materi,
                'id_kelas' => $m->id_kelas,
                'judul_materi' => $m->judul_materi,
                'deskripsi' => $m->deskripsi,
                'file_materi' => $m->file_materi,
                'tgl_upload' => $m->tgl_upload ? $m->tgl_upload->format('Y-m-d H:i:s') : '',
            ])
            ->toArray();

        $tugasList = Tugas::where('id_kelas', $id_kelas)
            ->orderBy('deadline', 'asc')
            ->get()
            ->map(fn($t) => [
                'id_tugas' => $t->id_tugas,
                'id_kelas' => $t->id_kelas,
                'judul_tugas' => $t->judul_tugas,
                'deskripsi' => $t->deskripsi,
                'file_tugas' => $t->file_tugas,
                'deadline' => $t->deadline ? $t->deadline->format('Y-m-d H:i:s') : '',
            ])
            ->toArray();

        $pesertaRows = DB::select("
            SELECT m.nama_murid, m.nim 
            FROM tb_peserta_kelas pk 
            JOIN tb_murid m ON pk.nim = m.nim 
            WHERE pk.id_kelas = :id_kelas
        ", ['id_kelas' => $id_kelas]);
        $pesertaList = array_map(fn($r) => (array) $r, $pesertaRows);

        $diskusiRows = DB::select("
            SELECT d.*, u.username, u.role 
            FROM tb_diskusi d 
            JOIN tb_user u ON d.id_user = u.id_user 
            WHERE d.id_kelas = :id_kelas 
            ORDER BY d.tgl_post DESC
        ", ['id_kelas' => $id_kelas]);
        $diskusiList = array_map(fn($r) => (array) $r, $diskusiRows);

        $kuisRows = DB::select("
            SELECT * FROM tb_kuis WHERE id_kelas = :id_kelas ORDER BY tgl_dibuat DESC
        ", ['id_kelas' => $id_kelas]);
        $kuisList = array_map(fn($r) => (array) $r, $kuisRows);

        // Progress belajar untuk Murid
        $progressValue = 0;
        $submittedTugasIds = [];
        $nilaiKuisMap = [];

        if (strtolower($role) === 'murid' && !empty($nim)) {
            $totalTugas = count($tugasList);
            $tugasDoneRow = DB::selectOne("
                SELECT COUNT(DISTINCT s.id_tugas) as done 
                FROM tb_submission s 
                JOIN tb_tugas t ON s.id_tugas = t.id_tugas 
                WHERE t.id_kelas = :id_kelas AND s.nim = :nim
            ", ['id_kelas' => $id_kelas, 'nim' => $nim]);
            $tugasDone = $tugasDoneRow ? (int) $tugasDoneRow->done : 0;

            $totalKuis = count($kuisList);
            $kuisDoneRow = DB::selectOne("
                SELECT COUNT(DISTINCT n.id_kuis) as done 
                FROM tb_nilai_kuis n 
                JOIN tb_kuis k ON n.id_kuis = k.id_kuis 
                WHERE k.id_kelas = :id_kelas AND n.nim = :nim
            ", ['id_kelas' => $id_kelas, 'nim' => $nim]);
            $kuisDone = $kuisDoneRow ? (int) $kuisDoneRow->done : 0;

            $totalItems = $totalTugas + $totalKuis;
            $totalDone = $tugasDone + $kuisDone;

            if ($totalItems > 0) {
                $progressValue = round(($totalDone / $totalItems) * 100);
                if ($progressValue > 100) $progressValue = 100;
            } else {
                $progressValue = 100;
            }

            $submittedTugasIds = Submission::where('nim', $nim)->pluck('id_tugas')->toArray();

            $nilaiRows = NilaiKuis::where('nim', $nim)->get();
            foreach ($nilaiRows as $nr) {
                $nilaiKuisMap[$nr->id_kuis] = $nr->nilai;
            }
        }

        // Rekap Nilai untuk Pengajar / Admin
        $rekapNilai = [];
        if ($role === 'Pengajar' || strtolower($role) === 'pengajar' || strtolower($role) === 'admin') {
            $rekapRows = DB::select("
                SELECT m.nim, m.nama_murid,
                       (SELECT IFNULL(AVG(nilai), 0) FROM tb_submission s JOIN tb_tugas t ON s.id_tugas = t.id_tugas WHERE s.nim = m.nim AND t.id_kelas = pk.id_kelas) as avg_tugas,
                       (SELECT IFNULL(AVG(nilai), 0) FROM tb_nilai_kuis nk JOIN tb_kuis k ON nk.id_kuis = k.id_kuis WHERE nk.nim = m.nim AND k.id_kelas = pk.id_kelas) as avg_kuis
                FROM tb_peserta_kelas pk
                JOIN tb_murid m ON pk.nim = m.nim
                WHERE pk.id_kelas = :id_kelas
            ", ['id_kelas' => $id_kelas]);
            $rekapNilai = array_map(fn($r) => (array) $r, $rekapRows);
        }

        // Prepare Tugas Mendatang (top 2 closest future deadline)
        $upcomingTugas = [];
        $now = new \DateTime();
        foreach ($tugasList as $t) {
            if (!empty($t['deadline'])) {
                $deadline = new \DateTime($t['deadline']);
                if ($deadline >= $now && !in_array($t['id_tugas'], $submittedTugasIds)) {
                    $upcomingTugas[] = $t;
                }
            }
        }
        $upcomingTugas = array_slice($upcomingTugas, 0, 2);

        // Prepare Feed (Gabungan Materi & Tugas)
        $feedList = [];
        foreach ($materiList as $m) {
            $feedList[] = [
                'type' => 'materi',
                'id' => $m['id_materi'],
                'judul' => $m['judul_materi'],
                'deskripsi' => $m['deskripsi'],
                'tanggal' => $m['tgl_upload'],
                'file' => $m['file_materi'],
            ];
        }
        foreach ($tugasList as $t) {
            $feedList[] = [
                'type' => 'tugas',
                'id' => $t['id_tugas'],
                'judul' => $t['judul_tugas'],
                'deskripsi' => $t['deskripsi'],
                'tanggal' => $t['deadline'],
                'deadline' => $t['deadline'],
            ];
        }

        usort($feedList, function ($a, $b) {
            return strtotime((string) $b['tanggal']) - strtotime((string) $a['tanggal']);
        });

        $pesan = session('pesan', '');
        if ($request->query('msg') === 'success_post') {
            $pesan = "Postingan berhasil ditambahkan!";
        } elseif ($request->query('msg') === 'success_diskusi') {
            $pesan = "Topik diskusi berhasil dibuat!";
        } elseif ($request->query('msg') === 'success_kuis') {
            $pesan = "Kuis berhasil dibuat!";
        } elseif ($request->query('msg') === 'kuis_selesai') {
            $pesan = "Kuis berhasil diselesaikan!";
        }

        return view('kelas.detail', compact(
            'id_kelas',
            'role',
            'username',
            'nim',
            'kelasInfo',
            'materiList',
            'tugasList',
            'pesertaList',
            'diskusiList',
            'kuisList',
            'progressValue',
            'submittedTugasIds',
            'nilaiKuisMap',
            'rekapNilai',
            'upcomingTugas',
            'feedList',
            'pesan'
        ));
    }

    public function handlePost(Request $request, $id)
    {
        $id_kelas = $id;
        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role;
        $action = $request->input('action');

        $allowed_materi_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt', 'mp4'];
        $allowed_tugas_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt'];

        // 1. Tambah Materi (Tab Materi)
        if ($action === 'tambah_materi' && in_array(strtolower($role), ['pengajar', 'admin', 'pimpinan'])) {
            $judul = $request->input('judul_materi');
            $deskripsi = $request->input('hidden_deskripsi_materi');
            $nama_file = '';

            if ($request->hasFile('file_materi') && $request->file('file_materi')->isValid()) {
                $file = $request->file('file_materi');
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowed_materi_ext)) {
                    return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Ekstensi file tidak diizinkan!');
                }
                $materi_dir = public_path('uploads/materi');
                if (!is_dir($materi_dir)) mkdir($materi_dir, 0777, true);
                $nama_file = 'materi_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $file->move($materi_dir, $nama_file);
            }

            Materi::create([
                'id_kelas' => $id_kelas,
                'judul_materi' => $judul,
                'deskripsi' => $deskripsi,
                'file_materi' => $nama_file,
            ]);

            return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Materi berhasil ditambahkan!');
        }

        // 2. Tambah Tugas (Tab Tugas)
        if ($action === 'tambah_tugas' && in_array(strtolower($role), ['pengajar', 'admin', 'pimpinan'])) {
            $judul = $request->input('judul_tugas');
            $deskripsi = $request->input('hidden_deskripsi_tugas');
            $deadline = $request->input('deadline_tugas');
            $nama_file = '';

            if ($request->hasFile('file_tugas') && $request->file('file_tugas')->isValid()) {
                $file = $request->file('file_tugas');
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowed_tugas_ext)) {
                    return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Ekstensi file tidak diizinkan!');
                }
                $tugas_dir = public_path('uploads/tugas');
                if (!is_dir($tugas_dir)) mkdir($tugas_dir, 0777, true);
                $nama_file = 'tugas_ref_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $file->move($tugas_dir, $nama_file);
            }

            Tugas::create([
                'id_kelas' => $id_kelas,
                'judul_tugas' => $judul,
                'deskripsi' => $deskripsi,
                'file_tugas' => $nama_file,
                'deadline' => $deadline,
            ]);

            return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Tugas berhasil ditambahkan!');
        }

        // 3. Post Feed (Tab Upload)
        if ($action === 'post_feed' && ($role === 'Pengajar' || strtolower($role) === 'pengajar' || strtolower($role) === 'admin')) {
            $jenis_post = $request->input('jenis_post');
            $judul = $request->input('judul');
            $deskripsi = $request->input('deskripsi');

            if ($jenis_post === 'materi') {
                $file_materi = '';
                if ($request->hasFile('file_materi') && $request->file('file_materi')->isValid()) {
                    $file = $request->file('file_materi');
                    $ext = strtolower($file->getClientOriginalExtension());
                    if (!in_array($ext, $allowed_materi_ext)) {
                        return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Ekstensi file tidak diizinkan!');
                    }
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $targetDir = public_path('uploads/materi');
                    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                    $file->move($targetDir, $fileName);
                    $file_materi = $fileName;
                }
                Materi::create([
                    'id_kelas' => $id_kelas,
                    'judul_materi' => $judul,
                    'deskripsi' => $deskripsi,
                    'file_materi' => $file_materi,
                ]);
                return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_post']);
            } elseif ($jenis_post === 'tugas') {
                $deadline = $request->input('deadline');
                Tugas::create([
                    'id_kelas' => $id_kelas,
                    'judul_tugas' => $judul,
                    'deskripsi' => $deskripsi,
                    'deadline' => $deadline,
                ]);
                return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_post']);
            }
        }

        // 4. Post Diskusi (Tab Diskusi)
        if ($action === 'post_diskusi' && (strtolower($role) === 'murid' || $role === 'Pengajar' || strtolower($role) === 'pengajar')) {
            Diskusi::create([
                'id_kelas' => $id_kelas,
                'id_user' => $id_user,
                'judul_diskusi' => $request->input('judul_diskusi'),
                'isi_diskusi' => $request->input('isi_diskusi'),
            ]);
            return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_diskusi']);
        }

        // 5. Post Kuis (Tab Kuis)
        if ($action === 'post_kuis' && ($role === 'Pengajar' || strtolower($role) === 'pengajar' || strtolower($role) === 'admin')) {
            Kuis::create([
                'id_kelas' => $id_kelas,
                'judul_kuis' => $request->input('judul_kuis'),
                'deskripsi' => $request->input('deskripsi_kuis'),
                'durasi_menit' => $request->input('durasi_menit'),
            ]);
            return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_kuis']);
        }

        // 6. Upload Jawaban Tugas oleh Murid
        if ($request->hasFile('file_jawaban') && $request->filled('id_tugas')) {
            $mhs = Murid::where('id_user', $id_user)->first();
            $nim = $mhs ? $mhs->nim : '';

            $id_tugas = $request->input('id_tugas');
            $file = $request->file('file_jawaban');

            if ($file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowed_tugas_ext)) {
                    return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Ekstensi file tidak diizinkan!');
                }
                $fileName = time() . '_' . $file->getClientOriginalName();
                $targetDir = public_path('uploads/tugas');
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                $file->move($targetDir, $fileName);

                Submission::create([
                    'id_tugas' => $id_tugas,
                    'nim' => $nim,
                    'file_jawaban' => $fileName,
                ]);

                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Tugas berhasil dikumpulkan!');
            }

            return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Terjadi kesalahan saat mengunggah file.');
        }

        return redirect()->route('kelas.show', ['id' => $id_kelas]);
    }
}
