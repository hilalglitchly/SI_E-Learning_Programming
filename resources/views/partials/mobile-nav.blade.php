@php
    $isLogged = auth()->check();
    $role = $isLogged ? strtolower(auth()->user()->role ?? '') : '';
@endphp

<nav class="neo-mobile-nav no-print" aria-label="Navigasi Mobile E-FORVM">
    <a href="{{ $isLogged ? route('dashboard') : route('home') }}"
       class="{{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'is-active' : '' }}">
        <i class='bx bxs-home' aria-hidden="true"></i>
        <span>Beranda</span>
    </a>

    @if ($role === 'murid')
        <a href="{{ route('enroll.index') }}" class="{{ request()->routeIs('enroll.*') ? 'is-active' : '' }}">
            <i class='bx bx-search' aria-hidden="true"></i>
            <span>Kelas</span>
        </a>
        <a href="{{ route('tugas.index') }}" class="{{ request()->routeIs('tugas.index') ? 'is-active' : '' }}">
            <i class='bx bx-task' aria-hidden="true"></i>
            <span>Tugas</span>
        </a>
        <a href="{{ route('langganan.index') }}" class="{{ request()->routeIs('langganan.*') ? 'is-active' : '' }}">
            <i class='bx bxs-crown' aria-hidden="true"></i>
            <span>Pro</span>
        </a>
        <a href="{{ route('profil.index') }}" class="{{ request()->routeIs('profil.*') ? 'is-active' : '' }}">
            <i class='bx bxs-user-circle' aria-hidden="true"></i>
            <span>Akun</span>
        </a>
    @elseif ($role === 'pengajar')
        <a href="{{ route('tugas.index') }}" class="{{ request()->routeIs('tugas.index') || request()->routeIs('penilaian.*') ? 'is-active' : '' }}">
            <i class='bx bx-task' aria-hidden="true"></i>
            <span>Nilai</span>
        </a>
        <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
            <i class='bx bx-message-rounded-dots' aria-hidden="true"></i>
            <span>Forum</span>
        </a>
        <a href="{{ route('profil.index') }}" class="{{ request()->routeIs('profil.*') ? 'is-active' : '' }}">
            <i class='bx bxs-user-circle' aria-hidden="true"></i>
            <span>Akun</span>
        </a>
    @elseif ($role === 'admin')
        <a href="{{ route('admin.langganan.index') }}" class="{{ request()->routeIs('admin.langganan.*') ? 'is-active' : '' }}">
            <i class='bx bx-credit-card' aria-hidden="true"></i>
            <span>Bayar</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'is-active' : '' }}">
            <i class='bx bxs-report' aria-hidden="true"></i>
            <span>Laporan</span>
        </a>
        <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
            <i class='bx bx-message-rounded-dots' aria-hidden="true"></i>
            <span>Forum</span>
        </a>
        <a href="{{ route('profil.index') }}" class="{{ request()->routeIs('profil.*') ? 'is-active' : '' }}">
            <i class='bx bxs-user-circle' aria-hidden="true"></i>
            <span>Akun</span>
        </a>
    @elseif ($role === 'pimpinan')
        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'is-active' : '' }}">
            <i class='bx bxs-report' aria-hidden="true"></i>
            <span>Laporan</span>
        </a>
        <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
            <i class='bx bx-message-rounded-dots' aria-hidden="true"></i>
            <span>Forum</span>
        </a>
        <a href="{{ route('profil.index') }}" class="{{ request()->routeIs('profil.*') ? 'is-active' : '' }}">
            <i class='bx bxs-user-circle' aria-hidden="true"></i>
            <span>Akun</span>
        </a>
    @else
        <a href="{{ route('home') }}#katalog">
            <i class='bx bx-book-open' aria-hidden="true"></i>
            <span>Katalog</span>
        </a>
        <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
            <i class='bx bx-message-rounded-dots' aria-hidden="true"></i>
            <span>Forum</span>
        </a>
        <a href="{{ route('login') }}" class="{{ request()->routeIs('login') || request()->routeIs('register') ? 'is-active' : '' }}">
            <i class='bx bx-log-in' aria-hidden="true"></i>
            <span>Masuk</span>
        </a>
    @endif
</nav>
