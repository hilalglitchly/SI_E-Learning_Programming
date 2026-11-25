<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — @yield('title') | E-Learning Programming</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .err-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .err-card {
            width: 100%;
            max-width: 560px;
            background: var(--card);
            border: 4px solid var(--border);
            box-shadow: 10px 10px 0 var(--shadow-color);
            padding: 36px 28px;
            text-align: center;
        }
        .err-code {
            display: inline-block;
            font-size: clamp(3rem, 9vw, 4.5rem);
            font-weight: 900;
            line-height: 1;
            padding: 10px 22px;
            border: 3px solid var(--border);
            box-shadow: 5px 5px 0 var(--shadow-color);
            margin-bottom: 20px;
        }
        .err-title {
            font-size: 1.5rem;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0 0 10px;
        }
        .err-desc {
            color: var(--muted-foreground);
            font-weight: 600;
            line-height: 1.6;
            margin: 0 0 26px;
        }
        .err-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <main class="err-page">
        <section class="err-card" aria-labelledby="err-heading">
            <div class="err-code" style="background: @yield('badge_bg', 'var(--secondary)'); color: @yield('badge_fg', '#000');">
                @yield('code')
            </div>
            <h1 id="err-heading" class="err-title">@yield('title')</h1>
            <p class="err-desc">@yield('message')</p>
            <div class="err-actions">
                <a href="{{ url('/') }}" class="neo-btn" style="background: var(--accent); color: #fff;">Kembali ke Beranda</a>
                <button type="button" onclick="window.history.back()" class="neo-btn" style="background: var(--muted); color: var(--foreground);">Halaman Sebelumnya</button>
            </div>
        </section>
    </main>
</body>
</html>
