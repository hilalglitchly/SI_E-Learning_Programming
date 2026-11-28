@extends('layouts.app')

@section('title', 'Akun & Lainnya - E-FORVM')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner profile-stack" style="padding-top: 12px;">

            @if (!empty($pesan))
                <div class="auth-alert {{ $tipe_pesan === 'success' ? 'auth-alert--success' : 'auth-alert--error' }}" role="{{ $tipe_pesan === 'success' ? 'status' : 'alert' }}">
                    <i class='bx {{ $tipe_pesan === 'success' ? 'bxs-check-circle' : 'bxs-error-alt' }}' aria-hidden="true"></i>
                    <span>{{ $pesan }}</span>
                </div>
            @endif

            <!-- Ringkasan Identitas Akun -->
            <section class="neo-box" aria-label="Ringkasan identitas akun" style="margin-bottom: 18px;">
                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div style="width: 64px; height: 64px; flex-shrink: 0; background-color: var(--secondary); border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 900; color: #000;">
                        {{ strtoupper(mb_substr($nama_lengkap, 0, 2)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h1 style="margin: 0 0 4px 0; font-size: clamp(1.2rem, 3vw, 1.55rem); text-transform: none; line-height: 1.2;">{{ $nama_lengkap }}</h1>
                        <div style="font-size: 0.88rem; font-weight: 700; color: var(--muted-foreground); word-break: break-word;">
                            {{ $role }} &bull; {{ $nomor_induk }} &bull; {{ '@' . $username }}
                        </div>
                        <div style="font-size: 0.84rem; font-weight: 600; color: var(--muted-foreground); margin-top: 2px; word-break: break-word;">
                            {{ $email }}
                        </div>
                        <div style="margin-top: 8px; display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                            <span class="neo-status-pill neo-status-pill--success">Aktif</span>
                            @if (strtolower($role) === 'murid')
                                @if (!empty($activeSub))
                                    <span class="neo-status-pill neo-status-pill--pro"><i class='bx bxs-crown' aria-hidden="true"></i> Pro Aktif</span>
                                @else
                                    <span class="neo-status-pill neo-status-pill--neutral">Gratis</span>
                                @endif
                            @endif
                            @if (in_array(strtolower($role), ['murid', 'pengajar']))
                                @if (!empty($isEmailVerified))
                                    <span class="neo-status-pill neo-status-pill--success">Email Terverifikasi</span>
                                @else
                                    <span class="neo-status-pill neo-status-pill--pending">Belum Verifikasi</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @if (in_array(strtolower($role), ['murid', 'pengajar']) && empty($isEmailVerified))
                    <div style="margin-top: 16px; padding: 12px 14px; border: 3px solid var(--border); background-color: #FEF9C3; color: #000; display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <span style="font-weight: 700; font-size: 0.88rem;">Alamat email Anda belum diverifikasi.</span>
                        <form method="POST" action="{{ route('verification.send') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="neo-btn" style="min-height: 44px; padding: 8px 14px; font-size: 0.82rem; background-color: var(--accent); color: #FFF;">Kirim Link Verifikasi</button>
                        </form>
                    </div>
                @endif
            </section>

            <!-- Daftar Menu Akun (Native Mobile List Style) -->
            <section class="account-menu-card" aria-label="Menu akun dan pengaturan">
                <div class="account-menu-header">
                    <span>Akun &amp; Lainnya</span>
                    <span style="font-size: 0.78rem; color: var(--muted-foreground);">E-FORVM</span>
                </div>

                <div class="account-menu-list">
                    @if (in_array(strtolower($role), ['murid', 'pengajar']))
                        <button type="button" class="account-menu-row" onclick="toggleAccountPanel('form-profil')">
                            <span class="account-menu-icon"><i class='bx bx-user-check' aria-hidden="true"></i></span>
                            <span class="account-menu-text">
                                <strong>Kelola Profil</strong>
                                <small>Ubah nama lengkap dan alamat email</small>
                            </span>
                            <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                        </button>
                    @endif

                    <button type="button" class="account-menu-row" onclick="toggleAccountPanel('form-password')">
                        <span class="account-menu-icon"><i class='bx bx-lock-alt' aria-hidden="true"></i></span>
                        <span class="account-menu-text">
                            <strong>Kelola Akun &amp; Kata Sandi</strong>
                            <small>Perbarui password login keamanan akun</small>
                        </span>
                        <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                    </button>

                    @if (strtolower($role) === 'murid')
                        <a href="{{ route('langganan.index') }}" class="account-menu-row">
                            <span class="account-menu-icon" style="background-color: #EDE9FE; color: #6D28D9;"><i class='bx bxs-crown' aria-hidden="true"></i></span>
                            <span class="account-menu-text">
                                <strong>Pembayaran &amp; Status Pro</strong>
                                <small>{{ !empty($activeSub) ? 'Aktif s/d ' . $activeSub->tgl_berakhir->format('d M Y') : 'Upgrade ke Pro via Midtrans untuk kelas Premium' }}</small>
                            </span>
                            <span class="neo-status-pill {{ !empty($activeSub) ? 'neo-status-pill--pro' : 'neo-status-pill--pending' }}" style="font-size: 0.68rem;">
                                {{ !empty($activeSub) ? 'PRO' : 'GRATIS' }}
                            </span>
                            <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                        </a>
                    @elseif (strtolower($role) === 'admin')
                        <a href="{{ route('admin.langganan.index') }}" class="account-menu-row">
                            <span class="account-menu-icon" style="background-color: #EDE9FE; color: #6D28D9;"><i class='bx bx-credit-card' aria-hidden="true"></i></span>
                            <span class="account-menu-text">
                                <strong>Pembayaran &amp; Paket Pro</strong>
                                <small>Riwayat Midtrans dan kelola harga paket</small>
                            </span>
                            <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                        </a>
                    @elseif (strtolower($role) === 'pimpinan')
                        <a href="{{ route('laporan.index') }}" class="account-menu-row">
                            <span class="account-menu-icon" style="background-color: #EDE9FE; color: #6D28D9;"><i class='bx bxs-report' aria-hidden="true"></i></span>
                            <span class="account-menu-text">
                                <strong>Laporan Keuangan &amp; Akademik</strong>
                                <small>Rekap pendapatan Pro dan data kelas</small>
                            </span>
                            <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                        </a>
                    @endif

                    <button type="button" class="account-menu-row" onclick="toggleAccountPanel('profil-notif-panel')">
                        <span class="account-menu-icon"><i class='bx bxs-bell' aria-hidden="true"></i></span>
                        <span class="account-menu-text">
                            <strong>Notifikasi</strong>
                            <small>Lihat pembaruan forum, diskusi, dan pembayaran</small>
                        </span>
                        <span id="profil-notif-badge" class="neo-status-pill neo-status-pill--danger" style="display: none; font-size: 0.7rem;">0</span>
                        <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                    </button>

                    <button type="button" class="account-menu-row" onclick="toggleDarkMode()">
                        <span class="account-menu-icon"><i class='bx bxs-moon' id="profil-theme-icon" aria-hidden="true"></i></span>
                        <span class="account-menu-text">
                            <strong>Pengaturan Tampilan</strong>
                            <small>Ganti tema antara mode terang dan gelap</small>
                        </span>
                        <span id="profil-theme-label" class="neo-status-pill neo-status-pill--neutral" style="font-size: 0.7rem;">Mode Gelap</span>
                        <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                    </button>

                    <button type="button" class="account-menu-row js-pwa-install-btn" style="display: none;">
                        <span class="account-menu-icon" style="background-color: #DCFCE7; color: #047857;"><i class='bx bx-download' aria-hidden="true"></i></span>
                        <span class="account-menu-text">
                            <strong>Install Aplikasi E-FORVM</strong>
                            <small>Tambahkan ikon aplikasi ke layar utama HP</small>
                        </span>
                        <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                    </button>

                    <button type="button" class="account-menu-row" onclick="toggleAccountPanel('faq-section')">
                        <span class="account-menu-icon"><i class='bx bx-help-circle' aria-hidden="true"></i></span>
                        <span class="account-menu-text">
                            <strong>FAQ &amp; Panduan Pengguna</strong>
                            <small>Jawaban cepat seputar kelas, kuis, dan Pro</small>
                        </span>
                        <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                    </button>

                    <form method="POST" action="{{ route('logout') }}" onsubmit="event.preventDefault(); NeoConfirm('Konfirmasi Keluar', 'Apakah Anda yakin ingin keluar dari akun E-FORVM?', () => this.submit());" style="margin: 0;">
                        @csrf
                        <button type="submit" class="account-menu-row account-menu-row--danger">
                            <span class="account-menu-icon account-menu-icon--danger"><i class='bx bx-log-out' aria-hidden="true"></i></span>
                            <span class="account-menu-text">
                                <strong>Keluar (Exit)</strong>
                                <small>Akhiri sesi pada perangkat ini</small>
                            </span>
                            <i class='bx bx-chevron-right account-menu-chevron' aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            </section>

            @if (in_array(strtolower($role), ['murid', 'pengajar']))
                <!-- Panel Form Edit Profil -->
                <section id="form-profil" class="neo-box account-subpanel" style="display: none; border-left: 8px solid var(--secondary);">
                    <h2 style="font-size: 1.2rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 18px;">Kelola Profil Pengguna</h2>
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
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button type="submit" class="neo-btn" style="flex: 1; min-height: 46px; background-color: var(--secondary); color: #000;">Simpan Profil</button>
                            <button type="button" onclick="toggleAccountPanel('form-profil')" class="neo-btn" style="min-height: 46px; background-color: var(--muted); color: var(--foreground);">Tutup</button>
                        </div>
                    </form>
                </section>
            @endif

            <!-- Panel Form Ganti Password -->
            <section id="form-password" class="neo-box account-subpanel" style="display: {{ !empty($pesan) && $tipe_pesan === 'error' ? 'block' : 'none' }}; border-left: 8px solid #DC2626;">
                <h2 style="font-size: 1.2rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 18px;">Ubah Kata Sandi</h2>

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

                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="submit" name="ganti_password" value="1" class="neo-btn" style="flex: 1; min-height: 46px;" data-loading-text="Menyimpan...">Simpan Password Baru</button>
                        <button type="button" onclick="toggleAccountPanel('form-password')" class="neo-btn" style="min-height: 46px; background-color: var(--muted); color: var(--foreground);">Tutup</button>
                    </div>
                </form>
            </section>

            <!-- Panel Notifikasi -->
            <section id="profil-notif-panel" class="neo-box account-subpanel" style="display: none; border-left: 8px solid var(--accent);" aria-label="Notifikasi terbaru">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 12px;">
                    <h2 style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                        <i class='bx bxs-bell' aria-hidden="true"></i> Notifikasi Terbaru
                    </h2>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="button" onclick="if (window.markAllRead) markAllRead()" class="neo-btn" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem; background-color: var(--secondary); color: #000;">Tandai Dibaca</button>
                        <button type="button" onclick="if (window.deleteAllNotif) deleteAllNotif()" class="neo-btn neo-btn--danger" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem;">Hapus Semua</button>
                    </div>
                </div>
                <div id="profil-notif-list" style="max-height: 320px; overflow-y: auto; border: 2px solid var(--border); background-color: var(--background);">
                    <div class="neo-skeleton-list" role="status" aria-label="Memuat notifikasi">
                        <div class="neo-skeleton-item">
                            <span class="neo-skeleton-line neo-skeleton-line--title"></span>
                            <span class="neo-skeleton-line neo-skeleton-line--body"></span>
                            <span class="neo-skeleton-line neo-skeleton-line--short"></span>
                        </div>
                        <div class="neo-skeleton-item">
                            <span class="neo-skeleton-line neo-skeleton-line--title"></span>
                            <span class="neo-skeleton-line neo-skeleton-line--body"></span>
                            <span class="neo-skeleton-line neo-skeleton-line--short"></span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Panel FAQ & Bantuan -->
            <section id="faq-section" class="neo-box account-subpanel" style="display: none; border-left: 8px solid #047857;" aria-label="FAQ dan Panduan">
                <h2 style="font-size: 1.2rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 14px;">FAQ &amp; Panduan E-FORVM</h2>

                <details class="account-faq-item" open>
                    <summary>Bagaimana cara bergabung ke kelas baru?</summary>
                    <p>Buka tab <strong>Kelas</strong> di navigasi bawah (atau menu <strong>Cari Kelas</strong>), filter antara kelas Gratis atau Pro, lalu tekan tombol <strong>Gabung Kelas</strong>.</p>
                </details>

                <details class="account-faq-item">
                    <summary>Bagaimana cara mengaktifkan Langganan Pro?</summary>
                    <p>Pilih menu <strong>Pembayaran &amp; Status Pro</strong>, pilih paket Bulanan atau Tahunan, lalu selesaikan pembayaran lewat Midtrans (QRIS, Virtual Account, E-Wallet, atau Kartu). Setelah lunas, akses Pro aktif otomatis.</p>
                </details>

                <details class="account-faq-item">
                    <summary>Bagaimana cara mencetak struk pembayaran?</summary>
                    <p>Pada halaman <strong>Langganan Pro</strong>, scroll ke bagian <strong>Riwayat Pembayaran Anda</strong>, lalu klik tombol <strong>Struk</strong> pada transaksi yang berstatus Aktif.</p>
                </details>

                <details class="account-faq-item">
                    <summary>Bagaimana jika lupa kata sandi atau butuh bantuan admin?</summary>
                    <p>Gunakan fitur <strong>Lupa Password</strong> pada halaman login untuk menerima tautan reset lewat email, atau hubungi tim dukungan di <strong>{{ config('app.contact.email') }}</strong>.</p>
                </details>
            </section>

            <div style="text-align: center; font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); padding: 8px 0 4px;">
                E-FORVM v2.0 &bull; Progressive Web App
            </div>

        </div>
    </main>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/auth-form.js') }}?v={{ file_exists(public_path('assets/js/auth-form.js')) ? filemtime(public_path('assets/js/auth-form.js')) : time() }}"></script>
<script>
function toggleAccountPanel(panelId) {
    const target = document.getElementById(panelId);
    if (!target) return;
    const isOpening = target.style.display === 'none' || target.style.display === '';
    document.querySelectorAll('.account-subpanel').forEach((panel) => {
        if (panel.id !== panelId) panel.style.display = 'none';
    });
    target.style.display = isOpening ? 'block' : 'none';
    if (isOpening) {
        if (panelId === 'profil-notif-panel' && typeof window.loadNotifikasi === 'function') {
            window.loadNotifikasi(true);
        }
        target.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: no-preference)').matches ? 'smooth' : 'auto', block: 'start' });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hash === '#faq-section') {
        toggleAccountPanel('faq-section');
    } else if (window.location.hash === '#profil-notif-panel') {
        toggleAccountPanel('profil-notif-panel');
    }
});
</script>
@endsection
