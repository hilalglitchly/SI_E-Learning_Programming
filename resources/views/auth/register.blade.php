@extends('layouts.app')

@section('title', 'Daftar Akun - E-FORVM')

@section('content')
    <main class="neo-container auth-page">
        <section class="auth-card" aria-labelledby="auth-heading">
            <nav class="auth-tabs" aria-label="Navigasi autentikasi">
                <a href="{{ route('login') }}" class="auth-tab">
                    <i class='bx bx-log-in' aria-hidden="true"></i> Masuk
                </a>
                <a href="{{ route('register') }}" class="auth-tab is-active" aria-current="page">
                    <i class='bx bx-user-plus' aria-hidden="true"></i> Daftar
                </a>
            </nav>

            <div class="auth-body">
                <h1 id="auth-heading" class="auth-title">Buat Akun Murid</h1>
                <p class="auth-subtitle">Lengkapi data di bawah untuk mulai belajar di kelas gratis maupun Pro.</p>

                @if (!empty($error))
                    <div class="auth-alert auth-alert--error" role="alert">
                        <i class='bx bxs-error-alt' aria-hidden="true"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" data-auth-form>
                    @csrf

                    <div class="auth-field">
                        <label class="neo-label" for="nama_lengkap">Nama Lengkap</label>
                        <input
                            type="text"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            class="neo-input auth-input"
                            placeholder="Contoh: Adit Pratama"
                            value="{{ old('nama_lengkap') }}"
                            minlength="2"
                            maxlength="100"
                            required
                            autocomplete="name"
                            enterkeyhint="next"
                            data-error="nama-error"
                        >
                        <div id="nama-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Masukkan nama lengkap Anda (2–100 karakter).
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="email">Alamat Email</label>
                        <span id="email-hint" class="auth-hint">Format: nama@domain.com</span>
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
                            enterkeyhint="next"
                            aria-describedby="email-hint"
                            data-error="email-error"
                        >
                        <div id="email-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Masukkan alamat email aktif dengan format nama@domain.com.
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="username">Username</label>
                        <span id="username-hint" class="auth-hint">3–50 karakter: huruf, angka, titik (.), garis bawah (_), atau tanda hubung (-).</span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="neo-input auth-input"
                            placeholder="Contoh: adit.pratama"
                            value="{{ old('username') }}"
                            pattern="[a-zA-Z0-9_.-]{3,50}"
                            minlength="3"
                            maxlength="50"
                            required
                            autocomplete="username"
                            autocapitalize="none"
                            autocorrect="off"
                            spellcheck="false"
                            enterkeyhint="next"
                            aria-describedby="username-hint"
                            data-error="username-error"
                        >
                        <div id="username-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Gunakan 3–50 karakter (huruf, angka, titik, garis bawah, atau tanda hubung tanpa spasi).
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="password">Password</label>
                        <span id="password-hint" class="auth-hint">Minimal 8 karakter.</span>
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
                                enterkeyhint="next"
                                aria-describedby="password-hint"
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
                            <span role="img" aria-label="Error">!</span> Password minimal harus 8 karakter.
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="konfirmasi_password">Konfirmasi Password</label>
                        <div class="auth-pw-wrap">
                            <input
                                type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                class="neo-input auth-input"
                                placeholder="Ulangi password di atas"
                                minlength="8"
                                required
                                autocomplete="new-password"
                                enterkeyhint="done"
                                data-match-password="password"
                                data-error="konfirmasi-error"
                            >
                            <button
                                type="button"
                                class="auth-pw-toggle"
                                data-toggle-password
                                aria-controls="konfirmasi_password"
                                aria-pressed="false"
                                aria-label="Tampilkan konfirmasi password"
                            >
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="konfirmasi-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Konfirmasi password harus sama dengan password di atas.
                        </div>
                    </div>

                    <button type="submit" name="register" class="neo-btn auth-submit auth-submit--register" data-loading-text="Mendaftarkan akun...">
                        Buat Akun Sekarang
                    </button>
                </form>

                <div class="auth-footer">
                    <span>Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="auth-link auth-link--neutral">Masuk di sini</a>
                </div>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/auth-form.js') }}?v={{ file_exists(public_path('assets/js/auth-form.js')) ? filemtime(public_path('assets/js/auth-form.js')) : time() }}"></script>
@endsection
