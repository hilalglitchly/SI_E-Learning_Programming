@extends('layouts.app')

@section('title', 'Langganan Pro - E-Learning Programming')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1120px; margin: 0 auto; padding-top: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                <h1 style="margin: 0; font-size: clamp(1.5rem, 3vw, 2rem); display: flex; align-items: center; gap: 10px; text-transform: uppercase;">
                    <i class='bx bxs-crown' style="color: #D97706;" aria-hidden="true"></i> Langganan Pro
                </h1>
                <a href="{{ route('enroll.index') }}" class="neo-btn" style="min-height: 44px; background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">
                    <i class='bx bx-left-arrow-alt' aria-hidden="true"></i> Kembali ke Cari Kelas
                </a>
            </div>

            @if (!empty($pesan))
                <div class="auth-alert {{ $tipe_pesan === 'error' ? 'auth-alert--error' : 'auth-alert--success' }}" role="{{ $tipe_pesan === 'error' ? 'alert' : 'status' }}">
                    <i class='bx {{ $tipe_pesan === 'error' ? 'bxs-error-alt' : 'bxs-check-circle' }}' aria-hidden="true"></i>
                    <span>{{ $pesan }}</span>
                </div>
            @endif

            <!-- Status Langganan Saat Ini -->
            <div class="neo-box" style="border-left: 10px solid {{ $activeSub ? '#047857' : '#D97706' }}; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--muted-foreground);">Status Akun Anda</div>
                    @if ($activeSub)
                        <h2 style="margin: 6px 0; font-size: 1.45rem; display: flex; align-items: center; gap: 8px;">
                            <i class='bx bxs-crown' style="color: #D97706;" aria-hidden="true"></i> Pro Aktif
                        </h2>
                        <p style="margin: 0; font-weight: 600;">
                            Berlaku sampai: <strong>{{ $activeSub->tgl_berakhir->format('d M Y, H:i') }} WIB</strong>
                            (sisa {{ max(1, (int) ceil(now()->diffInHours($activeSub->tgl_berakhir, false) / 24)) }} hari)
                        </p>
                    @else
                        <h2 style="margin: 6px 0; font-size: 1.45rem;">Akun Gratis</h2>
                        <p style="margin: 0; color: var(--muted-foreground); font-weight: 600;">
                            Anda dapat mengakses seluruh kelas Gratis. Aktifkan paket Pro untuk membuka seluruh kelas Premium.
                        </p>
                    @endif
                </div>
                <span class="neo-status-pill {{ $activeSub ? 'neo-status-pill--success' : 'neo-status-pill--pending' }}" style="padding: 8px 16px; font-size: 0.9rem;">
                    {{ $activeSub ? 'PRO MEMBER' : 'FREE TIER' }}
                </span>
            </div>

            <!-- Pilihan Paket (2 Kolom Penuh — Bebas Ruang Kosong Kanan) -->
            <div class="neo-grid-2" style="margin-bottom: 28px;">
                @foreach ($paketList as $idx => $paket)
                    <div class="neo-card plan-option-card {{ $idx === 0 ? 'is-selected' : '' }}" data-plan-card="{{ $paket->id_paket }}">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px;">
                                <span class="badge-kode" style="margin: 0; background-color: var(--secondary); color: #000;">{{ $paket->durasi_hari }} HARI</span>
                                @if ($paket->durasi_hari >= 365)
                                    <span class="neo-status-pill neo-status-pill--success">Hemat 2 Bulan</span>
                                @endif
                            </div>
                            <h3 style="font-size: 1.5rem; margin-bottom: 8px;">{{ $paket->nama_paket }}</h3>
                            <div style="font-size: 2rem; font-weight: 900; color: var(--foreground); margin-bottom: 12px;">
                                Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </div>
                            <p style="color: var(--muted-foreground); font-weight: 500; margin-bottom: 18px;">{{ $paket->deskripsi }}</p>
                        </div>
                        <button
                            type="button"
                            class="neo-btn"
                            data-pick-plan="{{ $paket->id_paket }}"
                            style="width: 100%; min-height: 46px; background-color: var(--secondary); color: #000;"
                        >
                            Pilih {{ $paket->nama_paket }}
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Instruksi Transfer & Form Upload Bukti Bayar -->
            <div id="checkout-box" class="neo-box" style="border-left: 10px solid var(--accent);">
                <h2 style="font-size: 1.3rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class='bx bx-credit-card' aria-hidden="true"></i> Transfer Manual &amp; Konfirmasi Pembayaran
                </h2>

                <div style="background-color: var(--muted); border: 3px solid var(--border); padding: 16px; margin-bottom: 20px;">
                    <div style="font-weight: 900; text-transform: uppercase; font-size: 0.82rem; margin-bottom: 6px; color: var(--muted-foreground);">Rekening Tujuan Transfer</div>
                    <div style="font-size: 1.15rem; font-weight: 900;">
                        Bank {{ $payment['bank_name'] ?? 'BCA' }} &mdash; {{ $payment['account_number'] ?? '-' }}
                    </div>
                    <div style="font-weight: 700; color: var(--muted-foreground);">a.n. {{ $payment['account_holder'] ?? 'E-Learning Programming' }}</div>
                </div>

                @if ($hasPending)
                    <div style="padding: 14px 16px; border: 3px solid #000; background-color: #FEF08A; color: #000; font-weight: 800; display: flex; align-items: center; gap: 10px;" role="status">
                        <i class='bx bx-time-five' style="font-size: 1.4rem; flex-shrink: 0;" aria-hidden="true"></i>
                        <span>Anda memiliki transaksi yang sedang menunggu verifikasi Admin. Mohon tunggu hingga proses selesai sebelum mengirim pengajuan baru.</span>
                    </div>
                @else
                    <form method="POST" action="{{ route('langganan.checkout') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="neo-form-group">
                            <label class="neo-label" for="select-paket">Pilih Paket Langganan</label>
                            <select id="select-paket" name="id_paket" class="neo-input" required>
                                @foreach ($paketList as $paket)
                                    <option value="{{ $paket->id_paket }}">
                                        {{ $paket->nama_paket }} — Rp {{ number_format($paket->harga, 0, ',', '.') }} ({{ $paket->durasi_hari }} hari)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="neo-form-group">
                            <label class="neo-label" for="bukti_bayar">Unggah Bukti Transfer</label>
                            <span id="bukti-hint" class="auth-hint">Format yang diterima: JPG, PNG, atau PDF (maksimal 5 MB).</span>
                            <input type="file" id="bukti_bayar" name="bukti_bayar" class="neo-input" accept=".jpg,.jpeg,.png,.pdf" aria-describedby="bukti-hint" required>
                            <div id="bukti-file-info" style="margin-top: 6px; font-size: 0.85rem; font-weight: 700; color: var(--muted-foreground);" aria-live="polite"></div>
                        </div>

                        <button type="submit" class="neo-btn" style="width: 100%; min-height: 50px; font-size: 1rem; padding: 14px; background-color: var(--accent); color: var(--accent-foreground);">
                            <i class='bx bx-upload' aria-hidden="true"></i> Kirim Bukti Pembayaran untuk Diverifikasi
                        </button>
                    </form>
                @endif
            </div>

            <!-- Riwayat Transaksi -->
            <div class="neo-box">
                <h2 style="font-size: 1.3rem; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class='bx bx-receipt' aria-hidden="true"></i> Riwayat Pembayaran Anda
                </h2>
                <div style="overflow-x: auto;">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Paket</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Masa Aktif</th>
                                <th>Bukti &amp; Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayat as $row)
                                <tr>
                                    <td style="font-weight: 900;">{{ $row->kode_invoice }}</td>
                                    <td>{{ $row->paket->nama_paket ?? '-' }}</td>
                                    <td style="font-weight: 800;">Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($row->status === 'active')
                                            <span class="neo-status-pill {{ $row->isCurrentlyActive() ? 'neo-status-pill--success' : 'neo-status-pill--neutral' }}">
                                                {{ $row->isCurrentlyActive() ? 'Aktif' : 'Berakhir' }}
                                            </span>
                                        @elseif ($row->status === 'rejected')
                                            <span class="neo-status-pill neo-status-pill--danger">Ditolak</span>
                                        @else
                                            <span class="neo-status-pill neo-status-pill--pending">Pending</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        @if ($row->tgl_mulai && $row->tgl_berakhir)
                                            {{ $row->tgl_mulai->format('d M Y') }} – {{ $row->tgl_berakhir->format('d M Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        @if ($row->bukti_bayar)
                                            <a href="{{ route('files.bukti_bayar', ['id' => $row->id_langganan]) }}" target="_blank" rel="noopener" class="auth-link auth-link--neutral" style="min-height: 36px;">
                                                <i class='bx bx-download' aria-hidden="true"></i> Unduh Bukti
                                            </a>
                                        @endif
                                        @if ($row->catatan_admin)
                                            <div style="color: var(--muted-foreground); margin-top: 4px;">{{ $row->catatan_admin }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align: center;">Belum ada riwayat transaksi langganan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectPaket = document.getElementById('select-paket');
    const planCards = document.querySelectorAll('[data-plan-card]');
    const syncSelectedCard = (id) => {
        planCards.forEach((c) => c.classList.toggle('is-selected', c.dataset.planCard === String(id)));
    };

    document.querySelectorAll('[data-pick-plan]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.pickPlan;
            syncSelectedCard(id);
            if (selectPaket) selectPaket.value = id;
            const box = document.getElementById('checkout-box');
            if (box) box.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: no-preference)').matches ? 'smooth' : 'auto' });
        });
    });

    if (selectPaket) {
        selectPaket.addEventListener('change', () => syncSelectedCard(selectPaket.value));
    }

    const fileInput = document.getElementById('bukti_bayar');
    const fileInfo = document.getElementById('bukti-file-info');
    if (fileInput && fileInfo) {
        fileInput.addEventListener('change', () => {
            const f = fileInput.files && fileInput.files[0];
            if (!f) { fileInfo.textContent = ''; return; }
            const kb = Math.max(1, Math.round(f.size / 1024));
            fileInfo.textContent = `Berkas dipilih: ${f.name} (${kb >= 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb + ' KB'})`;
        });
    }
});
</script>
@endsection
