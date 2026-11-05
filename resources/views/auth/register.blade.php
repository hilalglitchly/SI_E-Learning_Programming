@extends('layouts.app')

@section('title', 'Daftar Akun - E-Learning')

@section('styles')
    <style>
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 380px);
            padding: 40px 15px;
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
            <div class="login-title">Buat Akun</div>
            <div class="login-subtitle">Daftar sekarang untuk mulai belajar</div>

            @if (!empty($error))
                <div class="error-alert">
                    <i class='bx bxs-error-alt'></i> {{ $error }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="neo-form-group">
                    <label class="neo-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="neo-input" placeholder="Pilih username unik..." value="{{ old('username') }}" required autocomplete="off">
                </div>

                <div class="neo-form-group">
                    <label class="neo-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="neo-input" placeholder="Buat password..." required>
                </div>

                <div class="neo-form-group" style="margin-bottom: 30px;">
                    <label class="neo-label" for="konfirmasi_password">Konfirmasi Password</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="neo-input" placeholder="Ulangi password..." required>
                </div>

                <button type="submit" name="register" class="neo-btn" style="width: 100%; font-size: 1.1rem; padding: 15px; background-color: var(--secondary); color: #000;">
                    DAFTAR SEKARANG &rarr;
                </button>
            </form>

            <!-- Link kembali ke Login -->
            <div style="margin-top: 25px; text-align: center; border-top: 2px dashed var(--border); padding-top: 20px;">
                <span style="font-weight: 600; color: var(--muted-foreground);">Sudah punya akun?</span>
                <a href="{{ route('login') }}" style="font-weight: 900; color: var(--foreground); text-decoration: none; text-transform: uppercase; margin-left: 5px; border-bottom: 2px solid var(--foreground);">Login di sini</a>
            </div>
        </div>
    </div>
@endsection
