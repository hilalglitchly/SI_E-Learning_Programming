@extends('layouts.app')

@section('title', 'E-Learning Programming - Belajar Koding Jadi Mudah')

@section('styles')
    <style>
        /* Scroll Progress Indicator (Native CSS scroll-timeline + JS fallback) */
        .home-scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: #DC2626;
            border-bottom: 2px solid #000;
            transform-origin: 0 50%;
            transform: scaleX(0);
            z-index: 1100;
            pointer-events: none;
        }

        @media (prefers-reduced-motion: no-preference) {
            @supports (animation-timeline: scroll()) {
                @keyframes home-grow-progress {
                    from { transform: scaleX(0); }
                    to { transform: scaleX(1); }
                }
                .home-scroll-progress {
                    animation: home-grow-progress auto linear;
                    animation-timeline: scroll();
                }
            }
        }

        /* Scroll Entry Reveal (Native CSS view() + IntersectionObserver fallback) */
        @media (prefers-reduced-motion: no-preference) {
            @supports ((animation-timeline: view()) and (animation-range: entry)) {
                @keyframes home-reveal-up {
                    from {
                        opacity: 0;
                        translate: 0 24px;
                    }
                    to {
                        opacity: 1;
                        translate: 0 0;
                    }
                }
                [data-home-reveal] {
                    animation: home-reveal-up linear both;
                    animation-timeline: view();
                    animation-range: entry 0% entry 28%;
                }
            }
        }

        .reveal-fallback-pending {
            opacity: 0;
            translate: 0 24px;
            transition: opacity 0.4s cubic-bezier(0.22, 1, 0.36, 1), translate 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .reveal-fallback-pending.is-revealed {
            opacity: 1;
            translate: 0 0;
        }

        .home-main {
            max-width: 1400px;
            padding-top: 10px;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            text-align: center;
            padding: clamp(48px, 7vw, 84px) 20px;
            background-color: var(--card);
            border: 4px solid var(--border);
            box-shadow: 12px 12px 0px var(--shadow-color);
            margin-bottom: 52px;
            overflow: hidden;
            z-index: 1;
        }

        /* Compositor-only Floating Tech Badges */
        .floating-box {
            position: absolute;
            border: 3px solid var(--border);
            box-shadow: 5px 5px 0px var(--shadow-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 2.3rem;
            z-index: -1;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            user-select: none;
        }
        .floating-box:hover {
            transform: scale(1.12) rotate(0deg) !important;
            box-shadow: 8px 8px 0px var(--shadow-color);
        }
        .box-js { width: 76px; height: 76px; background-color: #F7DF1E; color: #000; top: 10%; left: 5%; transform: rotate(-14deg); }
        .box-python { width: 84px; height: 84px; background-color: #3776AB; color: #FFF; bottom: 14%; right: 6%; transform: rotate(12deg); }
        .box-react { width: 68px; height: 68px; background-color: #61DAFB; color: #000; top: 14%; right: 8%; transform: rotate(18deg); border-radius: 50%; }
        .box-cpp { width: 72px; height: 72px; background-color: #00599C; color: #FFF; bottom: 12%; left: 8%; transform: rotate(-8deg); }
        .box-php { width: 82px; height: 52px; background-color: #777BB4; color: #FFF; top: 48%; left: 3%; transform: rotate(10deg); border-radius: 26px; font-size: 1.9rem; }
        .box-html { width: 68px; height: 68px; background-color: #C2410C; color: #FFF; top: 45%; right: 4%; transform: rotate(-16deg); }
        .box-java { width: 72px; height: 72px; background-color: #D97706; color: #000; top: 8%; left: 24%; transform: rotate(8deg); border-radius: 10px; }
        .box-git { width: 64px; height: 64px; background-color: #DC2626; color: #FFF; bottom: 8%; right: 24%; transform: rotate(-12deg); }

        @media (prefers-reduced-motion: no-preference) {
            @keyframes floatCompositor {
                0%, 100% { translate: 0 0; }
                50% { translate: 0 -14px; }
            }
            .box-js { animation: floatCompositor 6s ease-in-out infinite; }
            .box-python { animation: floatCompositor 7s ease-in-out infinite 1s; }
            .box-react { animation: floatCompositor 5.2s ease-in-out infinite 0.5s; }
            .box-cpp { animation: floatCompositor 6.5s ease-in-out infinite 1.4s; }
            .box-php { animation: floatCompositor 5.6s ease-in-out infinite 0.8s; }
            .box-html { animation: floatCompositor 6.2s ease-in-out infinite 1.8s; }
            .box-java { animation: floatCompositor 6.8s ease-in-out infinite 1.1s; }
            .box-git { animation: floatCompositor 5.8s ease-in-out infinite 0.3s; }
        }

        @media (max-width: 960px) {
            .floating-box { display: none; }
        }

        .hero-title {
            font-size: clamp(2.15rem, 5.2vw, 4.3rem);
            font-weight: 900;
            line-height: 1.08;
            margin-bottom: 22px;
            text-transform: uppercase;
            letter-spacing: -0.03em;
        }
        .hero-highlight {
            background-color: #DC2626;
            color: #FFFFFF;
            padding: 2px 16px;
            display: inline-block;
            transform: rotate(1deg);
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
            margin-top: 10px;
        }
        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--muted-foreground);
            max-width: 680px;
            margin: 0 auto 36px auto;
            font-weight: 600;
            line-height: 1.6;
        }
        .hero-cta-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-cta-primary,
        .btn-cta-secondary {
            min-height: 54px;
            padding: 14px 32px;
            font-size: 1.08rem;
            font-weight: 900;
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
            text-decoration: none;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-cta-primary {
            background-color: #DC2626;
            color: #FFFFFF;
        }
        .btn-cta-secondary {
            background-color: #FFFFFF;
            color: #000000;
        }

        /* 4-Column Balanced Stats Banner (Zero empty right slot) */
        .home-stats-section {
            background-color: #A855F7;
            border: 4px solid #000;
            box-shadow: 10px 10px 0px #000;
            padding: 28px;
            margin-bottom: 56px;
        }
        .home-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }
        .home-stat-card {
            margin: 0;
            padding: 24px 16px;
            text-align: center;
            background-color: #FFFFFF;
            color: #000000;
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
            transition: transform 0.16s ease, box-shadow 0.16s ease;
        }
        .home-stat-card:hover {
            transform: translate(-3px, -3px) rotate(0deg) !important;
            box-shadow: 9px 9px 0px #000;
        }
        .home-stat-card--tilt-l { transform: rotate(-1deg); }
        .home-stat-card--tilt-r { transform: rotate(1deg); }
        .home-stat-card--accent { background-color: #FFD700; }
        .home-stat-number {
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            font-weight: 900;
            line-height: 1.05;
            margin-bottom: 6px;
        }
        .home-stat-label {
            font-weight: 900;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #000000;
        }

        /* Section Containers & Headers */
        .home-section {
            margin-bottom: 64px;
            border: 4px solid #000;
            box-shadow: 12px 12px 0px #000;
            padding: 36px 28px;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 24px;
            border-bottom: 4px solid #000;
            padding-bottom: 16px;
            flex-wrap: wrap;
            gap: 16px;
        }
        .section-title {
            font-size: clamp(1.55rem, 3vw, 2.15rem);
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-tag {
            display: inline-block;
            font-weight: 900;
            padding: 4px 10px;
            font-size: 0.78rem;
            text-transform: uppercase;
            border: 2px solid #000;
            margin-bottom: 8px;
        }

        /* Catalog Controls: Filter, Search, Carousel Controls */
        .catalog-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 16px;
        }
        .catalog-filters {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .catalog-filter-btn {
            min-height: 44px;
            padding: 8px 16px;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 900;
            text-transform: uppercase;
            background-color: #FFFFFF;
            color: #000000;
            border: 3px solid #000;
            box-shadow: 3px 3px 0px #000;
            cursor: pointer;
            transition: transform 0.12s ease, background-color 0.12s ease;
        }
        .catalog-filter-btn.is-active {
            background-color: #000000;
            color: #FFD700;
        }
        .catalog-filter-btn:hover:not(.is-active) {
            transform: translate(-2px, -2px);
        }
        .catalog-search-controls {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            flex: 1 1 300px;
            justify-content: flex-end;
        }
        .catalog-search-input {
            width: 100%;
            max-width: 280px;
            min-height: 44px;
            padding: 8px 14px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            background-color: #FFFFFF;
            color: #000000;
            border: 3px solid #000;
            box-shadow: 3px 3px 0px #000;
        }
        .catalog-ctrl-btn {
            min-width: 44px;
            min-height: 44px;
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-family: inherit;
            font-size: 0.85rem;
            font-weight: 900;
            text-transform: uppercase;
            background-color: #FFFFFF;
            color: #000000;
            border: 3px solid #000;
            box-shadow: 3px 3px 0px #000;
            cursor: pointer;
        }
        .catalog-ctrl-btn:hover {
            background-color: #000000;
            color: #FFFFFF;
        }
        .catalog-meta-line {
            font-size: 0.88rem;
            font-weight: 800;
            color: #000000;
            margin-bottom: 10px;
        }

        /* Horizontal Course Scroller */
        .course-scroll-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 6px 0 18px 0;
        }
        .course-scroll-container {
            display: flex;
            gap: 22px;
            overflow-x: auto;
            scroll-behavior: auto;
            scrollbar-width: thin;
            padding: 8px 4px 14px;
            cursor: grab;
        }
        .course-scroll-container:active {
            cursor: grabbing;
        }
        .course-card-item {
            flex: 0 0 340px;
            max-width: 340px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: var(--card);
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
        }
        .course-card-item[hidden] {
            display: none !important;
        }
        .badge-pro-tier {
            background-color: #6D28D9;
            color: #FFFFFF;
            border: 2px solid #000;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 900;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-free-tier {
            background-color: #047857;
            color: #FFFFFF;
            border: 2px solid #000;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 900;
            text-transform: uppercase;
        }

        /* Real 2-Tier Free vs Pro Comparison */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 28px;
            align-items: stretch;
        }
        .plan-card {
            background-color: var(--card);
            color: var(--card-foreground);
            border: 4px solid var(--border);
            box-shadow: 8px 8px 0px var(--shadow-color);
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .plan-card--pro {
            background-color: #FFD700;
            color: #000000;
            border-color: #000;
            box-shadow: 8px 8px 0px #000;
        }
        .plan-list {
            list-style: none;
            padding: 0;
            margin: 18px 0 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .plan-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .plan-list i {
            font-size: 1.25rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .plan-rates {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(175px, 1fr));
            gap: 12px;
            margin: 14px 0 18px;
        }
        .plan-rate-pill {
            background-color: #FFFFFF;
            color: #000000;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px #000;
            padding: 12px 14px;
        }

        /* 4-Column Features Grid (Zero empty right slot) */
        .home-features-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 22px;
        }
        .feature-box {
            padding: 26px 22px;
            background-color: var(--card);
            color: var(--card-foreground);
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .feature-box:hover {
            transform: translate(-4px, -4px);
            box-shadow: 10px 10px 0px #000;
        }
        .feature-icon {
            font-size: 2.1rem;
            width: 58px;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #000;
            box-shadow: 4px 4px 0px #000;
            margin-bottom: 18px;
            color: #000;
        }

        /* Live Forum Preview Grid */
        .forum-preview-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }
        .forum-preview-card {
            background-color: var(--card);
            color: var(--card-foreground);
            border: 4px solid var(--border);
            box-shadow: 6px 6px 0px var(--shadow-color);
            padding: 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .forum-preview-card:hover {
            transform: translate(-4px, -4px);
            box-shadow: 10px 10px 0px var(--shadow-color);
        }

        /* Accessible FAQ Accordion with Rotating Icon */
        .faq-item {
            margin: 0;
            background-color: var(--card);
            color: var(--card-foreground);
            border: 4px solid #000;
            box-shadow: 6px 6px 0px #000;
        }
        .faq-summary {
            font-weight: 900;
            font-size: 1.05rem;
            list-style: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            min-height: 44px;
        }
        .faq-summary::-webkit-details-marker { display: none; }
        .faq-summary:focus-visible,
        .catalog-filter-btn:focus-visible,
        .catalog-ctrl-btn:focus-visible,
        .catalog-search-input:focus-visible,
        .marquee-pause-btn:focus-visible {
            outline: 3px solid #000;
            outline-offset: 2px;
        }
        .faq-toggle-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .faq-item[open] .faq-toggle-icon {
            transform: rotate(45deg);
        }

        /* Contact Marquee with Accessible Pause */
        .contact-marquee-bar {
            background-color: #000000;
            color: #FFD700;
            border-bottom: 4px solid #000;
            display: flex;
            align-items: center;
        }
        .contact-marquee-viewport {
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
            padding: 10px 0;
            font-weight: 900;
            font-size: 0.95rem;
            text-transform: uppercase;
        }
        .contact-marquee-track {
            display: inline-block;
        }
        @media (prefers-reduced-motion: no-preference) {
            @keyframes homeMarquee {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            .contact-marquee-track {
                animation: homeMarquee 24s linear infinite;
            }
        }
        .contact-marquee-track.is-paused {
            animation-play-state: paused !important;
        }
        .marquee-pause-btn {
            flex-shrink: 0;
            min-height: 44px;
            padding: 6px 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background-color: #FFD700;
            color: #000000;
            border: none;
            border-left: 3px solid #000;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 900;
            text-transform: uppercase;
            cursor: pointer;
        }
        .contact-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 16px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1080px) {
            .home-stats-grid,
            .home-features-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .forum-preview-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 780px) {
            .plans-grid,
            .forum-preview-grid {
                grid-template-columns: 1fr;
            }
            .home-section {
                padding: 26px 18px;
                box-shadow: 8px 8px 0px #000;
            }
            .home-stats-section {
                padding: 20px 16px;
                box-shadow: 7px 7px 0px #000;
            }
        }
        @media (max-width: 520px) {
            .home-stats-grid,
            .home-features-grid,
            .contact-form-row {
                grid-template-columns: 1fr;
            }
            .course-card-item {
                flex: 0 0 285px;
                max-width: 285px;
            }
            .catalog-search-input {
                max-width: 100%;
            }
            .hero-section {
                box-shadow: 7px 7px 0px var(--shadow-color);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .home-stat-card,
            .feature-box,
            .forum-preview-card,
            .faq-toggle-icon,
            .catalog-filter-btn {
                transition: none !important;
            }
            .home-stat-card:hover,
            .feature-box:hover,
            .forum-preview-card:hover {
                transform: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="home-scroll-progress" id="homeScrollProgress" aria-hidden="true"></div>

    <main class="neo-container home-main">

        <!-- HERO SECTION -->
        <section class="hero-section" aria-labelledby="hero-heading">
            <div class="floating-box box-js" aria-hidden="true"><i class='bx bxl-javascript'></i></div>
            <div class="floating-box box-python" aria-hidden="true"><i class='bx bxl-python'></i></div>
            <div class="floating-box box-react" aria-hidden="true"><i class='bx bxl-react'></i></div>
            <div class="floating-box box-cpp" aria-hidden="true"><i class='bx bxl-c-plus-plus'></i></div>
            <div class="floating-box box-php" aria-hidden="true"><i class='bx bxl-php'></i></div>
            <div class="floating-box box-html" aria-hidden="true"><i class='bx bxl-html5'></i></div>
            <div class="floating-box box-java" aria-hidden="true"><i class='bx bxl-java'></i></div>
            <div class="floating-box box-git" aria-hidden="true"><i class='bx bxl-git'></i></div>

            <h1 id="hero-heading" class="hero-title">
                Kuasai Pemrograman<br>
                <span class="hero-highlight">Dari Nol Sampai Mahir</span>
            </h1>
            <p class="hero-subtitle">
                Pelajari pemrograman lewat modul terstruktur, tugas praktik koding, kuis berbatas waktu, dan forum diskusi terbuka bersama pengajar praktisi.
            </p>
            <div class="hero-cta-group">
                <a href="#katalog" class="btn-cta-primary brutal-hover">
                    <i class='bx bx-compass' aria-hidden="true"></i> Jelajahi Kelas
                </a>
                <a href="{{ route('register') }}" class="btn-cta-secondary brutal-hover">
                    <i class='bx bxs-user-plus' aria-hidden="true"></i> Buat Akun Gratis
                </a>
            </div>
        </section>

        <!-- STATISTIK REAL (4 Kolom Penuh — Bebas Ruang Kosong) -->
        <section class="home-stats-section" aria-label="Statistik platform" data-home-reveal>
            <div class="home-stats-grid">
                <div class="home-stat-card home-stat-card--tilt-l">
                    <div class="home-stat-number" style="color: #DC2626;" data-count-target="{{ $totalKelas }}" data-count-suffix="+">{{ $totalKelas }}+</div>
                    <div class="home-stat-label">Kelas Pemrograman</div>
                </div>
                <div class="home-stat-card home-stat-card--tilt-r home-stat-card--accent">
                    <div class="home-stat-number" style="color: #000000;" data-count-target="{{ $totalPengajar }}" data-count-suffix="+">{{ $totalPengajar }}+</div>
                    <div class="home-stat-label">Mentor Praktisi</div>
                </div>
                <div class="home-stat-card home-stat-card--tilt-l">
                    <div class="home-stat-number" style="color: #047857;" data-count-target="{{ $totalMurid }}" data-count-suffix="+">{{ $totalMurid }}+</div>
                    <div class="home-stat-label">Siswa Bergabung</div>
                </div>
                <div class="home-stat-card home-stat-card--tilt-r">
                    <div class="home-stat-number" style="color: #6D28D9;" data-count-target="{{ $totalMateri }}" data-count-suffix="+">{{ $totalMateri }}+</div>
                    <div class="home-stat-label">Modul &amp; Materi</div>
                </div>
            </div>
        </section>

        <!-- KATALOG KELAS + FILTER & SEARCH -->
        <section id="katalog" class="home-section" style="background-color: #FCD34D;" data-home-reveal>
            <div class="section-header">
                <div>
                    <span class="section-tag" style="background-color: #000; color: #FFF;">Katalog Terbuka</span>
                    <h2 class="section-title" style="color: #000;">
                        <i class='bx bxs-flame' style="color: #DC2626;" aria-hidden="true"></i> Kelas Pemrograman
                    </h2>
                </div>
                <a href="{{ route('login', ['redirect' => 'enroll']) }}" class="brutal-hover" style="min-height: 44px; display: inline-flex; align-items: center; gap: 6px; font-weight: 900; color: #000; background: #FFF; padding: 8px 16px; border: 3px solid #000; box-shadow: 4px 4px 0px #000; text-decoration: none; text-transform: uppercase;">
                    Masuk untuk Gabung <i class='bx bx-right-arrow-alt' aria-hidden="true"></i>
                </a>
            </div>

            @if ($daftarKelas->count() > 0)
                <div class="catalog-toolbar">
                    <div class="catalog-filters" role="group" aria-label="Filter tipe kelas">
                        <button type="button" class="catalog-filter-btn is-active" data-course-filter="all" aria-pressed="true">Semua ({{ $daftarKelas->count() }})</button>
                        <button type="button" class="catalog-filter-btn" data-course-filter="gratis" aria-pressed="false">Gratis ({{ $daftarKelas->where('is_premium', false)->count() }})</button>
                        <button type="button" class="catalog-filter-btn" data-course-filter="pro" aria-pressed="false">Pro ({{ $daftarKelas->where('is_premium', true)->count() }})</button>
                    </div>

                    <div class="catalog-search-controls">
                        <input type="search" id="courseSearch" class="catalog-search-input" placeholder="Cari kelas atau pengajar..." aria-label="Cari kelas pemrograman">
                        <button type="button" id="coursePrevBtn" class="catalog-ctrl-btn" aria-label="Geser kelas ke kiri"><i class='bx bx-chevron-left' aria-hidden="true"></i></button>
                        <button type="button" id="courseNextBtn" class="catalog-ctrl-btn" aria-label="Geser kelas ke kanan"><i class='bx bx-chevron-right' aria-hidden="true"></i></button>
                        <button type="button" id="coursePauseBtn" class="catalog-ctrl-btn">
                            <i class='bx bx-pause' aria-hidden="true"></i>
                            <span data-pause-label>Jeda</span>
                        </button>
                    </div>
                </div>

                <div id="courseVisibleCount" class="catalog-meta-line" aria-live="polite">
                    Menampilkan {{ $daftarKelas->count() }} dari {{ $daftarKelas->count() }} kelas
                </div>

                <div class="course-scroll-wrapper">
                    <div class="course-scroll-container" id="courseScroll">
                        @foreach ($daftarKelas as $k)
                            @php
                                $searchBlob = strtolower($k->nama_kelas . ' ' . ($k->deskripsi ?? '') . ' ' . ($k->pengajar->nama_pengajar ?? ''));
                            @endphp
                            <article
                                class="neo-card course-card-item"
                                data-course-card
                                data-tier="{{ $k->is_premium ? 'pro' : 'gratis' }}"
                                data-search="{{ $searchBlob }}"
                            >
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 12px; gap: 6px;">
                                        <span class="badge-kode" style="margin: 0; background-color: var(--secondary); color: #000; border: 2px solid #000;">
                                            <i class='bx bx-code-alt' aria-hidden="true"></i> {{ $k->total_materi }} Materi
                                        </span>
                                        @if ($k->is_premium)
                                            <span class="badge-pro-tier"><i class='bx bxs-crown' aria-hidden="true"></i> Pro</span>
                                        @else
                                            <span class="badge-free-tier">Gratis</span>
                                        @endif
                                        <span style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground);">
                                            <i class='bx bxs-group' aria-hidden="true"></i> {{ $k->total_peserta }} Siswa
                                        </span>
                                    </div>
                                    <h3 style="font-size: 1.3rem; margin-bottom: 10px; line-height: 1.3;">{{ $k->nama_kelas }}</h3>
                                    <p style="font-size: 0.94rem; color: var(--muted-foreground); margin-bottom: 20px; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $k->deskripsi }}
                                    </p>
                                </div>

                                <div style="border-top: 2px dashed var(--border); padding-top: 14px; margin-top: auto;">
                                    <div style="font-size: 0.85rem; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                                        <i class='bx bxs-user-badge' style="font-size: 1.2rem; color: var(--accent);" aria-hidden="true"></i>
                                        <span>Pengajar: <strong>{{ $k->pengajar->nama_pengajar ?? 'Tim Pengajar' }}</strong></span>
                                    </div>
                                    <a href="{{ route('login', ['redirect' => 'enroll']) }}" class="neo-btn" style="text-align: center; text-decoration: none; display: flex; width: 100%; min-height: 44px; padding: 10px;">
                                        Mulai Belajar
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div id="courseEmptyState" class="neo-box" style="text-align: center; padding: 28px; background: #FFF; color: #000; margin: 0;" hidden>
                        <p style="font-weight: 800; font-size: 1.05rem; margin-bottom: 12px;">Tidak ada kelas yang cocok dengan filter atau kata kunci Anda.</p>
                        <button type="button" id="courseResetBtn" class="neo-btn" style="background-color: #000; color: #FFD700;">Tampilkan Semua Kelas</button>
                    </div>
                </div>
            @else
                <div class="neo-box" style="text-align: center; padding: 36px; background: #FFF; color: #000; margin: 0;">
                    <p style="font-weight: 700; font-size: 1.05rem;">Belum ada kelas yang dipublikasikan. Silakan kembali lagi nanti atau hubungi admin.</p>
                </div>
            @endif
        </section>

        <!-- PAKET BELAJAR: GRATIS VS PRO (Data Real tb_paket_langganan) -->
        @if ($paketList->count() > 0)
            <section id="paket" class="home-section" style="background-color: var(--card);" data-home-reveal>
                <div class="section-header" style="border-bottom-color: var(--border);">
                    <div>
                        <span class="section-tag" style="background-color: #6D28D9; color: #FFF;">Akses Fleksibel</span>
                        <h2 class="section-title">
                            <i class='bx bxs-crown' style="color: #D97706;" aria-hidden="true"></i> Mulai Gratis, Upgrade ke Pro Kapan Saja
                        </h2>
                    </div>
                </div>

                <div class="plans-grid">
                    <!-- Free Tier -->
                    <div class="plan-card">
                        <div>
                            <span class="badge-free-tier">Akun Gratis</span>
                            <h3 style="font-size: 1.65rem; margin: 12px 0 6px;">Free Tier</h3>
                            <div style="font-size: 2.1rem; font-weight: 900; margin-bottom: 8px;">Rp 0 <span style="font-size: 0.9rem; font-weight: 700; color: var(--muted-foreground);">/ selamanya</span></div>
                            <p style="color: var(--muted-foreground); font-weight: 600;">Cocok untuk memulai dasar pemrograman dan bergabung ke diskusi komunitas.</p>
                            <ul class="plan-list">
                                <li><i class='bx bx-check-square' style="color: #047857;" aria-hidden="true"></i> Akses seluruh kelas berlabel Gratis</li>
                                <li><i class='bx bx-check-square' style="color: #047857;" aria-hidden="true"></i> Unduh modul materi, kumpulkan tugas &amp; kerjakan kuis</li>
                                <li><i class='bx bx-check-square' style="color: #047857;" aria-hidden="true"></i> Bertanya &amp; berbagi solusi di Forum E-FORVM</li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="neo-btn" style="width: 100%; min-height: 48px; background-color: var(--muted); color: var(--foreground);">
                            Daftar Akun Gratis
                        </a>
                    </div>

                    <!-- Pro Tier (Real Plans from DB) -->
                    <div class="plan-card plan-card--pro">
                        <div>
                            <span class="badge-pro-tier"><i class='bx bxs-crown' aria-hidden="true"></i> Pro Member</span>
                            <h3 style="font-size: 1.65rem; margin: 12px 0 6px; color: #000;">Langganan Pro</h3>
                            <p style="color: #1F2937; font-weight: 700; margin-bottom: 8px;">Buka seluruh kelas Premium dan materi lanjutan dengan pembayaran transfer manual terverifikasi admin.</p>

                            <div class="plan-rates">
                                @foreach ($paketList as $paket)
                                    <div class="plan-rate-pill">
                                        <div style="font-size: 0.78rem; font-weight: 900; text-transform: uppercase; color: #6D28D9;">{{ $paket->nama_paket }}</div>
                                        <div style="font-size: 1.35rem; font-weight: 900; color: #000;">Rp {{ number_format($paket->harga, 0, ',', '.') }}</div>
                                        <div style="font-size: 0.8rem; font-weight: 700; color: #374151;">Aktif {{ $paket->durasi_hari }} hari</div>
                                    </div>
                                @endforeach
                            </div>

                            <ul class="plan-list" style="color: #000;">
                                <li><i class='bx bx-check-square' style="color: #000;" aria-hidden="true"></i> Semua manfaat Akun Gratis</li>
                                <li><i class='bx bx-check-square' style="color: #000;" aria-hidden="true"></i> Akses penuh seluruh kelas Premium (Keamanan Jaringan, ML, Backend, dll.)</li>
                                <li><i class='bx bx-check-square' style="color: #000;" aria-hidden="true"></i> Masa aktif otomatis terakumulasi jika diperpanjang</li>
                            </ul>
                        </div>
                        <a href="{{ route('register') }}" class="neo-btn" style="width: 100%; min-height: 48px; background-color: #000; color: #FFD700;">
                            Daftar &amp; Aktifkan Pro
                        </a>
                    </div>
                </div>
            </section>
        @endif

        <!-- FITUR UNGGULAN (4 Kolom Penuh — WCAG AA Header) -->
        <section class="home-section" style="background-color: #BE185D;" data-home-reveal>
            <div class="section-header" style="border-bottom-color: #000;">
                <div>
                    <span class="section-tag" style="background-color: #FFD700; color: #000;">Fitur Platform</span>
                    <h2 class="section-title" style="color: #FFFFFF;">
                        <i class='bx bxs-star' style="color: #FFD700;" aria-hidden="true"></i> Cara Belajar yang Terukur
                    </h2>
                </div>
            </div>

            <div class="home-features-grid">
                <div class="feature-box">
                    <div class="feature-icon" style="background-color: #FFD700;">
                        <i class='bx bx-laptop' aria-hidden="true"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 8px;">Materi &amp; Tugas Praktik</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500; font-size: 0.94rem;">
                        Setiap kelas menyediakan modul unduhan, tugas koding berbatas tenggat, serta umpan balik nilai langsung dari pengajar.
                    </p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon" style="background-color: #60A5FA;">
                        <i class='bx bx-conversation' aria-hidden="true"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 8px;">Diskusi Kelas &amp; Forum</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500; font-size: 0.94rem;">
                        Tanyakan bug atau arsitektur kode di ruang diskusi per kelas maupun di forum terbuka E-FORVM dengan sistem solusi.
                    </p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon" style="background-color: #34D399;">
                        <i class='bx bx-line-chart' aria-hidden="true"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 8px;">Kuis &amp; Progress Real-Time</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500; font-size: 0.94rem;">
                        Kerjakan kuis pilihan ganda dengan timer otomatis dan pantau persentase penyelesaian belajar langsung dari dashboard.
                    </p>
                </div>

                <div class="feature-box">
                    <div class="feature-icon" style="background-color: #C4B5FD;">
                        <i class='bx bxs-crown' aria-hidden="true"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 8px;">Jalur Kelas Pro</h3>
                    <p style="color: var(--muted-foreground); line-height: 1.6; font-weight: 500; font-size: 0.94rem;">
                        Lanjutkan ke topik lanjutan seperti keamanan jaringan, machine learning, dan backend melalui paket Pro fleksibel.
                    </p>
                </div>
            </div>
        </section>

        <!-- CUPLIKAN FORUM KOMUNITAS (Data Real tb_forum_post) -->
        @if ($recentPosts->count() > 0)
            <section class="home-section" style="background-color: var(--card);" data-home-reveal>
                <div class="section-header" style="border-bottom-color: var(--border);">
                    <div>
                        <span class="section-tag" style="background-color: #FFD700; color: #000;">Komunitas Aktif</span>
                        <h2 class="section-title">
                            <i class='bx bx-message-rounded-dots' style="color: var(--accent);" aria-hidden="true"></i> Diskusi Terbaru di E-FORVM
                        </h2>
                    </div>
                    <a href="{{ route('forum.index') }}" class="neo-btn" style="min-height: 44px; background-color: var(--secondary); color: #000;">
                        Buka Forum <i class='bx bx-right-arrow-alt' aria-hidden="true"></i>
                    </a>
                </div>

                <div class="forum-preview-grid">
                    @foreach ($recentPosts as $post)
                        <a href="{{ route('forum.show', ['id' => $post->id_post]) }}" class="forum-preview-card">
                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 10px; font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground);">
                                    <span>{{ $post->user->username ?? 'Anggota' }}</span>
                                    @if ($post->status === 'solved')
                                        <span style="background-color: #047857; color: #FFF; padding: 2px 8px; border: 2px solid #000; font-size: 0.72rem;">SOLVED</span>
                                    @else
                                        <span style="text-transform: uppercase;">{{ $post->tipe }}</span>
                                    @endif
                                </div>
                                <h3 style="font-size: 1.12rem; line-height: 1.35; margin-bottom: 12px; color: var(--foreground);">{{ $post->judul }}</h3>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap; border-top: 2px dashed var(--border); padding-top: 12px; font-size: 0.82rem; font-weight: 800; color: var(--muted-foreground);">
                                <span><i class='bx bx-comment-detail' aria-hidden="true"></i> {{ $post->jawaban_count }} Jawaban</span>
                                @if ($post->tags->isNotEmpty())
                                    <span>#{{ $post->tags->first()->nama_tag }}</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- FAQ & VIDEO SECTION -->
        <section id="faq" class="home-section" style="background-color: #10B981;" data-home-reveal>
            <div class="section-header">
                <div>
                    <span class="section-tag" style="background-color: #FFF; color: #000;">Tanya Jawab</span>
                    <h2 class="section-title" style="color: #000;">
                        <i class='bx bxs-help-circle' style="color: #000;" aria-hidden="true"></i> Pertanyaan Umum (FAQ)
                    </h2>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 28px; align-items: start;">
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <details class="neo-box faq-item" open>
                        <summary class="faq-summary">
                            <span>Apakah kelas di sini cocok untuk pemula?</span>
                            <i class='bx bx-plus faq-toggle-icon' aria-hidden="true"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Sangat cocok. Materi disusun bertahap mulai dari sintaks dasar hingga studi kasus pembangunan aplikasi nyata.
                        </p>
                    </details>

                    <details class="neo-box faq-item">
                        <summary class="faq-summary">
                            <span>Bagaimana cara mendaftar dan mengikuti kelas?</span>
                            <i class='bx bx-plus faq-toggle-icon' aria-hidden="true"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Buat akun gratis melalui tombol <strong>Daftar</strong>, lalu buka menu <strong>Cari Kelas</strong> di dashboard untuk bergabung ke kelas yang diminati.
                        </p>
                    </details>

                    <details class="neo-box faq-item">
                        <summary class="faq-summary">
                            <span>Bagaimana cara mengaktifkan Langganan Pro untuk kelas Premium?</span>
                            <i class='bx bx-plus faq-toggle-icon' aria-hidden="true"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Setelah login sebagai murid, buka menu <strong>Pro</strong>, pilih paket Bulanan atau Tahunan, lalu unggah bukti transfer bank. Admin akan memverifikasi pembayaran dan akses kelas Premium langsung terbuka.
                        </p>
                    </details>

                    <details class="neo-box faq-item">
                        <summary class="faq-summary">
                            <span>Apakah ada evaluasi atau penilaian?</span>
                            <i class='bx bx-plus faq-toggle-icon' aria-hidden="true"></i>
                        </summary>
                        <p style="margin-top: 12px; color: var(--muted-foreground); line-height: 1.6; font-weight: 500; border-top: 2px dashed var(--border); padding-top: 12px;">
                            Ya, setiap kelas memiliki tugas praktik yang dinilai langsung oleh pengajar serta kuis pilihan ganda dengan hasil otomatis.
                        </p>
                    </details>
                </div>

                <!-- Video Youtube Embed (Lazy loaded below the fold) -->
                <div class="neo-box" style="margin: 0; padding: 16px; background-color: #FFD700; color: #000; border: 4px solid #000; box-shadow: 8px 8px 0px #000;">
                    <div style="background-color: #000; color: #FFF; padding: 8px 12px; font-weight: 900; text-transform: uppercase; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; font-size: 0.9rem;">
                        <i class='bx bxl-youtube' style="color: #F87171; font-size: 1.3rem;" aria-hidden="true"></i> Cuplikan Pengenalan Pemrograman
                    </div>
                    <div style="position: relative; aspect-ratio: 16 / 9; overflow: hidden; border: 3px solid #000; background-color: #000;">
                        <iframe
                            style="position: absolute; inset: 0; width: 100%; height: 100%; border: 0;"
                            src="https://www.youtube.com/embed/0EdYaYIUOvA"
                            title="Apa itu E-Learning Programming"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- HUBUNGI KAMI -->
        <section id="kontak" class="home-section" style="padding: 0; background-color: #06B6D4; overflow: hidden;" data-home-reveal>
            <div class="contact-marquee-bar">
                <div class="contact-marquee-viewport" aria-hidden="true">
                    <div id="contactMarqueeTrack" class="contact-marquee-track">
                        BUTUH BANTUAN? HUBUNGI TIM KAMI &bull; KONSULTASI KELAS GRATIS &bull; LAYANAN SENIN - JUMAT (08.00 - 17.00) &bull; MARI BERKOLABORASI &bull; BUTUH BANTUAN? HUBUNGI TIM KAMI &bull; KONSULTASI KELAS GRATIS &bull; LAYANAN SENIN - JUMAT (08.00 - 17.00) &bull;
                    </div>
                </div>
                <button type="button" id="marqueePauseBtn" class="marquee-pause-btn">
                    <i class='bx bx-pause' aria-hidden="true"></i>
                    <span data-marquee-label>Jeda</span>
                </button>
            </div>

            <div style="padding: 36px 28px;">
                <div class="section-header">
                    <div>
                        <span class="section-tag" style="background-color: #C2410C; color: #FFF; transform: rotate(-1deg);">Layanan Pengguna</span>
                        <h2 class="section-title" style="color: #000;">
                            <i class='bx bxs-paper-plane' style="color: #000;" aria-hidden="true"></i> Hubungi Kami
                        </h2>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 30px; align-items: stretch;">
                    <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 18px;">
                        <div class="neo-box brutal-hover" style="margin: 0; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; display: flex; align-items: center; gap: 18px; padding: 18px;">
                            <div style="width: 56px; height: 56px; background-color: #FFD700; border: 3px solid #000; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0;">
                                <i class='bx bxs-envelope' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.78rem; font-weight: 900; text-transform: uppercase; color: #374151;">Email Resmi</div>
                                <div style="font-size: 1.1rem; font-weight: 900; word-break: break-word;">{{ config('app.contact.email') }}</div>
                                <span style="font-size: 0.76rem; background: #E5E7EB; padding: 2px 6px; font-weight: 700; border: 1px solid #000;">Respon di hari kerja</span>
                            </div>
                        </div>

                        <div class="neo-box brutal-hover" style="margin: 0; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; display: flex; align-items: center; gap: 18px; padding: 18px;">
                            <div style="width: 56px; height: 56px; background-color: #34D399; border: 3px solid #000; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0;">
                                <i class='bx bxl-whatsapp' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.78rem; font-weight: 900; text-transform: uppercase; color: #374151;">WhatsApp CS</div>
                                <div style="font-size: 1.1rem; font-weight: 900;">{{ config('app.contact.phone') }}</div>
                                <span style="font-size: 0.76rem; background: #D1FAE5; padding: 2px 6px; font-weight: 700; border: 1px solid #000;">Senin - Jumat (08.00 - 17.00)</span>
                            </div>
                        </div>

                        <div class="neo-box brutal-hover" style="margin: 0; background-color: #FFF; color: #000; border: 4px solid #000; box-shadow: 6px 6px 0px #000; display: flex; align-items: center; gap: 18px; padding: 18px;">
                            <div style="width: 56px; height: 56px; background-color: #F472B6; border: 3px solid #000; box-shadow: 3px 3px 0px #000; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0;">
                                <i class='bx bxs-map-pin' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.78rem; font-weight: 900; text-transform: uppercase; color: #374151;">Alamat Kantor</div>
                                <div style="font-size: 1.02rem; font-weight: 900;">{{ config('app.contact.address_line1') }}</div>
                                <div style="font-size: 0.85rem; font-weight: 600;">{{ config('app.contact.address_line2') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin: 0; padding: 0; background-color: var(--card); border: 4px solid #000; box-shadow: 8px 8px 0px #000; display: flex; flex-direction: column;">
                        <div style="background-color: #000; color: #FFF; padding: 12px 20px; border-bottom: 4px solid #000; font-weight: 900; text-transform: uppercase; letter-spacing: 0.04em; display: flex; align-items: center; gap: 8px;">
                            <i class='bx bxs-message-detail' style="color: #FFD700;" aria-hidden="true"></i> Kirim Pesan ke Admin
                        </div>

                        <form method="POST" action="{{ route('kontak.send') }}" style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            @csrf
                            <div>
                                @if (session('kontak_success'))
                                    <div class="auth-alert auth-alert--success" role="status">
                                        <i class='bx bxs-check-circle' aria-hidden="true"></i>
                                        <span>{{ session('kontak_success') }}</span>
                                    </div>
                                @elseif ($errors->any())
                                    <div class="auth-alert auth-alert--error" role="alert">
                                        <i class='bx bxs-error-alt' aria-hidden="true"></i>
                                        <span>{{ $errors->first() }}</span>
                                    </div>
                                @endif

                                <div class="contact-form-row">
                                    <div>
                                        <label for="kontak-nama" style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Nama Lengkap</label>
                                        <input type="text" id="kontak-nama" name="nama" class="neo-input" placeholder="Nama lengkap Anda" value="{{ old('nama') }}" maxlength="100" autocomplete="name" required style="margin: 0; border: 3px solid #000;">
                                    </div>
                                    <div>
                                        <label for="kontak-kategori" style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Kategori</label>
                                        <select id="kontak-kategori" name="kategori" class="neo-input" style="margin: 0; border: 3px solid #000; font-weight: 700;">
                                            <option value="Tanya Kelas" {{ old('kategori') === 'Tanya Kelas' ? 'selected' : '' }}>Tanya Kelas</option>
                                            <option value="Kendala Akun" {{ old('kategori') === 'Kendala Akun' ? 'selected' : '' }}>Kendala Akun</option>
                                            <option value="Kerjasama" {{ old('kategori') === 'Kerjasama' ? 'selected' : '' }}>Kerjasama</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="margin-bottom: 16px;">
                                    <label for="kontak-email" style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Alamat Email</label>
                                    <input type="email" id="kontak-email" name="email" class="neo-input" placeholder="nama@email.com" value="{{ old('email') }}" maxlength="100" autocomplete="email" spellcheck="false" required style="margin: 0; border: 3px solid #000;">
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <label for="kontak-pesan" style="font-weight: 900; display: block; margin-bottom: 6px; text-transform: uppercase; font-size: 0.8rem;">Detail Pesan</label>
                                    <textarea id="kontak-pesan" name="pesan" class="neo-input" rows="3" placeholder="Tuliskan pertanyaan atau kendala Anda..." maxlength="1000" required style="margin: 0; border: 3px solid #000;">{{ old('pesan') }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="neo-btn brutal-hover" style="width: 100%; min-height: 48px; background-color: #FFD700; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #000; font-size: 1rem;">
                                <i class='bx bxs-send' aria-hidden="true"></i> Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection

@section('footer')
    @include('partials.footer')
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/home.js') }}?v={{ file_exists(public_path('assets/js/home.js')) ? filemtime(public_path('assets/js/home.js')) : time() }}"></script>
@endsection
