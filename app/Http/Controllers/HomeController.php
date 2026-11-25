<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Notifikasi;
use App\Models\PaketLangganan;
use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $daftarKelas = Kelas::with('pengajar')
            ->withCount([
                'peserta as total_peserta',
                'materi as total_materi',
            ])
            ->orderByDesc('is_premium')
            ->orderBy('id_kelas', 'desc')
            ->get();

        $totalKelas = Kelas::count();
        $totalMurid = Murid::count();
        $totalPengajar = Pengajar::count();
        $totalMateri = Materi::count();

        $paketList = PaketLangganan::where('is_active', true)
            ->orderBy('harga', 'asc')
            ->get();

        $recentPosts = ForumPost::with(['user', 'tags'])
            ->withCount('jawaban')
            ->orderByDesc('tgl_post')
            ->limit(3)
            ->get();

        return view('home', compact(
            'daftarKelas',
            'totalKelas',
            'totalMurid',
            'totalPengajar',
            'totalMateri',
            'paketList',
            'recentPosts'
        ));
    }

    public function sendKontak(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kategori' => ['required', 'in:Tanya Kelas,Kendala Akun,Kerjasama'],
            'email' => ['required', 'email:filter', 'max:100', 'not_regex:/[\r\n]/'],
            'pesan' => ['required', 'string', 'max:1000'],
        ]);

        $admins = User::whereRaw('LOWER(role) = ?', ['admin'])->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id_user,
                'judul' => 'Pesan Kontak (' . $validated['kategori'] . ')',
                'pesan' => $validated['nama'] . ' (' . $validated['email'] . '): ' . mb_strimwidth($validated['pesan'], 0, 200, '...'),
                'link' => route('dashboard', [], false),
            ]);
        }

        return redirect(route('home') . '#kontak')
            ->with('kontak_success', 'Terima kasih! Pesan Anda telah diterima oleh tim administrator kami.');
    }

    public function legacyRedirect(Request $request, string $page)
    {
        return match ($page) {
            'index', 'logout' => redirect()->route('home'),
            'login' => redirect()->route('login', $request->query()),
            'register' => redirect()->route('register'),
            'dashboard' => redirect()->route('dashboard', $request->query()),
            'enroll' => redirect()->route('enroll.index'),
            'tugas_global' => redirect()->route('tugas.index'),
            'kelas_detail' => $request->filled('id')
                ? redirect()->route('kelas.show', ['id' => (int) $request->query('id')] + $request->except('id'))
                : redirect()->route('dashboard'),
            'penilaian' => redirect()->route('penilaian.index', $request->query()),
            'manage_kuis' => $request->filled('id_kuis')
                ? redirect()->route('kuis.manage', ['id_kuis' => (int) $request->query('id_kuis')])
                : redirect()->route('dashboard'),
            'kerjakan_kuis' => $request->filled('id_kuis')
                ? redirect()->route('kuis.kerjakan', ['id_kuis' => (int) $request->query('id_kuis')])
                : redirect()->route('dashboard'),
            'forum_detail' => $request->filled('id')
                ? redirect()->route('diskusi.show', ['id' => (int) $request->query('id')] + $request->except('id'))
                : redirect()->route('dashboard'),
            'forum' => redirect()->route('forum.index', $request->query()),
            'forum_buat_post' => redirect()->route('forum.create'),
            'forum_post_detail' => $request->filled('id')
                ? redirect()->route('forum.show', ['id' => (int) $request->query('id')] + $request->except('id'))
                : redirect()->route('forum.index'),
            'laporan' => redirect()->route('laporan.index'),
            'profil' => redirect()->route('profil.index'),
            'tambah_user' => redirect()->route('admin.users.create'),
            'update_user' => $request->filled('id')
                ? redirect()->route('admin.users.edit', ['id' => (int) $request->query('id')])
                : redirect()->route('dashboard'),
            default => abort(404),
        };
    }
}
