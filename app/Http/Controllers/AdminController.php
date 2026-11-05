<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function createUser()
    {
        if (strtolower(auth()->user()->role) !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Administrator.');
        }

        $pesan = session('pesan', '');
        $tipe_pesan = session('tipe_pesan', '');

        return view('admin.tambah_user', compact('pesan', 'tipe_pesan'));
    }

    public function storeUser(Request $request)
    {
        if (strtolower(auth()->user()->role) !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Administrator.');
        }

        $role_baru = $request->input('role_pilihan');
        $username_baru = trim((string) $request->input('username'));
        $password_baru = trim((string) $request->input('password'));
        $nomor_induk = trim((string) $request->input('nomor_induk'));
        $nama_lengkap = trim((string) $request->input('nama_lengkap'));
        $email = trim((string) $request->input('email'));

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $username_baru,
                'password' => Hash::make($password_baru),
                'role' => $role_baru,
            ]);

            if ($role_baru === 'Pengajar') {
                Pengajar::create([
                    'nidn' => $nomor_induk,
                    'nama_pengajar' => $nama_lengkap,
                    'email' => $email,
                    'id_user' => $user->id_user,
                ]);
            } elseif ($role_baru === 'Murid') {
                Murid::create([
                    'nim' => $nomor_induk,
                    'nama_murid' => $nama_lengkap,
                    'email' => $email,
                    'id_user' => $user->id_user,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.create')
                ->with('pesan', "Berhasil! Akun {$role_baru} atas nama '{$nama_lengkap}' telah ditambahkan.")
                ->with('tipe_pesan', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.create')
                ->withInput()
                ->with('pesan', 'Gagal menambahkan pengguna: Pastikan Username, NIM/NIDN, atau Email belum pernah terdaftar sebelumnya.')
                ->with('tipe_pesan', 'error');
        }
    }

    public function editUser(Request $request, $id = null)
    {
        if (strtolower(auth()->user()->role) !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $id_target = $id ?? $request->query('id');
        if (empty($id_target)) {
            return redirect()->route('dashboard');
        }

        $userData = User::find($id_target);
        if (!$userData) {
            abort(404, 'Pengguna tidak ditemukan.');
        }

        $role_target = $userData->role;
        $username_target = $userData->username;
        $nomor_induk = '';
        $nama_lengkap = '';
        $email = '';

        if ($role_target === 'Pengajar' || strtolower($role_target) === 'pengajar') {
            $prof = Pengajar::where('id_user', $id_target)->first();
            if ($prof) {
                $nomor_induk = $prof->nidn;
                $nama_lengkap = $prof->nama_pengajar;
                $email = $prof->email;
            }
        } elseif (strtolower($role_target) === 'murid') {
            $prof = Murid::where('id_user', $id_target)->first();
            if ($prof) {
                $nomor_induk = $prof->nim;
                $nama_lengkap = $prof->nama_murid;
                $email = $prof->email;
            }
        }

        $pesan = session('pesan', '');
        $tipe_pesan = session('tipe_pesan', '');

        return view('admin.update_user', compact(
            'id_target',
            'role_target',
            'username_target',
            'nomor_induk',
            'nama_lengkap',
            'email',
            'pesan',
            'tipe_pesan'
        ));
    }

    public function updateUser(Request $request, $id)
    {
        if (strtolower(auth()->user()->role) !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $id_target = $id;
        $userData = User::findOrFail($id_target);
        $role_target = $userData->role;

        $username_baru = trim((string) $request->input('username'));
        $password_baru = trim((string) $request->input('password'));
        $nama_baru = trim((string) $request->input('nama_lengkap'));
        $email_baru = trim((string) $request->input('email'));

        try {
            DB::beginTransaction();

            $userData->username = $username_baru;
            if ($password_baru !== '') {
                $userData->password = Hash::make($password_baru);
            }
            $userData->save();

            if ($role_target === 'Pengajar' || strtolower($role_target) === 'pengajar') {
                Pengajar::where('id_user', $id_target)->update([
                    'nama_pengajar' => $nama_baru,
                    'email' => $email_baru,
                ]);
            } elseif (strtolower($role_target) === 'murid') {
                Murid::where('id_user', $id_target)->update([
                    'nama_murid' => $nama_baru,
                    'email' => $email_baru,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard', ['msg' => 'updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.edit', ['id' => $id_target])
                ->with('pesan', 'Gagal memperbarui data: Username atau Email mungkin sudah digunakan oleh pengguna lain.')
                ->with('tipe_pesan', 'error');
        }
    }

    public function deleteUser($id)
    {
        $user = auth()->user();
        if (strtolower($user->role) !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        if ((int) $id !== (int) $user->id_user) {
            User::where('id_user', $id)->delete();
        }

        return redirect()->route('dashboard', ['msg' => 'deleted']);
    }
}
