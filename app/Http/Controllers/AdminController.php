<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Langganan;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\Submission;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

        $role_baru = (string) $request->input('role_pilihan');
        $username_baru = trim((string) $request->input('username'));
        $password_baru = (string) $request->input('password');
        $nomor_induk = trim((string) $request->input('nomor_induk'));
        $nama_lengkap = trim((string) $request->input('nama_lengkap'));
        $email = strtolower(trim((string) $request->input('email')));

        if (!in_array($role_baru, ['Murid', 'Pengajar'], true)) {
            return redirect()->route('admin.users.create')
                ->withInput($request->except('password'))
                ->with('pesan', 'Gagal: Role yang dipilih tidak valid.')
                ->with('tipe_pesan', 'error');
        }

        if (
            !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username_baru) ||
            strlen($password_baru) < 8 ||
            $nomor_induk === '' || strlen($nomor_induk) > 20 ||
            $nama_lengkap === '' || mb_strlen($nama_lengkap) > 100 ||
            mb_strlen($email) > 100 ||
            preg_match('/[\r\n]/', $email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return redirect()->route('admin.users.create')
                ->withInput($request->except('password'))
                ->with('pesan', 'Gagal: Pastikan semua kolom valid (username 3–50 huruf/angka/._-, password minimal 8 karakter, dan format email benar).')
                ->with('tipe_pesan', 'error');
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $username_baru,
                'password' => Hash::make($password_baru),
                'role' => $role_baru,
                'email_verified_at' => now(),
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
            $this->flushDirectoryCaches();

            return redirect()->route('admin.users.create')
                ->with('pesan', "Berhasil! Akun {$role_baru} atas nama '{$nama_lengkap}' telah ditambahkan.")
                ->with('tipe_pesan', 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('admin.users.create')
                ->withInput($request->except('password'))
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
        $password_baru = (string) $request->input('password');
        $nama_baru = trim((string) $request->input('nama_lengkap'));
        $email_baru = strtolower(trim((string) $request->input('email')));

        if (
            !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username_baru) ||
            ($password_baru !== '' && strlen($password_baru) < 8) ||
            $nama_baru === '' || mb_strlen($nama_baru) > 100 ||
            mb_strlen($email_baru) > 100 ||
            preg_match('/[\r\n]/', $email_baru) ||
            !filter_var($email_baru, FILTER_VALIDATE_EMAIL)
        ) {
            return redirect()->route('admin.users.edit', ['id' => $id_target])
                ->with('pesan', 'Gagal: Pastikan format username, nama, email valid, dan password baru minimal 8 karakter jika diisi.')
                ->with('tipe_pesan', 'error');
        }

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
            $this->flushDirectoryCaches();

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
            $buktiFiles = Langganan::where('id_user', (int) $id)
                ->whereNotNull('bukti_bayar')
                ->pluck('bukti_bayar')
                ->all();

            $nim = Murid::where('id_user', (int) $id)->value('nim');
            $submissionFiles = $nim
                ? Submission::where('nim', $nim)->whereNotNull('file_jawaban')->pluck('file_jawaban')->all()
                : [];

            User::where('id_user', (int) $id)->delete();

            $this->deletePrivateFiles('bukti_bayar', $buktiFiles);
            $this->deletePrivateFiles('submission', $submissionFiles);
            $this->flushDirectoryCaches();
        }

        return redirect()->route('dashboard', ['msg' => 'deleted']);
    }

    public function storeKelas(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $nama = trim((string) $request->input('nama_kelas'));
        $deskripsi = trim((string) $request->input('deskripsi'));
        $nidn = trim((string) $request->input('nidn'));

        if ($nama === '' || mb_strlen($nama) > 100) {
            return redirect()->route('dashboard')
                ->with('pesan', 'Gagal: Nama kelas wajib diisi (maks. 100 karakter).')
                ->with('tipe_pesan', 'error');
        }

        if ($nidn !== '' && !Pengajar::where('nidn', $nidn)->exists()) {
            return redirect()->route('dashboard')
                ->with('pesan', 'Gagal: Pengajar yang dipilih tidak ditemukan.')
                ->with('tipe_pesan', 'error');
        }

        Kelas::create([
            'nama_kelas' => $nama,
            'deskripsi' => $deskripsi,
            'nidn' => $nidn !== '' ? $nidn : null,
            'is_premium' => $request->boolean('is_premium'),
        ]);

        return redirect()->route('dashboard')
            ->with('pesan', "Kelas '{$nama}' berhasil ditambahkan.")
            ->with('tipe_pesan', 'success');
    }

    public function updateKelas(Request $request, $id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $kelas = Kelas::findOrFail((int) $id);
        $nama = trim((string) $request->input('nama_kelas'));
        $deskripsi = trim((string) $request->input('deskripsi'));
        $nidn = trim((string) $request->input('nidn'));

        if ($nama === '' || mb_strlen($nama) > 100) {
            return redirect()->route('dashboard')
                ->with('pesan', 'Gagal: Nama kelas wajib diisi (maks. 100 karakter).')
                ->with('tipe_pesan', 'error');
        }

        if ($nidn !== '' && !Pengajar::where('nidn', $nidn)->exists()) {
            return redirect()->route('dashboard')
                ->with('pesan', 'Gagal: Pengajar yang dipilih tidak ditemukan.')
                ->with('tipe_pesan', 'error');
        }

        $kelas->update([
            'nama_kelas' => $nama,
            'deskripsi' => $deskripsi,
            'nidn' => $nidn !== '' ? $nidn : null,
            'is_premium' => $request->boolean('is_premium'),
        ]);

        return redirect()->route('dashboard')
            ->with('pesan', "Kelas '{$nama}' berhasil diperbarui.")
            ->with('tipe_pesan', 'success');
    }

    public function deleteKelas($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $kelas = Kelas::findOrFail((int) $id);
        $nama = $kelas->nama_kelas;

        $materiFiles = Materi::where('id_kelas', $kelas->id_kelas)
            ->whereNotNull('file_materi')
            ->pluck('file_materi')
            ->all();

        $tugasRows = Tugas::where('id_kelas', $kelas->id_kelas)->get(['id_tugas', 'file_tugas']);
        $tugasIds = $tugasRows->pluck('id_tugas')->all();
        $tugasFiles = $tugasRows->pluck('file_tugas')->filter()->all();
        $submissionFiles = !empty($tugasIds)
            ? Submission::whereIn('id_tugas', $tugasIds)->whereNotNull('file_jawaban')->pluck('file_jawaban')->all()
            : [];

        $kelas->delete();

        $this->deletePrivateFiles('materi', $materiFiles);
        $this->deletePrivateFiles('tugas', $tugasFiles);
        $this->deletePrivateFiles('submission', $submissionFiles);

        return redirect()->route('dashboard')
            ->with('pesan', "Kelas '{$nama}' berhasil dihapus.")
            ->with('tipe_pesan', 'success');
    }

    private function flushDirectoryCaches(): void
    {
        Cache::forget('admin.pengajar_options.v1');
        Cache::forget('kelas.catalog.v1');
        Cache::forget('home.public_payload.v1');
        Cache::forget('dashboard.pimpinan_stats.v1');
    }

    private function deletePrivateFiles(string $folder, array $files): void
    {
        foreach ($files as $file) {
            $safeName = basename((string) $file);
            if ($safeName !== '' && $safeName !== '.' && $safeName !== '..') {
                Storage::disk('local')->delete('private/' . $folder . '/' . $safeName);
            }
        }
    }
}
