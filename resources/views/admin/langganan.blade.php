@extends('layouts.app')

@section('title', 'Riwayat Pembayaran Midtrans - Admin E-FORVM')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h1 style="margin: 0; font-size: clamp(1.45rem, 3vw, 2rem); display: flex; align-items: center; gap: 10px; text-transform: uppercase;">
                        <i class='bx bx-credit-card' aria-hidden="true"></i> Pembayaran Midtrans
                    </h1>
                    <span style="background: {{ $midtransIsProd ? '#047857' : '#000' }}; color: {{ $midtransIsProd ? '#fff' : '#FFD700' }}; padding: 4px 10px; font-size: 0.75rem; font-weight: 900; border: 2px solid #000;">
                        {{ $midtransIsProd ? 'PRODUCTION' : 'SANDBOX' }}
                    </span>
                </div>
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

            <!-- Panel Verifikasi Webhook Midtrans -->
            @php
                $webhookUrl = url('/api/midtrans/webhook');
            @endphp
            <div class="neo-box" style="margin-bottom: 22px; background-color: {{ $midtransConfigured ? 'var(--card)' : '#FEF08A' }}; color: {{ $midtransConfigured ? 'var(--foreground)' : '#000' }}; border-left: 10px solid {{ $midtransConfigured ? '#047857' : '#DC2626' }};">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <strong style="font-size: 0.92rem; text-transform: uppercase;">Webhook Midtrans (Payment Notification URL)</strong>
                        <span style="padding: 2px 8px; font-size: 0.72rem; font-weight: 900; border: 2px solid #000; background-color: {{ $midtransConfigured ? '#BBF7D0' : '#FCA5A5' }}; color: #000;">
                            {{ $midtransConfigured ? 'KEY TERHUBUNG' : 'KEY BELUM DIATUR' }}
                        </span>
                    </div>
                    <button type="button" id="copy-webhook-url-btn" class="neo-btn" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem; background-color: var(--secondary); color: #000;" onclick="copyMidtransWebhookUrl()">
                        <i class='bx bx-copy' aria-hidden="true"></i> <span id="copy-webhook-url-label">Salin URL</span>
                    </button>
                </div>
                <input type="text" id="midtrans-webhook-url-input" class="neo-input" value="{{ $webhookUrl }}" readonly aria-label="Payment Notification URL Midtrans" style="margin: 0 0 8px 0; font-family: monospace; font-size: 0.86rem; font-weight: 700; background-color: var(--background); color: var(--foreground);">
                <div style="font-size: 0.8rem; font-weight: 600; color: {{ $midtransConfigured ? 'var(--muted-foreground)' : '#000' }};">
                    @if (!$midtransConfigured)
                        <code>MIDTRANS_SERVER_KEY</code> / <code>MIDTRANS_CLIENT_KEY</code> belum diisi di Environment Variables.
                    @endif
                    Pastikan URL di atas terpasang di <strong>Midtrans Dashboard &rarr; Settings &rarr; Configuration &rarr; Payment Notification URL</strong> agar status Pro aktif otomatis setelah dibayar.
                </div>
            </div>
            <script>
                function copyMidtransWebhookUrl() {
                    const input = document.getElementById('midtrans-webhook-url-input');
                    const label = document.getElementById('copy-webhook-url-label');
                    if (!input) return;
                    const done = () => {
                        if (!label) return;
                        label.textContent = 'Tersalin!';
                        setTimeout(() => { label.textContent = 'Salin URL'; }, 1800);
                    };
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(input.value).then(done).catch(() => {
                            input.select();
                            document.execCommand('copy');
                            done();
                        });
                    } else {
                        input.select();
                        document.execCommand('copy');
                        done();
                    }
                }
            </script>

            <!-- Ringkasan (3 Kolom Penuh) -->
            <div class="neo-grid-3" style="margin-bottom: 28px;">
                <div class="neo-box" style="margin: 0; background-color: #FEF08A; color: #000; border-color: #000; box-shadow: 6px 6px 0px #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Menunggu Pembayaran</div>
                    <div style="font-size: 2.1rem; font-weight: 900; line-height: 1.15;">{{ $pendingCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #BBF7D0; color: #000; border-color: #000; box-shadow: 6px 6px 0px #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Langganan Pro Aktif</div>
                    <div style="font-size: 2.1rem; font-weight: 900; line-height: 1.15;">{{ $activeCount }}</div>
                </div>
                <div class="neo-box" style="margin: 0; background-color: #DDD6FE; color: #000; border-color: #000; box-shadow: 6px 6px 0px #000;">
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">Total Pendapatan Lunas</div>
                    <div style="font-size: 1.85rem; font-weight: 900; line-height: 1.15;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </div>

            <!-- Filter Status & Pencarian Invoice -->
            <div class="enroll-toolbar" style="margin-bottom: 18px;">
                <div class="enroll-filters" role="navigation" aria-label="Filter status transaksi">
                    @foreach (['all' => 'Semua', 'pending' => 'Pending', 'active' => 'Aktif', 'cancelled' => 'Dibatalkan', 'expired' => 'Kedaluwarsa', 'failed' => 'Gagal'] as $key => $label)
                        <a
                            href="{{ route('admin.langganan.index', array_filter(['status' => $key === 'all' ? null : $key, 'q' => $searchQuery ?: null])) }}"
                            class="enroll-filter-btn {{ $statusFilter === $key ? 'is-active' : '' }}"
                            style="display: inline-flex; align-items: center; text-decoration: none;"
                            @if ($statusFilter === $key) aria-current="page" @endif
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('admin.langganan.index') }}" style="display: flex; gap: 8px; flex: 1 1 280px; max-width: 420px;">
                    @if ($statusFilter !== 'all')
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    <input type="search" name="q" value="{{ $searchQuery }}" class="neo-input" placeholder="Cari invoice, username, atau nama..." aria-label="Cari transaksi langganan" style="margin: 0; background-color: var(--card);">
                    <button type="submit" class="neo-btn" style="min-height: 44px; padding: 8px 16px;">Cari</button>
                    @if ($searchQuery !== '' || $statusFilter !== 'all')
                        <a href="{{ route('admin.langganan.index') }}" class="neo-btn" style="min-height: 44px; padding: 8px 12px; background-color: var(--muted); color: var(--foreground);">Reset</a>
                    @endif
                </form>
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
                                <th style="width: 240px;">Sinkronisasi / Info</th>
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
                                    <td style="font-size: 0.82rem;">
                                        @if ($order->payment_type)
                                            <span style="display: inline-block; background: var(--muted); border: 2px solid var(--border); padding: 2px 6px; font-weight: 800; text-transform: uppercase;">
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
                                            <span class="neo-status-pill {{ $order->isCurrentlyActive() ? 'neo-status-pill--success' : 'neo-status-pill--neutral' }}">
                                                {{ $order->isCurrentlyActive() ? 'Aktif' : 'Berakhir' }}
                                            </span>
                                            @if ($order->tgl_berakhir)
                                                <div style="font-size: 0.78rem; margin-top: 4px;">s/d {{ $order->tgl_berakhir->format('d M Y') }}</div>
                                            @endif
                                        @elseif ($order->status === 'pending')
                                            <span class="neo-status-pill neo-status-pill--pending">Pending</span>
                                        @else
                                            <span class="neo-status-pill neo-status-pill--danger">{{ strtoupper($order->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->status === 'pending' && $midtransConfigured)
                                            <form method="POST" action="{{ route('admin.langganan.sync', ['id' => $order->id_langganan]) }}" style="margin: 0;">
                                                @csrf
                                                <button type="submit" class="neo-btn" style="min-height: 40px; padding: 6px 12px; font-size: 0.78rem; background-color: var(--accent); color: var(--accent-foreground); white-space: nowrap;">
                                                    <i class='bx bx-refresh' aria-hidden="true"></i> Sinkronkan Status
                                                </button>
                                            </form>
                                        @else
                                            <div style="font-size: 0.82rem; color: var(--muted-foreground);">
                                                {{ $order->catatan_admin ?: ($order->gateway_status ? 'Midtrans: ' . $order->gateway_status : '—') }}
                                                @if ($order->status === 'active')
                                                    <div style="margin-top: 6px;">
                                                        <a href="{{ route('langganan.invoice', ['id' => $order->id_langganan]) }}" class="neo-btn" style="min-height: 36px; padding: 4px 10px; font-size: 0.75rem; background-color: var(--secondary); color: #000;">
                                                            <i class='bx bxs-printer' aria-hidden="true"></i> Struk
                                                        </a>
                                                    </div>
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

            <!-- Kelola Paket & Harga Langganan -->
            <details class="neo-box" style="margin-top: 24px;">
                <summary style="font-weight: 900; font-size: 1.15rem; text-transform: uppercase; cursor: pointer; display: flex; align-items: center; justify-content: space-between; min-height: 44px;">
                    <span style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class='bx bxs-crown' style="color: #D97706;" aria-hidden="true"></i> Kelola Paket &amp; Harga Langganan ({{ $paketList->count() }})
                    </span>
                    <span style="font-size: 0.8rem; color: var(--muted-foreground);">Klik untuk ubah harga / durasi</span>
                </summary>

                <div class="neo-grid-2" style="margin-top: 18px; padding-top: 18px; border-top: 3px solid var(--border);">
                    @foreach ($paketList as $paket)
                        <form method="POST" action="{{ route('admin.langganan.paket.update', ['id' => $paket->id_paket]) }}" style="border: 3px solid var(--border); padding: 18px; background-color: var(--muted);">
                            @csrf
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span class="badge-kode" style="margin: 0;">PKT-{{ $paket->id_paket }}</span>
                                <label style="display: inline-flex; align-items: center; gap: 6px; font-weight: 800; font-size: 0.82rem; cursor: pointer;">
                                    <input type="checkbox" name="is_active" value="1" {{ $paket->is_active ? 'checked' : '' }}>
                                    Aktif
                                </label>
                            </div>
                            <div class="neo-form-group" style="margin-bottom: 12px;">
                                <label class="neo-label" for="paket-nama-{{ $paket->id_paket }}">Nama Paket</label>
                                <input type="text" id="paket-nama-{{ $paket->id_paket }}" name="nama_paket" class="neo-input" value="{{ $paket->nama_paket }}" maxlength="80" required>
                            </div>
                            <div class="neo-grid-2" style="gap: 12px; margin-bottom: 12px;">
                                <div>
                                    <label class="neo-label" for="paket-harga-{{ $paket->id_paket }}">Harga (Rp)</label>
                                    <input type="number" id="paket-harga-{{ $paket->id_paket }}" name="harga" class="neo-input" value="{{ $paket->harga }}" min="1000" max="50000000" required>
                                </div>
                                <div>
                                    <label class="neo-label" for="paket-durasi-{{ $paket->id_paket }}">Durasi (Hari)</label>
                                    <input type="number" id="paket-durasi-{{ $paket->id_paket }}" name="durasi_hari" class="neo-input" value="{{ $paket->durasi_hari }}" min="1" max="3650" required>
                                </div>
                            </div>
                            <div class="neo-form-group" style="margin-bottom: 14px;">
                                <label class="neo-label" for="paket-desc-{{ $paket->id_paket }}">Deskripsi</label>
                                <input type="text" id="paket-desc-{{ $paket->id_paket }}" name="deskripsi" class="neo-input" value="{{ $paket->deskripsi }}" maxlength="255">
                            </div>
                            <button type="submit" class="neo-btn" style="width: 100%; min-height: 44px; background-color: var(--secondary); color: #000;">Simpan Perubahan Paket</button>
                        </form>
                    @endforeach
                </div>
            </details>

        </div>
    </main>
@endsection
