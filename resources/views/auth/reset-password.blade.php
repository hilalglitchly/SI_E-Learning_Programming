@extends('layouts.app')

@section('title', 'Reset Password - E-Learning Programming')

@section('content')
    <main class="neo-container auth-page">
        <section class="auth-card" aria-labelledby="reset-heading">
            <div class="auth-body">
                <h1 id="reset-heading" class="auth-title">Buat Password Baru</h1>
                <p class="auth-subtitle">Masukkan password baru minimal 8 karakter untuk akun Anda.</p>

                @if (session('auth_error'))
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class='bx bxs-error-alt' aria-hidden="true"></i>
                        <span>{{ session('auth_error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" data-auth-form>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="auth-field">
                        <label class="neo-label" for="email">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="neo-input auth-input"
                            value="{{ old('email', $email) }}"
                            maxlength="100"
                            required
                            autocomplete="email"
                            spellcheck="false"
                            data-error="email-error"
                        >
                        <div id="email-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Masukkan alamat email yang valid.
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="password">Password Baru</label>
                        <span id="pw-hint" class="auth-hint">Minimal 8 karakter.</span>
                        <div class="auth-pw-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="neo-input auth-input"
                                placeholder="Minimal 8 karakter"
                                minlength="8"
                                required
                                autocomplete="new-password"
                                aria-describedby="pw-hint"
                                data-error="password-error"
                            >
                            <button type="button" class="auth-pw-toggle" data-toggle-password aria-controls="password" aria-pressed="false" aria-label="Tampilkan password baru">
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="password-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Password baru minimal 8 karakter.
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="auth-pw-wrap">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="neo-input auth-input"
                                placeholder="Ketik ulang password baru"
                                minlength="8"
                                required
                                autocomplete="new-password"
                                data-match-password="password"
                                data-error="confirm-error"
                            >
                            <button type="button" class="auth-pw-toggle" data-toggle-password aria-controls="password_confirmation" aria-pressed="false" aria-label="Tampilkan konfirmasi password">
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="confirm-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Konfirmasi password tidak cocok.
                        </div>
                    </div>

                    <button type="submit" class="neo-btn auth-submit" data-loading-text="Menyimpan password...">
                        Simpan Password Baru
                    </button>
                </form>

                <div class="auth-footer">
                    <a href="{{ route('login') }}" class="auth-link">Kembali ke Masuk</a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/auth-form.js') }}?v={{ file_exists(public_path('assets/js/auth-form.js')) ? filemtime(public_path('assets/js/auth-form.js')) : time() }}"></script>
@endsection
