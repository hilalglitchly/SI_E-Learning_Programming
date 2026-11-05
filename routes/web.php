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
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// 3. AJAX API Endpoints (Session-aware)
Route::match(['get', 'post'], '/api/notifikasi', [NotifikasiController::class, 'handle'])->name('api.notifikasi');
Route::post('/api/forum/vote', [ForumController::class, 'vote'])->name('api.forum.vote');
Route::post('/api/forum/simpan', [ForumController::class, 'simpan'])->name('api.forum.simpan');

// 4. Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Enroll / Cari Kelas (Murid)
    Route::get('/enroll', [KelasController::class, 'enrollIndex'])->name('enroll.index');
    Route::post('/enroll', [KelasController::class, 'enrollStore'])->name('enroll.store');

    // Detail Kelas & Stream/Feed/Materi/Tugas/Diskusi/Kuis Actions
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('kelas.show');
    Route::post('/kelas/{id}', [KelasController::class, 'handlePost'])->name('kelas.post');

    // Tugas Global & Penilaian
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/penilaian', [TugasController::class, 'penilaian'])->name('penilaian.index');
    Route::post('/penilaian', [TugasController::class, 'storeNilai'])->name('penilaian.store');

    // Kuis (Kelola Soal & Kerjakan Kuis)
    Route::get('/kuis/{id_kuis}/manage', [KuisController::class, 'manage'])->name('kuis.manage');
    Route::post('/kuis/{id_kuis}/manage', [KuisController::class, 'storeSoal'])->name('kuis.soal.store');
    Route::get('/kuis/{id_kuis}/soal/{id_soal}/delete', [KuisController::class, 'deleteSoal'])->name('kuis.soal.delete');
    Route::get('/kuis/{id_kuis}/kerjakan', [KuisController::class, 'kerjakan'])->name('kuis.kerjakan');
    Route::post('/kuis/{id_kuis}/kerjakan', [KuisController::class, 'submit'])->name('kuis.submit');

    // Diskusi Kelas Detail
    Route::get('/diskusi/{id}', [DiskusiController::class, 'show'])->name('diskusi.show');
    Route::post('/diskusi/{id}', [DiskusiController::class, 'storeBalasan'])->name('diskusi.balasan.store');

    // Laporan Rekapitulasi (Admin & Pimpinan)
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Profil & Ganti Password
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::post('/profil', [ProfilController::class, 'updatePassword'])->name('profil.password');

    // Manajemen Pengguna (Admin)
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/tambah', [AdminController::class, 'createUser'])->name('create');
        Route::post('/tambah', [AdminController::class, 'storeUser'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::post('/{id}/edit', [AdminController::class, 'updateUser'])->name('update');
        Route::get('/{id}/delete', [AdminController::class, 'deleteUser'])->name('delete');
    });
});

// 5. Legacy .php URL Compatibility Redirects
Route::get('/index.php', fn() => redirect()->route('home'));
Route::get('/login.php', fn(Request $r) => redirect()->route('login', $r->query()));
Route::get('/register.php', fn() => redirect()->route('register'));
Route::get('/logout.php', [AuthController::class, 'logout']);
Route::get('/dashboard.php', fn(Request $r) => redirect()->route('dashboard', $r->query()));
Route::get('/enroll.php', fn() => redirect()->route('enroll.index'));
Route::get('/tugas_global.php', fn() => redirect()->route('tugas.index'));
Route::get('/kelas_detail.php', fn(Request $r) => redirect()->route('kelas.show', ['id' => $r->query('id')] + $r->except('id')));
Route::get('/penilaian.php', fn(Request $r) => redirect()->route('penilaian.index', $r->query()));
Route::get('/manage_kuis.php', fn(Request $r) => redirect()->route('kuis.manage', ['id_kuis' => $r->query('id_kuis')]));
Route::get('/kerjakan_kuis.php', fn(Request $r) => redirect()->route('kuis.kerjakan', ['id_kuis' => $r->query('id_kuis')]));
Route::get('/forum_detail.php', fn(Request $r) => redirect()->route('diskusi.show', ['id' => $r->query('id')] + $r->except('id')));
Route::get('/forum.php', fn(Request $r) => redirect()->route('forum.index', $r->query()));
Route::get('/forum_buat_post.php', fn() => redirect()->route('forum.create'));
Route::get('/forum_post_detail.php', fn(Request $r) => redirect()->route('forum.show', ['id' => $r->query('id')] + $r->except('id')));
Route::get('/laporan.php', fn() => redirect()->route('laporan.index'));
Route::get('/profil.php', fn() => redirect()->route('profil.index'));
Route::get('/tambah_user.php', fn() => redirect()->route('admin.users.create'));
Route::get('/update_user.php', fn(Request $r) => redirect()->route('admin.users.edit', ['id' => $r->query('id')]));
