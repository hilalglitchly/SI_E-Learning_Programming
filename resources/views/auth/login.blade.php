@extends('layouts.app')

@section('title', 'Login - E-Learning')

@section('styles')
    <style>
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 85vh;
        }
        .login-box {
            width: 100%;
            max-width: 450px;
            background-color: var(--card);
            border: 4px solid var(--border);
            box-shadow: 8px 8px 0px var(--shadow-color);
            padding: 40px 30px;
            border-radius: 0;
            position: relative;
        }
        .login-title {
            font-size: 2.2rem;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: var(--foreground);
            letter-spacing: -0.04em;
        }
        .login-subtitle {
            font-size: 1rem;
            font-weight: 600;
            color: var(--muted-foreground);
            margin-bottom: 30px;
        }
        .error-alert {
            background-color: var(--primary);
            color: var(--primary-foreground);
            border: 3px solid var(--border);
            box-shadow: 4px 4px 0px var(--shadow-color);
            padding: 12px 15px;
            font-weight: 800;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
    </style>
@endsection

@section('content')
    <div class="neo-container login-wrapper">
        <div class="login-box">
            <!-- Hiasan Neo-Brutalism di pojok -->
            <div style="width: 25px; height: 25px; background-color: var(--secondary); border: 3px solid var(--border); position: absolute; top: -12px; right: -12px; box-shadow: 3px 3px 0px var(--shadow-color);"></div>
            
            <div class="login-title">Selamat Datang</div>
            <div class="login-subtitle">Silakan masuk ke akun E-Learning Anda</div>

            @if (!empty($error))
                <div class="error-alert">
                    <i class='bx bxs-error-alt'></i> {{ $error }}
                </div>
            @endif

            @if (request()->query('msg') == 'register_success' || session('msg') == 'register_success')
                <div class="error-alert" style="background-color: #a8e6cf; color: #000;">
                    <i class='bx bxs-check-circle'></i> Registrasi berhasil! Silakan login dengan akun baru Anda.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="neo-form-group">
                    <label class="neo-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="neo-input" placeholder="Masukkan username..." value="{{ old('username') }}" required autocomplete="off">
                </div>

                <div class="neo-form-group" style="margin-bottom: 30px;">
                    <label class="neo-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="neo-input" placeholder="Masukkan password..." required>
                </div>

                <button type="submit" name="login" class="neo-btn" style="width: 100%; font-size: 1.1rem; padding: 15px;">
                    MASUK SEKARANG &rarr;
                </button>
            </form>

            <!-- Link ke Halaman Registrasi -->
            <div style="margin-top: 25px; text-align: center; border-top: 2px dashed var(--border); padding-top: 20px;">
                <span style="font-weight: 600; color: var(--muted-foreground);">Belum punya akun?</span>
                <a href="{{ route('register') }}" style="font-weight: 900; color: var(--primary); text-decoration: none; text-transform: uppercase; margin-left: 5px; border-bottom: 2px solid var(--primary);">Daftar di sini</a>
            </div>
        </div>
    </div>
@endsection
