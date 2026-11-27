@extends('layouts.app')

@section('title', 'Cari Kelas - E-FORVM')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">

            @if (!empty($pesan))
                <div class="auth-alert auth-alert--success" role="status">
                    <i class='bx bxs-info-circle' aria-hidden="true"></i>
                    <span><strong>Info:</strong> {{ $pesan }}</span>
                </div>
            @endif

            @php
                $totalAvailable = count($kelasTersedia);
                $gratisCount = count(array_filter($kelasTersedia, fn($k) => empty($k['is_premium'])));
                $proCount = $totalAvailable - $gratisCount;
            @endphp

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 18px;">
                <h1 style="margin: 0; font-size: clamp(1.5rem, 3vw, 2rem); text-transform: uppercase;">Kelas yang Tersedia</h1>
                @if (empty($hasActivePro))
                    <a href="{{ route('langganan.index') }}" class="neo-btn neo-btn--pro" style="min-height: 44px; padding: 8px 16px; font-size: 0.85rem;">
                        <i class='bx bxs-crown' aria-hidden="true"></i> Upgrade ke Pro
                    </a>
                @endif
            </div>

            @if ($totalAvailable > 0)
                <div class="enroll-toolbar">
                    <div class="enroll-filters" role="group" aria-label="Filter tipe kelas">
                        <button type="button" class="enroll-filter-btn is-active" data-enroll-filter="all" aria-pressed="true">Semua ({{ $totalAvailable }})</button>
                        <button type="button" class="enroll-filter-btn" data-enroll-filter="gratis" aria-pressed="false">Gratis ({{ $gratisCount }})</button>
                        <button type="button" class="enroll-filter-btn" data-enroll-filter="pro" aria-pressed="false">Pro ({{ $proCount }})</button>
                    </div>

                    <div style="flex: 1 1 260px; max-width: 380px;">
                        <input type="search" id="searchKelas" class="neo-input" placeholder="Cari nama kelas atau pengajar..." aria-label="Cari nama kelas atau pengajar" style="margin-bottom: 0; background-color: var(--card);">
                    </div>
                </div>

                <div id="enrollVisibleCount" style="font-size: 0.88rem; font-weight: 800; color: var(--muted-foreground); margin-bottom: 16px;" aria-live="polite">
                    Menampilkan {{ $totalAvailable }} dari {{ $totalAvailable }} kelas
                </div>

                <div class="neo-grid" id="kelasGrid">
                    @foreach ($kelasTersedia as $kelas)
                        @php
                            $isPremium = !empty($kelas['is_premium']);
                            $searchBlob = strtolower(($kelas['nama_kelas'] ?? '') . ' ' . ($kelas['nama_pengajar'] ?? '') . ' ' . ($kelas['deskripsi'] ?? ''));
                        @endphp
                        <article
                            class="neo-card kelas-item"
                            data-tier="{{ $isPremium ? 'pro' : 'gratis' }}"
                            data-search="{{ $searchBlob }}"
                            style="display: flex; flex-direction: column; justify-content: space-between;"
                        >
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;">
                                    <span class="badge-kode" style="margin: 0;">KODE: KLS-{{ $kelas['id_kelas'] }}</span>
                                    @if ($isPremium)
                                        <span class="neo-status-pill neo-status-pill--pro"><i class='bx bxs-crown' aria-hidden="true"></i> Premium (Pro)</span>
                                    @else
                                        <span class="neo-status-pill neo-status-pill--success">Gratis</span>
                                    @endif
                                </div>
                                <h2 class="nama-kelas" style="font-size: 1.3rem; text-transform: none;">{{ $kelas['nama_kelas'] }}</h2>
                                <div class="nama-pengajar" style="font-size: 0.9em; margin-bottom: 12px; color: var(--foreground); display: flex; align-items: center; gap: 6px;">
                                    <i class='bx bxs-user-voice' style="color: var(--accent);" aria-hidden="true"></i>
                                    <span>Pengajar: <strong>{{ $kelas['nama_pengajar'] ?? 'Belum ditentukan' }}</strong></span>
                                </div>
                                <p class="deskripsi-kelas" style="font-size: 0.94em; margin-bottom: 20px; color: var(--muted-foreground);">
                                    {{ $kelas['deskripsi'] }}
                                </p>
                            </div>

                            @if ($isPremium && empty($hasActivePro))
                                <a href="{{ route('langganan.index') }}" class="neo-btn neo-btn--pro" style="margin-top: auto; width: 100%; min-height: 46px; text-align: center; text-decoration: none;">
                                    <i class='bx bxs-lock-alt' aria-hidden="true"></i> Aktifkan Pro untuk Gabung
                                </a>
                            @else
                                <form method="POST" action="{{ route('enroll.store') }}" data-kelas-nama="{{ $kelas['nama_kelas'] }}" style="margin-top: auto;" onsubmit="event.preventDefault(); NeoConfirm('Gabung Kelas', 'Yakin ingin bergabung ke kelas ' + this.dataset.kelasNama + '?', () => this.submit());">
                                    @csrf
                                    <input type="hidden" name="id_kelas" value="{{ $kelas['id_kelas'] }}">
                                    <button type="submit" class="neo-btn" style="width: 100%; min-height: 46px; background-color: var(--accent);">Gabung Kelas</button>
                                </form>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div id="noResultMsg" class="neo-box" style="display: none; text-align: center; padding: 32px;">
                    <p style="font-weight: 800; margin-bottom: 14px;">Kelas yang Anda cari tidak ditemukan.</p>
                    <button type="button" id="resetEnrollFilter" class="neo-btn" style="background-color: var(--secondary); color: #000;">Tampilkan Semua Kelas</button>
                </div>
            @else
                <div class="neo-box" style="text-align: center; padding: 40px 24px;">
                    <p style="font-weight: 700; margin-bottom: 18px;">Tidak ada kelas baru yang tersedia saat ini, atau Anda sudah mengambil semua kelas yang ada.</p>
                    <a href="{{ route('dashboard') }}" class="neo-btn">Kembali ke Dashboard</a>
                </div>
            @endif

        </div>
    </main>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchKelas');
    const filterButtons = document.querySelectorAll('[data-enroll-filter]');
    const cards = document.querySelectorAll('.kelas-item');
    const noResultMsg = document.getElementById('noResultMsg');
    const countLabel = document.getElementById('enrollVisibleCount');
    const resetBtn = document.getElementById('resetEnrollFilter');
    let activeTier = 'all';

    const applyFilter = () => {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const tierOk = activeTier === 'all' || card.dataset.tier === activeTier;
            const queryOk = query === '' || (card.dataset.search || '').includes(query);
            const show = tierOk && queryOk;
            card.style.display = show ? 'flex' : 'none';
            if (show) visible++;
        });

        if (countLabel) countLabel.textContent = `Menampilkan ${visible} dari ${cards.length} kelas`;
        if (noResultMsg) noResultMsg.style.display = visible === 0 ? 'block' : 'none';
    };

    filterButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            activeTier = btn.dataset.enrollFilter || 'all';
            filterButtons.forEach((b) => {
                const active = b === btn;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            applyFilter();
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilter);

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            activeTier = 'all';
            if (searchInput) searchInput.value = '';
            filterButtons.forEach((b) => {
                const active = b.dataset.enrollFilter === 'all';
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            applyFilter();
        });
    }
});
</script>
@endsection
