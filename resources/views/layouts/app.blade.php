<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#FFD700">
    <meta name="application-name" content="E-FORVM">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="E-FORVM">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/icons/icon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icons/apple-touch-icon.png') }}">
    <title>@yield('title', 'E-FORVM')</title>
    @yield('meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @yield('styles')
    @stack('styles')
    <script type="speculationrules">
    {
        "prefetch": [{
            "where": {
                "and": [
                    { "href_matches": "/*" },
                    { "not": { "href_matches": "/logout" } },
                    { "not": { "href_matches": "/files/*" } },
                    { "not": { "href_matches": "/api/*" } },
                    { "not": { "href_matches": "/kuis/*" } },
                    { "not": { "selector_matches": "[target=_blank], [download], [rel~=nofollow]" } }
                ]
            },
            "eagerness": "moderate"
        }]
    }
    </script>
</head>
<body class="{{ auth()->check() ? 'is-auth-shell' : 'is-guest-shell' }}" @hasSection('body_style') style="@yield('body_style')" @endif>
<div id="nav-progress-bar" aria-hidden="true"></div>
@auth
<div id="mobile-ptr-pill" class="no-print" role="status" aria-live="polite" aria-hidden="true">
    <i class='bx bx-refresh' aria-hidden="true"></i>
    <span id="mobile-ptr-label">Tarik untuk muat ulang</span>
</div>
@endauth

@include('partials.navbar')

@yield('content')

@hasSection('footer')
    @yield('footer')
@else
    @include('partials.footer')
@endif

@auth
    @include('partials.mobile-nav')
@endauth
@include('partials.cursor')

@yield('scripts')
@stack('scripts')
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }

    (function () {
        let deferredPrompt = null;
        const installBtns = document.querySelectorAll('.js-pwa-install-btn');
        if (!installBtns.length) return;

        const setVisible = (show) => {
            installBtns.forEach((btn) => {
                btn.style.display = show ? 'inline-flex' : 'none';
            });
        };

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            setVisible(true);
        });

        installBtns.forEach((btn) => {
            btn.addEventListener('click', async () => {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                await deferredPrompt.userChoice;
                deferredPrompt = null;
                setVisible(false);
            });
        });

        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            setVisible(false);
        });
    })();

    // Instant tap/navigation progress bar + smart link prefetch for snappy mobile UX
    (function () {
        const bar = document.getElementById('nav-progress-bar');
        const prefetched = new Set();
        const skipPrefixes = ['/logout', '/files/', '/api/', '/kuis/'];

        function canPrefetch(anchor) {
            if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) return false;
            const raw = anchor.getAttribute('href') || '';
            if (!raw || raw.startsWith('#') || raw.startsWith('mailto:') || raw.startsWith('javascript:')) return false;
            try {
                const url = new URL(anchor.href, window.location.origin);
                if (url.origin !== window.location.origin) return false;
                if (url.pathname === window.location.pathname && url.search === window.location.search) return false;
                if (skipPrefixes.some((p) => url.pathname.startsWith(p))) return false;
                return url.href;
            } catch (e) {
                return false;
            }
        }

        function prefetchLink(anchor) {
            const href = canPrefetch(anchor);
            if (!href || prefetched.has(href)) return;
            prefetched.add(href);
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.as = 'document';
            link.href = href;
            document.head.appendChild(link);
        }

        document.addEventListener('touchstart', (e) => {
            const a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (a) prefetchLink(a);
        }, { passive: true });

        document.addEventListener('pointerenter', (e) => {
            const a = e.target && e.target.closest ? e.target.closest('nav a, header a, .account-menu-row') : null;
            if (a) prefetchLink(a);
        }, true);

        document.addEventListener('click', (e) => {
            if (!bar || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            const a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (!a || !canPrefetch(a)) return;
            bar.classList.add('is-loading');
        });

        window.addEventListener('pageshow', () => {
            if (bar) bar.classList.remove('is-loading');
        });
    })();
@auth

    // Lightweight mobile Pull-to-Refresh for authenticated app shell (passive listeners, never blocks normal scroll)
    (function () {
        const pill = document.getElementById('mobile-ptr-pill');
        const label = document.getElementById('mobile-ptr-label');
        if (!pill || !label) return;

        let startY = 0;
        let pulling = false;
        let pullDist = 0;
        const threshold = 76;

        const isBlockedTarget = (el) => !!(el && el.closest && el.closest('input, textarea, select, [contenteditable], .ql-editor, #notif-menu, .neo-modal-overlay'));

        window.addEventListener('touchstart', (e) => {
            if (window.innerWidth > 768 || window.scrollY > 0 || e.touches.length !== 1 || isBlockedTarget(e.target)) {
                pulling = false;
                return;
            }
            startY = e.touches[0].clientY;
            pullDist = 0;
            pulling = true;
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (!pulling) return;
            const dy = e.touches[0].clientY - startY;
            if (dy <= 10 || window.scrollY > 0) {
                pullDist = 0;
                pill.classList.remove('is-pulling', 'is-ready');
                pill.style.opacity = '0';
                pill.style.transform = 'translate(-50%, -64px)';
                return;
            }
            pullDist = Math.min(dy * 0.55, 104);
            const ready = pullDist >= threshold;
            pill.classList.add('is-pulling');
            pill.classList.toggle('is-ready', ready);
            pill.setAttribute('aria-hidden', 'false');
            pill.style.opacity = String(Math.min(1, pullDist / 48));
            pill.style.transform = `translate(-50%, ${Math.round(pullDist - 54)}px)`;
            label.textContent = ready ? 'Lepas untuk muat ulang' : 'Tarik untuk muat ulang';
        }, { passive: true });

        window.addEventListener('touchend', () => {
            if (!pulling) return;
            const shouldReload = pullDist >= threshold;
            pulling = false;
            pill.classList.remove('is-pulling');
            if (shouldReload) {
                label.textContent = 'Memuat ulang...';
                pill.style.transform = 'translate(-50%, 10px)';
                pill.style.opacity = '1';
                window.location.reload();
                return;
            }
            pill.classList.remove('is-ready');
            pill.setAttribute('aria-hidden', 'true');
            pill.style.opacity = '0';
            pill.style.transform = 'translate(-50%, -64px)';
        }, { passive: true });
    })();
@endauth
</script>
</body>
</html>
