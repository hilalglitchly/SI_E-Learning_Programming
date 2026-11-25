@extends('layouts.app')

@section('title', 'Struk Invoice ' . $order->kode_invoice . ' - E-Learning Programming')

@section('styles')
<style>
    .invoice-shell {
        max-width: 780px;
        margin: 0 auto;
        padding: 20px 16px 56px;
    }
    .invoice-card {
        background-color: var(--card);
        color: var(--card-foreground);
        border: 4px solid var(--border);
        box-shadow: 10px 10px 0px var(--shadow-color);
        padding: 32px;
    }
    .invoice-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        border-bottom: 4px solid var(--border);
        padding-bottom: 20px;
        margin-bottom: 24px;
    }
    .invoice-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 560px) {
        .invoice-meta-grid { grid-template-columns: 1fr; }
        .invoice-card { padding: 22px 18px; }
    }
    @media print {
        .neo-navbar, .neo-site-footer, .invoice-no-print, #custom-cursor { display: none !important; }
        body, .invoice-shell { background: #fff !important; color: #000 !important; padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        .invoice-card { box-shadow: none !important; border: 2px solid #000 !important; background: #fff !important; color: #000 !important; }
    }
</style>
@endsection

@section('content')
<main class="invoice-shell">
    <div class="invoice-no-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.langganan.index') : route('langganan.index') }}" class="neo-btn" style="min-height: 44px; background-color: var(--muted); color: var(--foreground);">
            <i class='bx bx-left-arrow-alt' aria-hidden="true"></i> Kembali
        </a>
        <button type="button" onclick="window.print()" class="neo-btn" style="min-height: 44px; background-color: var(--secondary); color: #000;">
            <i class='bx bxs-printer' aria-hidden="true"></i> Cetak / Simpan PDF
        </button>
    </div>

    <article class="invoice-card">
        <div class="invoice-head">
            <div>
                <div style="font-size: 0.82rem; font-weight: 900; text-transform: uppercase; color: var(--muted-foreground);">Bukti Pembayaran Resmi</div>
                <h1 style="font-size: 1.75rem; margin: 4px 0; text-transform: uppercase;">&lt;/&gt; E-Learning Programming</h1>
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--muted-foreground);">
                    {{ config('app.contact.email') }} &bull; {{ config('app.contact.phone') }}
                </div>
            </div>
            <div style="text-align: right;">
                <span class="neo-status-pill neo-status-pill--success" style="font-size: 0.88rem; padding: 6px 12px;">LUNAS / TERVERIFIKASI</span>
                <div style="font-size: 1.15rem; font-weight: 900; margin-top: 8px;">{{ $order->kode_invoice }}</div>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--muted-foreground);">
                    Diverifikasi: {{ $order->verified_at ? $order->verified_at->format('d M Y, H:i') . ' WIB' : '-' }}
                </div>
            </div>
        </div>

        <div class="invoice-meta-grid">
            <div>
                <div style="font-size: 0.78rem; font-weight: 900; text-transform: uppercase; color: var(--muted-foreground); margin-bottom: 4px;">Diterbitkan Untuk</div>
                <div style="font-size: 1.08rem; font-weight: 900;">{{ $order->user->murid->nama_murid ?? $order->user->username ?? '-' }}</div>
                <div style="font-size: 0.88rem; font-weight: 600; color: var(--muted-foreground);">
                    Username: {{ $order->user->username ?? '-' }}
                    @if ($order->user?->murid)
                        <br>NIM: {{ $order->user->murid->nim }}<br>Email: {{ $order->user->murid->email }}
                    @endif
                </div>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 900; text-transform: uppercase; color: var(--muted-foreground); margin-bottom: 4px;">Detail Pembayaran</div>
                <div style="font-size: 0.92rem; font-weight: 700;">
                    Metode: Transfer Bank {{ $payment['bank_name'] ?? 'BCA' }}<br>
                    Rekening: {{ $payment['account_number'] ?? '-' }}<br>
                    Verifikator: {{ $order->verifier->username ?? 'Admin' }}
                </div>
            </div>
        </div>

        <div style="overflow-x: auto; margin-bottom: 24px;">
            <table class="neo-table">
                <thead>
                    <tr>
                        <th>Deskripsi Paket</th>
                        <th>Masa Aktif</th>
                        <th style="text-align: right;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="font-weight: 900;">{{ $order->paket->nama_paket ?? 'Langganan Pro' }}</div>
                            <small style="color: var(--muted-foreground);">{{ $order->paket->deskripsi ?? 'Akses kelas Premium E-Learning Programming.' }}</small>
                        </td>
                        <td style="font-weight: 700; white-space: nowrap;">
                            @if ($order->tgl_mulai && $order->tgl_berakhir)
                                {{ $order->tgl_mulai->format('d M Y') }} – {{ $order->tgl_berakhir->format('d M Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="text-align: right; font-weight: 900; font-size: 1.08rem; white-space: nowrap;">
                            Rp {{ number_format($order->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right; font-weight: 900; text-transform: uppercase;">Total Dibayar</td>
                        <td style="text-align: right; font-weight: 900; font-size: 1.2rem;">Rp {{ number_format($order->nominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if ($order->catatan_admin)
            <div style="background-color: var(--muted); border: 2px solid var(--border); padding: 12px 14px; font-size: 0.88rem; font-weight: 700;">
                Catatan Admin: {{ $order->catatan_admin }}
            </div>
        @endif
    </article>
</main>
@endsection
