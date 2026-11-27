@extends('layouts.app')

@section('title', 'Lupa Password - E-FORVM')

@section('content')
    <main class="neo-container auth-page">
        <section class="auth-card" aria-labelledby="forgot-heading">
            <div class="auth-body">
                <h1 id="forgot-heading" class="auth-title">Lupa Password</h1>
                <p class="auth-subtitle">Masukkan alamat email akun Anda untuk menerima tautan pengaturan ulang password.</p>

                @if (session('status'))
                    <div class="auth-alert auth-alert--success" role="status">
                        <i class='bx bxs-check-circle' aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('auth_error'))
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class='bx bxs-error-alt' aria-hidden="true"></i>
                        <span>{{ session('auth_error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" data-auth-form>
                    @csrf
                    <div class="auth-field">
                        <label class="neo-label" for="email">Alamat Email Terdaftar</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="neo-input auth-input"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                            maxlength="100"
                            required
                            autocomplete="email"
                            spellcheck="false"
                            enterkeyhint="send"
                            data-error="email-error"
                        >
                        <div id="email-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Masukkan alamat email yang valid.
                        </div>
                    </div>

                    <button type="submit" class="neo-btn auth-submit" data-loading-text="Mengirim tautan...">
                        Kirim Tautan Reset Password
                    </button>
                </form>

                <div class="auth-footer">
                    <span>Ingat password Anda?</span>
                    <a href="{{ route('login') }}" class="auth-link">Kembali ke Masuk</a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/auth-form.js') }}?v={{ file_exists(public_path('assets/js/auth-form.js')) ? filemtime(public_path('assets/js/auth-form.js')) : time() }}"></script>
@endsection
