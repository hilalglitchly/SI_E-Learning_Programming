@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran - Admin E-Learning')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                <h1 style="margin: 0; font-size: clamp(1.45rem, 3vw, 2rem); display: flex; align-items: center; gap: 10px; text-transform: uppercase;">
                    <i class='bx bx-credit-card' aria-hidden="true"></i> Verifikasi Pembayaran Langganan
                </h1>
                <a href="{{ route('dashboard') }}" class="neo-btn" style="min-height: 44px; background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">
                    <i class='bx bx-left-arrow-alt' aria-hidden="true"></i> Kembali ke Dashboard
                </a>
            </div>

            @if (!empty($pesan))
                <div class="auth-alert {{ $tipe_pesan === 'error' ? 'auth-alert--error' : 'auth-alert--success' }}" role="{{ $tipe_pesan === 'error' ? 'alert' : 'status' }}">
                    <i class='bx {{ $tipe_pesan === 'error' ? 'bxs-error-alt' : 'bxs-check-circle' }}' aria-hidden="true"></i>
                    <span>{{ $pesan }}</span>
                </div>
            @endif

            <!-- Ringkasan (3 Kolom Penuh — Bebas Ruang Kosong Kanan) -->
            <div class="neo-grid-3" style="margin-bottom: 28px;">
                <div class="neo-box" style="margin: 0; background-color: #FEF08A; color: #000; border-color: #000; box-shadow: 6px 6px 0px #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Menunggu Verifikasi</div>
                    <div style="font-size: 2.1rem; font-weight: 900; line-height: 1.15;">{{ $pendingCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #BBF7D0; color: #000; border-color: #000; box-shadow: 6px 6px 0px #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Langganan Pro Aktif</div>
                    <div style="font-size: 2.1rem; font-weight: 900; line-height: 1.15;">{{ $activeCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #DDD6FE; color: #000; border-color: #000; box-shadow: 6px 6px 0px #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Total Pendapatan Disetujui</div>
                    <div style="font-size: 1.85rem; font-weight: 900; line-height: 1.15;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Tabel Transaksi -->
            <div class="neo-box">
                <div style="overflow-x: auto;">
                    <table class="neo-table">
                        <thead>
                            <tr>
                                <th>Invoice &amp; Tanggal</th>
                                <th>Murid</th>
                                <th>Paket &amp; Nominal</th>
                                <th>Bukti Transfer</th>
                                <th>Status</th>
                                <th style="width: 320px;">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>
                                        <div style="font-weight: 900;">{{ $order->kode_invoice }}</div>
                                        <small style="color: var(--muted-foreground);">{{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800;">{{ $order->user->murid->nama_murid ?? $order->user->username ?? '-' }}</div>
                                        <small style="color: var(--muted-foreground);">{{ $order->user->username ?? '-' }} {{ $order->user?->murid ? '• ' . $order->user->murid->nim : '' }}</small>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800;">{{ $order->paket->nama_paket ?? '-' }}</div>
                                        <div style="font-weight: 900;">Rp {{ number_format($order->nominal, 0, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @if ($order->bukti_bayar)
                                            <a href="{{ route('files.bukti_bayar', ['id' => $order->id_langganan]) }}" target="_blank" rel="noopener" class="neo-btn" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem; background-color: var(--accent);">
                                                <i class='bx bx-download' aria-hidden="true"></i> Lihat Bukti
                                            </a>
                                        @else
                                            <span style="color: var(--muted-foreground);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'active')
                                            <span class="neo-status-pill neo-status-pill--success">Aktif</span>
                                            @if ($order->tgl_berakhir)
                                                <div style="font-size: 0.78rem; margin-top: 4px;">s/d {{ $order->tgl_berakhir->format('d M Y') }}</div>
                                            @endif
                                        @elseif ($order->status === 'rejected')
                                            <span class="neo-status-pill neo-status-pill--danger">Ditolak</span>
                                        @else
                                            <span class="neo-status-pill neo-status-pill--pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'pending')
                                            <form method="POST" action="{{ route('admin.langganan.approve', ['id' => $order->id_langganan]) }}" style="margin-bottom: 8px; display: flex; gap: 6px;">
                                                @csrf
                                                <input type="text" name="catatan_admin" class="neo-input" placeholder="Catatan persetujuan (opsional)..." maxlength="255" aria-label="Catatan persetujuan untuk {{ $order->kode_invoice }}" style="margin: 0; padding: 6px 8px; font-size: 0.82rem;">
                                                <button type="submit" class="neo-btn neo-btn--success" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem; white-space: nowrap;">
                                                    <i class='bx bx-check' aria-hidden="true"></i> Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.langganan.reject', ['id' => $order->id_langganan]) }}" style="display: flex; gap: 6px;">
                                                @csrf
                                                <input type="text" name="catatan_admin" class="neo-input" placeholder="Alasan penolakan..." maxlength="255" required aria-label="Alasan penolakan untuk {{ $order->kode_invoice }}" style="margin: 0; padding: 6px 8px; font-size: 0.82rem;">
                                                <button type="submit" class="neo-btn neo-btn--danger" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem; white-space: nowrap;">
                                                    <i class='bx bx-x' aria-hidden="true"></i> Tolak
                                                </button>
                                            </form>
                                        @else
                                            <div style="font-size: 0.82rem; color: var(--muted-foreground);">
                                                {{ $order->catatan_admin ?: 'Sudah diverifikasi' }}
                                                @if ($order->verifier)
                                                    <br><small>Oleh: {{ $order->verifier->username }}</small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align: center;">Belum ada transaksi langganan masuk.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($orders->hasPages())
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 16px; flex-wrap: wrap;">
                        @if ($orders->onFirstPage())
                            <span></span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="neo-btn" style="min-height: 44px; padding: 8px 14px; font-size: 0.85rem; background-color: var(--muted); color: var(--foreground);">Sebelumnya</a>
                        @endif
                        <span style="font-weight: 800; font-size: 0.88rem;">Halaman {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}</span>
                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="neo-btn" style="min-height: 44px; padding: 8px 14px; font-size: 0.85rem; background-color: var(--muted); color: var(--foreground);">Berikutnya</a>
                        @else
                            <span></span>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </main>
@endsection
