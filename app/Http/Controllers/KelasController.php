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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $hasActivePro = $user->hasActiveSubscription();

        return view('enroll', compact('kelasTersedia', 'pesan', 'hasActivePro'));
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
        $id_kelas_ambil = (int) $request->input('id_kelas');

        $kelas = Kelas::find($id_kelas_ambil);
        if (!$kelas) {
            abort(404, 'Kelas tidak ditemukan.');
        }

        if ($kelas->is_premium && !$user->hasActiveSubscription()) {
            return redirect()->route('langganan.index')
                ->with('pesan', 'Kelas ini bersifat Premium. Aktifkan langganan Pro terlebih dahulu untuk bergabung.')
                ->with('tipe_pesan', 'error');
        }

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
        $id_kelas = (int) ($id ?? $request->query('id'));
        if ($id_kelas <= 0) {
            abort(404, 'ID Kelas tidak ditemukan.');
        }

        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role;
        $username = $user->username;

        $kelasModel = Kelas::find($id_kelas);
        if (!$kelasModel) {
            abort(404, 'Kelas tidak ditemukan.');
        }

        if (!$kelasModel->canView($user)) {
            // If enrolled in a Premium class whose Pro plan expired/inactive, guide student to subscription page
            if ($user->isMurid() && $kelasModel->is_premium && !$user->hasActiveSubscription()) {
                $nimCheck = Murid::where('id_user', $id_user)->value('nim');
                if ($nimCheck && PesertaKelas::where('id_kelas', $id_kelas)->where('nim', $nimCheck)->exists()) {
                    return redirect()->route('langganan.index')
                        ->with('pesan', 'Akses kelas Premium membutuhkan langganan Pro aktif. Perpanjang langganan untuk melanjutkan belajar.')
                        ->with('tipe_pesan', 'error');
                }
            }
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin mengakses kelas ini.');
        }

        $nim = '';
        if ($user->isMurid()) {
            $mhs = Murid::where('id_user', $id_user)->first();
            if (!$mhs) {
                abort(403, 'Data murid tidak ditemukan di database.');
            }
            $nim = $mhs->nim;
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
        $submissionMap = [];
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
                $progressValue = 0;
            }

            $submissions = Submission::where('nim', $nim)->get();
            foreach ($submissions as $sub) {
                $submittedTugasIds[] = $sub->id_tugas;
                $submissionMap[$sub->id_tugas] = [
                    'id_submission' => $sub->id_submission,
                    'nilai' => $sub->nilai,
                    'catatan' => $sub->catatan,
                    'tgl_kumpul' => $sub->tgl_kumpul ? $sub->tgl_kumpul->format('Y-m-d H:i') : '',
                ];
            }

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
                       (SELECT COALESCE(AVG(nilai), 0) FROM tb_submission s JOIN tb_tugas t ON s.id_tugas = t.id_tugas WHERE s.nim = m.nim AND t.id_kelas = pk.id_kelas AND s.nilai IS NOT NULL) as avg_tugas,
                       (SELECT COALESCE(AVG(nilai), 0) FROM tb_nilai_kuis nk JOIN tb_kuis k ON nk.id_kuis = k.id_kuis WHERE nk.nim = m.nim AND k.id_kelas = pk.id_kelas) as avg_kuis,
                       (SELECT COUNT(DISTINCT s2.id_tugas) FROM tb_submission s2 JOIN tb_tugas t2 ON s2.id_tugas = t2.id_tugas WHERE s2.nim = m.nim AND t2.id_kelas = pk.id_kelas) as tugas_selesai,
                       (SELECT COUNT(DISTINCT nk2.id_kuis) FROM tb_nilai_kuis nk2 JOIN tb_kuis k2 ON nk2.id_kuis = k2.id_kuis WHERE nk2.nim = m.nim AND k2.id_kelas = pk.id_kelas) as kuis_selesai
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
            'submissionMap',
            'nilaiKuisMap',
            'rekapNilai',
            'upcomingTugas',
            'feedList',
            'pesan'
        ));
    }

    public function handlePost(Request $request, $id)
    {
        $id_kelas = (int) $id;
        $user = auth()->user();
        $id_user = $user->id_user;
        $action = $request->input('action');

        $kelasModel = Kelas::findOrFail($id_kelas);

        $allowed_materi_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt', 'mp4'];
        $allowed_tugas_ext = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt'];

        // 1. Tambah Materi (Tab Materi)
        if ($action === 'tambah_materi') {
            if (!$kelasModel->canManage($user)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kelas ini.');
            }

            $judul = trim((string) $request->input('judul_materi'));
            $deskripsi = sanitize_rich_text((string) $request->input('hidden_deskripsi_materi'));
            if ($judul === '') {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Judul materi wajib diisi!');
            }

            $upload = $this->storePrivateUpload($request, 'file_materi', 'materi', $allowed_materi_ext, 'materi');
            if (!$upload['ok']) {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', $upload['error']);
            }

            Materi::create([
                'id_kelas' => $id_kelas,
                'judul_materi' => $judul,
                'deskripsi' => $deskripsi,
                'file_materi' => $upload['filename'],
            ]);

            return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Materi berhasil ditambahkan!');
        }

        // 2. Tambah Tugas (Tab Tugas)
        if ($action === 'tambah_tugas') {
            if (!$kelasModel->canManage($user)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kelas ini.');
            }

            $judul = trim((string) $request->input('judul_tugas'));
            $deskripsi = sanitize_rich_text((string) $request->input('hidden_deskripsi_tugas'));
            $deadline = $request->input('deadline_tugas');
            if ($judul === '' || empty($deadline)) {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Judul tugas dan deadline wajib diisi!');
            }

            $upload = $this->storePrivateUpload($request, 'file_tugas', 'tugas', $allowed_tugas_ext, 'tugas_ref');
            if (!$upload['ok']) {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', $upload['error']);
            }

            Tugas::create([
                'id_kelas' => $id_kelas,
                'judul_tugas' => $judul,
                'deskripsi' => $deskripsi,
                'file_tugas' => $upload['filename'],
                'deadline' => $deadline,
            ]);

            return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Tugas berhasil ditambahkan!');
        }

        // 3. Post Feed (Tab Upload)
        if ($action === 'post_feed') {
            if (!$kelasModel->canManage($user)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin mengelola kelas ini.');
            }

            $jenis_post = $request->input('jenis_post');
            $judul = trim((string) $request->input('judul'));
            $deskripsi = sanitize_rich_text((string) $request->input('deskripsi'));

            if ($judul === '') {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Judul wajib diisi!');
            }

            if ($jenis_post === 'materi') {
                $upload = $this->storePrivateUpload($request, 'file_materi', 'materi', $allowed_materi_ext, 'materi');
                if (!$upload['ok']) {
                    return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', $upload['error']);
                }

                Materi::create([
                    'id_kelas' => $id_kelas,
                    'judul_materi' => $judul,
                    'deskripsi' => $deskripsi,
                    'file_materi' => $upload['filename'],
                ]);
                return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_post']);
            } elseif ($jenis_post === 'tugas') {
                $deadline = $request->input('deadline');
                if (empty($deadline)) {
                    return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Deadline tugas wajib diisi!');
                }
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
        if ($action === 'post_diskusi') {
            if (!$kelasModel->canView($user) || $user->isPimpinan()) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin membuat diskusi di kelas ini.');
            }

            $judul_diskusi = trim((string) $request->input('judul_diskusi'));
            $isi_diskusi = sanitize_rich_text((string) $request->input('isi_diskusi'));
            if ($judul_diskusi === '' || trim(strip_tags($isi_diskusi)) === '') {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Judul dan isi diskusi wajib diisi!');
            }

            Diskusi::create([
                'id_kelas' => $id_kelas,
                'id_user' => $id_user,
                'judul_diskusi' => $judul_diskusi,
                'isi_diskusi' => $isi_diskusi,
            ]);
            return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_diskusi']);
        }

        // 5. Post Kuis (Tab Kuis)
        if ($action === 'post_kuis') {
            if (!$kelasModel->canManage($user)) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin membuat kuis di kelas ini.');
            }

            $judul_kuis = trim((string) $request->input('judul_kuis'));
            $deskripsi_kuis = trim((string) $request->input('deskripsi_kuis'));
            $durasi = (int) $request->input('durasi_menit', 30);
            if ($judul_kuis === '' || $durasi < 1 || $durasi > 600) {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Gagal: Judul kuis dan durasi (1–600 menit) wajib valid!');
            }

            Kuis::create([
                'id_kelas' => $id_kelas,
                'judul_kuis' => $judul_kuis,
                'deskripsi' => $deskripsi_kuis,
                'durasi_menit' => $durasi,
            ]);
            return redirect()->route('kelas.show', ['id' => $id_kelas, 'msg' => 'success_kuis']);
        }

        // 6. Upload Jawaban Tugas oleh Murid
        if ($request->hasFile('file_jawaban') && $request->filled('id_tugas')) {
            if (!$user->isMurid() || !$kelasModel->canView($user)) {
                abort(403, 'Akses ditolak. Anda tidak terdaftar sebagai murid di kelas ini.');
            }

            $mhs = Murid::where('id_user', $id_user)->first();
            if (!$mhs) {
                abort(403, 'Data murid tidak ditemukan.');
            }
            $nim = $mhs->nim;

            $id_tugas = (int) $request->input('id_tugas');
            $tugasValid = Tugas::where('id_tugas', $id_tugas)
                ->where('id_kelas', $id_kelas)
                ->exists();
            if (!$tugasValid) {
                abort(403, 'Tugas tidak ditemukan pada kelas ini.');
            }

            if (Submission::where('id_tugas', $id_tugas)->where('nim', $nim)->exists()) {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Anda sudah mengumpulkan jawaban untuk tugas ini.');
            }

            $upload = $this->storePrivateUpload($request, 'file_jawaban', 'submission', $allowed_tugas_ext, 'jawaban');
            if (!$upload['ok']) {
                return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', $upload['error']);
            }

            Submission::create([
                'id_tugas' => $id_tugas,
                'nim' => $nim,
                'file_jawaban' => $upload['filename'],
            ]);

            return redirect()->route('kelas.show', ['id' => $id_kelas])->with('pesan', 'Tugas berhasil dikumpulkan!');
        }

        return redirect()->route('kelas.show', ['id' => $id_kelas]);
    }

    public function updateMateri(Request $request, $id, $id_materi)
    {
        $kelas = Kelas::findOrFail((int) $id);
        if (!$kelas->canManage(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        $materi = Materi::where('id_materi', (int) $id_materi)->where('id_kelas', $kelas->id_kelas)->firstOrFail();
        $judul = trim((string) $request->input('judul_materi'));
        if ($judul === '' || mb_strlen($judul) > 150) {
            return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Gagal: Judul materi wajib diisi (maks. 150 karakter).');
        }

        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt', 'mp4'];
        $upload = $this->storePrivateUpload($request, 'file_materi', 'materi', $allowed, 'materi');
        if (!$upload['ok']) {
            return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', $upload['error']);
        }

        $newFile = $materi->file_materi;
        if ($upload['filename'] !== '') {
            $this->deletePrivateFile('materi', $materi->file_materi);
            $newFile = $upload['filename'];
        }

        $materi->update([
            'judul_materi' => $judul,
            'deskripsi' => sanitize_rich_text((string) $request->input('deskripsi')),
            'file_materi' => $newFile,
        ]);

        return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Materi berhasil diperbarui!');
    }

    public function deleteMateri($id, $id_materi)
    {
        $kelas = Kelas::findOrFail((int) $id);
        if (!$kelas->canManage(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        $materi = Materi::where('id_materi', (int) $id_materi)->where('id_kelas', $kelas->id_kelas)->firstOrFail();
        $this->deletePrivateFile('materi', $materi->file_materi);
        $materi->delete();

        return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Materi berhasil dihapus.');
    }

    public function updateTugas(Request $request, $id, $id_tugas)
    {
        $kelas = Kelas::findOrFail((int) $id);
        if (!$kelas->canManage(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        $tugas = Tugas::where('id_tugas', (int) $id_tugas)->where('id_kelas', $kelas->id_kelas)->firstOrFail();
        $judul = trim((string) $request->input('judul_tugas'));
        $deadline = $request->input('deadline_tugas');
        if ($judul === '' || mb_strlen($judul) > 150 || empty($deadline)) {
            return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Gagal: Judul tugas (maks. 150 karakter) dan deadline wajib diisi.');
        }

        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt'];
        $upload = $this->storePrivateUpload($request, 'file_tugas', 'tugas', $allowed, 'tugas_ref');
        if (!$upload['ok']) {
            return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', $upload['error']);
        }

        $newFile = $tugas->file_tugas;
        if ($upload['filename'] !== '') {
            $this->deletePrivateFile('tugas', $tugas->file_tugas);
            $newFile = $upload['filename'];
        }

        $tugas->update([
            'judul_tugas' => $judul,
            'deskripsi' => sanitize_rich_text((string) $request->input('deskripsi')),
            'file_tugas' => $newFile,
            'deadline' => $deadline,
        ]);

        return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Tugas berhasil diperbarui!');
    }

    public function deleteTugas($id, $id_tugas)
    {
        $kelas = Kelas::findOrFail((int) $id);
        if (!$kelas->canManage(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        $tugas = Tugas::where('id_tugas', (int) $id_tugas)->where('id_kelas', $kelas->id_kelas)->firstOrFail();
        $this->deletePrivateFile('tugas', $tugas->file_tugas);
        foreach ($tugas->submissions as $sub) {
            $this->deletePrivateFile('submission', $sub->file_jawaban);
        }
        $tugas->delete();

        return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Tugas berhasil dihapus.');
    }

    public function updateKuis(Request $request, $id, $id_kuis)
    {
        $kelas = Kelas::findOrFail((int) $id);
        if (!$kelas->canManage(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        $kuis = Kuis::where('id_kuis', (int) $id_kuis)->where('id_kelas', $kelas->id_kelas)->firstOrFail();
        $judul = trim((string) $request->input('judul_kuis'));
        $durasi = (int) $request->input('durasi_menit', 30);
        if ($judul === '' || mb_strlen($judul) > 150 || $durasi < 1 || $durasi > 600) {
            return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Gagal: Judul kuis dan durasi (1–600 menit) wajib valid.');
        }

        $kuis->update([
            'judul_kuis' => $judul,
            'deskripsi' => trim((string) $request->input('deskripsi_kuis')),
            'durasi_menit' => $durasi,
        ]);

        return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Kuis berhasil diperbarui!');
    }

    public function deleteKuis($id, $id_kuis)
    {
        $kelas = Kelas::findOrFail((int) $id);
        if (!$kelas->canManage(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        $kuis = Kuis::where('id_kuis', (int) $id_kuis)->where('id_kelas', $kelas->id_kelas)->firstOrFail();
        $kuis->delete();

        return redirect()->route('kelas.show', ['id' => $kelas->id_kelas])->with('pesan', 'Kuis berhasil dihapus.');
    }

    public function downloadMateri($id)
    {
        $materi = Materi::with('kelas')->findOrFail((int) $id);
        if (!$materi->kelas || !$materi->kelas->canView(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        return $this->streamPrivateFile('materi', 'materi', $materi->file_materi);
    }

    public function downloadTugas($id)
    {
        $tugas = Tugas::with('kelas')->findOrFail((int) $id);
        if (!$tugas->kelas || !$tugas->kelas->canView(auth()->user())) {
            abort(403, 'Akses ditolak.');
        }

        return $this->streamPrivateFile('tugas', 'tugas', $tugas->file_tugas);
    }

    public function downloadSubmission($id)
    {
        $submission = Submission::with('tugas.kelas')->findOrFail((int) $id);
        $user = auth()->user();
        $kelas = $submission->tugas?->kelas;

        $isOwnerStudent = $user->isMurid()
            && Murid::where('id_user', $user->id_user)->where('nim', $submission->nim)->exists();

        if (!$kelas || (!$kelas->canManage($user) && !$isOwnerStudent)) {
            abort(403, 'Akses ditolak.');
        }

        return $this->streamPrivateFile('submission', 'tugas', $submission->file_jawaban);
    }

    private function storePrivateUpload(Request $request, string $field, string $folder, array $allowedExt, string $prefix, int $maxKb = 10240): array
    {
        if (!$request->hasFile($field)) {
            return ['ok' => true, 'filename' => ''];
        }

        $file = $request->file($field);
        if (!$file || !$file->isValid()) {
            return ['ok' => false, 'error' => 'Terjadi kesalahan saat mengunggah file.'];
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $validator = Validator::make($request->all(), [
            $field => ['file', 'max:' . $maxKb, 'mimes:' . implode(',', $allowedExt)],
        ]);

        if ($validator->fails() || !in_array($ext, $allowedExt, true)) {
            return ['ok' => false, 'error' => 'Gagal: Format file tidak diizinkan atau ukuran melebihi ' . ($maxKb / 1024) . ' MB!'];
        }

        $safeName = $prefix . '_' . time() . '_' . Str::random(16) . '.' . $ext;
        Storage::disk('local')->putFileAs('private/' . $folder, $file, $safeName);

        return ['ok' => true, 'filename' => $safeName];
    }

    private function streamPrivateFile(string $folder, string $legacyPublicSubdir, ?string $filename)
    {
        $safeName = basename((string) $filename);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $privateRelPath = 'private/' . $folder . '/' . $safeName;
        if (Storage::disk('local')->exists($privateRelPath)) {
            return response()->download(Storage::disk('local')->path($privateRelPath), $safeName, [
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $legacyPath = public_path('uploads/' . $legacyPublicSubdir . '/' . $safeName);
        if (is_file($legacyPath)) {
            return response()->download($legacyPath, $safeName, [
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        abort(404, 'Berkas tidak ditemukan di server.');
    }

    private function deletePrivateFile(string $folder, ?string $filename): void
    {
        $safeName = basename((string) $filename);
        if ($safeName === '' || $safeName === '.' || $safeName === '..') {
            return;
        }
        $rel = 'private/' . $folder . '/' . $safeName;
        if (Storage::disk('local')->exists($rel)) {
            Storage::disk('local')->delete($rel);
        }
    }
}
