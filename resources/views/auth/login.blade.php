@extends('layouts.app')

@section('title', 'Masuk - E-FORVM')

@section('content')
    <main class="neo-container auth-page">
        <section class="auth-card" aria-labelledby="auth-heading">
            <nav class="auth-tabs" aria-label="Navigasi autentikasi">
                <a href="{{ route('login') }}" class="auth-tab is-active" aria-current="page">
                    <i class='bx bx-log-in' aria-hidden="true"></i> Masuk
                </a>
                <a href="{{ route('register') }}" class="auth-tab">
                    <i class='bx bx-user-plus' aria-hidden="true"></i> Daftar
                </a>
            </nav>

            <div class="auth-body">
                <h1 id="auth-heading" class="auth-title">Selamat Datang</h1>
                <p class="auth-subtitle">Masuk ke akun E-FORVM Anda.</p>

                @if (!empty($error))
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class='bx bxs-error-alt' aria-hidden="true"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endif

                @if (session('status'))
                    <div class="auth-alert auth-alert--success" role="status">
                        <i class='bx bxs-check-circle' aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @elseif (request()->query('msg') == 'register_success' || session('msg') == 'register_success')
                    <div class="auth-alert auth-alert--success" role="status">
                        <i class='bx bxs-check-circle' aria-hidden="true"></i>
                        <span>Registrasi berhasil! Silakan cek email untuk verifikasi dan masuk dengan akun baru Anda.</span>
                    </div>
                @elseif (request()->query('msg') == 'login_required')
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class='bx bxs-info-circle' aria-hidden="true"></i>
                        <span>Silakan masuk terlebih dahulu untuk mengakses fitur tersebut.</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" data-auth-form>
                    @csrf
                    @if (request()->query('redirect') === 'enroll' || old('redirect') === 'enroll')
                        <input type="hidden" name="redirect" value="enroll">
                    @endif

                    <div class="auth-field">
                        <label class="neo-label" for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="neo-input auth-input"
                            placeholder="Contoh: student_adit"
                            value="{{ old('username') }}"
                            required
                            autocomplete="username"
                            autocapitalize="none"
                            autocorrect="off"
                            spellcheck="false"
                            enterkeyhint="next"
                            data-error="username-error"
                        >
                        <div id="username-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Masukkan username akun Anda.
                        </div>
                    </div>

                    <div class="auth-field">
                        <div class="auth-label-row">
                            <label class="neo-label" for="password">Password</label>
                        </div>
                        <div class="auth-pw-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="neo-input auth-input"
                                placeholder="Masukkan password Anda"
                                required
                                autocomplete="current-password"
                                enterkeyhint="done"
                                data-error="password-error"
                            >
                            <button
                                type="button"
                                class="auth-pw-toggle"
                                data-toggle-password
                                aria-controls="password"
                                aria-pressed="false"
                                aria-label="Tampilkan password"
                            >
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Masukkan password akun Anda.
                        </div>
                        <div class="auth-helper-row">
                            <a href="{{ route('password.request') }}" class="auth-link auth-link--neutral">Lupa password?</a>
                        </div>
                    </div>

                    <button type="submit" name="login" class="neo-btn auth-submit" data-loading-text="Sedang masuk...">
                        Masuk ke Akun
                    </button>
                </form>

                <div class="auth-footer">
                    <span>Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="auth-link">Daftar di sini</a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/auth-form.js') }}?v={{ file_exists(public_path('assets/js/auth-form.js')) ? filemtime(public_path('assets/js/auth-form.js')) : time() }}"></script>
@endsection
