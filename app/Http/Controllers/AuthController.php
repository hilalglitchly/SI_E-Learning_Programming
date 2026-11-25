<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $error = session('auth_error', session('login_error', ''));
        return view('auth.login', compact('error'));
    }

    public function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $redirectParam = $request->input('redirect') === 'enroll' ? ['redirect' => 'enroll'] : [];

        if ($username === '' || $password === '') {
            return redirect()->route('login', $redirectParam)
                ->withInput($request->only('username'))
                ->with('auth_error', 'Username dan password wajib diisi!');
        }

        $userKey = 'login:' . strtolower($username) . '|' . $request->ip();
        $ipKey = 'login-ip:' . $request->ip();

        if (RateLimiter::tooManyAttempts($userKey, 5) || RateLimiter::tooManyAttempts($ipKey, 20)) {
            $seconds = max(RateLimiter::availableIn($userKey), RateLimiter::availableIn($ipKey));
            $sisa_waktu = max(1, (int) ceil($seconds / 60));
            return redirect()->route('login', $redirectParam)
                ->withInput($request->only('username'))
                ->with('auth_error', "Terlalu banyak percobaan gagal! Silakan tunggu {$sisa_waktu} menit lagi.");
        }

        $user = User::where('username', $username)->first();

        if ($user && Hash::check($password, $user->password)) {
            RateLimiter::clear($userKey);
            Auth::login($user);
            $request->session()->regenerate();

            session([
                'id_user' => $user->id_user,
                'username' => $user->username,
                'role' => $user->role,
            ]);

            if ($request->input('redirect') === 'enroll' && $user->isMurid()) {
                return redirect()->route('enroll.index');
            }

            return redirect()->route('dashboard');
        }

        RateLimiter::hit($userKey, 900);
        RateLimiter::hit($ipKey, 900);

        $sisa_percobaan = max(0, 5 - RateLimiter::attempts($userKey));

        return redirect()->route('login', $redirectParam)
            ->withInput($request->only('username'))
            ->with('auth_error', "Username atau password salah! (Sisa percobaan: {$sisa_percobaan})");
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $error = session('auth_error', '');
        return view('auth.register', compact('error'));
    }

    public function register(Request $request)
    {
        $nama_lengkap = trim((string) $request->input('nama_lengkap'));
        $email = strtolower(trim((string) $request->input('email')));
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $konfirmasi = (string) $request->input('konfirmasi_password');

        $oldInput = [
            'nama_lengkap' => $nama_lengkap,
            'email' => $email,
            'username' => $username,
        ];

        if ($nama_lengkap === '' || $email === '' || $username === '' || $password === '' || $konfirmasi === '') {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Semua kolom wajib diisi!');
        }

        if (mb_strlen($nama_lengkap) < 2 || mb_strlen($nama_lengkap) > 100) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Nama lengkap harus antara 2 hingga 100 karakter.');
        }

        if (mb_strlen($email) > 100 || preg_match('/[\r\n]/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Format alamat email tidak valid.');
        }

        if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username)) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Username harus 3–50 karakter dan hanya boleh huruf, angka, titik, garis bawah, atau tanda hubung.');
        }

        if (strlen($password) < 8) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Password minimal harus 8 karakter!');
        }

        if ($password !== $konfirmasi) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Konfirmasi password tidak cocok!');
        }

        if (User::where('username', $username)->exists()) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Username sudah digunakan, silakan pilih yang lain!');
        }

        if (Murid::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Alamat email sudah terdaftar, silakan gunakan email lain atau langsung masuk.');
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'Murid',
            ]);

            $nim_auto = 'NIM' . date('ymd') . random_int(100000, 999999);

            Murid::create([
                'nim' => $nim_auto,
                'nama_murid' => $nama_lengkap,
                'email' => $email,
                'id_user' => $user->id_user,
            ]);

            DB::commit();

            return redirect()->route('login', ['msg' => 'register_success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->route('register')->withInput($oldInput)->with('auth_error', 'Gagal mendaftar. Silakan coba lagi beberapa saat.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
