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

            @if (!$midtransConfigured)
                <div class="neo-box" role="alert" style="background-color: #FEF08A; color: #000; border-left: 10px solid #DC2626; font-weight: 700;">
                    Payment gateway Midtrans belum dikonfigurasi. Isi <code>MIDTRANS_SERVER_KEY</code> dan <code>MIDTRANS_CLIENT_KEY</code> di file <code>.env</code> untuk mengaktifkan pembayaran otomatis.
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
                            Anda dapat mengakses seluruh kelas Gratis. Aktifkan paket Pro melalui Midtrans (QRIS, Virtual Account, E-Wallet, Kartu) untuk membuka seluruh kelas Premium secara instan.
                        </p>
                    @endif
                </div>
                <span class="neo-status-pill {{ $activeSub ? 'neo-status-pill--success' : 'neo-status-pill--pending' }}" style="padding: 8px 16px; font-size: 0.9rem;">
                    {{ $activeSub ? 'PRO MEMBER' : 'FREE TIER' }}
                </span>
            </div>

            <!-- Transaksi Pending (Lanjutkan Bayar / Cek Status / Batalkan) -->
            @if ($pendingOrder)
                <div class="neo-box" style="border-left: 10px solid #D97706; background-color: #FEF9C3; color: #000;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 14px;">
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 900; text-transform: uppercase;">Menunggu Pembayaran</div>
                            <h2 style="margin: 4px 0; font-size: 1.35rem; color: #000;">
                                {{ $pendingOrder->paket->nama_paket ?? 'Paket Pro' }} — Rp {{ number_format($pendingOrder->nominal, 0, ',', '.') }}
                            </h2>
                            <div style="font-weight: 700; font-size: 0.9rem;">Invoice: <code>{{ $pendingOrder->kode_invoice }}</code></div>
                        </div>
                        @if (!$midtransIsProd)
                            <span style="background: #000; color: #FFD700; padding: 4px 10px; font-size: 0.75rem; font-weight: 900;">SANDBOX MODE</span>
                        @endif
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                        @if ($midtransConfigured && $pendingOrder->snap_token)
                            <button type="button" id="btn-snap-pay" class="neo-btn" style="min-height: 44px; background-color: #047857; color: #fff; padding: 10px 18px;">
                                <i class='bx bx-credit-card' aria-hidden="true"></i> Bayar Sekarang (Midtrans)
                            </button>
                        @endif

                        @if ($pendingOrder->payment_url)
                            <a href="{{ $pendingOrder->payment_url }}" target="_blank" rel="noopener noreferrer" class="neo-btn" style="min-height: 44px; background-color: var(--accent); color: var(--accent-foreground); padding: 10px 16px;">
                                <i class='bx bx-link-external' aria-hidden="true"></i> Buka Halaman Bayar
                            </a>
                        @endif

                        <form id="sync-pending-form" method="POST" action="{{ route('langganan.sync', ['id' => $pendingOrder->id_langganan]) }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="neo-btn" style="min-height: 44px; background-color: #fff; color: #000; padding: 10px 16px;">
                                <i class='bx bx-refresh' aria-hidden="true"></i> Cek Status Pembayaran
                            </button>
                        </form>

                        <form method="POST" action="{{ route('langganan.cancel', ['id' => $pendingOrder->id_langganan]) }}" class="js-confirm-form" data-confirm-title="Batalkan Transaksi" data-confirm-message="Batalkan transaksi {{ $pendingOrder->kode_invoice }}? Anda bisa memilih paket ulang setelah dibatalkan." style="margin: 0;">
                            @csrf
                            <button type="submit" class="neo-btn neo-btn--danger" style="min-height: 44px; padding: 10px 16px;">
                                <i class='bx bx-x' aria-hidden="true"></i> Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Pilihan Paket -->
            <div class="neo-grid-2" style="margin-bottom: 28px;">
                @foreach ($paketList as $idx => $paket)
                    <div class="neo-card plan-option-card {{ $idx === 0 ? 'is-selected' : '' }}">
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

                        <form method="POST" action="{{ route('langganan.checkout') }}" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="id_paket" value="{{ $paket->id_paket }}">
                            <button
                                type="submit"
                                class="neo-btn"
                                style="width: 100%; min-height: 46px; background-color: {{ ($pendingOrder || !$midtransConfigured) ? 'var(--muted)' : 'var(--secondary)' }}; color: #000;"
                                {{ ($pendingOrder || !$midtransConfigured) ? 'disabled' : '' }}
                            >
                                <i class='bx bx-credit-card' aria-hidden="true"></i> Bayar {{ $paket->nama_paket }} via Midtrans
                            </button>
                        </form>
                    </div>
                @endforeach
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
                                <th>Metode &amp; Keterangan</th>
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
                                        @elseif ($row->status === 'pending')
                                            <span class="neo-status-pill neo-status-pill--pending">Pending</span>
                                        @else
                                            <span class="neo-status-pill neo-status-pill--danger">{{ strtoupper($row->status) }}</span>
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
                                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                                            @if ($row->payment_type)
                                                <span style="display: inline-block; background: var(--muted); border: 2px solid var(--border); padding: 2px 6px; font-weight: 800; font-size: 0.72rem; text-transform: uppercase;">
                                                    {{ str_replace('_', ' ', $row->payment_type) }}
                                                </span>
                                            @endif
                                            @if ($row->status === 'active')
                                                <a href="{{ route('langganan.invoice', ['id' => $row->id_langganan]) }}" class="neo-btn" style="min-height: 36px; padding: 4px 10px; font-size: 0.76rem; background-color: var(--secondary); color: #000;">
                                                    <i class='bx bxs-printer' aria-hidden="true"></i> Struk
                                                </a>
                                            @endif
                                        </div>
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

@if ($midtransConfigured && $pendingOrder && $pendingOrder->snap_token)
    @push('scripts')
        <script src="{{ $snapJsUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
        <script>
            (function () {
                const token = @json($pendingOrder->snap_token);
                const paymentUrl = @json($pendingOrder->payment_url);
                const autoPay = @json($autoPayInvoice === $pendingOrder->kode_invoice);
                const syncForm = document.getElementById('sync-pending-form');
                const payBtn = document.getElementById('btn-snap-pay');

                function openSnap() {
                    if (typeof window.snap === 'undefined') {
                        if (paymentUrl) {
                            window.open(paymentUrl, '_blank', 'noopener');
                        }
                        return;
                    }
                    window.snap.pay(token, {
                        onSuccess: function () { if (syncForm) syncForm.submit(); },
                        onPending: function () { if (syncForm) syncForm.submit(); },
                        onError: function () { if (syncForm) syncForm.submit(); }
                    });
                }

                if (payBtn) payBtn.addEventListener('click', openSnap);
                if (autoPay) window.addEventListener('load', openSnap);
            })();
        </script>
    @endpush
@endif
