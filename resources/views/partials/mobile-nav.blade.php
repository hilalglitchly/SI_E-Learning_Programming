@php
    $role = strtolower(auth()->user()->role ?? '');
@endphp

<nav class="neo-mobile-nav no-print" aria-label="Navigasi Mobile E-FORVM">
    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') || request()->routeIs('kelas.*') || request()->routeIs('diskusi.*') ? 'is-active' : '' }}">
        <i class='bx bxs-home' aria-hidden="true"></i>
        <span>Beranda</span>
    </a>

    @if ($role === 'murid')
        <a href="{{ route('enroll.index') }}" class="{{ request()->routeIs('enroll.*') ? 'is-active' : '' }}">
            <i class='bx bx-search' aria-hidden="true"></i>
            <span>Kelas</span>
        </a>
        <a href="{{ route('tugas.index') }}" class="{{ request()->routeIs('tugas.index') || request()->routeIs('kuis.*') ? 'is-active' : '' }}">
            <i class='bx bx-task' aria-hidden="true"></i>
            <span>Tugas</span>
        </a>
        <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
            <i class='bx bx-message-rounded-dots' aria-hidden="true"></i>
            <span>Forum</span>
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
    @elseif ($role === 'pimpinan')
        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'is-active' : '' }}">
            <i class='bx bxs-report' aria-hidden="true"></i>
            <span>Laporan</span>
        </a>
        <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
            <i class='bx bx-message-rounded-dots' aria-hidden="true"></i>
            <span>Forum</span>
        </a>
    @endif

    <a href="{{ route('profil.index') }}" class="neo-mobile-nav-akun {{ request()->routeIs('profil.*') || request()->routeIs('langganan.*') ? 'is-active' : '' }}">
        <span class="neo-mobile-nav-icon-wrap">
            <i class='bx bxs-user-circle' aria-hidden="true"></i>
            <span id="mobile-nav-notif-badge" class="neo-mobile-nav-badge" style="display: none;">0</span>
        </span>
        <span>Akun</span>
    </a>
</nav>
