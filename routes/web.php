<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\KuisController;
use App\Http\Controllers\DiskusiController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - SI E-Learning Programming (Laravel MVC)
|--------------------------------------------------------------------------
*/

// 1. Landing Page & Public Forum
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/kontak', [HomeController::class, 'sendKontak'])->middleware('throttle:5,10')->name('kontak.send');
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/buat', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum/buat', [ForumController::class, 'store'])->name('forum.store');
Route::get('/forum/{id}', [ForumController::class, 'show'])->whereNumber('id')->name('forum.show');
Route::post('/forum/{id}', [ForumController::class, 'handlePost'])->whereNumber('id')->name('forum.post');

// 2. Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. AJAX API Endpoints (Session-aware)
Route::match(['get', 'post'], '/api/notifikasi', [NotifikasiController::class, 'handle'])->name('api.notifikasi');
Route::post('/api/forum/vote', [ForumController::class, 'vote'])->name('api.forum.vote');
Route::post('/api/forum/simpan', [ForumController::class, 'simpan'])->name('api.forum.simpan');

// 4. Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Enroll / Cari Kelas (Murid)
    Route::middleware('role:murid')->group(function () {
        Route::get('/enroll', [KelasController::class, 'enrollIndex'])->name('enroll.index');
        Route::post('/enroll', [KelasController::class, 'enrollStore'])->name('enroll.store');
        Route::get('/kuis/{id_kuis}/kerjakan', [KuisController::class, 'kerjakan'])->whereNumber('id_kuis')->name('kuis.kerjakan');
        Route::post('/kuis/{id_kuis}/kerjakan', [KuisController::class, 'submit'])->whereNumber('id_kuis')->name('kuis.submit');
    });

    // Detail Kelas & Stream/Feed/Materi/Tugas/Diskusi/Kuis Actions
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->whereNumber('id')->name('kelas.show');
    Route::post('/kelas/{id}', [KelasController::class, 'handlePost'])->whereNumber('id')->name('kelas.post');

    Route::middleware('role:pengajar,admin')->group(function () {
        Route::post('/kelas/{id}/materi/{id_materi}', [KelasController::class, 'updateMateri'])->whereNumber(['id', 'id_materi'])->name('kelas.materi.update');
        Route::delete('/kelas/{id}/materi/{id_materi}', [KelasController::class, 'deleteMateri'])->whereNumber(['id', 'id_materi'])->name('kelas.materi.delete');
        Route::post('/kelas/{id}/tugas/{id_tugas}', [KelasController::class, 'updateTugas'])->whereNumber(['id', 'id_tugas'])->name('kelas.tugas.update');
        Route::delete('/kelas/{id}/tugas/{id_tugas}', [KelasController::class, 'deleteTugas'])->whereNumber(['id', 'id_tugas'])->name('kelas.tugas.delete');
        Route::post('/kelas/{id}/kuis/{id_kuis}', [KelasController::class, 'updateKuis'])->whereNumber(['id', 'id_kuis'])->name('kelas.kuis.update');
        Route::delete('/kelas/{id}/kuis/{id_kuis}', [KelasController::class, 'deleteKuis'])->whereNumber(['id', 'id_kuis'])->name('kelas.kuis.delete');
    });

    // Unduh Berkas Privat Terautentikasi
    Route::get('/files/materi/{id}', [KelasController::class, 'downloadMateri'])->whereNumber('id')->name('files.materi');
    Route::get('/files/tugas/{id}', [KelasController::class, 'downloadTugas'])->whereNumber('id')->name('files.tugas');
    Route::get('/files/submission/{id}', [KelasController::class, 'downloadSubmission'])->whereNumber('id')->name('files.submission');

    // Tugas Global & Penilaian
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::middleware('role:pengajar,admin')->group(function () {
        Route::get('/penilaian', [TugasController::class, 'penilaian'])->name('penilaian.index');
        Route::post('/penilaian', [TugasController::class, 'storeNilai'])->name('penilaian.store');

        // Kuis (Kelola Soal)
        Route::get('/kuis/{id_kuis}/manage', [KuisController::class, 'manage'])->whereNumber('id_kuis')->name('kuis.manage');
        Route::post('/kuis/{id_kuis}/manage', [KuisController::class, 'storeSoal'])->whereNumber('id_kuis')->name('kuis.soal.store');
        Route::post('/kuis/{id_kuis}/soal/{id_soal}', [KuisController::class, 'updateSoal'])->whereNumber(['id_kuis', 'id_soal'])->name('kuis.soal.update');
        Route::delete('/kuis/{id_kuis}/soal/{id_soal}', [KuisController::class, 'deleteSoal'])->whereNumber(['id_kuis', 'id_soal'])->name('kuis.soal.delete');
    });

    // Diskusi Kelas Detail (Edit & Hapus)
    Route::get('/diskusi/{id}', [DiskusiController::class, 'show'])->whereNumber('id')->name('diskusi.show');
    Route::post('/diskusi/{id}', [DiskusiController::class, 'storeBalasan'])->whereNumber('id')->name('diskusi.balasan.store');
    Route::post('/diskusi/{id}/update', [DiskusiController::class, 'update'])->whereNumber('id')->name('diskusi.update');
    Route::delete('/diskusi/{id}', [DiskusiController::class, 'destroy'])->whereNumber('id')->name('diskusi.delete');
    Route::post('/diskusi/{id}/balasan/{id_balasan}', [DiskusiController::class, 'updateBalasan'])->whereNumber(['id', 'id_balasan'])->name('diskusi.balasan.update');
    Route::delete('/diskusi/{id}/balasan/{id_balasan}', [DiskusiController::class, 'deleteBalasan'])->whereNumber(['id', 'id_balasan'])->name('diskusi.balasan.delete');

    // Moderasi & Edit Forum (Penulis / Admin)
    Route::post('/forum/{id}/update', [ForumController::class, 'update'])->whereNumber('id')->name('forum.update');
    Route::delete('/forum/{id}', [ForumController::class, 'destroy'])->whereNumber('id')->name('forum.delete');
    Route::post('/forum/{id}/jawaban/{id_jawaban}', [ForumController::class, 'updateJawaban'])->whereNumber(['id', 'id_jawaban'])->name('forum.jawaban.update');
    Route::delete('/forum/{id}/jawaban/{id_jawaban}', [ForumController::class, 'deleteJawaban'])->whereNumber(['id', 'id_jawaban'])->name('forum.jawaban.delete');

    // Laporan Rekapitulasi (Admin & Pimpinan)
    Route::get('/laporan', [LaporanController::class, 'index'])->middleware('role:admin,pimpinan')->name('laporan.index');

    // Profil, Edit Profil & Ganti Password
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil', [ProfilController::class, 'updatePassword'])->name('profil.password');
    Route::post('/profil/info', [ProfilController::class, 'updateProfile'])->name('profil.update');

    // Manajemen Pengguna & Kelas (Admin)
    Route::prefix('admin/users')->name('admin.users.')->middleware('role:admin')->group(function () {
        Route::get('/tambah', [AdminController::class, 'createUser'])->name('create');
        Route::post('/tambah', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'editUser'])->whereNumber('id')->name('edit');
        Route::post('/{id}/edit', [AdminController::class, 'updateUser'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteUser'])->whereNumber('id')->name('delete');
    });

    Route::prefix('admin/kelas')->name('admin.kelas.')->middleware('role:admin')->group(function () {
        Route::post('/', [AdminController::class, 'storeKelas'])->name('store');
        Route::post('/{id}', [AdminController::class, 'updateKelas'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [AdminController::class, 'deleteKelas'])->whereNumber('id')->name('delete');
    });
});

// 5. Legacy .php URL Compatibility Redirects (controller-backed so `artisan route:cache` works)
Route::get('/{page}.php', [HomeController::class, 'legacyRedirect'])
    ->whereIn('page', [
        'index', 'login', 'register', 'logout', 'dashboard', 'enroll', 'tugas_global',
        'kelas_detail', 'penilaian', 'manage_kuis', 'kerjakan_kuis', 'forum_detail',
        'forum', 'forum_buat_post', 'forum_post_detail', 'laporan', 'profil',
        'tambah_user', 'update_user',
    ]);
