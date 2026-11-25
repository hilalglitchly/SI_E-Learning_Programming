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
        } elseif (strtolower($role) === 'pimpinan') {
            $nama_lengkap = "Pimpinan Institusi";
            $nomor_induk = "PMP-00" . $id_user;
            $email = config('app.contact.email');
        } else {
            $nama_lengkap = "Administrator Sistem";
            $nomor_induk = "ADM-00" . $id_user;
            $email = config('app.contact.email');
        }

        $pesan = session('pesan', '');
        $tipe_pesan = session('tipe_pesan', '');
        $isEmailVerified = $user->hasVerifiedEmail();

        return view('profil.index', compact(
            'role',
            'username',
            'nama_lengkap',
            'nomor_induk',
            'email',
            'isEmailVerified',
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

        if (strlen($pass_baru) < 8) {
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Password baru minimal harus 8 karakter!')
                ->with('tipe_pesan', 'error');
        }

        if ($pass_baru !== $konfirmasi) {
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Konfirmasi password baru tidak cocok!')
                ->with('tipe_pesan', 'error');
        }

        if (!Hash::check($pass_lama, $user->password)) {
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Password lama yang Anda masukkan salah!')
                ->with('tipe_pesan', 'error');
        }

        $user->password = Hash::make($pass_baru);
        $user->save();
        $request->session()->regenerate();

        return redirect()->route('profil.index')
            ->with('pesan', 'Berhasil: Password akun Anda telah diperbarui!')
            ->with('tipe_pesan', 'success');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user->isMurid() && !$user->isPengajar()) {
            abort(403, 'Profil untuk role ini tidak dapat diubah dari halaman ini.');
        }

        $nama = trim((string) $request->input('nama_lengkap'));
        $email = strtolower(trim((string) $request->input('email')));

        if (
            $nama === '' ||
            mb_strlen($nama) > 100 ||
            mb_strlen($email) > 100 ||
            preg_match('/[\r\n]/', $email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Pastikan nama lengkap (maks. 100 karakter) dan format email valid.')
                ->with('tipe_pesan', 'error');
        }

        $oldEmail = strtolower(trim((string) $user->getEmailAddress()));

        try {
            if ($user->isMurid()) {
                Murid::where('id_user', $user->id_user)->update([
                    'nama_murid' => $nama,
                    'email' => $email,
                ]);
            } else {
                Pengajar::where('id_user', $user->id_user)->update([
                    'nama_pengajar' => $nama,
                    'email' => $email,
                ]);
            }

            if ($oldEmail !== $email) {
                $user->email_verified_at = null;
                $user->save();
                AuthController::sendVerificationEmail($user, $email, $nama);

                return redirect()->route('profil.index')
                    ->with('pesan', 'Profil diperbarui! Karena alamat email berubah, tautan verifikasi baru telah dikirim ke ' . $email . '.')
                    ->with('tipe_pesan', 'success');
            }

            return redirect()->route('profil.index')
                ->with('pesan', 'Berhasil: Data profil Anda telah diperbarui!')
                ->with('tipe_pesan', 'success');
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('profil.index')
                ->with('pesan', 'Gagal: Email mungkin sudah digunakan akun lain.')
                ->with('tipe_pesan', 'error');
        }
    }

    public function apiUser(Request $request)
    {
        return $request->user();
    }
}
