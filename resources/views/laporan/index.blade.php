@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi - E-Learning')

@section('styles')
    <style>
        /* ===== BASE STYLES ===== */
        .lap-page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 10px 24px 60px 24px;
        }

        /* ===== HEADER BANNER ===== */
        .lap-header {
            background-color: var(--card);
            border: 3px solid var(--border);
            border-left: 12px solid #7C3AED;
            box-shadow: 6px 6px 0px var(--shadow-color);
            padding: 24px 28px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .lap-header-tag {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted-foreground);
            margin-bottom: 4px;
        }
        .lap-header h1 {
            font-size: 1.9rem;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0 0 6px 0;
            line-height: 1.15;
        }
        .lap-header-meta {
            font-size: 0.88rem;
            color: var(--muted-foreground);
            font-weight: 600;
            margin: 0;
        }
        .lap-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* ===== STAT CARDS GRID ===== */
        .lap-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            margin-bottom: 32px;
        }
        @media (max-width: 1100px) {
            .lap-stats-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 650px) {
            .lap-stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        .lap-stat-card {
            border: 3px solid #000;
            box-shadow: 5px 5px 0px #000;
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        .lap-stat-card:hover {
            transform: translate(3px, 3px);
            box-shadow: 2px 2px 0px #000;
        }
        .lap-stat-icon {
            width: 52px;
            height: 52px;
            border: 3px solid #000;
            box-shadow: 2px 2px 0px #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            flex-shrink: 0;
        }
        .lap-stat-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.75;
            margin-bottom: 2px;
        }
        .lap-stat-num {
            font-size: 2.1rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.03em;
        }
        .lap-stat-sub {
            font-size: 0.7rem;
            font-weight: 700;
            opacity: 0.65;
            margin-top: 2px;
        }

        /* ===== TABEL LAPORAN ===== */
        .lap-table-box {
            background-color: var(--card);
            border: 3px solid var(--border);
            box-shadow: 6px 6px 0px var(--shadow-color);
            padding: 24px;
            margin-bottom: 32px;
        }
        .lap-table-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--border);
            padding-bottom: 14px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .lap-table-head h2 {
            font-size: 1.25rem;
            font-weight: 900;
            text-transform: uppercase;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .lap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }
        .lap-table thead tr {
            background-color: #FFD700;
            color: #000;
            border-bottom: 3px solid #000;
        }
        .lap-table th {
            padding: 12px 14px;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.04em;
            border: 2px solid #000;
            white-space: nowrap;
        }
        .lap-table td {
            padding: 11px 14px;
            border: 2px solid var(--border);
            vertical-align: middle;
        }
        .lap-table tbody tr:nth-child(even) {
            background-color: var(--muted);
        }
        .lap-table tbody tr:hover {
            background-color: #FEF9C3;
            color: #000;
        }
        .lap-table tbody tr:hover td,
        .lap-table tbody tr:hover td div,
        .lap-table tbody tr:hover td span:not(.badge-aktif):not(.badge-kosong) {
            color: #000 !important;
        }
        .lap-table tfoot tr {
            background-color: #E5E7EB;
            color: #000;
            font-weight: 900;
            border-top: 3px solid #000;
        }
        .lap-table tfoot td {
            border: 2px solid #000;
            padding: 11px 14px;
        }
        .badge-num {
            display: inline-block;
            font-weight: 900;
            font-size: 0.95rem;
            padding: 2px 10px;
            border: 2px solid #000;
            min-width: 36px;
            text-align: center;
        }
        .badge-aktif {
            background-color: #4CAF50;
            color: #fff;
            font-weight: 900;
            font-size: 0.72rem;
            padding: 3px 9px;
            border: 2px solid #000;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-kosong {
            background-color: #E5E7EB;
            color: #374151;
            font-weight: 700;
            font-size: 0.72rem;
            padding: 3px 9px;
            border: 2px solid #9CA3AF;
            text-transform: uppercase;
        }

        /* ===== PRINT HEADER & FOOTER (Hanya muncul saat cetak) ===== */
        .print-only-header,
        .print-only-footer {
            display: none;
        }

        /* ===== PRINT MEDIA QUERY ===== */
        @media print {
            @page {
                size: A4 landscape;
                margin: 15mm 12mm;
            }

            /* Sembunyikan elemen navigasi & kontrol layar */
            .neo-navbar,
            .no-print,
            #custom-cursor {
                display: none !important;
            }

            /* Reset warna & background untuk hemat tinta */
            body,
            .lap-page {
                background: #fff !important;
                color: #000 !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                font-size: 10pt !important;
            }

            /* Tampilkan kop surat cetak */
            .print-only-header {
                display: flex !important;
                justify-content: space-between;
                align-items: flex-end;
                border-bottom: 3px double #000;
                padding-bottom: 10px;
                margin-bottom: 16px;
            }

            /* Tampilkan tanda tangan cetak */
            .print-only-footer {
                display: flex !important;
                justify-content: space-between;
                align-items: flex-end;
                margin-top: 28px;
                padding-top: 12px;
                border-top: 1.5px solid #000;
                font-size: 9pt;
                page-break-inside: avoid;
            }

            /* Sembunyikan header layar saat print (diganti print-only-header) */
            .lap-header {
                display: none !important;
            }

            /* Stat cards dalam 1 baris 5 kolom saat print */
            .lap-stats-grid {
                grid-template-columns: repeat(5, 1fr) !important;
                gap: 8px !important;
                margin-bottom: 16px !important;
            }
            .lap-stat-card {
                box-shadow: none !important;
                border: 1.5px solid #000 !important;
                padding: 8px 10px !important;
                gap: 8px !important;
                background: #f9f9f9 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .lap-stat-icon {
                width: 34px !important;
                height: 34px !important;
                font-size: 1.1rem !important;
                box-shadow: none !important;
                border: 1.5px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .lap-stat-num {
                font-size: 1.4rem !important;
            }
            .lap-stat-label, .lap-stat-sub {
                font-size: 7pt !important;
            }

            /* Tabel cetak bersih */
            .lap-table-box {
                box-shadow: none !important;
                border: 1.5px solid #000 !important;
                padding: 12px !important;
                margin-bottom: 0 !important;
            }
            .lap-table-head {
                padding-bottom: 8px !important;
                margin-bottom: 10px !important;
                border-bottom: 2px solid #000 !important;
            }
            .lap-table-head h2 {
                font-size: 11pt !important;
            }
            .lap-table {
                font-size: 8.5pt !important;
            }
            .lap-table thead tr {
                background-color: #FFD700 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .lap-table th,
            .lap-table td {
                padding: 6px 8px !important;
                border: 1px solid #000 !important;
            }
            .lap-table tbody tr:nth-child(even) {
                background-color: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .lap-table tfoot tr {
                background-color: #e5e7eb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-num, .badge-aktif, .badge-kosong {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('content')
<div class="lap-page">

    <!-- =====================================================
         KOP SURAT (Hanya tampil saat Cetak / Print PDF)
         ===================================================== -->
    <div class="print-only-header">
        <div>
            <div style="font-size: 14pt; font-weight: 900; text-transform: uppercase; letter-spacing: -0.02em;">
                &lt;/&gt; SISTEM INFORMASI E-LEARNING PROGRAMMING
            </div>
            <div style="font-size: 11pt; font-weight: 800; text-transform: uppercase; margin-top: 2px;">
                Laporan Rekapitulasi Akademik &amp; Aktivitas Kelas
            </div>
            <div style="font-size: 8.5pt; color: #444; margin-top: 2px;">
                Gedung Inovasi Digital Lt. 4, Jl. Teknologi Raya No. 404, Jakarta Selatan &bull; support@elearning.local
            </div>
        </div>
        <div style="text-align: right; font-size: 8.5pt;">
            <div><strong>Tanggal Cetak:</strong> {{ date('d F Y, H:i') }} WIB</div>
            <div><strong>Dicetak oleh:</strong> {{ $username }} ({{ $role }})</div>
            <div><strong>Semester:</strong> Genap 2025/2026</div>
        </div>
    </div>

    <!-- =====================================================
         HEADER LAYAR (Disembunyikan saat Print)
         ===================================================== -->
    <div class="lap-header">
        <div>
            <div class="lap-header-tag">📊 Sistem Informasi E-Learning Programming &bull; Semester Genap 2025/2026</div>
            <h1>
                Laporan Rekapitulasi Sistem
            </h1>
            <p class="lap-header-meta">
                Dicetak oleh: <strong>{{ $username }}</strong> ({{ $role }}) &bull;
                Per tanggal: <strong>{{ date('d F Y, H:i') }} WIB</strong>
            </p>
        </div>
        <div class="lap-header-actions no-print">
            <a href="{{ route('dashboard') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); font-size: 0.88rem; padding: 10px 16px;">
                &larr; Dashboard
            </a>
            <button onclick="window.print()" class="neo-btn brutal-hover" style="background-color: #FFD700; color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #000; font-size: 0.95rem; padding: 10px 22px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class='bx bxs-printer' style="font-size: 1.2rem;"></i> CETAK / SIMPAN PDF
            </button>
        </div>
    </div>

    <!-- =====================================================
         5 KARTU RINGKASAN STATISTIK
         ===================================================== -->
    <div class="lap-stats-grid">
        <!-- Total Kelas -->
        <div class="lap-stat-card" style="background-color: #FEF08A; color: #000;">
            <div class="lap-stat-icon" style="background-color: #FACC15; color: #000;">
                <i class='bx bxs-book-content'></i>
            </div>
            <div>
                <div class="lap-stat-label">Total Kelas</div>
                <div class="lap-stat-num" data-target="{{ $totalKelas }}">{{ $totalKelas }}</div>
                <div class="lap-stat-sub">Mata Kuliah Aktif</div>
            </div>
        </div>

        <!-- Total Pengajar -->
        <div class="lap-stat-card" style="background-color: #DDD6FE; color: #000;">
            <div class="lap-stat-icon" style="background-color: #7C3AED; color: #FFF;">
                <i class='bx bxs-user-voice'></i>
            </div>
            <div>
                <div class="lap-stat-label">Total Pengajar</div>
                <div class="lap-stat-num" data-target="{{ $totalPengajar }}">{{ $totalPengajar }}</div>
                <div class="lap-stat-sub">Dosen Pengampu</div>
            </div>
        </div>

        <!-- Total Murid -->
        <div class="lap-stat-card" style="background-color: #BBF7D0; color: #000;">
            <div class="lap-stat-icon" style="background-color: #16A34A; color: #FFF;">
                <i class='bx bxs-graduation'></i>
            </div>
            <div>
                <div class="lap-stat-label">Total Mahasiswa</div>
                <div class="lap-stat-num" data-target="{{ $totalMurid }}">{{ $totalMurid }}</div>
                <div class="lap-stat-sub">Murid Terdaftar</div>
            </div>
        </div>

        <!-- Total Tugas -->
        <div class="lap-stat-card" style="background-color: #BAE6FD; color: #000;">
            <div class="lap-stat-icon" style="background-color: #0284C7; color: #FFF;">
                <i class='bx bxs-task'></i>
            </div>
            <div>
                <div class="lap-stat-label">Total Tugas</div>
                <div class="lap-stat-num" data-target="{{ $totalTugas }}">{{ $totalTugas }}</div>
                <div class="lap-stat-sub">Tugas Diberikan</div>
            </div>
        </div>

        <!-- Total Pengumpulan -->
        <div class="lap-stat-card" style="background-color: #FED7AA; color: #000;">
            <div class="lap-stat-icon" style="background-color: #EA580C; color: #FFF;">
                <i class='bx bxs-file-import'></i>
            </div>
            <div>
                <div class="lap-stat-label">Pengumpulan</div>
                <div class="lap-stat-num" data-target="{{ $totalSubmission }}">{{ $totalSubmission }}</div>
                <div class="lap-stat-sub">Jawaban Masuk</div>
            </div>
        </div>
    </div>

    <!-- =====================================================
         TABEL REKAPITULASI DATA SELURUH KELAS
         ===================================================== -->
    <div class="lap-table-box">
        <div class="lap-table-head">
            <h2>
                <i class='bx bxs-table'></i> Tabel Rekapitulasi Aktivitas Seluruh Kelas
            </h2>
            <span style="font-size: 0.8rem; font-weight: 800; background-color: var(--muted); padding: 5px 12px; border: 2px solid var(--border);">
                Total: {{ count($dataLaporan) }} Kelas Terdaftar
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="lap-table">
                <thead>
                    <tr>
                        <th style="width: 42px; text-align: center;">No</th>
                        <th style="width: 90px;">Kode</th>
                        <th>Nama Kelas / Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                        <th style="text-align: center;">Peserta</th>
                        <th style="text-align: center;">Materi</th>
                        <th style="text-align: center;">Tugas</th>
                        <th style="text-align: center;">Kuis</th>
                        <th style="text-align: center;">Jawaban Masuk</th>
                        <th style="text-align: center;">Rata-rata Nilai</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumPeserta = 0;
                        $sumMateri  = 0;
                        $sumTugas   = 0;
                        $sumKuis    = 0;
                        $sumSub     = 0;
                    @endphp
                    @if (count($dataLaporan) > 0)
                        @foreach ($dataLaporan as $i => $row)
                            @php
                                $sumPeserta += $row['jumlah_peserta'];
                                $sumMateri  += $row['jumlah_materi'];
                                $sumTugas   += $row['jumlah_tugas'];
                                $sumKuis    += $row['jumlah_kuis'];
                                $sumSub     += $row['jumlah_submission'];
                            @endphp
                            <tr>
                                <td style="text-align: center; font-weight: 900;">{{ $i + 1 }}</td>
                                <td>
                                    <span class="badge-kode" style="margin: 0; font-size: 0.78rem;">KLS-{{ $row['id_kelas'] }}</span>
                                </td>
                                <td style="font-weight: 800;">{{ $row['nama_kelas'] }}</td>
                                <td>
                                    @if ($row['nama_pengajar'])
                                        <div style="font-weight: 700;">{{ $row['nama_pengajar'] }}</div>
                                        <div style="font-size: 0.75rem; color: var(--muted-foreground);">NIDN: {{ $row['nidn'] ?? '-' }}</div>
                                    @else
                                        <span style="color: var(--muted-foreground); font-style: italic;">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-num" style="background-color: #BBF7D0; color: #000;">
                                        {{ $row['jumlah_peserta'] }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 800;">{{ $row['jumlah_materi'] }}</td>
                                <td style="text-align: center; font-weight: 800;">{{ $row['jumlah_tugas'] }}</td>
                                <td style="text-align: center; font-weight: 800;">{{ $row['jumlah_kuis'] }}</td>
                                <td style="text-align: center;">
                                    <span class="badge-num" style="background-color: #FED7AA; color: #000;">
                                        {{ $row['jumlah_submission'] }}
                                    </span>
                                </td>
                                <td style="text-align: center; font-weight: 900;">
                                    @if ($row['rata_nilai'] !== null)
                                        <span style="color: {{ $row['rata_nilai'] >= 75 ? '#16A34A' : '#DC2626' }};">
                                            {{ number_format($row['rata_nilai'], 1) }}
                                        </span>
                                    @else
                                        <span style="color: var(--muted-foreground); font-weight: 600;">—</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if ($row['jumlah_peserta'] > 0)
                                        <span class="badge-aktif">Aktif</span>
                                    @else
                                        <span class="badge-kosong">Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 30px; font-weight: 700;">
                                Belum ada data kelas di dalam sistem.
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if (count($dataLaporan) > 0)
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align: right; text-transform: uppercase; letter-spacing: 0.04em;">
                                Total Keseluruhan ({{ count($dataLaporan) }} Kelas)
                            </td>
                            <td style="text-align: center;">{{ $sumPeserta }}</td>
                            <td style="text-align: center;">{{ $sumMateri }}</td>
                            <td style="text-align: center;">{{ $sumTugas }}</td>
                            <td style="text-align: center;">{{ $sumKuis }}</td>
                            <td style="text-align: center;">{{ $sumSub }}</td>
                            <td colspan="2" style="text-align: center;">—</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- =====================================================
         BLOK TANDA TANGAN (Hanya muncul saat Cetak / Print PDF)
         ===================================================== -->
    <div class="print-only-footer">
        <div>
            <div><strong>Catatan Dokumen:</strong></div>
            <div>Dokumen ini dihasilkan secara otomatis oleh Sistem Informasi E-Learning Programming.</div>
            <div>Data bersifat valid sesuai waktu cetak yang tertera pada kop dokumen.</div>
        </div>
        <div style="text-align: center; min-width: 220px;">
            <div>Jakarta, {{ date('d F Y') }}</div>
            <div style="margin-top: 4px; font-weight: 700;">{{ strtoupper($role) }} INSTITUSI,</div>
            <br><br><br>
            <div style="border-bottom: 1.5px solid #000; font-weight: 900; padding-bottom: 2px;">
                {{ strtoupper($username) }}
            </div>
            <div style="font-size: 8pt; color: #444; margin-top: 2px;">Sistem Informasi E-Learning</div>
        </div>
    </div>

</div><!-- /.lap-page -->
@endsection

@section('scripts')
<script>
    // Animasi Count-Up angka statistik saat halaman dibuka
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.lap-stat-num[data-target]').forEach(el => {
            const target = parseInt(el.getAttribute('data-target'), 10);
            if (!target || target === 0) { el.textContent = '0'; return; }
            let current = 0;
            const step = Math.max(1, Math.ceil(target / 25));
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current;
            }, 28);
        });
    });
</script>
@endsection
