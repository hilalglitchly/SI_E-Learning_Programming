@extends('layouts.app')

@section('title', 'Langganan Pro - E-Learning')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1200px; margin: 0 auto; padding-top: 20px;">

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                <h2 style="margin: 0;"><i class='bx bxs-crown' style="color: #F59E0B;"></i> Langganan Pro</h2>
                <a href="{{ route('enroll.index') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">← Kembali ke Cari Kelas</a>
            </div>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: {{ $tipe_pesan === 'error' ? 'var(--primary)' : '#a8e6cf' }}; color: {{ $tipe_pesan === 'error' ? '#fff' : '#000' }}; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif

            <!-- Status Langganan Saat Ini -->
            <div class="neo-box" style="border-left: 10px solid {{ $activeSub ? '#16A34A' : '#F59E0B' }}; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: var(--muted-foreground);">Status Akun Anda</div>
                    @if ($activeSub)
                        <h3 style="margin: 4px 0; color: #16A34A; font-size: 1.5rem;">👑 PRO AKTIF</h3>
                        <p style="margin: 0; font-weight: 600;">
                            Berlaku sampai: <strong>{{ $activeSub->tgl_berakhir->format('d M Y, H:i') }} WIB</strong>
                            (sisa {{ max(1, (int) ceil(now()->diffInHours($activeSub->tgl_berakhir, false) / 24)) }} hari)
                        </p>
                    @else
                        <h3 style="margin: 4px 0; font-size: 1.5rem;">AKUN GRATIS</h3>
                        <p style="margin: 0; color: var(--muted-foreground); font-weight: 600;">
                            Anda dapat mengakses seluruh kelas Gratis. Aktifkan paket Pro untuk membuka seluruh kelas Premium.
                        </p>
                    @endif
                </div>
                <span class="badge-kode" style="margin: 0; padding: 8px 16px; font-size: 0.95rem; background-color: {{ $activeSub ? '#16A34A' : 'var(--secondary)' }}; color: {{ $activeSub ? '#fff' : '#000' }};">
                    {{ $activeSub ? 'PRO MEMBER' : 'FREE TIER' }}
                </span>
            </div>

            <!-- Pilihan Paket -->
            <div class="neo-grid" style="margin-bottom: 32px;">
                @foreach ($paketList as $paket)
                    <div class="neo-card" style="display: flex; flex-direction: column; justify-content: space-between; border: 4px solid #000;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <span class="badge-kode" style="margin: 0; background-color: var(--secondary); color: #000;">{{ $paket->durasi_hari }} HARI</span>
                                @if ($paket->durasi_hari >= 365)
                                    <span style="background-color: #16A34A; color: #fff; padding: 3px 8px; font-size: 0.75rem; font-weight: 900; border: 2px solid #000;">HEMAT</span>
                                @endif
                            </div>
                            <h3 style="font-size: 1.5rem; margin-bottom: 8px;">{{ $paket->nama_paket }}</h3>
                            <div style="font-size: 2rem; font-weight: 900; color: var(--primary); margin-bottom: 12px;">
                                Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            </div>
                            <p style="color: var(--muted-foreground); font-weight: 500; margin-bottom: 16px;">{{ $paket->deskripsi }}</p>
                        </div>
                        <button type="button" class="neo-btn" style="width: 100%; background-color: var(--secondary); color: #000;" onclick="document.getElementById('select-paket').value='{{ $paket->id_paket }}'; document.getElementById('checkout-box').scrollIntoView({behavior:'smooth'});">
                            Pilih {{ $paket->nama_paket }} →
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Instruksi Transfer & Form Upload Bukti Bayar -->
            <div id="checkout-box" class="neo-box" style="border-left: 10px solid var(--accent);">
                <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 18px;">💳 Transfer Manual &amp; Konfirmasi Pembayaran</h3>

                <div style="background-color: var(--muted); border: 3px solid var(--border); padding: 16px; margin-bottom: 20px;">
                    <div style="font-weight: 900; text-transform: uppercase; font-size: 0.85rem; margin-bottom: 6px;">Rekening Tujuan Transfer</div>
                    <div style="font-size: 1.15rem; font-weight: 900;">
                        Bank {{ $payment['bank_name'] ?? 'BCA' }} &mdash; {{ $payment['account_number'] ?? '-' }}
                    </div>
                    <div style="font-weight: 700; color: var(--muted-foreground);">a.n. {{ $payment['account_holder'] ?? 'E-Learning Programming' }}</div>
                </div>

                @if ($hasPending)
                    <div style="padding: 14px; border: 3px solid #000; background-color: #FEF08A; color: #000; font-weight: 800;">
                        ⏳ Anda memiliki transaksi yang sedang menunggu verifikasi Admin. Mohon tunggu hingga proses selesai sebelum mengirim pengajuan baru.
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
                            <label class="neo-label" for="bukti_bayar">Unggah Bukti Transfer (JPG / PNG / PDF, maks. 5 MB)</label>
                            <input type="file" id="bukti_bayar" name="bukti_bayar" class="neo-input" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>

                        <button type="submit" class="neo-btn" style="width: 100%; font-size: 1.05rem; padding: 14px; background-color: var(--accent); color: var(--accent-foreground);">
                            📤 Kirim Bukti Pembayaran untuk Diverifikasi
                        </button>
                    </form>
                @endif
            </div>

            <!-- Riwayat Transaksi -->
            <div class="neo-box">
                <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 16px;">🧾 Riwayat Pembayaran Anda</h3>
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
                                            <span style="background: #16A34A; color: #fff; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">
                                                {{ $row->isCurrentlyActive() ? 'AKTIF' : 'BERAKHIR' }}
                                            </span>
                                        @elseif ($row->status === 'rejected')
                                            <span style="background: var(--primary); color: #fff; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">DITOLAK</span>
                                        @else
                                            <span style="background: #FEF08A; color: #000; padding: 3px 8px; border: 2px solid #000; font-weight: 900; font-size: 0.78rem;">PENDING</span>
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
                                            <a href="{{ route('files.bukti_bayar', ['id' => $row->id_langganan]) }}" target="_blank" rel="noopener" style="font-weight: 800;">📥 Bukti</a>
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
