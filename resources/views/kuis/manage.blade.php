@extends('layouts.app')

@section('title', 'Kelola Soal - ' . $kuis['judul_kuis'])

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1000px; margin: 0 auto; padding-top: 20px;">
            
            <div style="margin-bottom: 20px;">
                <a href="{{ route('kelas.show', ['id' => $kuis['id_kelas']]) }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">&larr; Kembali ke Kelas</a>
            </div>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: #a8e6cf; color: #000; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif

            <!-- Info Kuis -->
            <div class="neo-box" style="background-color: var(--secondary); color: var(--secondary-foreground);">
                <span class="badge-kode" style="background-color: #fff;">{{ $kuis['nama_kelas'] }}</span>
                <h2 style="margin-bottom: 8px;">{{ $kuis['judul_kuis'] }}</h2>
                <p style="margin-bottom: 8px;">{{ $kuis['deskripsi'] }}</p>
                <div style="font-weight: bold; font-size: 0.9em;">⏱ Durasi: {{ $kuis['durasi_menit'] }} Menit | Total Soal: {{ count($daftarSoal) }}</div>
            </div>

            <!-- Form Tambah Soal -->
            <div class="neo-box">
                <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 20px;">+ Tambah Soal Pilihan Ganda</h3>
                <form method="POST" action="{{ route('kuis.soal.store', ['id_kuis' => $id_kuis]) }}">
                    @csrf
                    <input type="hidden" name="action" value="tambah_soal">
                    
                    <div class="neo-form-group">
                        <label class="neo-label">Pertanyaan Soal</label>
                        <textarea name="pertanyaan" rows="3" class="neo-input" placeholder="Tuliskan pertanyaan di sini..." required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="neo-form-group">
                            <label class="neo-label">Opsi A</label>
                            <input type="text" name="opsi_a" class="neo-input" required>
                        </div>
                        <div class="neo-form-group">
                            <label class="neo-label">Opsi B</label>
                            <input type="text" name="opsi_b" class="neo-input" required>
                        </div>
                        <div class="neo-form-group">
                            <label class="neo-label">Opsi C</label>
                            <input type="text" name="opsi_c" class="neo-input" required>
                        </div>
                        <div class="neo-form-group">
                            <label class="neo-label">Opsi D</label>
                            <input type="text" name="opsi_d" class="neo-input" required>
                        </div>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Kunci Jawaban Benar</label>
                        <select name="jawaban_benar" class="neo-input" style="font-weight: bold; max-width: 200px;" required>
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                        </select>
                    </div>

                    <button type="submit" class="neo-btn">Simpan Soal</button>
                </form>
            </div>

            <!-- Daftar Soal yang Sudah Dibuat -->
            <h3>Daftar Soal Saat Ini ({{ count($daftarSoal) }})</h3>
            @if (count($daftarSoal) > 0)
                @foreach ($daftarSoal as $index => $s)
                    <div class="neo-box" style="position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <strong style="font-size: 1.1em;">Soal #{{ $index + 1 }}</strong>
                            <a href="{{ route('kuis.soal.delete', ['id_kuis' => $id_kuis, 'id_soal' => $s['id_soal']]) }}" 
                               class="neo-btn" 
                               style="padding: 4px 10px; font-size: 0.75rem; background-color: var(--primary); color: #fff;"
                               onclick="event.preventDefault(); const url = this.href; NeoConfirm('Hapus Soal', 'Hapus soal ini?', () => window.location.href = url);">Hapus</a>
                        </div>
                        <p style="font-size: 1.05em; margin-bottom: 16px; font-weight: 500;">{!! nl2br(e($s['pertanyaan'])) !!}</p>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.95em;">
                            <div style="padding: 8px; border: 2px solid var(--border); {{ $s['jawaban_benar'] === 'A' ? 'background-color: #a8e6cf; color: #000; font-weight: bold;' : '' }}">A. {{ $s['opsi_a'] }}</div>
                            <div style="padding: 8px; border: 2px solid var(--border); {{ $s['jawaban_benar'] === 'B' ? 'background-color: #a8e6cf; color: #000; font-weight: bold;' : '' }}">B. {{ $s['opsi_b'] }}</div>
                            <div style="padding: 8px; border: 2px solid var(--border); {{ $s['jawaban_benar'] === 'C' ? 'background-color: #a8e6cf; color: #000; font-weight: bold;' : '' }}">C. {{ $s['opsi_c'] }}</div>
                            <div style="padding: 8px; border: 2px solid var(--border); {{ $s['jawaban_benar'] === 'D' ? 'background-color: #a8e6cf; color: #000; font-weight: bold;' : '' }}">D. {{ $s['opsi_d'] }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="neo-box">
                    <p>Belum ada soal untuk kuis ini. Silakan tambahkan soal melalui formulir di atas.</p>
                </div>
            @endif

        </div>
    </main>
@endsection
