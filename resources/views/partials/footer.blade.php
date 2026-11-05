@php
    $isLogged = auth()->check();
@endphp
<footer class="neo-site-footer no-print">
    <!-- Top Marquee Bar -->
    <div class="neo-footer-marquee">
        <div class="neo-footer-marquee-track">
            <span><i class='bx bxs-zap'></i> PLATFORM BELAJAR KODING TERBUKA INDONESIA</span>
            <span>&bull;</span>
            <span><i class='bx bx-code-alt'></i> KURIKULUM PRAKTIS BERBASIS PROYEK</span>
            <span>&bull;</span>
            <span><i class='bx bx-message-rounded-dots'></i> DISKUSI TEKNIS &amp; MENTORING E-FORVM</span>
            <span>&bull;</span>
            <span><i class='bx bxs-trophy'></i> PANTAU PROGRES &amp; PORTOFOLIO REAL-TIME</span>
            <span>&bull;</span>
            <span><i class='bx bxs-zap'></i> PLATFORM BELAJAR KODING TERBUKA INDONESIA</span>
            <span>&bull;</span>
            <span><i class='bx bx-code-alt'></i> KURIKULUM PRAKTIS BERBASIS PROYEK</span>
            <span>&bull;</span>
            <span><i class='bx bx-message-rounded-dots'></i> DISKUSI TEKNIS &amp; MENTORING E-FORVM</span>
            <span>&bull;</span>
            <span><i class='bx bxs-trophy'></i> PANTAU PROGRES &amp; PORTOFOLIO REAL-TIME</span>
        </div>
    </div>

    <!-- Main 4-Column Content -->
    <div class="neo-footer-main">
        <!-- Kolom 1: Brand & Deskripsi -->
        <div>
            <a href="{{ $isLogged ? route('dashboard') : route('home') }}" class="neo-footer-brand-logo">
                <span style="font-family: monospace; font-size: 2rem; background-color: #FFD700; color: #000; padding: 2px 8px; border: 2px solid #000; box-shadow: 3px 3px 0px #FF4C4C;">&lt;/&gt;</span>
                <div style="display: flex; flex-direction: column;">
                    <span>E-LEARNING</span>
                    <span>PROGRAMMING</span>
                </div>
            </a>
            <p class="neo-footer-brand-desc">
                Platform belajar pemrograman interaktif dengan pendekatan praktis. Bangun portofolio nyata, diskusikan arsitektur kode di E-FORVM, dan raih karir impianmu di industri teknologi.
            </p>
            <div class="neo-footer-socials">
                <a href="https://www.instagram.com/hilal_s28" target="_blank" rel="noopener" title="Instagram" class="neo-footer-social-btn" style="box-shadow: 4px 4px 0px #FF4C4C;">
                    <i class='bx bxl-instagram'></i>
                </a>
                <a href="https://github.com/hilalglitchly/SI_E-Learning_Programming" target="_blank" rel="noopener" title="GitHub Repository" class="neo-footer-social-btn" style="box-shadow: 4px 4px 0px #3B82F6;">
                    <i class='bx bxl-github'></i>
                </a>
                <a href="https://youtube.com/@hilal_k?si=R1T3G98B7C280_1M" target="_blank" rel="noopener" title="YouTube" class="neo-footer-social-btn" style="box-shadow: 4px 4px 0px #10B981;">
                    <i class='bx bxl-youtube'></i>
                </a>
            </div>
        </div>

        <!-- Kolom 2: Navigasi Cepat -->
        <div>
            <h4 class="neo-footer-heading" style="border-bottom: 3px solid #FF4C4C;">Navigasi Cepat</h4>
            <ul class="neo-footer-list">
                @if ($isLogged)
                    <li><a href="{{ route('dashboard') }}" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Dashboard Utama</a></li>
                    <li><a href="{{ route('forum.index') }}" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Forum Diskusi (E-FORVM)</a></li>
                    <li><a href="{{ route('profil.index') }}" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Profil &amp; Akun Saya</a></li>
                @else
                    <li><a href="{{ route('home') }}" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Beranda Utama</a></li>
                    <li><a href="{{ route('home') }}#katalog" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Katalog Kelas</a></li>
                    <li><a href="{{ route('forum.index') }}" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Forum Diskusi (E-FORVM)</a></li>
                    <li><a href="{{ route('home') }}#faq" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Pertanyaan Umum (FAQ)</a></li>
                    <li><a href="{{ route('login') }}" class="neo-footer-link"><i class='bx bx-chevron-right'></i> Masuk / Daftar Akun</a></li>
                @endif
            </ul>
        </div>

        <!-- Kolom 3: Alur Belajar -->
        <div>
            <h4 class="neo-footer-heading" style="border-bottom: 3px solid #3B82F6;">Alur Belajar</h4>
            <ul class="neo-footer-list">
                <li><i class='bx bx-code-curly' style="color: #FFD700; margin-right: 6px;"></i> Frontend Web Developer</li>
                <li><i class='bx bx-server' style="color: #60A5FA; margin-right: 6px;"></i> Backend API Engineer</li>
                <li><i class='bx bx-mobile-alt' style="color: #34D399; margin-right: 6px;"></i> Mobile App (Kotlin)</li>
                <li><i class='bx bx-brain' style="color: #F472B6; margin-right: 6px;"></i> Data Science &amp; AI</li>
                <li><i class='bx bx-shield-quarter' style="color: #A78BFA; margin-right: 6px;"></i> Cybersecurity Dasar</li>
            </ul>
        </div>

        <!-- Kolom 4: Kantor Pusat -->
        <div>
            <h4 class="neo-footer-heading" style="border-bottom: 3px solid #10B981;">Kantor Pusat</h4>
            <div class="neo-footer-contact-item">
                <i class='bx bxs-map' style="color: #FF4C4C;"></i>
                <span>Gedung Inovasi Digital Lt. 4<br>Jl. Teknologi Raya No. 404, Jakarta Selatan</span>
            </div>
            <div class="neo-footer-contact-item">
                <i class='bx bxs-envelope' style="color: #FFD700;"></i>
                <span>support@elearning.local</span>
            </div>
            <div class="neo-footer-contact-item">
                <i class='bx bxs-phone' style="color: #10B981;"></i>
                <span>+62 812-3456-7890</span>
            </div>
        </div>
    </div>

    <!-- Copyright Bottom -->
    <div class="neo-footer-bottom">
        <div>&copy; {{ date('Y') }} Sistem Informasi E-Learning Programming. Hak Cipta Dilindungi.</div>
        <div class="neo-footer-bottom-links">
            <a href="{{ route('home') }}#faq">Pusat Bantuan</a>
            <a href="{{ route('forum.index') }}">Komunitas E-FORVM</a>
            <a href="{{ route('home') }}#kontak">Hubungi Kami</a>
        </div>
    </div>
</footer>
