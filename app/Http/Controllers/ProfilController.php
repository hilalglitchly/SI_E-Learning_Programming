<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $id_user = $user->id_user;
        $role = $user->role;
        $username = $user->username;

        $nama_lengkap = $username;
        $nomor_induk = '-';
        $email = '-';

        if (strtolower($role) === 'murid') {
            $mhs = Murid::where('id_user', $id_user)->first();
            if ($mhs) {
                $nama_lengkap = $mhs->nama_murid;
                $nomor_induk = $mhs->nim;
                $email = $mhs->email;
            }
        } elseif ($role === 'Pengajar' || strtolower($role) === 'pengajar') {
            $dsn = Pengajar::where('id_user', $id_user)->first();
            if ($dsn) {
                $nama_lengkap = $dsn->nama_pengajar;
                $nomor_induk = $dsn->nidn;
                $email = $dsn->email;
            }
        } else {
            $nama_lengkap = "Administrator Sistem";
            $nomor_induk = "ADM-00" . $id_user;
            $email = "admin@elearning.local";
        }

        $pesan = session('pesan', '');
        $tipe_pesan = session('tipe_pesan', '');

        return view('profil.index', compact(
            'role',
            'username',
            'nama_lengkap',
            'nomor_induk',
            'email',
            'pesan',
            'tipe_pesan'
        ));
    }

    public function updatePassword(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id_user);

        $pass_lama = (string) $request->input('password_lama');
        $pass_baru = (string) $request->input('password_baru');
        $konfirmasi = (string) $request->input('konfirmasi_password');

        if ($pass_baru !== $konfirmasi) {
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Konfirmasi password baru tidak cocok!')
                ->with('tipe_pesan', 'error');
        }

        $isOldValid = Hash::check($pass_lama, $user->password) || ($pass_lama === $user->password);

        if (!$isOldValid) {
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Password lama yang Anda masukkan salah!')
                ->with('tipe_pesan', 'error');
        }

        $user->password = Hash::make($pass_baru);
        $user->save();

        return redirect()->route('profil.index')
            ->with('pesan', 'Berhasil: Password akun Anda telah diperbarui!')
            ->with('tipe_pesan', 'success');
    }
}
