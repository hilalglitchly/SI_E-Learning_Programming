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

        $error = session('login_error', '');
        return view('auth.login', compact('error'));
    }

    public function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        if ($username === '' || $password === '') {
            $error = "Username dan password wajib diisi!";
            return view('auth.login', compact('error'));
        }

        $userKey = 'login:' . strtolower($username) . '|' . $request->ip();
        $ipKey = 'login-ip:' . $request->ip();

        if (RateLimiter::tooManyAttempts($userKey, 5) || RateLimiter::tooManyAttempts($ipKey, 20)) {
            $seconds = max(RateLimiter::availableIn($userKey), RateLimiter::availableIn($ipKey));
            $sisa_waktu = max(1, (int) ceil($seconds / 60));
            $error = "Terlalu banyak percobaan gagal! Silakan tunggu {$sisa_waktu} menit lagi.";
            return view('auth.login', compact('error'));
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
        $error = "Username atau password salah! (Sisa percobaan: {$sisa_percobaan})";

        return view('auth.login', compact('error'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $error = '';
        return view('auth.register', compact('error'));
    }

    public function register(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');
        $konfirmasi = (string) $request->input('konfirmasi_password');
        $error = '';

        if ($username === '' || $password === '' || $konfirmasi === '') {
            $error = "Semua kolom wajib diisi!";
            return view('auth.register', compact('error'));
        }

        if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username)) {
            $error = "Username harus 3–50 karakter dan hanya boleh huruf, angka, titik, garis bawah, atau tanda hubung.";
            return view('auth.register', compact('error'));
        }

        if (strlen($password) < 8) {
            $error = "Password minimal harus 8 karakter!";
            return view('auth.register', compact('error'));
        }

        if ($password !== $konfirmasi) {
            $error = "Konfirmasi password tidak cocok!";
            return view('auth.register', compact('error'));
        }

        if (User::where('username', $username)->exists()) {
            $error = "Username sudah digunakan, silakan pilih yang lain!";
            return view('auth.register', compact('error'));
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'Murid',
            ]);

            $nim_dummy = 'NIM' . date('ymd') . random_int(100000, 999999);
            $email_dummy = strtolower($username) . '@student.local';

            Murid::create([
                'nim' => $nim_dummy,
                'nama_murid' => $username,
                'email' => $email_dummy,
                'id_user' => $user->id_user,
            ]);

            DB::commit();

            return redirect()->route('login', ['msg' => 'register_success']);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            $error = "Gagal mendaftar. Silakan coba lagi beberapa saat.";
            return view('auth.register', compact('error'));
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
