@extends('layouts.app')

@section('title', 'E-Learning Programming - Belajar Koding Jadi Mudah')

@section('styles')
    <style>
        /* Hero Section Enhancements */
        .hero-section {
            position: relative;
            text-align: center;
            padding: 80px 20px;
            background-color: var(--card);
            border: 4px solid var(--border);
            box-shadow: 12px 12px 0px var(--shadow-color);
            margin-bottom: 60px;
            overflow: hidden;
            z-index: 1;
        }
        
        /* Floating Interactive Elements */
        .floating-box {
            position: absolute;
            border: 3px solid var(--border);
            box-shadow: 5px 5px 0px var(--shadow-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 2.5rem;
            z-index: -1;
            transition: transform 0.3s ease, background-color 0.3s;
            cursor: pointer;
            user-select: none;
        }
        .floating-box:hover {
            transform: scale(1.1) rotate(0deg) !important;
            box-shadow: 8px 8px 0px var(--shadow-color);
        }
        .box-js {
            width: 80px; height: 80px;
            background-color: #F7DF1E; color: #000;
            top: 10%; left: 5%;
            transform: rotate(-15deg);
            animation: floatSlow 6s ease-in-out infinite;
        }
        .box-python {
            width: 90px; height: 90px;
            background-color: #3776AB; color: #FFF;
            bottom: 15%; right: 6%;
            transform: rotate(12deg);
            animation: floatSlow 7s ease-in-out infinite 1s;
        }
        .box-react {
            width: 70px; height: 70px;
            background-color: #61DAFB; color: #000;
            top: 15%; right: 8%;
            transform: rotate(20deg);
            border-radius: 50%;
            animation: floatSlow 5s ease-in-out infinite 0.5s;
        }
        .box-cpp {
            width: 75px; height: 75px;
            background-color: #00599C; color: #FFF;
            bottom: 12%; left: 8%;
            transform: rotate(-8deg);
            animation: floatSlow 6.5s ease-in-out infinite 1.5s;
        }
        .box-php {
            width: 85px; height: 55px;
            background-color: #777BB4; color: #FFF;
            top: 48%; left: 3%;
            transform: rotate(10deg);
            border-radius: 30px;
            font-size: 2rem;
            animation: floatSlow 5.5s ease-in-out infinite 0.8s;
        }
        .box-html {
            width: 70px; height: 70px;
            background-color: #E34F26; color: #FFF;
            top: 45%; right: 4%;
            transform: rotate(-18deg);
            animation: floatSlow 6.2s ease-in-out infinite 2s;
        }
        .box-java {
            width: 75px; height: 75px;
            background-color: #ED8B00; color: #FFF;
            top: 8%; left: 25%;
            transform: rotate(8deg);
            border-radius: 12px;
            animation: floatSlow 6.8s ease-in-out infinite 1.2s;
        }
        .box-git {
            width: 65px; height: 65px;
            background-color: #F05032; color: #FFF;
            bottom: 8%; right: 25%;
            transform: rotate(-12deg);
            animation: floatSlow 5.8s ease-in-out infinite 0.3s;
        }

        @keyframes floatSlow {
            0%, 100% { margin-top: 0px; }
            50% { margin-top: -18px; }
        }

        @media (max-width: 900px) {
            .floating-box { display: none; }
        }

        .hero-badge {
            display: inline-block;
            background-color: var(--secondary);
            color: #000;
            font-weight: 900;
            padding: 8px 16px;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px #000;
            text-transform: uppercase;
            margin-bottom: 24px;
            transform: rotate(-2deg);
            transition: transform 0.2s;
        }
        .hero-badge:hover {
            transform: rotate(2deg) scale(1.05);
        }
        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 900;
            line-height: 1.05;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: -0.03em;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--muted-foreground);
            max-width: 700px;
            margin: 0 auto 40px auto;
            font-weight: 600;
            line-height: 1.6;
        }
        .hero-cta-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-cta-primary {
            padding: 16px 36px;
            font-size: 1.2rem;
            font-weight: 900;
            background-color: var(--primary);
            color: #FFF;
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
            text-decoration: none;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-cta-secondary {
            padding: 16px 36px;
            font-size: 1.2rem;
            font-weight: 900;
            background-color: #FFF;
            color: #000;
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
            text-decoration: none;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
            border-bottom: 4px solid var(--border);
            padding-bottom: 16px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .section-title {
            font-size: 2.2rem;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .feature-box {
            padding: 30px;
            background-color: var(--card);
            border: 3px solid var(--border);
            box-shadow: 6px 6px 0px var(--shadow-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-box:hover {
            transform: translate(-4px, -4px);
            box-shadow: 10px 10px 0px var(--shadow-color);
        }
        .feature-icon {
            font-size: 2.5rem;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px #000;
            margin-bottom: 20px;
            color: #000;
        }
        
        /* Horizontal Auto-Scrolling Course Container */
        .course-scroll-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 10px 0 30px 0;
        }
        .course-scroll-container {
            display: flex;
            gap: 24px;
            overflow-x: auto;
            scroll-behavior: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE and Edge */
            padding: 10px 5px;
            cursor: grab;
        }
        .course-scroll-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
        }
        .course-scroll-container:active {
            cursor: grabbing;
        }
        .course-card-item {
            flex: 0 0 350px;
            max-width: 350px;
            user-select: none;
        }
        @media (max-width: 500px) {
            .course-card-item {
                flex: 0 0 290px;
                max-width: 290px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="neo-container" style="max-width: 1400px; padding-top: 10px;">
        
        <!-- HERO SECTION -->
        <section class="hero-section">
            <!-- Decorative Interactive Elements -->
            <div class="floating-box box-js" title="JavaScript"><i class='bx bxl-javascript'></i></div>
            <div class="floating-box box-python" title="Python"><i class='bx bxl-python'></i></div>
            <div class="floating-box box-react" title="React"><i class='bx bxl-react'></i></div>
            <div class="floating-box box-cpp" title="C++"><i class='bx bxl-c-plus-plus'></i></div>
            <div class="floating-box box-php" title="PHP"><i class='bx bxl-php'></i></div>
            <div class="floating-box box-html" title="HTML5"><i class='bx bxl-html5'></i></div>
            <div class="floating-box box-java" title="Java"><i class='bx bxl-java'></i></div>
            <div class="floating-box box-git" title="Git"><i class='bx bxl-git'></i></div>

            <div class="hero-badge">
                <i class='bx bxs-zap'></i> Platform Belajar Koding Terbuka
            </div>
            <h1 class="hero-title">
                Kuasai Pemrograman<br>
                <span style="background-color: var(--primary); color: #FFF; padding: 0 16px; display: inline-block; transform: rotate(1deg); border: 4px solid #000; box-shadow: 6px 6px 0px #000; margin-top: 8px;">Dari Nol Sampai Mahir</span>
            </h1>
            <p class="hero-subtitle">
                Jelajahi berbagai materi pemrograman, studi kasus nyata, dan tingkatkan keahlian rekayasa perangkat lunak Anda bersama pengajar berpengalaman.
            </p>
            <div class="hero-cta-group">
                <a href="#katalog" class="btn-cta-primary brutal-hover">
                    <i class='bx bx-compass'></i> Jelajahi Kelas
                </a>
                <a href="{{ route('register') }}" class="btn-cta-secondary brutal-hover">
                    <i class='bx bxs-user-plus'></i> Buat Akun Gratis
                </a>
            </div>
        </section>

        <!-- STATISTIK SINGKAT (Full-bleed Purple Background like Reference) -->
        <section style="background-color: #A855F7; border: 4px solid #000; box-shadow: 10px 10px 0px #000; padding: 30px; margin-bottom: 60px;">
            <div class="neo-grid" style="margin: 0; gap: 25px;">
                <div class="neo-box" style="margin: 0; text-align: center; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; transform: rotate(-1deg);">
                    <div style="font-size: 3rem; font-weight: 900; color: var(--primary);">{{ $totalKelas }}+</div>
                    <div style="font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em;">Kelas Pemrograman</div>
                </div>
                <div class="neo-box" style="margin: 0; text-align: center; background-color: #FFD700; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; transform: rotate(1deg);">
                    <div style="font-size: 3rem; font-weight: 900; color: #000;">{{ $totalPengajar }}+</div>
                    <div style="font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em;">Mentor Praktisi</div>
                </div>
                <div class="neo-box" style="margin: 0; text-align: center; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; transform: rotate(-1deg);">
                    <div style="font-size: 3rem; font-weight: 900; color: #10B981;">{{ $totalMurid }}+</div>
                    <div style="font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em;">Siswa Bergabung</div>
                </div>
            </div>
        </section>

        <!-- KATALOG KELAS POPULER (Yellow Block Container) -->
        <section id="katalog" style="margin-bottom: 70px; background-color: #FCD34D; border: 4px solid #000; box-shadow: 12px 12px 0px #000; padding: 40px 30px;">
            <div class="section-header" style="border-bottom-color: #000;">
                <div>
                    <span style="background-color: #000; color: #FFF; font-weight: 900; padding: 4px 10px; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 8px;">Katalog Terbuka</span>
                    <h2 class="section-title" style="color: #000;"><i class='bx bxs-flame' style="color: var(--primary);"></i> Kelas Populer</h2>
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span style="font-weight: 800; color: #000; font-size: 0.9rem; background: #FFF; padding: 6px 12px; border: 2px solid #000; box-shadow: 3px 3px 0px #000;"><i class='bx bx-mouse'></i> Geser atau Arahkan Kursor</span>
                    <a href="{{ route('login') }}" class="brutal-hover" style="font-weight: 900; color: #000; background: #FFF; padding: 8px 16px; border: 3px solid #000; box-shadow: 4px 4px 0px #000; text-decoration: none; text-transform: uppercase;">
                        Lihat Semua <i class='bx bx-right-arrow-alt'></i>
                    </a>
                </div>
            </div>

            @if ($daftarKelas->count() > 0)
                <div class="course-scroll-wrapper">
                    <div class="course-scroll-container" id="courseScroll">
                        @php
                            $allCards = $daftarKelas->concat($daftarKelas);
                        @endphp
                        @foreach ($allCards as $k)
                            <div class="neo-card course-card-item" style="display: flex; flex-direction: column; justify-content: space-between; background-color: var(--card); border: 4px solid #000; box-shadow: 6px 6px 0px #000;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <span class="badge-kode" style="margin: 0; background-color: var(--secondary); color: #000; border: 2px solid #000;">
                                            <i class='bx bx-code-alt'></i> {{ $k->total_materi }} Materi
                                        </span>
                                        <span style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground);">
                                            <i class='bx bxs-group'></i> {{ $k->total_peserta }} Siswa
                                        </span>
                                    </div>
                                    <h3 style="font-size: 1.35rem; margin-bottom: 10px; line-height: 1.3;">{{ $k->nama_kelas }}</h3>
                                    <p style="font-size: 0.95rem; color: var(--muted-foreground); margin-bottom: 20px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $k->deskripsi }}
                                    </p>
                                </div>
                                
                                <div style="border-top: 2px dashed var(--border); padding-top: 16px; margin-top: auto;">
                                    <div style="font-size: 0.85rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                                        <i class='bx bxs-user-badge' style="font-size: 1.2rem; color: var(--accent);"></i>
                                        <span>Pengajar: <strong>{{ $k->pengajar->nama_pengajar ?? 'Tim Pengajar' }}</strong></span>
                                    </div>
                                    <a href="{{ route('login', ['redirect' => 'enroll']) }}" class="neo-btn" style="text-align: center; text-decoration: none; display: block; padding: 10px;">
                                        Mulai Belajar <i class='bx bx-Log-in-circle'></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="neo-box" style="text-align: center; padding: 40px; background: #FFF; color: #000;">
                    <p style="font-weight: 700; font-size: 1.1rem;">Belum ada kelas yang tersedia saat ini.</p>
                </div>
            @endif
        </section>

        <!-- FITUR UNGGULAN (Pink Block Container) -->
        <section style="margin-bottom: 80px; background-color: #EC4899; border: 4px solid #000; box-shadow: 12px 12px 0px #000; padding: 40px 30px;">
            <div class="section-header" style="border-bottom-color: #000;">
                <div>
                    <span style="background-color: #FFD700; color: #000; border: 2px solid #000; font-weight: 900; padding: 4px 10px; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 8px;">Kenapa Memilih Kami?</span>
                    <h2 class="section-title" style="color: #FFF; text-shadow: 3px 3px 0px #000;"><i class='bx bxs-star' style="color: #FFD700;"></i> Pengalaman Belajar Terbaik</h2>
                </div>
            </div>

            <div class="neo-grid">
                <div class="feature-box" style="border: 4px solid #000; box-shadow: 6px 6px 0px #000;">
                    <div class="feature-icon" style="background-color: #FFD700;">
                        <i class='bx bx-laptop'></i>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Kurikulum Berbasis Proyek</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500;">
                        Setiap kelas dilengkapi dengan modul terstruktur, tugas praktik koding, dan kuis interaktif untuk menguji pemahaman Anda secara langsung.
                    </p>
                </div>

                <div class="feature-box" style="border: 4px solid #000; box-shadow: 6px 6px 0px #000;">
                    <div class="feature-icon" style="background-color: #60A5FA;">
                        <i class='bx bx-conversation'></i>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Forum Diskusi Interaktif</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500;">
                        Bingung dengan baris kode atau error? Diskusikan langsung bersama pengajar dan rekan sekelas di ruang diskusi setiap mata kuliah.
                    </p>
                </div>

                <div class="feature-box" style="border: 4px solid #000; box-shadow: 6px 6px 0px #000;">
                    <div class="feature-icon" style="background-color: #34D399;">
                        <i class='bx bx-line-chart'></i>
                    </div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Pantau Progress Belajar</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500;">
                        Sistem secara otomatis mencatat persentase penyelesaian tugas, kuis, dan rekapitulasi nilai Anda secara transparan dan real-time.
                    </p>
                </div>
            </div>
        </section>

        <!-- FAQ & VIDEO SECTION (Green Block Container) -->
        <section id="faq" style="margin-bottom: 80px; background-color: #10B981; border: 4px solid #000; box-shadow: 12px 12px 0px #000; padding: 40px 30px;">
            <div class="section-header" style="border-bottom-color: #000;">
                <div>
                    <span style="background-color: #FFF; color: #000; border: 2px solid #000; font-weight: 900; padding: 4px 10px; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 8px;">Tanya Jawab</span>
                    <h2 class="section-title" style="color: #000;"><i class='bx bxs-help-circle' style="color: #FFF;"></i> Pertanyaan Umum (FAQ)</h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; align-items: start;">
                <!-- Kolom Kiri: Accordion FAQ -->
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <details class="neo-box" style="margin: 0; cursor: pointer; background-color: var(--card); border: 4px solid #000; box-shadow: 6px 6px 0px #000;" open>
                        <summary style="font-weight: 900; font-size: 1.1rem; outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                            <span>Apakah kelas di sini cocok untuk pemula?</span>
                            <i class='bx bx-plus' style="font-size: 1.5rem;"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Sangat cocok! Materi disusun secara bertahap mulai dari pengenalan sintaks dasar hingga studi kasus pembuatan aplikasi nyata.
                        </p>
                    </details>

                    <details class="neo-box" style="margin: 0; cursor: pointer; background-color: var(--card); border: 4px solid #000; box-shadow: 6px 6px 0px #000;">
                        <summary style="font-weight: 900; font-size: 1.1rem; outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                            <span>Bagaimana cara mendaftar dan mengikuti kelas?</span>
                            <i class='bx bx-plus' style="font-size: 1.5rem;"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Anda cukup membuat akun gratis melalui tombol <strong>Daftar</strong>, lalu masuk ke menu <strong>Cari Kelas</strong> untuk memilih dan mengambil kelas yang Anda minati.
                        </p>
                    </details>

                    <details class="neo-box" style="margin: 0; cursor: pointer; background-color: var(--card); border: 4px solid #000; box-shadow: 6px 6px 0px #000;">
                        <summary style="font-weight: 900; font-size: 1.1rem; outline: none; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                            <span>Apakah ada evaluasi atau penilaian?</span>
                            <i class='bx bx-plus' style="font-size: 1.5rem;"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Ya, setiap kelas memiliki tugas praktik dan kuis pilihan ganda yang dinilai langsung oleh pengajar untuk mengukur pemahaman Anda.
                        </p>
                    </details>
                </div>

                <!-- Kolom Kanan: Video Youtube Embed -->
                <div class="neo-box" style="margin: 0; padding: 16px; background-color: #FFD700; border: 4px solid #000; box-shadow: 8px 8px 0px #000;">
                    <div style="background-color: #000; color: #FFF; padding: 8px 12px; font-weight: 900; text-transform: uppercase; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;">
                        <i class='bx bxl-youtube' style="color: #FF0000; font-size: 1.3rem;"></i> Cuplikan Pengenalan Pemrograman
                    </div>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border: 3px solid #000; background-color: #000;">
                        <iframe 
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                            src="https://www.youtube.com/embed/0EdYaYIUOvA" 
                            title="Pengenalan Pemrograman" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- HUBUNGI KAMI (Cyan Block Container) -->
        <section id="kontak" style="margin-bottom: 60px; background-color: #06B6D4; border: 4px solid #000; box-shadow: 12px 12px 0px #000; overflow: hidden;">
            
            <!-- Running Text / Marquee Top -->
            <div style="background-color: #000; color: #FFD700; padding: 10px 0; font-weight: 900; font-size: 1rem; text-transform: uppercase; border-bottom: 4px solid #000; white-space: nowrap; overflow: hidden;">
                <div style="display: inline-block; animation: marquee 20s linear infinite;">
                    ★ BUTUH BANTUAN? HUBUNGI TIM KAMI ★ KONSULTASI KELAS GRATIS ★ DUKUNGAN TEKNIS 24/7 ★ MARI BERKOLABORASI ★ BUTUH BANTUAN? HUBUNGI TIM KAMI ★ KONSULTASI KELAS GRATIS ★
                </div>
            </div>
            <style>
                @keyframes marquee {
                    0% { transform: translateX(0%); }
                    100% { transform: translateX(-50%); }
                }
            </style>

            <div style="padding: 40px 30px;">
                <div class="section-header" style="border-bottom-color: #000;">
                    <div>
                        <span style="background-color: #FF5722; color: #FFF; border: 2px solid #000; font-weight: 900; padding: 4px 10px; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 8px; transform: rotate(-2deg);">Layanan Pengguna</span>
                        <h2 class="section-title" style="color: #000;"><i class='bx bxs-paper-plane' style="color: #FFF; text-shadow: 3px 3px 0px #000;"></i> Hubungi Kami</h2>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 35px; align-items: stretch;">
                    
                    <!-- Info Kontak Cards (Stacked) -->
                    <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 20px;">
                        <div class="neo-box brutal-hover" style="margin: 0; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; display: flex; align-items: center; gap: 20px; padding: 20px;">
                            <div style="width: 60px; height: 60px; background-color: #FFD700; border: 3px solid #000; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;">
                                <i class='bx bxs-envelope'></i>
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 900; text-transform: uppercase; color: #555;">Email Resmi</div>
                                <div style="font-size: 1.2rem; font-weight: 900; word-break: break-all;">support@elearning.local</div>
                                <span style="font-size: 0.75rem; background: #E5E7EB; padding: 2px 6px; font-weight: 700; border: 1px solid #000;">Respon Cepat &lt; 24 Jam</span>
                            </div>
                        </div>

                        <div class="neo-box brutal-hover" style="margin: 0; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; display: flex; align-items: center; gap: 20px; padding: 20px;">
                            <div style="width: 60px; height: 60px; background-color: #34D399; border: 3px solid #000; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;">
                                <i class='bx bxl-whatsapp'></i>
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 900; text-transform: uppercase; color: #555;">WhatsApp CS</div>
                                <div style="font-size: 1.2rem; font-weight: 900;">+62 812-3456-7890</div>
                                <span style="font-size: 0.75rem; background: #D1FAE5; padding: 2px 6px; font-weight: 700; border: 1px solid #000;">Senin - Jumat (08.00 - 17.00)</span>
                            </div>
                        </div>

                        <div class="neo-box brutal-hover" style="margin: 0; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; display: flex; align-items: center; gap: 20px; padding: 20px;">
                            <div style="width: 60px; height: 60px; background-color: #F472B6; border: 3px solid #000; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; font-size: 2rem; flex-shrink: 0;">
                                <i class='bx bxs-map-pin'></i>
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 900; text-transform: uppercase; color: #555;">Markas Besar</div>
                                <div style="font-size: 1.05rem; font-weight: 900;">Gedung Inovasi Digital Lt. 4</div>
                                <div style="font-size: 0.85rem; font-weight: 600;">Jl. Teknologi Raya No. 404, Jakarta</div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Pesan Cepat (Ticket Window Style) -->
                    <div class="neo-box" style="margin: 0; padding: 0; background-color: var(--card); border: 4px solid #000; box-shadow: 8px 8px 0px #000; display: flex; flex-direction: column;">
                        <!-- Window Top Bar -->
                        <div style="background-color: #000; color: #FFF; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid #000;">
                            <span style="font-weight: 900; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
                                <i class='bx bxs-message-Detail' style="color: #FFD700;"></i> Kirim Tiket Pesan
                            </span>
                            <div style="display: flex; gap: 6px;">
                                <span style="width: 12px; height: 12px; background-color: #EF4444; border-radius: 50%; display: inline-block; border: 1px solid #FFF;"></span>
                                <span style="width: 12px; height: 12px; background-color: #F59E0B; border-radius: 50%; display: inline-block; border: 1px solid #FFF;"></span>
                                <span style="width: 12px; height: 12px; background-color: #10B981; border-radius: 50%; display: inline-block; border: 1px solid #FFF;"></span>
                            </div>
                        </div>

                        <form onsubmit="event.preventDefault(); NeoAlert('Terkirim!', 'Terima kasih! Pesan Anda telah kami terima dan akan segera dibalas melalui email.', 'success'); this.reset();" style="padding: 25px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 16px;">
                                    <div>
                                        <label style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Nama Lengkap</label>
                                        <input type="text" class="neo-input" placeholder="Budi Santoso" required style="margin: 0; border: 3px solid #000;">
                                    </div>
                                    <div>
                                        <label style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Kategori</label>
                                        <select class="neo-input" style="margin: 0; border: 3px solid #000; font-weight: 700;">
                                            <option>Tanya Kelas</option>
                                            <option>Kendala Akun</option>
                                            <option>Kerjasama</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="margin-bottom: 16px;">
                                    <label style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Alamat Email</label>
                                    <input type="email" class="neo-input" placeholder="budi@email.com" required style="margin: 0; border: 3px solid #000;">
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <label style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Detail Pesan</label>
                                    <textarea class="neo-input" rows="3" placeholder="Tuliskan pertanyaan Anda di sini..." required style="margin: 0; border: 3px solid #000;"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="neo-btn brutal-hover" style="width: 100%; background-color: #FFD700; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #000; font-size: 1.05rem;">
                                <i class='bx bxs-send'></i> Kirim Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@section('footer')
    @include('partials.footer')
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const scrollContainer = document.getElementById("courseScroll");
            if (!scrollContainer) return;

            let isHovered = false;
            let isDown = false;
            let startX;
            let scrollLeft;
            const scrollSpeed = 1; // Kecepatan geser otomatis

            // Fungsi Loop Tak Terbatas (Infinite Scroll)
            function autoScroll() {
                if (!isHovered && !isDown) {
                    scrollContainer.scrollLeft += scrollSpeed;
                    // Jika sudah mencapai setengah dari total lebar (karena kartu diduplikasi 2x),
                    // kembalikan posisi ke 0 secara mulus tanpa terlihat patah
                    if (scrollContainer.scrollLeft >= scrollContainer.scrollWidth / 2) {
                        scrollContainer.scrollLeft = 0;
                    }
                }
                requestAnimationFrame(autoScroll);
            }

            // Mulai animasi
            requestAnimationFrame(autoScroll);

            // Berhenti saat kursor berada di atas kartu
            scrollContainer.addEventListener("mouseenter", () => {
                isHovered = true;
            });
            scrollContainer.addEventListener("mouseleave", () => {
                isHovered = false;
                isDown = false;
            });

            // Fitur Drag menggunakan Mouse
            scrollContainer.addEventListener("mousedown", (e) => {
                isDown = true;
                startX = e.pageX - scrollContainer.offsetLeft;
                scrollLeft = scrollContainer.scrollLeft;
            });
            scrollContainer.addEventListener("mouseup", () => {
                isDown = false;
            });
            scrollContainer.addEventListener("mousemove", (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - scrollContainer.offsetLeft;
                const walk = (x - startX) * 2;
                scrollContainer.scrollLeft = scrollLeft - walk;
            });

            // Berhenti saat disentuh di layar HP (Touch)
            scrollContainer.addEventListener("touchstart", () => {
                isHovered = true;
            });
            scrollContainer.addEventListener("touchend", () => {
                isHovered = false;
            });
        });
    </script>
@endsection

