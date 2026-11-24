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
        .opsi-radio {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            pointer-events: none;
        }
        .opsi-radio:checked + .opsi-label {
            background-color: var(--secondary);
            color: #000;
            font-weight: 800;
            box-shadow: 3px 3px 0px var(--shadow-color);
        }
        .kuis-timer-bar {
            position: sticky;
            top: 95px;
            z-index: 90;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 18px;
            margin-bottom: 20px;
            background-color: var(--secondary);
            color: #000;
            border: 3px solid #000;
            box-shadow: 5px 5px 0px #000;
            font-weight: 900;
        }
        .kuis-timer-bar.urgent {
            background-color: #EF4444;
            color: #fff;
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
                <div id="kuis-timer-bar" class="kuis-timer-bar">
                    <span>⏱ SISA WAKTU PENGERJAAN</span>
                    <span id="kuis-countdown" style="font-size: 1.35rem; font-family: monospace;">--:--</span>
                </div>

                <form id="kuis-form" method="POST" action="{{ route('kuis.submit', ['id_kuis' => $id_kuis]) }}">
                    @csrf
                    @foreach ($daftarSoal as $index => $s)
                        <div class="neo-box soal-card" data-soal-id="{{ $s['id_soal'] }}" data-soal-num="{{ $index + 1 }}">
                            <div style="font-weight: 800; font-size: 1.1em; margin-bottom: 12px; border-bottom: 2px solid var(--border); padding-bottom: 8px;">
                                Pertanyaan #{{ $index + 1 }}
                            </div>
                            <p style="font-size: 1.1em; margin-bottom: 20px; font-weight: 500;">{!! nl2br(e($s['pertanyaan'])) !!}</p>

                            <div>
                                @foreach (['A' => 'opsi_a', 'B' => 'opsi_b', 'C' => 'opsi_c', 'D' => 'opsi_d'] as $huruf => $kolom)
                                    <input type="radio" name="soal_{{ $s['id_soal'] }}" id="soal_{{ $s['id_soal'] }}_{{ $huruf }}" value="{{ $huruf }}" class="opsi-radio">
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

@section('scripts')
@if (count($daftarSoal) > 0)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('kuis-form');
    const timerBar = document.getElementById('kuis-timer-bar');
    const countdownEl = document.getElementById('kuis-countdown');
    let remaining = {{ (int) ($remainingSeconds ?? ($kuis['durasi_menit'] * 60)) }};
    let isSubmitting = false;

    function formatTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function updateTimer() {
        countdownEl.textContent = formatTime(Math.max(0, remaining));
        if (remaining <= 60) {
            timerBar.classList.add('urgent');
        }
        if (remaining <= 0 && !isSubmitting) {
            isSubmitting = true;
            clearInterval(interval);
            NeoToast('Waktu habis! Jawaban Anda sedang dikirim otomatis...', 'warning');
            form.submit();
            return;
        }
        remaining--;
    }

    updateTimer();
    const interval = setInterval(updateTimer, 1000);

    form.addEventListener('submit', function(e) {
        if (isSubmitting) return;
        e.preventDefault();

        const cards = form.querySelectorAll('.soal-card');
        let firstUnanswered = null;
        cards.forEach(card => {
            const id = card.dataset.soalId;
            const checked = form.querySelector(`input[name="soal_${id}"]:checked`);
            if (!checked) {
                card.style.borderColor = '#EF4444';
                if (!firstUnanswered) firstUnanswered = card;
            } else {
                card.style.borderColor = '';
            }
        });

        if (firstUnanswered) {
            NeoToast(`Pertanyaan #${firstUnanswered.dataset.soalNum} belum dijawab.`, 'warning');
            firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        NeoConfirm('Kumpulkan Kuis', 'Apakah Anda yakin sudah menjawab semua soal dengan benar? Jawaban tidak dapat diubah setelah dikirim.', () => {
            isSubmitting = true;
            clearInterval(interval);
            form.submit();
        });
    });
});
</script>
@endif
@endsection
