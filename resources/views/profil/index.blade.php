@extends('layouts.app')

@section('title', 'Profil Akun - E-FORVM')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner profile-stack" style="padding-top: 20px;">

            <h1 style="font-size: clamp(1.5rem, 3vw, 2rem); text-transform: uppercase; margin-bottom: 20px;">Informasi Akun Pengguna</h1>

            @if (!empty($pesan))
                <div class="auth-alert {{ $tipe_pesan === 'success' ? 'auth-alert--success' : 'auth-alert--error' }}" role="{{ $tipe_pesan === 'success' ? 'status' : 'alert' }}">
                    <i class='bx {{ $tipe_pesan === 'success' ? 'bxs-check-circle' : 'bxs-error-alt' }}' aria-hidden="true"></i>
                    <span>{{ $pesan }}</span>
                </div>
            @endif

            <!-- Kotak Utama Identitas -->
            <section class="neo-box" aria-label="Ringkasan profil">
                <div style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap; margin-bottom: 24px; border-bottom: var(--border-width) solid var(--border); padding-bottom: 20px;">
                    <div style="width: 72px; height: 72px; flex-shrink: 0; background-color: var(--secondary); border: var(--border-width) solid var(--border); box-shadow: var(--shadow-offset-x) var(--shadow-offset-y) 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 900; color: #000;">
                        {{ strtoupper(mb_substr($nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <h2 style="margin-bottom: 6px; font-size: 1.45rem; text-transform: none;">{{ $nama_lengkap }}</h2>
                        <span class="badge-kode" style="margin-bottom: 0;">Role: {{ $role }}</span>
                    </div>
                </div>

                <div class="profile-info-grid">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--muted-foreground); font-weight: 800; text-transform: uppercase;">Nomor Induk (NIM/NIDN)</div>
                        <div style="font-size: 1.05rem; font-weight: 800; margin-top: 4px;">{{ $nomor_induk }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: var(--muted-foreground); font-weight: 800; text-transform: uppercase;">Username Login</div>
                        <div style="font-size: 1.05rem; font-weight: 800; margin-top: 4px; word-break: break-word;">{{ $username }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: var(--muted-foreground); font-weight: 800; text-transform: uppercase;">Alamat Email</div>
                        <div style="font-size: 1.05rem; font-weight: 800; margin-top: 4px; word-break: break-word;">{{ $email }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; color: var(--muted-foreground); font-weight: 800; text-transform: uppercase;">Status Akun</div>
                        <div style="margin-top: 6px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                            <span class="neo-status-pill neo-status-pill--success">Aktif</span>
                            @if (in_array(strtolower($role), ['murid', 'pengajar']))
                                @if (!empty($isEmailVerified))
                                    <span class="neo-status-pill neo-status-pill--success">Email Terverifikasi</span>
                                @else
                                    <span class="neo-status-pill neo-status-pill--pending">Email Belum Verifikasi</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @if (in_array(strtolower($role), ['murid', 'pengajar']) && empty($isEmailVerified))
                    <div style="margin-bottom: 18px; padding: 14px 16px; border: 3px solid var(--border); background-color: #FEF9C3; color: #000; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <span style="font-weight: 700; font-size: 0.9rem;">Alamat email Anda belum diverifikasi. Kirim ulang tautan verifikasi ke <strong>{{ $email }}</strong>.</span>
                        <form method="POST" action="{{ route('verification.send') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="neo-btn" style="min-height: 44px; padding: 8px 14px; font-size: 0.82rem; background-color: var(--accent); color: #FFF;">Kirim Link Verifikasi</button>
                        </form>
                    </div>
                @endif

                <div style="border-top: 2px dashed var(--border); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <span style="font-weight: 800; font-size: 0.92rem;">Kelola akun Anda:</span>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @if (in_array(strtolower($role), ['murid', 'pengajar']))
                            <button type="button" onclick="toggleProfileForm()" class="neo-btn" style="min-height: 44px; background-color: var(--secondary); color: #000; padding: 8px 16px; font-size: 0.85rem;">
                                <i class='bx bx-edit' aria-hidden="true"></i> Edit Profil
                            </button>
                        @endif
                        <button type="button" onclick="togglePasswordForm()" class="neo-btn" style="min-height: 44px; background-color: var(--accent); color: var(--accent-foreground); padding: 8px 16px; font-size: 0.85rem;">
                            <i class='bx bx-lock-alt' aria-hidden="true"></i> Ubah Password
                        </button>
                    </div>
                </div>
            </section>

            @if (in_array(strtolower($role), ['murid', 'pengajar']))
                <!-- Kotak Form Edit Profil -->
                <section id="form-profil" class="neo-box" style="display: none; border-left: 8px solid var(--secondary);">
                    <h2 style="font-size: 1.25rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 20px;">Edit Profil Pengguna</h2>
                    <form method="POST" action="{{ route('profil.update') }}">
                        @csrf
                        <div class="neo-form-group">
                            <label class="neo-label" for="profil-nama">Nama Lengkap</label>
                            <input type="text" id="profil-nama" name="nama_lengkap" class="neo-input" value="{{ $nama_lengkap }}" maxlength="100" autocomplete="name" required>
                        </div>
                        <div class="neo-form-group">
                            <label class="neo-label" for="profil-email">Alamat Email</label>
                            <input type="email" id="profil-email" name="email" class="neo-input" value="{{ $email }}" maxlength="100" autocomplete="email" spellcheck="false" required>
                        </div>
                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <button type="submit" class="neo-btn" style="flex: 1; min-height: 46px; background-color: var(--secondary); color: #000;">Simpan Profil</button>
                            <button type="button" onclick="toggleProfileForm()" class="neo-btn" style="min-height: 46px; background-color: var(--muted); color: var(--foreground);">Batal</button>
                        </div>
                    </form>
                </section>
            @endif

            <!-- Kotak Form Ganti Password -->
            <section id="form-password" class="neo-box" style="display: {{ !empty($pesan) && $tipe_pesan === 'error' ? 'block' : 'none' }}; border-left: 8px solid #DC2626;">
                <h2 style="font-size: 1.25rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 20px;">Formulir Ganti Password</h2>

                <form method="POST" action="{{ route('profil.password') }}" data-auth-form>
                    @csrf
                    <div class="auth-field">
                        <label class="neo-label" for="password_lama">Password Lama</label>
                        <div class="auth-pw-wrap">
                            <input type="password" id="password_lama" name="password_lama" class="neo-input auth-input" placeholder="Masukkan password saat ini" autocomplete="current-password" required>
                            <button type="button" class="auth-pw-toggle" data-toggle-password aria-controls="password_lama" aria-pressed="false" aria-label="Tampilkan password lama">
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="password_baru">Password Baru</label>
                        <span id="pw-baru-hint" class="auth-hint">Minimal 8 karakter.</span>
                        <div class="auth-pw-wrap">
                            <input type="password" id="password_baru" name="password_baru" class="neo-input auth-input" placeholder="Minimal 8 karakter" minlength="8" autocomplete="new-password" aria-describedby="pw-baru-hint" required>
                            <button type="button" class="auth-pw-toggle" data-toggle-password aria-controls="password_baru" aria-pressed="false" aria-label="Tampilkan password baru">
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="neo-label" for="konfirmasi_password_baru">Konfirmasi Password Baru</label>
                        <div class="auth-pw-wrap">
                            <input type="password" id="konfirmasi_password_baru" name="konfirmasi_password" class="neo-input auth-input" placeholder="Ketik ulang password baru" minlength="8" autocomplete="new-password" data-match-password="password_baru" data-error="pw-confirm-error" required>
                            <button type="button" class="auth-pw-toggle" data-toggle-password aria-controls="konfirmasi_password_baru" aria-pressed="false" aria-label="Tampilkan konfirmasi password baru">
                                <i class='bx bx-show' aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="pw-confirm-error" class="auth-field-error">
                            <span role="img" aria-label="Error">!</span> Konfirmasi password baru harus sama dengan password baru di atas.
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <button type="submit" name="ganti_password" value="1" class="neo-btn" style="flex: 1; min-height: 46px;" data-loading-text="Menyimpan...">Simpan Password Baru</button>
                        <button type="button" onclick="togglePasswordForm()" class="neo-btn" style="min-height: 46px; background-color: var(--muted); color: var(--foreground);">Batal</button>
                    </div>
                </form>
            </section>

        </div>
    </main>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/auth-form.js') }}?v={{ file_exists(public_path('assets/js/auth-form.js')) ? filemtime(public_path('assets/js/auth-form.js')) : time() }}"></script>
<script>
function toggleProfileForm() {
    const form = document.getElementById('form-profil');
    if (!form) return;
    const opening = form.style.display === 'none';
    form.style.display = opening ? 'block' : 'none';
    if (opening) form.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: no-preference)').matches ? 'smooth' : 'auto' });
}

function togglePasswordForm() {
    const form = document.getElementById('form-password');
    if (!form) return;
    const opening = form.style.display === 'none';
    form.style.display = opening ? 'block' : 'none';
    if (opening) form.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: no-preference)').matches ? 'smooth' : 'auto' });
}
</script>
@endsection
