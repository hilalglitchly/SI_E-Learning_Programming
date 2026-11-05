<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Learning Programming')</title>
    @yield('meta')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @yield('styles')
    @stack('styles')
</head>
<body @hasSection('body_style') style="@yield('body_style')" @endif>

@include('partials.navbar')

@yield('content')

@yield('footer')

@include('partials.cursor')

@yield('scripts')
@stack('scripts')
</body>
</html>
