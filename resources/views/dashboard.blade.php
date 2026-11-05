@extends('layouts.app')

@section('title', 'Dashboard - E-Learning')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">
            
            @if (request()->query('msg') == 'deleted' || session('msg') == 'deleted')
                <div class="neo-box" style="background-color: var(--primary); color: var(--primary-foreground);">
                    <strong>Pesan:</strong> Akun pengguna berhasil dihapus secara permanen.
                </div>
            @elseif (request()->query('msg') == 'updated' || session('msg') == 'updated')
                <div class="neo-box" style="background-color: #a8e6cf; color: #000;">
                    <strong>Berhasil:</strong> Data pengguna telah berhasil diperbarui.
                </div>
            @endif

            @if (strtolower($role) === 'admin')
                <!-- ==========================================
                     TAMPILAN KHUSUS ADMIN: MANAJEMEN PENGGUNA 
                     ========================================== -->
                
                <!-- GREETING BANNER ADMIN -->
                <div class="neo-box" style="background-color: var(--card); border-left: 12px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h1 style="font-size: 2.2rem; margin-bottom: 5px; font-weight: 900;">
                            SELAMAT DATANG, <span style="background-color: var(--secondary); color: #000; padding: 2px 10px; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-block; transform: rotate(-1deg);">{{ strtoupper($username) }}!</span> 👋
                        </h1>
                        <p style="margin: 0; font-size: 1.05rem; color: var(--muted-foreground); font-weight: 600;">
                            Kelola data pengajar, murid, dan pantau sistem secara keseluruhan.
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge-kode" style="font-size: 1rem; padding: 8px 16px; background-color: var(--accent); color: #FFF; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                            <i class='bx bxs-shield-alt-2'></i> ROLE: {{ strtoupper($role) }}
                        </span>
                    </div>
                </div>

                <!-- SUMMARY CARDS ADMIN -->
                <div class="neo-grid" style="margin-top: 0; margin-bottom: 35px;">
                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 20px; background-color: var(--card);">
                        <div style="width: 60px; height: 60px; background-color: var(--primary); color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class='bx bxs-user-voice'></i>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Pengajar</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1;">{{ count($listPengajar) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 20px; background-color: var(--card);">
                        <div style="width: 60px; height: 60px; background-color: var(--secondary); color: #000; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class='bx bxs-graduation'></i>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Murid</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1;">{{ count($listMurid) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 20px; background-color: var(--card);">
                        <div style="width: 60px; height: 60px; background-color: var(--accent); color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class='bx bxs-group'></i>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Pengguna</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1;">{{ count($listPengajar) + count($listMurid) }}</div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2>Manajemen Pengguna Sistem</h2>
                    <a href="{{ route('admin.users.create') }}" class="neo-btn" style="background-color: var(--accent); color: var(--accent-foreground);">+ Tambah Pengguna Baru</a>
                </div>

                <!-- Tabel Data Pengajar -->
                <div class="neo-box">
                    <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 8px; margin-bottom: 16px;">👨‍🏫 Data Pengajar (Dosen)</h3>
                    <div style="overflow-x: auto;">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>NIDN</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th style="width: 180px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($listPengajar) > 0)
                                    @foreach ($listPengajar as $p)
                                        <tr>
                                            <td style="font-weight: bold;">{{ $p['nidn'] }}</td>
                                            <td>{{ $p['nama_pengajar'] }}</td>
                                            <td><span class="badge-kode" style="margin:0;">{{ $p['username'] }}</span></td>
                                            <td>{{ $p['email'] }}</td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <a href="{{ route('admin.users.edit', $p['id_user']) }}" class="neo-btn" style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--secondary); color: #000; margin-right: 4px;">Update</a>
                                                <a href="{{ route('admin.users.delete', $p['id_user']) }}" 
                                                   class="neo-btn" 
                                                   style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--primary); color: #fff;"
                                                   onclick="event.preventDefault(); const url = this.href; NeoConfirm('Hapus Pengajar', 'Yakin ingin menghapus pengajar {{ addslashes($p['nama_pengajar']) }}?', () => window.location.href = url);">
                                                   Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="5" style="text-align: center;">Belum ada data pengajar.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Data Murid -->
                <div class="neo-box">
                    <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 8px; margin-bottom: 16px;">🎓 Data Murid (Mahasiswa)</h3>
                    <div style="overflow-x: auto;">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th style="width: 180px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($listMurid) > 0)
                                    @foreach ($listMurid as $m)
                                        <tr>
                                            <td style="font-weight: bold;">{{ $m['nim'] }}</td>
                                            <td>{{ $m['nama_murid'] }}</td>
                                            <td><span class="badge-kode" style="margin:0;">{{ $m['username'] }}</span></td>
                                            <td>{{ $m['email'] }}</td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                <a href="{{ route('admin.users.edit', $m['id_user']) }}" class="neo-btn" style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--secondary); color: #000; margin-right: 4px;">Update</a>
                                                <a href="{{ route('admin.users.delete', $m['id_user']) }}" 
                                                   class="neo-btn" 
                                                   style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--primary); color: #fff;"
                                                   onclick="event.preventDefault(); const url = this.href; NeoConfirm('Hapus Murid', 'Yakin ingin menghapus murid {{ addslashes($m['nama_murid']) }}?', () => window.location.href = url);">
                                                   Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="5" style="text-align: center;">Belum ada data murid.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif (strtolower($role) === 'pimpinan')
                <!-- ==========================================
                     TAMPILAN KHUSUS PIMPINAN: LAPORAN EKSEKUTIF
                     ========================================== -->

                <style>
                    @media print {
                        .neo-navbar, .pimpinan-no-print, #custom-cursor { display: none !important; }
                        body, .neo-layout-container, .neo-content-inner { background: #fff !important; color: #000 !important; padding: 0 !important; margin: 0 !important; }
                        .neo-box { box-shadow: none !important; border: 2px solid #000 !important; page-break-inside: avoid; }
                        .pimpinan-stat-grid { grid-template-columns: repeat(4, 1fr) !important; }
                    }
                    .pimpinan-stat-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                        gap: 20px;
                        margin-bottom: 32px;
                    }
                    .pimpinan-stat-card {
                        border: 3px solid var(--border);
                        box-shadow: 6px 6px 0px var(--shadow-color);
                        padding: 24px 20px;
                        display: flex;
                        align-items: center;
                        gap: 18px;
                        transition: transform 0.15s ease, box-shadow 0.15s ease;
                    }
                    .pimpinan-stat-card:hover {
                        transform: translate(3px, 3px);
                        box-shadow: 3px 3px 0px var(--shadow-color);
                    }
                    .pimpinan-stat-icon {
                        width: 60px;
                        height: 60px;
                        border: 3px solid #000;
                        box-shadow: 3px 3px 0px #000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 2rem;
                        flex-shrink: 0;
                    }
                    .pimpinan-stat-number {
                        font-size: 2.4rem;
                        font-weight: 900;
                        line-height: 1;
                        letter-spacing: -0.03em;
                    }
                    .pimpinan-stat-label {
                        font-size: 0.8rem;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        margin-bottom: 4px;
                        opacity: 0.8;
                    }
                    .pimpinan-print-footer {
                        display: none;
                    }
                    @media print {
                        .pimpinan-print-footer {
                            display: flex !important;
                            justify-content: space-between;
                            margin-top: 50px;
                            padding-top: 20px;
                            border-top: 2px solid #000;
                            font-size: 0.85rem;
                        }
                    }
                </style>

                <div id="pimpinan-laporan">

                    <!-- HEADER BANNER PIMPINAN -->
                    <div class="neo-box" style="background-color: var(--card); border-left: 12px solid #7C3AED; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 28px;">
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted-foreground); margin-bottom: 4px;">
                                📊 SISTEM INFORMASI E-LEARNING PROGRAMMING
                            </div>
                            <h1 style="font-size: 2rem; margin: 0 0 6px 0; font-weight: 900; text-transform: uppercase;">
                                LAPORAN EKSEKUTIF — <span style="background-color: var(--secondary); color: #000; padding: 2px 10px; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-block; transform: rotate(-1deg);">{{ strtoupper($username) }}</span>
                            </h1>
                            <p style="margin: 0; font-size: 0.95rem; color: var(--muted-foreground); font-weight: 600;">
                                Ringkasan data akademik sistem secara real-time &bull; Tanggal cetak: <strong>{{ date('d F Y, H:i') }} WIB</strong>
                            </p>
                        </div>
                        <div class="pimpinan-no-print" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span class="badge-kode" style="font-size: 0.9rem; padding: 8px 14px; background-color: #7C3AED; color: #FFF; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                                <i class='bx bxs-crown'></i> ROLE: PIMPINAN
                            </span>
                            <button onclick="window.print()" class="neo-btn brutal-hover" style="background-color: var(--secondary); color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #000; font-size: 0.95rem; padding: 10px 20px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <i class='bx bxs-printer' style="font-size: 1.2rem;"></i> CETAK LAPORAN
                            </button>
                        </div>
                    </div>

                    <!-- 4 KARTU STATISTIK UTAMA -->
                    <div class="pimpinan-stat-grid">
                        <!-- Total Kelas -->
                        <div class="pimpinan-stat-card" style="background-color: #FEF08A; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #FACC15; color: #000;">
                                <i class='bx bxs-book-content'></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Kelas Aktif</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statKelas }}">0</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px; opacity: 0.7;">Mata Kuliah Terdaftar</div>
                            </div>
                        </div>

                        <!-- Total Pengajar -->
                        <div class="pimpinan-stat-card" style="background-color: #DDD6FE; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #7C3AED; color: #FFF;">
                                <i class='bx bxs-user-voice'></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Pengajar</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statPengajar }}">0</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px; opacity: 0.7;">Dosen Aktif Mengajar</div>
                            </div>
                        </div>

                        <!-- Total Murid -->
                        <div class="pimpinan-stat-card" style="background-color: #BBF7D0; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #16A34A; color: #FFF;">
                                <i class='bx bxs-graduation'></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Mahasiswa</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statMurid }}">0</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px; opacity: 0.7;">Murid Terdaftar Sistem</div>
                            </div>
                        </div>

                        <!-- Total Pengumpulan Tugas -->
                        <div class="pimpinan-stat-card" style="background-color: #FED7AA; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #EA580C; color: #FFF;">
                                <i class='bx bxs-file-Import'></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Pengumpulan</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statSubmission }}">0</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px; opacity: 0.7;">Tugas Dikumpulkan</div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL REKAP DATA KELAS -->
                    <div class="neo-box" style="margin-bottom: 28px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--border); padding-bottom: 12px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                            <h3 style="margin: 0; font-size: 1.3rem; font-weight: 900; text-transform: uppercase;">
                                <i class='bx bxs-table'></i> Rekap Data Seluruh Kelas
                            </h3>
                            <span style="font-size: 0.8rem; font-weight: 700; background-color: var(--muted); padding: 4px 10px; border: 2px solid var(--border);">
                                Total: {{ count($rekapKelas) }} Kelas
                            </span>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="neo-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px; text-align: center;">No</th>
                                        <th>Kode</th>
                                        <th>Nama Kelas / Mata Kuliah</th>
                                        <th>Dosen Pengampu</th>
                                        <th style="text-align: center;">Jml Murid</th>
                                        <th style="text-align: center;">Jml Materi</th>
                                        <th style="text-align: center;">Jml Tugas</th>
                                        <th style="text-align: center;">Jml Kuis</th>
                                        <th style="text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($rekapKelas) > 0)
                                        @foreach ($rekapKelas as $idx => $rk)
                                            <tr>
                                                <td style="text-align: center; font-weight: 900;">{{ $idx + 1 }}</td>
                                                <td><span class="badge-kode" style="margin: 0;">KLS-{{ $rk['id_kelas'] }}</span></td>
                                                <td style="font-weight: 800;">{{ $rk['nama_kelas'] }}</td>
                                                <td>
                                                    @if ($rk['nama_pengajar'])
                                                        <span style="font-weight: 600;">{{ $rk['nama_pengajar'] }}</span>
                                                    @else
                                                        <span style="color: var(--muted-foreground); font-style: italic;">Belum ditentukan</span>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    <span style="font-weight: 900; font-size: 1.05rem; background-color: #BBF7D0; color: #000; padding: 2px 10px; border: 2px solid #000; display: inline-block;">
                                                        {{ $rk['jumlah_murid'] }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center; font-weight: 800;">{{ $rk['jumlah_materi'] }}</td>
                                                <td style="text-align: center; font-weight: 800;">{{ $rk['jumlah_tugas'] }}</td>
                                                <td style="text-align: center; font-weight: 800;">{{ $rk['jumlah_kuis'] }}</td>
                                                <td style="text-align: center;">
                                                    @if ($rk['jumlah_murid'] > 0)
                                                        <span style="background-color: #4CAF50; color: #fff; font-weight: 900; font-size: 0.75rem; padding: 3px 8px; border: 2px solid #000; text-transform: uppercase;">Aktif</span>
                                                    @else
                                                        <span style="background-color: var(--muted); color: var(--foreground); font-weight: 700; font-size: 0.75rem; padding: 3px 8px; border: 2px solid var(--border); text-transform: uppercase;">Kosong</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="9" style="text-align: center;">Belum ada data kelas.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FOOTER KHUSUS CETAK -->
                    <div class="pimpinan-print-footer">
                        <div>
                            <strong>Sistem Informasi E-Learning Programming</strong><br>
                            Dicetak oleh: {{ $username }} (Pimpinan)<br>
                            Tanggal: {{ date('d F Y, H:i') }} WIB
                        </div>
                        <div style="text-align: right;">
                            Mengetahui,<br><br><br>
                            <strong>( _________________________ )</strong><br>
                            Pimpinan Institusi
                        </div>
                    </div>

                </div><!-- /#pimpinan-laporan -->

                <script>
                    // Animasi Count-Up untuk angka statistik Pimpinan
                    document.addEventListener("DOMContentLoaded", () => {
                        document.querySelectorAll('.pimpinan-stat-number[data-count]').forEach(el => {
                            const target = parseInt(el.getAttribute('data-count'), 10);
                            if (target === 0) { el.textContent = '0'; return; }
                            let current = 0;
                            const step = Math.max(1, Math.ceil(target / 30));
                            const timer = setInterval(() => {
                                current += step;
                                if (current >= target) {
                                    current = target;
                                    clearInterval(timer);
                                }
                                el.textContent = current;
                            }, 30);
                        });
                    });
                </script>

            @else
                <!-- ==========================================
                     TAMPILAN MURID & PENGAJAR: DAFTAR KELAS
                     ========================================== -->
                
                <!-- GREETING BANNER -->
                <div class="neo-box" style="background-color: var(--card); border-left: 12px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h1 style="font-size: 2.2rem; margin-bottom: 5px; font-weight: 900;">
                            SELAMAT DATANG, <span style="background-color: var(--secondary); color: #000; padding: 2px 10px; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-block; transform: rotate(-1deg);">{{ strtoupper($username) }}!</span> 👋
                        </h1>
                        <p style="margin: 0; font-size: 1.05rem; color: var(--muted-foreground); font-weight: 600;">
                            Siap untuk melanjutkan progres pembelajaran hari ini?
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge-kode" style="font-size: 1rem; padding: 8px 16px; background-color: var(--accent); color: #FFF; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                            <i class='bx bxs-id-card'></i> ROLE: {{ strtoupper($role) }}
                        </span>
                    </div>
                </div>

                <!-- SUMMARY CARDS (STATISTIK CEPAT) -->
                <div class="neo-grid" style="margin-top: 0; margin-bottom: 35px;">
                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 20px; background-color: var(--card);">
                        <div style="width: 60px; height: 60px; background-color: var(--primary); color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class='bx bxs-book-bookmark'></i>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Kelas Diikuti</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1;">{{ count($daftarKelas) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 20px; background-color: var(--card);">
                        <div style="width: 60px; height: 60px; background-color: var(--secondary); color: #000; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class='bx bxs-calendar'></i>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Semester Aktif</div>
                            <div style="font-size: 1.4rem; font-weight: 900; line-height: 1.2;">GENAP 2026</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 20px; background-color: var(--card);">
                        <div style="width: 60px; height: 60px; background-color: #4CAF50; color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            <i class='bx bxs-check-shield'></i>
                        </div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Status Akun</div>
                            <div style="font-size: 1.4rem; font-weight: 900; line-height: 1.2; color: #4CAF50;">AKTIF</div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 4px solid var(--border); padding-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 1.8rem;"><i class='bx bxs-grid-alt'></i> Daftar Kelas Anda</h2>
                    @if (strtolower($role) === 'murid')
                        <a href="{{ route('enroll.index') }}" class="neo-btn" style="padding: 8px 16px; font-size: 0.9rem;">+ Cari Kelas Lain</a>
                    @endif
                </div>
                
                @if (count($daftarKelas) > 0)
                    <div class="neo-grid">
                        @foreach ($daftarKelas as $kelas)
                            @php
                                $progress = 0;
                                if (strtolower($role) === 'murid') {
                                    $total_item = $kelas['total_tugas_kelas'] + $kelas['total_kuis_kelas'];
                                    $selesai_item = $kelas['tugas_selesai'] + $kelas['kuis_selesai'];
                                    if ($total_item > 0) {
                                        $progress = round(($selesai_item / $total_item) * 100);
                                        if ($progress > 100) $progress = 100;
                                    } else {
                                        $progress = 100;
                                    }
                                }
                            @endphp
                            <div class="neo-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                        <span class="badge-kode" style="margin: 0; background-color: var(--secondary); color: #000; border: 2px solid #000;">
                                            KODE: KLS-{{ $kelas['id_kelas'] }}
                                        </span>
                                        <i class='bx bx-code-block' style="font-size: 1.8rem; color: var(--muted-foreground);"></i>
                                    </div>
                                    
                                    <h3 style="font-size: 1.4rem; margin-bottom: 10px; line-height: 1.3;">{{ $kelas['nama_kelas'] }}</h3>
                                    
                                    @if (isset($kelas['nama_pengajar']))
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9em; margin-bottom: 12px; color: var(--foreground); background-color: var(--muted); padding: 6px 10px; border: 2px solid var(--border);">
                                            <i class='bx bxs-user-Voice' style="font-size: 1.2rem; color: var(--primary);"></i>
                                            <span>Pengajar: <strong>{{ $kelas['nama_pengajar'] ?? 'Belum ditentukan' }}</strong></span>
                                        </div>
                                    @endif

                                    <p style="font-size: 0.95em; margin-bottom: 20px; color: var(--muted-foreground); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $kelas['deskripsi'] }}
                                    </p>
                                </div>
                                
                                <div>
                                    @if (strtolower($role) === 'murid')
                                        <!-- Progress Bar Minimalis -->
                                        <div style="margin-bottom: 16px;">
                                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; margin-bottom: 4px; text-transform: uppercase;">
                                                <span>Progress Belajar</span>
                                                <span>{{ $progress }}%</span>
                                            </div>
                                            <div style="width: 100%; height: 12px; background-color: var(--background); border: 2px solid var(--border); overflow: hidden;">
                                                <div class="progress-fill" data-target="{{ $progress }}%" style="width: 0%; height: 100%; background-color: #4CAF50; transition: width 1.2s cubic-bezier(0.22, 1, 0.36, 1);"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <a href="{{ route('kelas.show', ['id' => $kelas['id_kelas']]) }}" class="neo-btn" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
                                        <span>MASUK KELAS</span> <i class='bx bx-right-arrow-alt' style="font-size: 1.3rem;"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="neo-box" style="text-align: center; padding: 50px 20px;">
                        <i class='bx bx-folder-open' style="font-size: 4rem; color: var(--muted-foreground); margin-bottom: 15px;"></i>
                        <h3 style="font-size: 1.5rem; margin-bottom: 10px;">Belum Ada Kelas</h3>
                        <p style="color: var(--muted-foreground); margin-bottom: 25px; font-weight: 500;">Anda belum terdaftar atau belum memiliki kelas apa pun saat ini.</p>
                        @if (strtolower($role) === 'murid')
                            <a href="{{ route('enroll.index') }}" class="neo-btn" style="font-size: 1.1rem; padding: 12px 24px;">Cari Kelas Sekarang &rarr;</a>
                        @endif
                    </div>
                @endif

            @endif

        </div>
    </main>
@endsection

@section('scripts')
<script>
    // Animasi Progress Bar saat halaman selesai dimuat
    document.addEventListener("DOMContentLoaded", () => {
        setTimeout(() => {
            const bars = document.querySelectorAll('.progress-fill');
            bars.forEach(bar => {
                bar.style.width = bar.getAttribute('data-target');
            });
        }, 150);
    });
</script>
@endsection
