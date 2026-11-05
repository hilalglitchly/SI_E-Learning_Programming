@extends('layouts.app')

@section('title', ($kelasInfo['nama_kelas'] ?? 'Detail Kelas') . ' - E-Learning')

@section('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Neo Brutalism Classroom Styles */
        body {
            --classroom-bg: var(--background);
            --classroom-accent: var(--primary);
            --brutal-border: 3px solid var(--border);
            --brutal-shadow: 5px 5px 0px var(--shadow-color);
            background-color: var(--classroom-bg);
        }

        /* TABS */
        .classroom-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: var(--brutal-border);
            padding-bottom: 1rem;
            overflow-x: auto;
        }

        .brutal-tab {
            background-color: var(--card);
            border: var(--brutal-border);
            padding: 0.8rem 1.5rem;
            font-weight: bold;
            font-size: 1.1rem;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: var(--brutal-shadow);
            transition: all 0.15s ease-in-out;
            color: var(--card-foreground);
            text-decoration: none;
            white-space: nowrap;
        }

        .brutal-tab.active {
            background-color: var(--classroom-accent);
            color: var(--primary-foreground);
        }

        .brutal-tab:hover, .brutal-hover:hover {
            box-shadow: 0px 0px 0px var(--border) !important;
            transform: translate(5px, 5px) !important;
        }

        /* BANNER */
        .classroom-banner {
            background-color: var(--classroom-accent);
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            position: relative;
            border-radius: 0;
            transition: all 0.15s ease-in-out;
        }

        .classroom-banner:hover {
            box-shadow: 0px 0px 0px var(--border);
            transform: translate(5px, 5px);
        }

        .banner-title {
            font-size: 2.5rem;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: var(--primary-foreground);
            letter-spacing: -1px;
        }

        .banner-subtitle {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-foreground);
        }

        /* LAYOUT: 2 COLUMNS */
        .classroom-layout {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        .col-left {
            width: 25%;
            min-width: 250px;
        }

        .col-right {
            width: 75%;
        }

        @media (max-width: 768px) {
            .classroom-layout {
                flex-direction: column;
            }
            .col-left, .col-right {
                width: 100%;
            }
        }

        /* CARDS */
        .brutal-card {
            background-color: var(--card);
            color: var(--card-foreground);
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.15s ease-in-out;
        }

        .brutal-card:hover {
            box-shadow: 0px 0px 0px var(--border);
            transform: translate(5px, 5px);
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border);
            padding-bottom: 0.5rem;
        }

        /* TUGAS ITEM */
        .tugas-item {
            margin-bottom: 1rem;
        }
        
        .tugas-item-title {
            font-weight: bold;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }

        .tugas-item-deadline {
            font-size: 0.85rem;
            color: var(--muted-foreground);
            font-weight: bold;
        }

        .btn-small {
            display: inline-block;
            background-color: var(--foreground);
            color: var(--primary-foreground);
            padding: 0.5rem 1rem;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.8rem;
            border: 2px solid var(--border);
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }
        
        .btn-small:hover {
            background-color: var(--classroom-accent);
            color: var(--primary-foreground);
        }

        /* INPUT SHARE */
        .share-input-box {
            background-color: var(--card);
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            color: var(--card-foreground);
        }

        .share-input-box:hover {
            box-shadow: 0px 0px 0px var(--border);
            transform: translate(5px, 5px);
        }

        .share-icon {
            width: 40px;
            height: 40px;
            background-color: var(--foreground);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-foreground);
            font-weight: bold;
            flex-shrink: 0;
        }

        .share-text {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--muted-foreground);
        }

        /* FEED ITEM */
        .feed-item {
            background-color: var(--card);
            color: var(--card-foreground);
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.15s ease-in-out;
        }

        .feed-item:hover {
            box-shadow: 0px 0px 0px var(--border);
            transform: translate(5px, 5px);
        }

        .feed-icon {
            width: 50px;
            height: 50px;
            background-color: var(--classroom-accent);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            box-shadow: 2px 2px 0px var(--border);
        }

        .feed-icon.tugas {
            background-color: var(--primary);
            color: var(--primary-foreground);
        }

        .feed-content {
            flex-grow: 1;
        }

        .feed-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.3rem;
            text-transform: uppercase;
        }

        .feed-date {
            font-size: 0.9rem;
            color: var(--muted-foreground);
            font-weight: bold;
        }
        
        .tab-section {
            display: none;
        }
        
        .tab-section.active {
            display: block;
        }

        /* CUSTOM BUTTONS FOR FORM/STREAM ACTION */
        .btn-accent {
            display: inline-block;
            background-color: var(--accent);
            color: var(--accent-foreground);
            padding: 0.8rem 1.5rem;
            text-transform: uppercase;
            font-weight: bold;
            font-family: inherit;
            font-size: 1rem;
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-align: center;
        }

        .btn-accent:hover {
            background-color: var(--primary);
            color: var(--primary-foreground);
            box-shadow: 0px 0px 0px var(--border) !important;
            transform: translate(5px, 5px) !important;
        }

        .btn-muted {
            display: inline-block;
            background-color: var(--muted);
            color: var(--foreground);
            padding: 0.8rem 1.5rem;
            text-transform: uppercase;
            font-weight: bold;
            font-family: inherit;
            font-size: 1rem;
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-align: center;
        }

        .btn-muted:hover {
            background-color: var(--foreground);
            color: var(--background);
            box-shadow: 0px 0px 0px var(--border) !important;
            transform: translate(5px, 5px) !important;
        }

        /* SUCCESS ALERT */
        .neo-alert-success {
            background-color: #a8e6cf;
            color: black;
            padding: 1rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            border: var(--brutal-border);
            box-shadow: var(--brutal-shadow);
        }

        /* DRAG AND DROP ZONE */
        .neo-drop-zone {
            background-color: var(--card);
            border: 4px dashed var(--border);
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            box-shadow: var(--brutal-shadow);
        }

        .neo-drop-zone.dragover {
            background-color: #A855F7;
            border: 4px solid var(--border);
            transform: scale(1.02);
            color: #FFF !important;
        }

        .neo-drop-zone.dragover #drop-text {
            color: #FFF !important;
        }

        .neo-drop-zone.dragover #drop-icon {
            transform: translateY(-10px) scale(1.2);
            transition: all 0.2s;
            color: #FFF;
        }
    </style>
@endsection

@section('content')
<!-- MAIN CONTENT -->
<main class="neo-layout-container" style="background-color: var(--classroom-bg);">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem; margin-top: 1rem; padding-left: 2rem;">
        <a href="{{ route('dashboard') }}" class="brutal-hover neo-box" style="background-color: var(--classroom-accent); padding: 0.5rem 1rem; text-decoration: none; color: var(--primary-foreground); font-weight: 800; font-size: 0.9rem; display: inline-block;">← KEMBALI</a>
    </div>

    <div class="neo-content-inner" style="max-width: 1200px; padding: 0 2rem 3rem 2rem;">

        @if (!empty($pesan))
            <div class="neo-alert-success brutal-hover">{{ $pesan }}</div>
        @endif

        <!-- PROGRESS BELAJAR (HANYA MURID) -->
        @if (strtolower($role) === 'murid')
        <div class="brutal-card" style="margin-bottom: 2rem; border-left: 10px solid #4CAF50;">
            <h3 style="font-size: 1.2rem; text-transform: uppercase; font-weight: 800; margin-bottom: 0.5rem;">Progress Belajar Anda</h3>
            <div style="background-color: var(--background); border: 3px solid var(--border); height: 30px; position: relative; width: 100%; box-shadow: 3px 3px 0px var(--border);">
                <div style="background-color: #4CAF50; height: 100%; width: {{ $progressValue }}%; transition: width 0.5s ease-in-out;"></div>
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 900; color: var(--foreground); mix-blend-mode: difference; text-shadow: 1px 1px 0px #fff;">
                    {{ $progressValue }}%
                </div>
            </div>
        </div>
        @endif

        <!-- TABS HORIZONTAL -->
        <div class="classroom-tabs">
            <button class="brutal-tab active" onclick="switchClassTab('beranda', this)">Beranda</button>
            <button class="brutal-tab" onclick="switchClassTab('materi', this)">Materi</button>
            <button class="brutal-tab" onclick="switchClassTab('tugas', this)">Tugas</button>
            <button class="brutal-tab" onclick="switchClassTab('kuis', this)">Kuis</button>
            <button class="brutal-tab" onclick="switchClassTab('diskusi', this)">Diskusi</button>
            <button class="brutal-tab" onclick="switchClassTab('anggota', this)">Anggota</button>
            @if ($role === 'Pengajar' || strtolower($role) === 'admin')
            <button id="btn-tab-upload" class="brutal-tab" onclick="switchClassTab('upload', this)">Upload</button>
            <button class="brutal-tab" onclick="switchClassTab('rekap', this)">Rekap Nilai</button>
            @endif
        </div>

        <!-- ==============================
             TAB 1: BERANDA (STREAM/FEED)
             ============================== -->
        <div id="tab-beranda" class="tab-section active">
            <!-- BANNER UTAMA -->
            <div class="classroom-banner">
                <div class="banner-title">{{ $kelasInfo['nama_kelas'] ?? 'Nama Kelas' }}</div>
                <div class="banner-subtitle">Sistem Informasi E-Learning Programming - Kelompok 3</div>
            </div>

            <!-- LAYOUT 2 KOLOM -->
            <div class="classroom-layout">
                
                <!-- KOLOM KIRI (Tugas Mendatang) -->
                <div class="col-left">
                    <div class="brutal-card">
                        <div class="card-title">Tugas Mendatang</div>
                        @if (count($upcomingTugas) > 0)
                            @foreach ($upcomingTugas as $t)
                                <div class="tugas-item">
                                    <div class="tugas-item-title">{{ $t['judul_tugas'] }}</div>
                                    <div class="tugas-item-deadline">Tenggat: {{ $t['deadline'] }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="tugas-item">
                                <div class="tugas-item-deadline">Hore, tidak ada tugas yang perlu segera diselesaikan!</div>
                            </div>
                        @endif
                        <div style="margin-top: 1rem; text-align: right;">
                            <button class="btn-small" onclick="document.querySelectorAll('.brutal-tab')[2].click()">Lihat Semua</button>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (Stream / Feed Utama) -->
                <div class="col-right">
                    
                    <!-- KOTAK PENGUMUMAN / INPUT -->
                    @if ($role === 'Pengajar' || strtolower($role) === 'admin')
                        <div class="share-input-box" onclick="document.getElementById('btn-tab-upload').click()">
                            <div class="share-icon">👤</div>
                            <div class="share-text">Bagikan materi atau tugas baru ke kelas...</div>
                        </div>
                    @else
                        <div class="share-input-box" style="cursor: default;">
                            <div class="share-icon">👤</div>
                            <div class="share-text">Bagikan sesuatu dengan kelas Anda...</div>
                        </div>
                    @endif

                    <!-- FEED VERTIKAL -->
                    @if (count($feedList) > 0)
                        @foreach ($feedList as $feed)
                            <div class="feed-item">
                                @if ($feed['type'] === 'materi')
                                    <div class="feed-icon">📄</div>
                                @else
                                    <div class="feed-icon tugas">📝</div>
                                @endif
                                
                                <div class="feed-content">
                                    <div class="feed-title">
                                        {{ $kelasInfo['nama_pengajar'] ?? 'Pengajar' }} memposting {{ $feed['type'] === 'materi' ? 'materi baru' : 'tugas baru' }}: {{ $feed['judul'] }}
                                    </div>
                                    <div class="feed-date">
                                        {{ $feed['tanggal'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="brutal-card" style="text-align: center; padding: 3rem 1rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                            <h3 style="text-transform: uppercase;">Belum ada postingan</h3>
                            <p>Materi dan tugas yang diposting akan muncul di sini.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- ==============================
             TAB 2: MATERI
             ============================== -->
        <div id="tab-materi" class="tab-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin: 0;">Daftar Materi</h3>
                @if (in_array(strtolower($role), ['pengajar', 'admin', 'pimpinan']))
                    <button class="btn-accent" onclick="toggleUploadMateri()">+ UPLOAD MATERI</button>
                @endif
            </div>

            <!-- Form Upload Materi (Tersembunyi by default) -->
            @if (in_array(strtolower($role), ['pengajar', 'admin', 'pimpinan']))
            <div id="formUploadMateri" class="brutal-card" style="display: none; margin-bottom: 2rem; border-color: var(--primary);">
                <h4 style="text-transform: uppercase; margin-bottom: 1rem; font-size: 1.3rem;">📤 Tambah Materi Baru</h4>
                <form action="{{ route('kelas.post', ['id' => $id_kelas]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="tambah_materi">
                    <input type="hidden" name="hidden_deskripsi_materi" id="hidden_deskripsi_materi">
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Judul Materi</label>
                        <input type="text" name="judul_materi" class="neo-input neo-box" required>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Isi Materi / Deskripsi</label>
                        <div id="editor_materi" style="height: 200px;"></div>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem; text-transform: uppercase;">Lampiran File (Opsional)</label>
                        <div id="drop-zone-materi" class="neo-drop-zone brutal-hover">
                            <div id="drop-icon-materi" style="font-size: 4rem; margin-bottom: 1rem;"><i class='bx bx-cloud-upload'></i></div>
                            <div id="drop-text-materi" style="font-size: 1.2rem; font-weight: 800; color: var(--foreground); margin-bottom: 0.5rem; line-height: 1.5;">Seret & Lepaskan File Materi di Sini<br>Atau Klik untuk Memilih</div>
                            <div id="file-name-display-materi" style="font-size: 1rem; font-weight: 600; color: var(--primary); display: none; background-color: var(--foreground); color: var(--background); padding: 0.5rem; margin-top: 1rem; border: 2px solid var(--border);">Tidak ada file yang dipilih</div>
                            <input type="file" id="file_materi" name="file_materi" style="display: none;" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-accent" style="width: 100%;">SIMPAN MATERI</button>
                </form>
            </div>
            @endif
            @if (count($materiList) > 0)
                @foreach ($materiList as $materi)
                    <div class="brutal-card">
                        <div class="feed-title" style="font-size: 1.5rem; margin-bottom: 0.5rem;">{{ $materi['judul_materi'] }}</div>
                        <div style="font-size: 1rem; margin-bottom: 1rem; line-height: 1.5;" class="ql-editor-content">{!! sanitize_rich_text($materi['deskripsi']) !!}</div>
                        @if (!empty($materi['file_materi']))
                            <div style="margin-bottom: 1rem;">
                                <a href="{{ asset('uploads/materi/' . $materi['file_materi']) }}" target="_blank" class="btn-accent" style="text-decoration: none; padding: 0.5rem 1rem; font-size: 0.9rem;">📥 Unduh File</a>
                            </div>
                        @endif
                        <small style="color: var(--muted-foreground); font-weight: bold;">Diupload: {{ $materi['tgl_upload'] }}</small>
                    </div>
                @endforeach
            @else
                <div class="brutal-card"><p style="font-weight: bold;">Belum ada materi untuk kelas ini.</p></div>
            @endif
        </div>

        <!-- ==============================
             TAB 3: TUGAS
             ============================== -->
        <div id="tab-tugas" class="tab-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin: 0;">Daftar Tugas</h3>
                @if (in_array(strtolower($role), ['pengajar', 'admin', 'pimpinan']))
                    <button class="btn-accent" onclick="toggleUploadTugas()">+ BUAT TUGAS</button>
                @endif
            </div>

            <!-- Form Upload Tugas (Tersembunyi by default) -->
            @if (in_array(strtolower($role), ['pengajar', 'admin', 'pimpinan']))
            <div id="formUploadTugas" class="brutal-card" style="display: none; margin-bottom: 2rem; border-color: var(--primary);">
                <h4 style="text-transform: uppercase; margin-bottom: 1rem; font-size: 1.3rem;">📝 Buat Tugas Baru</h4>
                <form action="{{ route('kelas.post', ['id' => $id_kelas]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="tambah_tugas">
                    <input type="hidden" name="hidden_deskripsi_tugas" id="hidden_deskripsi_tugas">
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Judul Tugas</label>
                        <input type="text" name="judul_tugas" class="neo-input neo-box" required>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Instruksi Tugas</label>
                        <div id="editor_tugas" style="height: 200px;"></div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem;">Tenggat Waktu (Deadline)</label>
                        <input type="datetime-local" name="deadline_tugas" class="neo-input neo-box" required>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <label style="display: block; font-weight: bold; margin-bottom: 0.5rem; text-transform: uppercase;">Lampiran Referensi Tugas (Opsional)</label>
                        <div id="drop-zone-tugas" class="neo-drop-zone brutal-hover">
                            <div id="drop-icon-tugas" style="font-size: 4rem; margin-bottom: 1rem;"><i class='bx bx-cloud-upload'></i></div>
                            <div id="drop-text-tugas" style="font-size: 1.2rem; font-weight: 800; color: var(--foreground); margin-bottom: 0.5rem; line-height: 1.5;">Seret & Lepaskan File Referensi di Sini<br>Atau Klik untuk Memilih</div>
                            <div id="file-name-display-tugas" style="font-size: 1rem; font-weight: 600; color: var(--primary); display: none; background-color: var(--foreground); color: var(--background); padding: 0.5rem; margin-top: 1rem; border: 2px solid var(--border);">Tidak ada file yang dipilih</div>
                            <input type="file" id="file_tugas" name="file_tugas" style="display: none;" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-accent" style="width: 100%;">TERBITKAN TUGAS</button>
                </form>
            </div>
            @endif

            @if (count($tugasList) > 0)
                @foreach ($tugasList as $tugas)
                    <div class="brutal-card" style="border-left: 10px solid var(--primary);">
                        <div class="feed-title" style="font-size: 1.5rem; margin-bottom: 0.5rem;">{{ $tugas['judul_tugas'] }}</div>
                        <div style="font-size: 1rem; margin-bottom: 1rem; line-height: 1.5;" class="ql-editor-content">{!! sanitize_rich_text($tugas['deskripsi']) !!}</div>
                        
                        @if (!empty($tugas['file_tugas']))
                            <div style="margin-bottom: 1rem;">
                                <a href="{{ asset('uploads/tugas/' . $tugas['file_tugas']) }}" target="_blank" class="btn-accent" style="text-decoration: none; padding: 0.5rem 1rem; font-size: 0.9rem; background-color: var(--secondary); color: #000;">📎 Unduh Lampiran Referensi</a>
                            </div>
                        @endif

                        <div style="display: inline-block; background-color: var(--foreground); padding: 0.4rem 0.8rem; font-weight: bold; color: var(--primary-foreground); font-size: 0.9rem; text-transform: uppercase;">
                            Tenggat: {{ $tugas['deadline'] }}
                        </div>
                        @if (in_array($tugas['id_tugas'], $submittedTugasIds))
                            <div style="display: inline-block; margin-left: 1rem; background-color: #4CAF50; padding: 0.4rem 0.8rem; font-weight: bold; color: #fff; font-size: 0.9rem; text-transform: uppercase; border: 2px solid #000; box-shadow: 2px 2px 0px #000;">
                                ✅ SUDAH DIKUMPULKAN
                            </div>
                        @endif
                    </div>
                @endforeach

                <!-- Form Upload (Hanya tampil jika user adalah Murid) -->
                @if (strtolower($role) === 'murid')
                <div class="brutal-card" style="margin-top: 2rem;">
                    <h3 style="text-transform: uppercase; margin-bottom: 1.5rem; font-size: 1.4rem;">📤 Kumpulkan Tugas</h3>
                    <form action="{{ route('kelas.post', ['id' => $id_kelas]) }}" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault(); NeoConfirm('Konfirmasi', 'Yakin ingin mengumpulkan tugas ini?', () => this.submit());">
                        @csrf
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-weight: bold; margin-bottom: 0.5rem; text-transform: uppercase;">Pilih Tugas</label>
                            <select name="id_tugas" class="neo-input neo-box" style="margin-top: 0.5rem;" required>
                                <option value="">-- Pilih Tugas --</option>
                                @foreach ($tugasList as $tugas)
                                    @if (!in_array($tugas['id_tugas'], $submittedTugasIds))
                                        <option value="{{ $tugas['id_tugas'] }}">{{ $tugas['judul_tugas'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom: 2rem;">
                            <label style="display: block; font-weight: bold; margin-bottom: 0.5rem; text-transform: uppercase;">Unggah File Jawaban</label>
                            <div id="drop-zone" class="neo-drop-zone brutal-hover">
                                <div id="drop-icon" style="font-size: 4rem; margin-bottom: 1rem;"><i class='bx bx-cloud-upload'></i></div>
                                <div id="drop-text" style="font-size: 1.2rem; font-weight: 800; color: var(--foreground); margin-bottom: 0.5rem; line-height: 1.5;">Seret & Lepaskan File Jawaban di Sini<br>Atau Klik untuk Memilih</div>
                                <div id="file-name-display" style="font-size: 1rem; font-weight: 600; color: var(--primary); display: none; background-color: var(--foreground); color: var(--background); padding: 0.5rem; margin-top: 1rem; border: 2px solid var(--border);">Tidak ada file yang dipilih</div>
                                <input type="file" id="file_jawaban" name="file_jawaban" required style="display: none;">
                            </div>
                        </div>
                        <button type="submit" class="btn-accent" style="width: 100%; padding: 1rem; font-size: 1.2rem; font-weight: 900;">Kumpulkan Tugas</button>
                    </form>
                </div>
                @endif
            @else
                <div class="brutal-card"><p style="font-weight: bold;">Belum ada tugas untuk kelas ini.</p></div>
            @endif
        </div>

        <!-- ==============================
             TAB 4: ANGGOTA
             ============================== -->
        <div id="tab-anggota" class="tab-section">
            <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem;">Pengajar</h3>
            <div class="brutal-card" style="display: flex; align-items: center; gap: 1rem;">
                <div class="share-icon" style="background-color: var(--classroom-accent); color: var(--primary-foreground); border: 3px solid var(--border); box-shadow: var(--shadow-offset-x) var(--shadow-offset-y) var(--shadow-blur) var(--shadow-color);">👨‍🏫</div>
                <div>
                    <div style="font-size: 1.2rem; font-weight: bold;">{{ $kelasInfo['nama_pengajar'] ?? 'Belum Ditentukan' }}</div>
                    <div style="font-size: 0.9rem; color: var(--muted-foreground); font-weight: bold;">{{ $kelasInfo['email_pengajar'] ?? '' }}</div>
                </div>
            </div>

            <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem; margin-top: 2rem;">Teman Sekelas ({{ count($pesertaList) }})</h3>
            @if (count($pesertaList) > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                    @foreach ($pesertaList as $p)
                        <div class="brutal-card" style="display: flex; align-items: center; gap: 1rem; padding: 1rem;">
                            <div class="share-icon" style="background-color: var(--primary-foreground); color: var(--foreground); border: 3px solid var(--border); box-shadow: var(--shadow-offset-x) var(--shadow-offset-y) var(--shadow-blur) var(--shadow-color);">👤</div>
                            <div>
                                <div style="font-weight: bold; font-size: 1.1rem;">{{ $p['nama_murid'] }}</div>
                                <div style="font-size: 0.85rem; color: var(--muted-foreground); font-weight: bold;">{{ $p['nim'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="brutal-card"><p style="font-weight: bold;">Belum ada peserta di kelas ini.</p></div>
            @endif
        </div>

        <!-- ==============================
             TAB 5: UPLOAD (ADMIN & PENGAJAR)
             ============================== -->
        @if ($role === 'Pengajar' || strtolower($role) === 'admin')
        <div id="tab-upload" class="tab-section">
            <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem;">Upload Tugas / Materi</h3>
            <div class="brutal-card" style="margin-bottom: 2rem;">
                <form method="POST" action="{{ route('kelas.post', ['id' => $id_kelas]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action" value="post_feed">
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5rem;">Jenis Postingan</label>
                        <select id="jenis_post" name="jenis_post" class="neo-input neo-box" style="font-weight: bold;" onchange="togglePostType()">
                            <option value="materi">Unggah Materi</option>
                            <option value="tugas">Buat Tugas Baru</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5rem;">Judul</label>
                        <input type="text" name="judul" required class="neo-input neo-box">
                    </div>
                    
                    <div style="margin-bottom: 2.5rem;">
                        <label style="display: block; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5rem;">Deskripsi</label>
                        <input type="hidden" name="deskripsi" id="hidden_deskripsi">
                        <div id="editor_deskripsi" style="height: 150px;"></div>
                    </div>
                    
                    <!-- Input Khusus Materi -->
                    <div id="input_materi" style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5rem;">File Materi (Opsional)</label>
                        <input type="file" name="file_materi" class="neo-input neo-box">
                    </div>
                    
                    <!-- Input Khusus Tugas -->
                    <div id="input_tugas" style="margin-bottom: 1.5rem; display: none;">
                        <label style="display: block; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5rem;">Batas Waktu (Deadline)</label>
                        <input type="datetime-local" name="deadline" class="neo-input neo-box">
                    </div>
                    
                    <button type="submit" class="btn-accent" style="width: 100%; padding: 1rem; font-size: 1.2rem; font-weight: 900;">Posting Sekarang</button>
                </form>
            </div>
        </div>
        @endif

        <!-- ==============================
             TAB 6: DISKUSI
             ============================== -->
        <div id="tab-diskusi" class="tab-section">
            <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem;">Forum Diskusi Kelas</h3>
            
            @if (strtolower($role) === 'murid' || $role === 'Pengajar')
            <div class="brutal-card" style="margin-bottom: 2rem;">
                <h4 style="text-transform: uppercase; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Buat Topik Baru</h4>
                <form method="POST" action="{{ route('kelas.post', ['id' => $id_kelas]) }}">
                    @csrf
                    <input type="hidden" name="action" value="post_diskusi">
                    <div style="margin-bottom: 1rem;">
                        <label style="font-weight: bold; text-transform: uppercase;">Judul Diskusi</label>
                        <input type="text" name="judul_diskusi" required class="neo-input neo-box" style="margin-top: 0.5rem;">
                    </div>
                    <div style="margin-bottom: 2.5rem;">
                        <label style="font-weight: bold; text-transform: uppercase;">Isi Diskusi</label>
                        <input type="hidden" name="isi_diskusi" id="hidden_isi_diskusi">
                        <div id="editor_diskusi" style="height: 120px; margin-top: 0.5rem;"></div>
                    </div>
                    <button type="submit" class="btn-accent">Mulai Diskusi</button>
                </form>
            </div>
            @endif

            <div class="diskusi-list">
                @if (count($diskusiList) > 0)
                    @foreach ($diskusiList as $disk)
                        <div class="brutal-card" style="border-left: 8px solid #FF6B6B;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <h4 style="font-size: 1.3rem; font-weight: 800; text-transform: uppercase; margin: 0;">{{ $disk['judul_diskusi'] }}</h4>
                                <span style="font-size: 0.8rem; background-color: var(--muted); padding: 0.2rem 0.6rem; font-weight: bold; color: var(--foreground); border: 2px solid var(--border);">{{ date('d M Y, H:i', strtotime($disk['tgl_post'])) }}</span>
                            </div>
                            <div style="font-size: 0.9rem; font-weight: bold; color: var(--muted-foreground); margin-bottom: 1rem;">
                                Oleh: {{ $disk['username'] }} ({{ $disk['role'] }})
                            </div>
                            <p style="margin-bottom: 1.5rem; line-height: 1.5; font-weight: 500;">
                                {{ mb_strimwidth(strip_tags($disk['isi_diskusi']), 0, 150, "...") }}
                            </p>
                            <a href="{{ route('diskusi.show', ['id' => $disk['id_diskusi']]) }}" class="btn-small" style="background-color: var(--primary); color: var(--primary-foreground);">Buka Diskusi &rarr;</a>
                        </div>
                    @endforeach
                @else
                    <div class="brutal-card" style="text-align: center; padding: 2rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">💬</div>
                        <h4 style="font-weight: 800; text-transform: uppercase;">Belum ada diskusi</h4>
                        <p style="font-weight: 500;">Jadilah yang pertama memulai diskusi di kelas ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ==============================
             TAB 7: KUIS
             ============================== -->
        <div id="tab-kuis" class="tab-section">
            <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem;">Daftar Kuis</h3>
            
            @if ($role === 'Pengajar' || strtolower($role) === 'admin')
            <div class="brutal-card" style="margin-bottom: 2rem;">
                <h4 style="text-transform: uppercase; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Buat Kuis Baru</h4>
                <form method="POST" action="{{ route('kelas.post', ['id' => $id_kelas]) }}">
                    @csrf
                    <input type="hidden" name="action" value="post_kuis">
                    <div style="margin-bottom: 1rem;">
                        <label style="font-weight: bold; text-transform: uppercase;">Judul Kuis</label>
                        <input type="text" name="judul_kuis" required class="neo-input neo-box" style="margin-top: 0.5rem;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="font-weight: bold; text-transform: uppercase;">Deskripsi Kuis</label>
                        <textarea name="deskripsi_kuis" rows="2" required class="neo-input neo-box" style="margin-top: 0.5rem;"></textarea>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="font-weight: bold; text-transform: uppercase;">Durasi Pengerjaan (Menit)</label>
                        <input type="number" name="durasi_menit" required class="neo-input neo-box" style="margin-top: 0.5rem;" min="1" value="30">
                    </div>
                    <button type="submit" class="btn-accent">Buat Kuis</button>
                </form>
            </div>
            @endif

            @if (count($kuisList) > 0)
                @foreach ($kuisList as $k)
                    <div class="brutal-card" style="border-left: 10px solid var(--primary); margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <h4 style="font-size: 1.4rem; font-weight: 900; text-transform: uppercase; margin: 0;">{{ $k['judul_kuis'] }}</h4>
                            <span style="font-size: 0.9rem; background-color: var(--muted); padding: 0.2rem 0.6rem; font-weight: bold; color: var(--foreground); border: 2px solid var(--border);">Durasi: {{ $k['durasi_menit'] }} Menit</span>
                        </div>
                        <p style="font-size: 1rem; margin-bottom: 1.5rem; line-height: 1.5; font-weight: 500;">{!! nl2br(e($k['deskripsi'])) !!}</p>
                        
                        @if ($role === 'Pengajar' || strtolower($role) === 'admin')
                            <a href="{{ route('kuis.manage', ['id_kuis' => $k['id_kuis']]) }}" class="btn-small" style="background-color: var(--accent); color: var(--primary-foreground);">Kelola Soal &rarr;</a>
                        @elseif (strtolower($role) === 'murid')
                            @php
                                $nilaiKuis = $nilaiKuisMap[$k['id_kuis']] ?? null;
                            @endphp
                            @if ($nilaiKuis !== null)
                                <div style="display: inline-block; background-color: #4CAF50; color: #fff; border: 3px solid #000; padding: 0.5rem 1rem; font-weight: 900; font-size: 1.1rem; box-shadow: 3px 3px 0px #000;">
                                    NILAI ANDA: {{ $nilaiKuis }}
                                </div>
                            @else
                                <a href="{{ route('kuis.kerjakan', ['id_kuis' => $k['id_kuis']]) }}" class="btn-small" style="background-color: var(--primary); color: var(--primary-foreground);">Kerjakan Kuis &rarr;</a>
                            @endif
                        @endif
                    </div>
                @endforeach
            @else
                <div class="brutal-card"><p style="font-weight: bold;">Belum ada kuis untuk kelas ini.</p></div>
            @endif
        </div>

        <!-- ==============================
             TAB 8: REKAP NILAI (PENGAJAR & ADMIN)
             ============================== -->
        @if ($role === 'Pengajar' || strtolower($role) === 'admin')
        <div id="tab-rekap" class="tab-section">
            <h3 style="font-size: 1.6rem; text-transform: uppercase; font-weight: 800; margin-bottom: 1.5rem;">Rekap Nilai Siswa</h3>
            <div class="brutal-card">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 3px solid var(--border);">
                                <th style="padding: 1rem; font-weight: 900; text-transform: uppercase;">NIM</th>
                                <th style="padding: 1rem; font-weight: 900; text-transform: uppercase;">Nama Murid</th>
                                <th style="padding: 1rem; font-weight: 900; text-transform: uppercase;">Rata-rata Tugas</th>
                                <th style="padding: 1rem; font-weight: 900; text-transform: uppercase;">Rata-rata Kuis</th>
                                <th style="padding: 1rem; font-weight: 900; text-transform: uppercase;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekapNilai as $rekap)
                                <tr style="border-bottom: 2px solid var(--border);">
                                    <td style="padding: 1rem; font-weight: bold;">{{ $rekap['nim'] }}</td>
                                    <td style="padding: 1rem; font-weight: 500;">{{ $rekap['nama_murid'] }}</td>
                                    <td style="padding: 1rem; font-weight: 900; color: var(--primary);">{{ round($rekap['avg_tugas'], 2) }}</td>
                                    <td style="padding: 1rem; font-weight: 900; color: var(--accent);">{{ round($rekap['avg_kuis'], 2) }}</td>
                                    <td style="padding: 1rem;">
                                        <a href="#" class="btn-small" style="background-color: var(--foreground); color: var(--background);">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</main>
@endsection

@section('scripts')
<script>
function switchClassTab(tabId, btn) {
    // Sembunyikan semua tab content
    document.querySelectorAll('.tab-section').forEach(function(el) {
        el.classList.remove('active');
    });
    // Hapus class active dari semua tombol tab
    document.querySelectorAll('.brutal-tab').forEach(function(el) {
        el.classList.remove('active');
    });
    // Tampilkan konten tab yang dipilih
    document.getElementById('tab-' + tabId).classList.add('active');
    // Tambahkan class active ke tombol yang diklik
    btn.classList.add('active');
}

function togglePostForm() {
    var form = document.getElementById('postFormContainer');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function togglePostType() {
    var type = document.getElementById('jenis_post').value;
    var divMateri = document.getElementById('input_materi');
    var divTugas = document.getElementById('input_tugas');
    var inputDeadline = document.getElementsByName('deadline')[0];
    
    if (type === 'materi') {
        divMateri.style.display = 'block';
        divTugas.style.display = 'none';
        inputDeadline.removeAttribute('required');
    } else {
        divMateri.style.display = 'none';
        divTugas.style.display = 'block';
        inputDeadline.setAttribute('required', 'true');
    }
}
</script>

<!-- Quill.js Library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'header': [1, 2, 3, false] }],
        ['link'],
        ['clean']                                         // remove formatting button
    ];

    // Initialize Quill for Upload Materi (Baru)
    var editorMateriContainer = document.getElementById('editor_materi');
    if (editorMateriContainer) {
        var quillMateri = new Quill('#editor_materi', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
        
        var formMateri = editorMateriContainer.closest('form');
        formMateri.addEventListener('submit', function() {
            var html = quillMateri.root.innerHTML;
            document.getElementById('hidden_deskripsi_materi').value = html;
        });
    }

    // Initialize Quill for Buat Tugas (Baru)
    var editorTugasContainer = document.getElementById('editor_tugas');
    if (editorTugasContainer) {
        var quillTugas = new Quill('#editor_tugas', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
        
        var formTugas = editorTugasContainer.closest('form');
        formTugas.addEventListener('submit', function() {
            var html = quillTugas.root.innerHTML;
            document.getElementById('hidden_deskripsi_tugas').value = html;
        });
    }

    // Initialize Quill for Upload Materi/Tugas (Lama/Stream)
    var editorDeskripsiContainer = document.getElementById('editor_deskripsi');
    if (editorDeskripsiContainer) {
        var quillDeskripsi = new Quill('#editor_deskripsi', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
        
        // Sync to hidden input before submit
        var formUpload = editorDeskripsiContainer.closest('form');
        formUpload.addEventListener('submit', function() {
            var html = quillDeskripsi.root.innerHTML;
            document.getElementById('hidden_deskripsi').value = html;
        });
    }

    // Initialize Quill for Buat Topik Diskusi
    var editorDiskusiContainer = document.getElementById('editor_diskusi');
    if (editorDiskusiContainer) {
        var quillDiskusi = new Quill('#editor_diskusi', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
        
        // Sync to hidden input before submit
        var formDiskusi = editorDiskusiContainer.closest('form');
        formDiskusi.addEventListener('submit', function() {
            var html = quillDiskusi.root.innerHTML;
            document.getElementById('hidden_isi_diskusi').value = html;
        });
    }

    function toggleUploadMateri() {
        var form = document.getElementById('formUploadMateri');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }

    function toggleUploadTugas() {
        var form = document.getElementById('formUploadTugas');
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
</script>

<script>
// Drag and Drop Script for Multiple Zones
document.addEventListener('DOMContentLoaded', () => {
    
    function setupDropZone(dropZoneId, fileInputId, dropTextId, fileNameDisplayId, dropIconId) {
        const dropZone = document.getElementById(dropZoneId);
        const fileInput = document.getElementById(fileInputId);
        const dropText = document.getElementById(dropTextId);
        const fileNameDisplay = document.getElementById(fileNameDisplayId);
        const dropIcon = document.getElementById(dropIconId);

        if (dropZone && fileInput) {
            dropZone.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) updateFileDisplay(this.files[0].name);
            });

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt.files.length > 0) {
                    fileInput.files = dt.files;
                    updateFileDisplay(dt.files[0].name);
                }
            }, false);

            function updateFileDisplay(name) {
                fileNameDisplay.style.display = 'inline-block';
                fileNameDisplay.innerHTML = '📁 ' + name;
                dropText.innerHTML = 'File Berhasil Dimasukkan!';
                dropZone.style.backgroundColor = '#4CAF50';
                dropZone.style.border = '4px solid var(--border)';
                dropText.style.color = '#FFF';
                if(dropIcon) {
                    dropIcon.style.color = '#FFF';
                    dropIcon.innerHTML = "<i class='bx bx-check-circle'></i>";
                }
            }
        }
    }

    // Setup for Murid (Jawaban Tugas)
    setupDropZone('drop-zone', 'file_jawaban', 'drop-text', 'file-name-display', 'drop-icon');
    
    // Setup for Pengajar (Upload Materi)
    setupDropZone('drop-zone-materi', 'file_materi', 'drop-text-materi', 'file-name-display-materi', 'drop-icon-materi');
    
    // Setup for Pengajar (Buat Tugas)
    setupDropZone('drop-zone-tugas', 'file_tugas', 'drop-text-tugas', 'file-name-display-tugas', 'drop-icon-tugas');
});
</script>
@endsection
