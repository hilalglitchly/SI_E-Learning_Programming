<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Murid;
use App\Models\Pengajar;

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
            ->orderBy('id_kelas', 'desc')
            ->limit(6)
            ->get();

        $totalKelas = Kelas::count();
        $totalMurid = Murid::count();
        $totalPengajar = Pengajar::count();

        return view('home', compact('daftarKelas', 'totalKelas', 'totalMurid', 'totalPengajar'));
    }
}
