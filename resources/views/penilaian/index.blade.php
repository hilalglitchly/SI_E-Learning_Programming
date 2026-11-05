@extends('layouts.app')

@section('title', 'Penilaian: ' . $infoTugas['judul_tugas'])

@section('content')
    <!-- MAIN CONTENT -->
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">
            
            <div style="margin-bottom: 20px;">
                <a href="{{ route('tugas.index') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">&larr; Kembali ke Daftar Tugas</a>
            </div>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: {{ $tipe_pesan === 'success' ? '#a8e6cf' : 'var(--primary)' }}; color: {{ $tipe_pesan === 'success' ? '#000' : '#fff' }}; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif

            <!-- Info Tugas -->
            <div class="neo-box" style="background-color: var(--secondary); color: var(--secondary-foreground);">
                <span class="badge-kode" style="background-color: #fff; color: #000;">{{ $infoTugas['nama_kelas'] }}</span>
                <h2 style="margin-bottom: 8px; color: #000;">{{ $infoTugas['judul_tugas'] }}</h2>
                <div style="margin-bottom: 12px; color: #000;">{!! sanitize_rich_text($infoTugas['deskripsi']) !!}</div>
                <div style="font-size: 0.9em; font-weight: bold; color: #000;">
                    ⏰ Tenggat Waktu: {{ date('d M Y, H:i', strtotime($infoTugas['deadline'])) }}
                </div>
            </div>

            <h3>Daftar Pengumpulan Jawaban ({{ count($daftarSubmission) }} Siswa)</h3>

            @if (count($daftarSubmission) > 0)
                <div class="neo-box" style="padding: 0; overflow-x: auto;">
                    <table class="neo-table" style="margin-top: 0; border: none; box-shadow: none;">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Siswa</th>
                                <th>Waktu Kumpul</th>
                                <th>File Jawaban</th>
                                <th style="width: 380px;">Form Penilaian (Nilai & Feedback)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarSubmission as $sub)
                                <tr>
                                    <td style="font-weight: bold;">{{ $sub['nim'] }}</td>
                                    <td>{{ $sub['nama_murid'] }}</td>
                                    <td>
                                        {{ date('d M Y, H:i', strtotime($sub['tgl_kumpul'])) }}
                                        @if (strtotime($sub['tgl_kumpul']) > strtotime($infoTugas['deadline']))
                                            <br><span style="color: var(--primary); font-size: 0.8em; font-weight: bold;">(Terlambat)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ asset('uploads/tugas/' . $sub['file_jawaban']) }}" target="_blank" class="neo-btn" style="padding: 6px 12px; font-size: 0.8rem; background-color: var(--accent);">
                                            📥 Unduh File
                                        </a>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('penilaian.store', ['id_tugas' => $id_tugas]) }}" style="display: flex; flex-direction: column; gap: 8px;">
                                            @csrf
                                            <input type="hidden" name="id_submission" value="{{ $sub['id_submission'] }}">
                                            
                                            <div style="display: flex; gap: 8px; align-items: center;">
                                                <input type="number" name="nilai" class="neo-input" style="margin-bottom: 0; width: 90px; padding: 8px;" placeholder="0-100" min="0" max="100" value="{{ $sub['nilai'] }}" required>
                                                <input type="text" name="catatan" class="neo-input" style="margin-bottom: 0; flex-grow: 1; padding: 8px;" placeholder="Tulis catatan/feedback..." value="{{ $sub['catatan'] ?? '' }}">
                                                <button type="submit" name="simpan_nilai" value="1" class="neo-btn" style="padding: 8px 14px; font-size: 0.85rem; white-space: nowrap;">
                                                    💾 Simpan
                                                </button>
                                            </div>
                                            @if ($sub['nilai'] !== null)
                                                <small style="color: #2ecc71; font-weight: bold;">✔ Sudah dinilai ({{ $sub['nilai'] }}/100)</small>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="neo-box">
                    <p style="font-weight: bold;">Belum ada siswa yang mengumpulkan jawaban untuk tugas ini.</p>
                </div>
            @endif

        </div>
    </main>
@endsection
