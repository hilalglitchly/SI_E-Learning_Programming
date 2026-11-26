@extends('layouts.app')

@section('title', 'Riwayat Pembayaran Midtrans - Admin E-Learning')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h2 style="margin: 0;"><i class='bx bx-credit-card'></i> Riwayat Pembayaran Midtrans</h2>
                    <span style="background: {{ $midtransIsProd ? '#16A34A' : '#000' }}; color: {{ $midtransIsProd ? '#fff' : '#FFD700' }}; padding: 4px 10px; font-size: 0.75rem; font-weight: 900; border: 2px solid #000;">
                        {{ $midtransIsProd ? 'PRODUCTION' : 'SANDBOX' }}
                    </span>
                </div>
                <a href="{{ route('dashboard') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">← Kembali ke Dashboard</a>
            </div>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: {{ $tipe_pesan === 'error' ? 'var(--primary)' : '#a8e6cf' }}; color: {{ $tipe_pesan === 'error' ? '#fff' : '#000' }}; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif

            @if (!$midtransConfigured)
                <div class="neo-box" style="background-color: #FEF08A; color: #000; border-left: 10px solid #DC2626; font-weight: 700;">
                    ⚠️ <code>MIDTRANS_SERVER_KEY</code> / <code>MIDTRANS_CLIENT_KEY</code> belum diisi di <code>.env</code>. Webhook Midtrans endpoint: <code>{{ url('/api/midtrans/webhook') }}</code>
                </div>
            @endif

            <!-- Ringkasan -->
            <div class="neo-grid" style="margin-top: 0; margin-bottom: 28px;">
                <div class="neo-box" style="margin: 0; background-color: #FEF08A; color: #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Menunggu Pembayaran</div>
                    <div style="font-size: 2rem; font-weight: 900;">{{ $pendingCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #BBF7D0; color: #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Langganan Pro Aktif</div>
                    <div style="font-size: 2rem; font-weight: 900;">{{ $activeCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #DDD6FE; color: #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Total Pendapatan Lunas</div>
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
                                <th>Metode Midtrans</th>
                                <th>Status</th>
                                <th style="width: 220px;">Sinkronisasi / Info</th>
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
                                    <td style="font-size: 0.82rem;">
                                        @if ($order->payment_type)
                                            <span style="display: inline-block; background: var(--muted); border: 2px solid #000; padding: 2px 6px; font-weight: 800; text-transform: uppercase;">
                                                {{ str_replace('_', ' ', $order->payment_type) }}
                                            </span>
                                        @else
                                            <span style="color: var(--muted-foreground);">—</span>
                                        @endif
                                        @if ($order->gateway_transaction_id)
                                            <div style="font-size: 0.72rem; color: var(--muted-foreground); margin-top: 4px;">ID: {{ $order->gateway_transaction_id }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'active')
                                            <span style="background: #16A34A; color: #fff; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">
                                                {{ $order->isCurrentlyActive() ? 'AKTIF' : 'BERAKHIR' }}
                                            </span>
                                            @if ($order->tgl_berakhir)
                                                <div style="font-size: 0.75rem; margin-top: 4px;">s/d {{ $order->tgl_berakhir->format('d M Y') }}</div>
                                            @endif
                                        @elseif ($order->status === 'pending')
                                            <span style="background: #FEF08A; color: #000; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">PENDING</span>
                                        @else
                                            <span style="background: var(--primary); color: #fff; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">{{ strtoupper($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'pending' && $midtransConfigured)
                                            <form method="POST" action="{{ route('admin.langganan.sync', ['id' => $order->id_langganan]) }}" style="margin: 0;">
                                                @csrf
                                                <button type="submit" class="neo-btn" style="padding: 6px 12px; font-size: 0.78rem; background-color: var(--accent); color: var(--accent-foreground); white-space: nowrap;">
                                                    🔄 Sinkronkan Status
                                                </button>
                                            </form>
                                        @else
                                            <div style="font-size: 0.8rem; color: var(--muted-foreground);">
                                                {{ $order->catatan_admin ?: ($order->gateway_status ? 'Midtrans: ' . $order->gateway_status : '—') }}
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
            </div>

        </div>
    </main>
@endsection
