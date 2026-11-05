<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        if (!session()->has('login_attempts')) {
            session([
                'login_attempts' => 0,
                'last_attempt_time' => time(),
            ]);
        }

        if (time() - session('last_attempt_time', time()) > 900) {
            session(['login_attempts' => 0]);
        }

        if (session('login_attempts', 0) >= 5) {
            $sisa_waktu = ceil((900 - (time() - session('last_attempt_time'))) / 60);
            $error = "Terlalu banyak percobaan gagal! Silakan tunggu {$sisa_waktu} menit lagi.";
            return view('auth.login', compact('error'));
        }

        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        if ($username === '' || $password === '') {
            $error = "Username dan password wajib diisi!";
            return view('auth.login', compact('error'));
        }

        $user = User::where('username', $username)->first();

        $passwordValid = false;
        if ($user) {
            if (Hash::check($password, $user->password)) {
                $passwordValid = true;
            } elseif ($password === $user->password) {
                $passwordValid = true;
                $user->password = Hash::make($password);
                $user->save();
            }
        }

        if ($user && $passwordValid) {
            session(['login_attempts' => 0]);
            Auth::login($user);
            $request->session()->regenerate();

            session([
                'id_user' => $user->id_user,
                'username' => $user->username,
                'role' => $user->role,
            ]);

            return redirect()->route('dashboard');
        }

        session([
            'login_attempts' => session('login_attempts', 0) + 1,
            'last_attempt_time' => time(),
        ]);
        $sisa_percobaan = 5 - session('login_attempts');
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

            $nim_dummy = 'NIM' . date('ymd') . rand(100, 999);
            $email_dummy = $username . '@student.local';

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
            $error = "Gagal mendaftar: " . $e->getMessage();
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
