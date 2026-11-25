@extends('layouts.app')

@section('title', 'Dashboard - E-Learning Programming')

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">

            @if (session('pesan'))
                <div class="auth-alert {{ session('tipe_pesan') === 'error' ? 'auth-alert--error' : 'auth-alert--success' }}" role="{{ session('tipe_pesan') === 'error' ? 'alert' : 'status' }}">
                    <i class='bx {{ session('tipe_pesan') === 'error' ? 'bxs-error-alt' : 'bxs-check-circle' }}' aria-hidden="true"></i>
                    <span>{{ session('pesan') }}</span>
                </div>
            @elseif (request()->query('msg') == 'deleted' || session('msg') == 'deleted')
                <div class="auth-alert auth-alert--error" role="status">
                    <i class='bx bxs-trash' aria-hidden="true"></i>
                    <span><strong>Pesan:</strong> Akun pengguna berhasil dihapus secara permanen.</span>
                </div>
            @elseif (request()->query('msg') == 'updated' || session('msg') == 'updated')
                <div class="auth-alert auth-alert--success" role="status">
                    <i class='bx bxs-check-circle' aria-hidden="true"></i>
                    <span><strong>Berhasil:</strong> Data pengguna telah berhasil diperbarui.</span>
                </div>
            @endif

            @if (strtolower($role) === 'admin')
                <!-- ==========================================
                     TAMPILAN KHUSUS ADMIN: MANAJEMEN PENGGUNA & KELAS
                     ========================================== -->

                <div class="neo-box" style="background-color: var(--card); border-left: 12px solid #DC2626; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h1 style="font-size: clamp(1.5rem, 3.2vw, 2.15rem); margin-bottom: 6px; font-weight: 900;">
                            SELAMAT DATANG, <span style="background-color: var(--secondary); color: #000; padding: 2px 10px; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-block; transform: rotate(-1deg);">{{ strtoupper($username) }}</span>
                        </h1>
                        <p style="margin: 0; font-size: 1rem; color: var(--muted-foreground); font-weight: 600;">
                            Kelola data pengajar, murid, kelas, dan verifikasi pembayaran Pro.
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <a href="{{ route('admin.langganan.index') }}" class="neo-btn neo-btn--pro" style="min-height: 44px; padding: 8px 14px; font-size: 0.85rem;">
                            <i class='bx bx-credit-card' aria-hidden="true"></i> Verifikasi Pro
                        </a>
                        <span class="badge-kode" style="margin: 0; font-size: 0.9rem; padding: 8px 14px; background-color: var(--accent); color: #FFF; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                            <i class='bx bxs-shield-alt-2' aria-hidden="true"></i> ROLE: {{ strtoupper($role) }}
                        </span>
                    </div>
                </div>

                <!-- SUMMARY CARDS ADMIN (4 Kolom Penuh — Bebas Ruang Kosong) -->
                <div class="neo-grid-4" style="margin-bottom: 32px;">
                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: #DC2626; color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-user-voice' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Pengajar</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1.05;">{{ count($listPengajar) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: var(--secondary); color: #000; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-graduation' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Murid</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1.05;">{{ count($listMurid) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: var(--accent); color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-group' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Pengguna</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1.05;">{{ count($listPengajar) + count($listMurid) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: #6D28D9; color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-book-content' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Total Kelas</div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1.05;">{{ count($listKelas) }}</div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Manajemen Pengguna Sistem</h2>
                    <a href="{{ route('admin.users.create') }}" class="neo-btn" style="min-height: 44px; background-color: var(--accent); color: var(--accent-foreground);">
                        <i class='bx bx-user-plus' aria-hidden="true"></i> Tambah Pengguna Baru
                    </a>
                </div>

                <!-- Tabel Data Pengajar -->
                <div class="neo-box">
                    <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class='bx bxs-user-voice' aria-hidden="true"></i> Data Pengajar (Dosen)
                    </h3>
                    <div style="overflow-x: auto;">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>NIDN</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th style="width: 190px; text-align: center;">Aksi</th>
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
                                                <form method="POST" action="{{ route('admin.users.delete', $p['id_user']) }}" class="js-confirm-form" data-confirm-title="Hapus Pengajar" data-confirm-message="Yakin ingin menghapus pengajar {{ $p['nama_pengajar'] }}?" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="neo-btn neo-btn--danger" style="padding: 6px 12px; font-size: 0.8rem;">Hapus</button>
                                                </form>
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
                    <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 8px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                        <i class='bx bxs-graduation' aria-hidden="true"></i> Data Murid (Mahasiswa)
                    </h3>
                    <div style="overflow-x: auto;">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama Lengkap</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th style="width: 190px; text-align: center;">Aksi</th>
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
                                                <form method="POST" action="{{ route('admin.users.delete', $m['id_user']) }}" class="js-confirm-form" data-confirm-title="Hapus Murid" data-confirm-message="Yakin ingin menghapus murid {{ $m['nama_murid'] }}?" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="neo-btn neo-btn--danger" style="padding: 6px 12px; font-size: 0.8rem;">Hapus</button>
                                                </form>
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

                <!-- Manajemen Kelas (Admin CRUD) -->
                <div class="neo-box" style="margin-top: 28px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                        <h3 style="margin: 0; display: flex; align-items: center; gap: 8px;">
                            <i class='bx bxs-book-content' aria-hidden="true"></i> Manajemen Kelas ({{ count($listKelas) }})
                        </h3>
                        <button type="button" class="neo-btn" style="min-height: 44px; padding: 6px 14px; font-size: 0.85rem; background-color: var(--accent); color: var(--accent-foreground);" onclick="const f = document.getElementById('form-tambah-kelas'); f.style.display = f.style.display === 'none' ? 'block' : 'none';">
                            <i class='bx bx-plus' aria-hidden="true"></i> Buat Kelas Baru
                        </button>
                    </div>

                    <form id="form-tambah-kelas" method="POST" action="{{ route('admin.kelas.store') }}" style="display: none; margin-bottom: 20px; padding: 16px; border: 3px solid var(--border); background-color: var(--muted);">
                        @csrf
                        <div class="neo-grid-2" style="gap: 12px; margin-bottom: 12px;">
                            <div>
                                <label class="neo-label" for="new-kelas-nama">Nama Kelas</label>
                                <input type="text" id="new-kelas-nama" name="nama_kelas" class="neo-input" maxlength="100" placeholder="Contoh: Pemrograman Web Laravel" required>
                            </div>
                            <div>
                                <label class="neo-label" for="new-kelas-nidn">Dosen Pengampu</label>
                                <select id="new-kelas-nidn" name="nidn" class="neo-input">
                                    <option value="">-- Belum ditentukan --</option>
                                    @foreach ($listPengajar as $p)
                                        <option value="{{ $p['nidn'] }}">{{ $p['nama_pengajar'] }} ({{ $p['nidn'] }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <label class="neo-label" for="new-kelas-desc">Deskripsi Kelas</label>
                            <textarea id="new-kelas-desc" name="deskripsi" rows="2" class="neo-input" maxlength="2000" placeholder="Deskripsi singkat materi kelas..."></textarea>
                        </div>
                        <div style="margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" id="new-kelas-premium" name="is_premium" value="1" style="width: 18px; height: 18px;">
                            <label for="new-kelas-premium" style="font-weight: 800; cursor: pointer;">Kelas Premium (Wajib Langganan Pro)</label>
                        </div>
                        <button type="submit" class="neo-btn" style="background-color: var(--secondary); color: #000;">Simpan Kelas Baru</button>
                    </form>

                    <div style="overflow-x: auto;">
                        <table class="neo-table">
                            <thead>
                                <tr>
                                    <th>Kode &amp; Tier</th>
                                    <th>Nama Kelas &amp; Deskripsi</th>
                                    <th>Pengajar</th>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="width: 220px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($listKelas as $k)
                                    <tr>
                                        <td>
                                            <span class="badge-kode" style="margin:0;">KLS-{{ $k['id_kelas'] }}</span>
                                            <div style="margin-top: 8px;">
                                                <label style="font-size: 0.78rem; font-weight: 900; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                                    <input type="checkbox" form="edit-kelas-{{ $k['id_kelas'] }}" name="is_premium" value="1" {{ !empty($k['is_premium']) ? 'checked' : '' }}>
                                                    <span class="neo-status-pill {{ !empty($k['is_premium']) ? 'neo-status-pill--pro' : 'neo-status-pill--success' }}" style="padding: 2px 6px; font-size: 0.7rem;">
                                                        {{ !empty($k['is_premium']) ? 'PRO' : 'GRATIS' }}
                                                    </span>
                                                </label>
                                            </div>
                                            <form method="POST" action="{{ route('admin.kelas.update', $k['id_kelas']) }}" id="edit-kelas-{{ $k['id_kelas'] }}" style="display: none;">
                                                @csrf
                                            </form>
                                        </td>
                                        <td>
                                            <input type="text" form="edit-kelas-{{ $k['id_kelas'] }}" name="nama_kelas" class="neo-input" value="{{ $k['nama_kelas'] }}" maxlength="100" required aria-label="Nama kelas {{ $k['nama_kelas'] }}" style="margin-bottom: 6px; padding: 6px 8px; font-weight: bold;">
                                            <input type="text" form="edit-kelas-{{ $k['id_kelas'] }}" name="deskripsi" class="neo-input" value="{{ $k['deskripsi'] }}" maxlength="2000" placeholder="Deskripsi..." aria-label="Deskripsi kelas {{ $k['nama_kelas'] }}" style="margin-bottom: 0; padding: 6px 8px; font-size: 0.85rem;">
                                        </td>
                                        <td>
                                            <select form="edit-kelas-{{ $k['id_kelas'] }}" name="nidn" class="neo-input" aria-label="Pengajar kelas {{ $k['nama_kelas'] }}" style="margin-bottom: 0; padding: 6px 8px;">
                                                <option value="">-- Belum ditentukan --</option>
                                                @foreach ($listPengajar as $p)
                                                    <option value="{{ $p['nidn'] }}" {{ $k['nidn'] === $p['nidn'] ? 'selected' : '' }}>{{ $p['nama_pengajar'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="text-align: center; font-weight: 900;">{{ $k['jumlah_murid'] }}</td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <a href="{{ route('kelas.show', ['id' => $k['id_kelas']]) }}" class="neo-btn" style="padding: 6px 10px; font-size: 0.78rem; background-color: var(--muted); color: var(--foreground); margin-right: 4px;">Buka</a>
                                            <button type="submit" form="edit-kelas-{{ $k['id_kelas'] }}" class="neo-btn" style="padding: 6px 10px; font-size: 0.78rem; background-color: var(--secondary); color: #000; margin-right: 4px;">Simpan</button>
                                            <form method="POST" action="{{ route('admin.kelas.delete', $k['id_kelas']) }}" class="js-confirm-form" data-confirm-title="Hapus Kelas" data-confirm-message="Yakin ingin menghapus kelas {{ $k['nama_kelas'] }} beserta materi/tugas di dalamnya?" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="neo-btn neo-btn--danger" style="padding: 6px 10px; font-size: 0.78rem;">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" style="text-align: center;">Belum ada kelas terdaftar.</td></tr>
                                @endforelse
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
                        .neo-navbar, .neo-site-footer, .pimpinan-no-print, #custom-cursor { display: none !important; }
                        body, .neo-layout-container, .neo-content-inner { background: #fff !important; color: #000 !important; padding: 0 !important; margin: 0 !important; }
                        .neo-box { box-shadow: none !important; border: 2px solid #000 !important; page-break-inside: avoid; }
                        .pimpinan-stat-grid { grid-template-columns: repeat(4, 1fr) !important; }
                    }
                    .pimpinan-stat-grid {
                        display: grid;
                        grid-template-columns: repeat(4, minmax(0, 1fr));
                        gap: 20px;
                        margin-bottom: 32px;
                    }
                    @media (max-width: 1024px) { .pimpinan-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
                    @media (max-width: 520px) { .pimpinan-stat-grid { grid-template-columns: 1fr; } }
                    .pimpinan-stat-card {
                        border: 3px solid var(--border);
                        box-shadow: 6px 6px 0px var(--shadow-color);
                        padding: 22px 18px;
                        display: flex;
                        align-items: center;
                        gap: 16px;
                        transition: transform 0.15s ease, box-shadow 0.15s ease;
                    }
                    .pimpinan-stat-card:hover {
                        transform: translate(3px, 3px);
                        box-shadow: 3px 3px 0px var(--shadow-color);
                    }
                    .pimpinan-stat-icon {
                        width: 56px;
                        height: 56px;
                        border: 3px solid #000;
                        box-shadow: 3px 3px 0px #000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.85rem;
                        flex-shrink: 0;
                    }
                    .pimpinan-stat-number {
                        font-size: 2.3rem;
                        font-weight: 900;
                        line-height: 1;
                        letter-spacing: -0.03em;
                    }
                    .pimpinan-stat-label {
                        font-size: 0.78rem;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        margin-bottom: 4px;
                    }
                    .pimpinan-print-footer { display: none; }
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
                    <div class="neo-box" style="background-color: var(--card); border-left: 12px solid #6D28D9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 28px;">
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted-foreground); margin-bottom: 4px;">
                                SISTEM INFORMASI E-LEARNING PROGRAMMING
                            </div>
                            <h1 style="font-size: clamp(1.45rem, 3vw, 2rem); margin: 0 0 6px 0; font-weight: 900; text-transform: uppercase;">
                                LAPORAN EKSEKUTIF — <span style="background-color: var(--secondary); color: #000; padding: 2px 10px; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-block; transform: rotate(-1deg);">{{ strtoupper($username) }}</span>
                            </h1>
                            <p style="margin: 0; font-size: 0.95rem; color: var(--muted-foreground); font-weight: 600;">
                                Ringkasan data akademik sistem secara real-time &bull; Tanggal cetak: <strong>{{ date('d F Y, H:i') }} WIB</strong>
                            </p>
                        </div>
                        <div class="pimpinan-no-print" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <span class="badge-kode" style="margin: 0; font-size: 0.9rem; padding: 8px 14px; background-color: #6D28D9; color: #FFF; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                                <i class='bx bxs-crown' aria-hidden="true"></i> ROLE: PIMPINAN
                            </span>
                            <button type="button" onclick="window.print()" class="neo-btn brutal-hover" style="min-height: 44px; background-color: var(--secondary); color: #000; border: 3px solid #000; box-shadow: 4px 4px 0px #000; font-size: 0.92rem; padding: 10px 18px;">
                                <i class='bx bxs-printer' style="font-size: 1.2rem;" aria-hidden="true"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <div class="pimpinan-stat-grid">
                        <div class="pimpinan-stat-card" style="background-color: #FEF08A; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #FACC15; color: #000;">
                                <i class='bx bxs-book-content' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Kelas Aktif</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statKelas }}">{{ $statKelas }}</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px;">Mata Kuliah Terdaftar</div>
                            </div>
                        </div>

                        <div class="pimpinan-stat-card" style="background-color: #DDD6FE; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #6D28D9; color: #FFF;">
                                <i class='bx bxs-user-voice' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Pengajar</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statPengajar }}">{{ $statPengajar }}</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px;">Dosen Aktif Mengajar</div>
                            </div>
                        </div>

                        <div class="pimpinan-stat-card" style="background-color: #BBF7D0; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #047857; color: #FFF;">
                                <i class='bx bxs-graduation' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Mahasiswa</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statMurid }}">{{ $statMurid }}</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px;">Murid Terdaftar Sistem</div>
                            </div>
                        </div>

                        <div class="pimpinan-stat-card" style="background-color: #FED7AA; color: #000;">
                            <div class="pimpinan-stat-icon" style="background-color: #C2410C; color: #FFF;">
                                <i class='bx bxs-file-import' aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="pimpinan-stat-label">Total Pengumpulan</div>
                                <div class="pimpinan-stat-number" data-count="{{ $statSubmission }}">{{ $statSubmission }}</div>
                                <div style="font-size: 0.75rem; font-weight: 700; margin-top: 2px;">Tugas Dikumpulkan</div>
                            </div>
                        </div>
                    </div>

                    <!-- RINGKASAN KEUANGAN LANGGANAN PRO -->
                    <div class="neo-box" style="margin-bottom: 28px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 18px; flex-wrap: wrap; gap: 8px;">
                            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 900; text-transform: uppercase; display: flex; align-items: center; gap: 8px;">
                                <i class='bx bxs-crown' style="color: #D97706;" aria-hidden="true"></i> Ringkasan Keuangan Langganan Pro
                            </h3>
                            <a href="{{ route('laporan.index') }}" class="pimpinan-no-print auth-link auth-link--neutral" style="min-height: 36px; font-size: 0.85rem;">Buka Laporan Lengkap</a>
                        </div>
                        <div class="pimpinan-stat-grid" style="margin-bottom: 0;">
                            <div class="pimpinan-stat-card" style="background-color: #EDE9FE; color: #000;">
                                <div>
                                    <div class="pimpinan-stat-label">Pendapatan Disetujui</div>
                                    <div style="font-size: 1.55rem; font-weight: 900; line-height: 1.15;">Rp {{ number_format($statProRevenue, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="pimpinan-stat-card" style="background-color: #DCFCE7; color: #000;">
                                <div>
                                    <div class="pimpinan-stat-label">Member Pro Aktif</div>
                                    <div style="font-size: 1.85rem; font-weight: 900; line-height: 1.1;">{{ $statProAktif }}</div>
                                </div>
                            </div>
                            <div class="pimpinan-stat-card" style="background-color: #FEF9C3; color: #000;">
                                <div>
                                    <div class="pimpinan-stat-label">Menunggu Verifikasi</div>
                                    <div style="font-size: 1.85rem; font-weight: 900; line-height: 1.1;">{{ $statProPending }}</div>
                                </div>
                            </div>
                            <div class="pimpinan-stat-card" style="background-color: #FEE2E2; color: #000;">
                                <div>
                                    <div class="pimpinan-stat-label">Pembayaran Ditolak</div>
                                    <div style="font-size: 1.85rem; font-weight: 900; line-height: 1.1;">{{ $statProDitolak }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 28px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid var(--border); padding-bottom: 12px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 900; text-transform: uppercase;">
                                <i class='bx bxs-table' aria-hidden="true"></i> Rekap Data Seluruh Kelas
                            </h3>
                            <span style="font-size: 0.8rem; font-weight: 800; background-color: var(--muted); padding: 4px 10px; border: 2px solid var(--border);">
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
                                                <td style="font-weight: 800;">
                                                    {{ $rk['nama_kelas'] }}
                                                    @if (!empty($rk['is_premium']))
                                                        <span class="neo-status-pill neo-status-pill--pro" style="margin-left: 6px; padding: 1px 6px; font-size: 0.68rem;">PRO</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($rk['nama_pengajar'])
                                                        <span style="font-weight: 600;">{{ $rk['nama_pengajar'] }}</span>
                                                    @else
                                                        <span style="color: var(--muted-foreground); font-style: italic;">Belum ditentukan</span>
                                                    @endif
                                                </td>
                                                <td style="text-align: center;">
                                                    <span style="font-weight: 900; font-size: 1rem; background-color: #BBF7D0; color: #000; padding: 2px 10px; border: 2px solid #000; display: inline-block;">
                                                        {{ $rk['jumlah_murid'] }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center; font-weight: 800;">{{ $rk['jumlah_materi'] }}</td>
                                                <td style="text-align: center; font-weight: 800;">{{ $rk['jumlah_tugas'] }}</td>
                                                <td style="text-align: center; font-weight: 800;">{{ $rk['jumlah_kuis'] }}</td>
                                                <td style="text-align: center;">
                                                    @if ($rk['jumlah_murid'] > 0)
                                                        <span class="neo-status-pill neo-status-pill--success">Aktif</span>
                                                    @else
                                                        <span class="neo-status-pill neo-status-pill--neutral">Kosong</span>
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
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        if (!window.matchMedia('(prefers-reduced-motion: no-preference)').matches) return;
                        document.querySelectorAll('.pimpinan-stat-number[data-count]').forEach(el => {
                            const target = parseInt(el.getAttribute('data-count'), 10);
                            if (!target || target <= 0) return;
                            const start = performance.now();
                            const step = (now) => {
                                const t = Math.min(1, (now - start) / 650);
                                el.textContent = Math.round(target * (1 - Math.pow(1 - t, 3)));
                                if (t < 1) requestAnimationFrame(step);
                            };
                            requestAnimationFrame(step);
                        });
                    });
                </script>

            @else
                <!-- ==========================================
                     TAMPILAN MURID & PENGAJAR: DAFTAR KELAS
                     ========================================== -->

                <div class="neo-box" style="background-color: var(--card); border-left: 12px solid #DC2626; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <h1 style="font-size: clamp(1.5rem, 3.2vw, 2.15rem); margin-bottom: 6px; font-weight: 900;">
                            SELAMAT DATANG, <span style="background-color: var(--secondary); color: #000; padding: 2px 10px; border: 2px solid #000; box-shadow: 3px 3px 0px #000; display: inline-block; transform: rotate(-1deg);">{{ strtoupper($username) }}</span>
                        </h1>
                        <p style="margin: 0; font-size: 1rem; color: var(--muted-foreground); font-weight: 600;">
                            Siap melanjutkan progres pembelajaran Anda hari ini?
                        </p>
                    </div>
                    <div>
                        <span class="badge-kode" style="margin: 0; font-size: 0.9rem; padding: 8px 14px; background-color: var(--accent); color: #FFF; border: 2px solid #000; box-shadow: 3px 3px 0px #000;">
                            <i class='bx bxs-id-card' aria-hidden="true"></i> ROLE: {{ strtoupper($role) }}
                        </span>
                    </div>
                </div>

                <!-- SUMMARY CARDS (3 Kolom Penuh — Bebas Ruang Kosong) -->
                <div class="neo-grid-3" style="margin-bottom: 32px;">
                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: #DC2626; color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-book-bookmark' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">
                                {{ strtolower($role) === 'murid' ? 'Total Kelas Diikuti' : 'Total Kelas Diampu' }}
                            </div>
                            <div style="font-size: 2rem; font-weight: 900; line-height: 1.05;">{{ count($daftarKelas) }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: var(--secondary); color: #000; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-calendar' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Semester Aktif</div>
                            <div style="font-size: 1.35rem; font-weight: 900; line-height: 1.2;">{{ (date('n') <= 6 ? 'GENAP ' : 'GANJIL ') . date('Y') }}</div>
                        </div>
                    </div>

                    <div class="neo-box" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px; background-color: var(--card);">
                        <div style="width: 58px; height: 58px; flex-shrink: 0; background-color: #047857; color: #FFF; border: 3px solid var(--border); box-shadow: 4px 4px 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            <i class='bx bxs-check-shield' aria-hidden="true"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 800; color: var(--muted-foreground); text-transform: uppercase;">Status Akun</div>
                            @if (strtolower($role) === 'murid')
                                @if (!empty($activeSubscription))
                                    <div style="margin-top: 4px;">
                                        <span class="neo-status-pill neo-status-pill--success"><i class='bx bxs-crown' aria-hidden="true"></i> Pro Aktif</span>
                                    </div>
                                    <small style="display: block; margin-top: 4px; font-weight: 700; color: var(--muted-foreground);">s/d {{ $activeSubscription->tgl_berakhir->format('d M Y') }}</small>
                                @else
                                    <div style="font-size: 1.2rem; font-weight: 900; line-height: 1.2;">AKUN GRATIS</div>
                                    <a href="{{ route('langganan.index') }}" class="auth-link" style="min-height: 32px; padding: 2px 0; font-size: 0.82rem;">Upgrade ke Pro</a>
                                @endif
                            @else
                                <div style="margin-top: 4px;">
                                    <span class="neo-status-pill neo-status-pill--success">Aktif</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 4px solid var(--border); padding-bottom: 10px;">
                    <h2 style="margin: 0; font-size: clamp(1.35rem, 2.5vw, 1.75rem); display: flex; align-items: center; gap: 8px;">
                        <i class='bx bxs-grid-alt' aria-hidden="true"></i> Daftar Kelas Anda
                    </h2>
                    @if (strtolower($role) === 'murid')
                        <a href="{{ route('enroll.index') }}" class="neo-btn" style="min-height: 44px; padding: 8px 16px; font-size: 0.9rem;">
                            <i class='bx bx-plus' aria-hidden="true"></i> Cari Kelas Lain
                        </a>
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
                                        $progress = min(100, round(($selesai_item / $total_item) * 100));
                                    }
                                }
                                $isLockedPro = strtolower($role) === 'murid' && !empty($kelas['is_premium']) && empty($activeSubscription);
                            @endphp
                            <div class="neo-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px; flex-wrap: wrap;">
                                        <span class="badge-kode" style="margin: 0; background-color: var(--secondary); color: #000; border: 2px solid #000;">
                                            KODE: KLS-{{ $kelas['id_kelas'] }}
                                        </span>
                                        @if (!empty($kelas['is_premium']))
                                            <span class="neo-status-pill neo-status-pill--pro"><i class='bx bxs-crown' aria-hidden="true"></i> Pro</span>
                                        @else
                                            <span class="neo-status-pill neo-status-pill--success">Gratis</span>
                                        @endif
                                    </div>

                                    <h3 style="font-size: 1.35rem; margin-bottom: 10px; line-height: 1.3;">{{ $kelas['nama_kelas'] }}</h3>

                                    @if (isset($kelas['nama_pengajar']))
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9em; margin-bottom: 12px; color: var(--foreground); background-color: var(--muted); padding: 6px 10px; border: 2px solid var(--border);">
                                            <i class='bx bxs-user-voice' style="font-size: 1.15rem; color: var(--accent);" aria-hidden="true"></i>
                                            <span>Pengajar: <strong>{{ $kelas['nama_pengajar'] ?? 'Belum ditentukan' }}</strong></span>
                                        </div>
                                    @endif

                                    <p style="font-size: 0.94em; margin-bottom: 20px; color: var(--muted-foreground); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $kelas['deskripsi'] }}
                                    </p>
                                </div>

                                <div>
                                    @if (strtolower($role) === 'murid')
                                        <div style="margin-bottom: 16px;">
                                            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; margin-bottom: 4px; text-transform: uppercase;">
                                                <span>Progress Belajar</span>
                                                <span>{{ $progress }}%</span>
                                            </div>
                                            <div style="width: 100%; height: 12px; background-color: var(--background); border: 2px solid var(--border); overflow: hidden;">
                                                <div class="progress-fill" data-target="{{ $progress }}%" style="width: {{ $progress }}%; height: 100%; background-color: #047857;"></div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($isLockedPro)
                                        <a href="{{ route('langganan.index') }}" class="neo-btn neo-btn--pro" style="min-height: 44px; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
                                            <i class='bx bxs-lock-alt' aria-hidden="true"></i> Aktifkan Pro untuk Akses
                                        </a>
                                    @else
                                        <a href="{{ route('kelas.show', ['id' => $kelas['id_kelas']]) }}" class="neo-btn" style="min-height: 44px; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;">
                                            <span>Masuk Kelas</span> <i class='bx bx-right-arrow-alt' style="font-size: 1.25rem;" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="neo-box" style="text-align: center; padding: 44px 20px;">
                        <i class='bx bx-folder-open' style="font-size: 3.5rem; color: var(--muted-foreground); margin-bottom: 12px;" aria-hidden="true"></i>
                        <h3 style="font-size: 1.45rem; margin-bottom: 10px;">Belum Ada Kelas</h3>
                        <p style="color: var(--muted-foreground); margin-bottom: 22px; font-weight: 500;">Anda belum terdaftar atau belum memiliki kelas apa pun saat ini.</p>
                        @if (strtolower($role) === 'murid')
                            <a href="{{ route('enroll.index') }}" class="neo-btn" style="min-height: 48px; font-size: 1rem; padding: 12px 24px;">Cari Kelas Sekarang</a>
                        @endif
                    </div>
                @endif

            @endif

        </div>
    </main>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
            const bars = document.querySelectorAll('.progress-fill');
            bars.forEach(bar => {
                const target = bar.getAttribute('data-target') || '0%';
                bar.style.width = '0%';
                bar.style.transition = 'width 0.85s cubic-bezier(0.22, 1, 0.36, 1)';
                requestAnimationFrame(() => { bar.style.width = target; });
            });
        }

        document.querySelectorAll('.js-confirm-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                NeoConfirm(
                    this.dataset.confirmTitle || 'Konfirmasi',
                    this.dataset.confirmMessage || 'Yakin melanjutkan tindakan ini?',
                    () => this.submit()
                );
            });
        });
    });
</script>
@endsection
