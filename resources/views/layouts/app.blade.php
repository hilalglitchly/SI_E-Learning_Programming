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
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @yield('styles')
    @stack('styles')
</head>
<body class="{{ auth()->check() ? 'is-auth-shell' : 'is-guest-shell' }}" @hasSection('body_style') style="@yield('body_style')" @endif>

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
</script>
</body>
</html>
