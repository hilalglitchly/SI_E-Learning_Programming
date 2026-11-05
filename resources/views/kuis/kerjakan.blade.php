@extends('layouts.app')

@section('title', 'Kerjakan Kuis: ' . $kuis['judul_kuis'])

@section('styles')
    <style>
        .opsi-label {
            display: block;
            padding: 12px 16px;
            border: var(--border-width) solid var(--border);
            margin-bottom: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.1s;
            background-color: var(--card);
        }
        .opsi-label:hover {
            background-color: var(--muted);
            transform: translate(2px, 2px);
        }
        .opsi-radio:checked + .opsi-label {
            background-color: var(--secondary);
            color: #000;
            font-weight: 800;
            box-shadow: 3px 3px 0px var(--shadow-color);
        }
    </style>
@endsection

@section('content')
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 900px; margin: 0 auto; padding-top: 20px;">
            
            <div style="margin-bottom: 20px;">
                <a href="{{ route('kelas.show', ['id' => $kuis['id_kelas']]) }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); padding: 8px 16px; font-size: 0.85rem;">&larr; Batal & Kembali</a>
            </div>

            <div class="neo-box" style="background-color: var(--accent); color: var(--accent-foreground);">
                <span class="badge-kode" style="background-color: #fff; color: #000;">{{ $kuis['nama_kelas'] }}</span>
                <h2 style="margin-bottom: 8px;">{{ $kuis['judul_kuis'] }}</h2>
                <p style="margin-bottom: 8px;">{{ $kuis['deskripsi'] }}</p>
                <div style="font-weight: bold;">⏱ Waktu Pengerjaan: {{ $kuis['durasi_menit'] }} Menit | Jumlah Soal: {{ count($daftarSoal) }}</div>
            </div>

            @if (count($daftarSoal) > 0)
                <form method="POST" action="{{ route('kuis.submit', ['id_kuis' => $id_kuis]) }}" onsubmit="event.preventDefault(); NeoConfirm('Kumpulkan Kuis', 'Apakah Anda yakin sudah menjawab semua soal dengan benar? Jawaban tidak dapat diubah setelah dikirim.', () => this.submit());">
                    @csrf
                    @foreach ($daftarSoal as $index => $s)
                        <div class="neo-box">
                            <div style="font-weight: 800; font-size: 1.1em; margin-bottom: 12px; border-bottom: 2px solid var(--border); padding-bottom: 8px;">
                                Pertanyaan #{{ $index + 1 }}
                            </div>
                            <p style="font-size: 1.1em; margin-bottom: 20px; font-weight: 500;">{!! nl2br(e($s['pertanyaan'])) !!}</p>

                            <div>
                                @foreach (['A' => 'opsi_a', 'B' => 'opsi_b', 'C' => 'opsi_c', 'D' => 'opsi_d'] as $huruf => $kolom)
                                    <input type="radio" name="soal_{{ $s['id_soal'] }}" id="soal_{{ $s['id_soal'] }}_{{ $huruf }}" value="{{ $huruf }}" class="opsi-radio" style="display: none;" required>
                                    <label for="soal_{{ $s['id_soal'] }}_{{ $huruf }}" class="opsi-label">
                                        <strong>{{ $huruf }}.</strong> {{ $s[$kolom] }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" name="selesai_kuis" value="1" class="neo-btn" style="width: 100%; font-size: 1.2rem; padding: 16px; background-color: #2ecc71; color: #000;">
                        ✔ Selesai & Kumpulkan Jawaban
                    </button>
                </form>
            @else
                <div class="neo-box">
                    <p style="font-weight: bold;">Pengajar belum menambahkan soal pada kuis ini.</p>
                </div>
            @endif

        </div>
    </main>
@endsection
