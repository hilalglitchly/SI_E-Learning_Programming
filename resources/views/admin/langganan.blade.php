@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran - Admin E-Learning')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                <h2 style="margin: 0;"><i class='bx bx-credit-card'></i> Verifikasi Pembayaran Langganan</h2>
                <a href="{{ route('dashboard') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">← Kembali ke Dashboard</a>
            </div>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: {{ $tipe_pesan === 'error' ? 'var(--primary)' : '#a8e6cf' }}; color: {{ $tipe_pesan === 'error' ? '#fff' : '#000' }}; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif

            <!-- Ringkasan -->
            <div class="neo-grid" style="margin-top: 0; margin-bottom: 28px;">
                <div class="neo-box" style="margin: 0; background-color: #FEF08A; color: #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Menunggu Verifikasi</div>
                    <div style="font-size: 2rem; font-weight: 900;">{{ $pendingCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #BBF7D0; color: #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Langganan Pro Aktif</div>
                    <div style="font-size: 2rem; font-weight: 900;">{{ $activeCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #DDD6FE; color: #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Total Pendapatan Disetujui</div>
                    <div style="font-size: 1.7rem; font-weight: 900;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
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
                                <th style="width: 310px;">Aksi Verifikasi</th>
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
                                        <div style="font-weight: 900; color: var(--primary);">Rp {{ number_format($order->nominal, 0, ',', '.') }}</div>
                                    </td>
                                    <td>
                                        @if ($order->bukti_bayar)
                                            <a href="{{ route('files.bukti_bayar', ['id' => $order->id_langganan]) }}" target="_blank" rel="noopener" class="neo-btn" style="padding: 6px 10px; font-size: 0.78rem; background-color: var(--accent);">📥 Lihat Bukti</a>
                                        @else
                                            <span style="color: var(--muted-foreground);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'active')
                                            <span style="background: #16A34A; color: #fff; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">AKTIF</span>
                                            @if ($order->tgl_berakhir)
                                                <div style="font-size: 0.75rem; margin-top: 4px;">s/d {{ $order->tgl_berakhir->format('d M Y') }}</div>
                                            @endif
                                        @elseif ($order->status === 'rejected')
                                            <span style="background: var(--primary); color: #fff; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">DITOLAK</span>
                                        @else
                                            <span style="background: #FEF08A; color: #000; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">PENDING</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'pending')
                                            <form method="POST" action="{{ route('admin.langganan.approve', ['id' => $order->id_langganan]) }}" style="margin-bottom: 8px; display: flex; gap: 6px;">
                                                @csrf
                                                <input type="text" name="catatan_admin" class="neo-input" placeholder="Catatan (opsional)..." maxlength="255" style="margin: 0; padding: 6px 8px; font-size: 0.8rem;">
                                                <button type="submit" class="neo-btn" style="padding: 6px 10px; font-size: 0.78rem; background-color: #16A34A; color: #fff; white-space: nowrap;">✔ Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.langganan.reject', ['id' => $order->id_langganan]) }}" style="display: flex; gap: 6px;">
                                                @csrf
                                                <input type="text" name="catatan_admin" class="neo-input" placeholder="Alasan penolakan..." maxlength="255" required style="margin: 0; padding: 6px 8px; font-size: 0.8rem;">
                                                <button type="submit" class="neo-btn" style="padding: 6px 10px; font-size: 0.78rem; background-color: var(--primary); color: #fff; white-space: nowrap;">✖ Tolak</button>
                                            </form>
                                        @else
                                            <div style="font-size: 0.8rem; color: var(--muted-foreground);">
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
                            <a href="{{ $orders->previousPageUrl() }}" class="neo-btn" style="padding: 8px 14px; font-size: 0.85rem; background-color: var(--muted); color: var(--foreground);">← Sebelumnya</a>
                        @endif
                        <span style="font-weight: 800; font-size: 0.88rem;">Halaman {{ $orders->currentPage() }} dari {{ $orders->lastPage() }}</span>
                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="neo-btn" style="padding: 8px 14px; font-size: 0.85rem; background-color: var(--muted); color: var(--foreground);">Berikutnya →</a>
                        @else
                            <span></span>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </main>
@endsection
