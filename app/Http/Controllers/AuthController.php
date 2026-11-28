<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
                'display_name' => $user->display_name,
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

            self::sendVerificationEmail($user, $email, $nama_lengkap);

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

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $email = strtolower(trim((string) $request->input('email')));

        if ($email === '' || mb_strlen($email) > 100 || preg_match('/[\r\n]/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('password.request')
                ->withInput($request->only('email'))
                ->with('auth_error', 'Format alamat email tidak valid.');
        }

        $account = $this->findAccountByEmail($email);

        if ($account) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
            try {
                Mail::send('emails.reset-password', [
                    'nama' => $account['nama'],
                    'resetUrl' => $resetUrl,
                ], function ($message) use ($email) {
                    $message->to($email)->subject('Reset Password — E-FORVM');
                });
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('password.request')
            ->with('status', 'Jika alamat email terdaftar di sistem kami, tautan pengaturan ulang password telah dikirim ke kotak masuk Anda.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $email = strtolower(trim((string) $request->query('email', '')));
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $token = trim((string) $request->input('token'));
        $email = strtolower(trim((string) $request->input('email')));
        $password = (string) $request->input('password');
        $confirmation = (string) $request->input('password_confirmation');

        if ($email === '' || mb_strlen($email) > 100 || preg_match('/[\r\n]/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->route('password.reset', ['token' => $token, 'email' => $email])
                ->with('auth_error', 'Format alamat email tidak valid.');
        }

        if (strlen($password) < 8) {
            return redirect()->route('password.reset', ['token' => $token, 'email' => $email])
                ->with('auth_error', 'Password baru minimal harus 8 karakter.');
        }

        if ($password !== $confirmation) {
            return redirect()->route('password.reset', ['token' => $token, 'email' => $email])
                ->with('auth_error', 'Konfirmasi password tidak cocok.');
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();
        if (
            !$record ||
            !Hash::check($token, (string) $record->token) ||
            Carbon::parse($record->created_at)->addMinutes(60)->isPast()
        ) {
            return redirect()->route('password.reset', ['token' => $token, 'email' => $email])
                ->with('auth_error', 'Tautan reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.');
        }

        $account = $this->findAccountByEmail($email);
        if (!$account) {
            return redirect()->route('password.request')
                ->with('auth_error', 'Akun tidak ditemukan.');
        }

        $user = $account['user'];
        $user->password = Hash::make($password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        RateLimiter::clear('login:' . strtolower($user->username) . '|' . $request->ip());

        return redirect()->route('login')
            ->with('status', 'Password berhasil diperbarui! Silakan masuk dengan password baru Anda.');
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = auth()->user();
        if ((int) $user->id_user !== (int) $id) {
            abort(403, 'Tautan verifikasi ini bukan milik akun yang sedang login.');
        }

        $email = strtolower(trim((string) $user->getEmailAddress()));
        if ($email === '' || !hash_equals(sha1($email), (string) $hash)) {
            abort(403, 'Tautan verifikasi email tidak valid.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect()->route('dashboard')
            ->with('pesan', 'Alamat email Anda berhasil diverifikasi!')
            ->with('tipe_pesan', 'success');
    }

    public function resendVerification(Request $request)
    {
        $user = auth()->user();
        if ($user->hasVerifiedEmail()) {
            return back()->with('pesan', 'Alamat email Anda sudah terverifikasi.')->with('tipe_pesan', 'success');
        }

        $email = strtolower(trim((string) $user->getEmailAddress()));
        if ($email === '') {
            return back()->with('pesan', 'Akun ini tidak memiliki alamat email terdaftar.')->with('tipe_pesan', 'error');
        }

        self::sendVerificationEmail($user, $email, $user->display_name);

        return back()->with('pesan', 'Tautan verifikasi baru telah dikirim ke ' . $email . '.')->with('tipe_pesan', 'success');
    }

    public static function sendVerificationEmail(User $user, string $email, string $nama): void
    {
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_user, 'hash' => sha1(strtolower(trim($email)))]
        );

        try {
            Mail::send('emails.verify-email', [
                'nama' => $nama,
                'verifyUrl' => $verifyUrl,
            ], function ($message) use ($email) {
                $message->to($email)->subject('Verifikasi Email — E-FORVM');
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function findAccountByEmail(string $email): ?array
    {
        $murid = Murid::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($murid && $murid->id_user) {
            $user = User::find($murid->id_user);
            if ($user) {
                return ['user' => $user, 'nama' => $murid->nama_murid];
            }
        }

        $pengajar = Pengajar::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($pengajar && $pengajar->id_user) {
            $user = User::find($pengajar->id_user);
            if ($user) {
                return ['user' => $user, 'nama' => $pengajar->nama_pengajar];
            }
        }

        return null;
    }
}
