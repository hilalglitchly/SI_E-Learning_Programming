@extends('layouts.app')

@section('title', 'Daftar Tugas - E-Learning')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">
            
            <h2>{{ strtolower($role) === 'murid' ? 'Tugas Aktif (Belum Dikumpulkan)' : 'Daftar Tugas di Kelas Anda' }}</h2>
            
            @if (count($daftarTugas) > 0)
                <div class="neo-grid">
                    @foreach ($daftarTugas as $tugas)
                        <div class="neo-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <span class="badge-kode" style="background-color: var(--accent); color: var(--accent-foreground);">
                                    {{ $tugas['nama_kelas'] }}
                                </span>
                                <h3>{{ $tugas['judul_tugas'] }}</h3>
                                
                                <p style="font-size: 0.95em; margin-bottom: 16px; color: var(--muted-foreground);">
                                    {{ Str::limit(strip_tags($tugas['deskripsi']), 120, '...') }}
                                </p>
                            </div>
                            
                            <div>
                                @php
                                    $isOverdue = strtotime($tugas['deadline']) < time();
                                    $badgeColor = $isOverdue ? 'var(--primary)' : 'var(--secondary)';
                                    $textColor = $isOverdue ? 'var(--primary-foreground)' : 'var(--secondary-foreground)';
                                @endphp
                                <div style="margin-bottom: 16px; padding: 8px; border: var(--border-width) solid var(--border); background-color: {{ $badgeColor }}; color: {{ $textColor }}; font-weight: bold; font-size: 0.85em; text-align: center;">
                                    ⏰ Tenggat: {{ date('d M Y, H:i', strtotime($tugas['deadline'])) }}
                                    {!! $isOverdue ? '<br>(TERLEWAT)' : '' !!}
                                </div>

                                @if (strtolower($role) === 'murid')
                                    <a href="{{ route('kelas.show', ['id' => $tugas['id_kelas']]) }}" class="neo-btn" style="text-align: center; text-decoration: none; display: block;">Kerjakan di Kelas &rarr;</a>
                                @else
                                    <div style="display: flex; gap: 8px; flex-direction: column;">
                                        <a href="{{ route('penilaian.index', ['id_tugas' => $tugas['id_tugas']]) }}" class="neo-btn" style="text-align: center; text-decoration: none; display: block; background-color: var(--accent);">Nilai Jawaban ({{ $tugas['jumlah_kumpul'] }}) &rarr;</a>
                                        <a href="{{ route('kelas.show', ['id' => $tugas['id_kelas']]) }}" class="neo-btn" style="text-align: center; text-decoration: none; display: block; background-color: var(--muted); color: var(--foreground);">Buka Kelas</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="neo-box">
                    @if (strtolower($role) === 'murid')
                        <p style="font-size: 1.1em; font-weight: bold;">🎉 Hore! Tidak ada tugas aktif yang perlu dikumpulkan saat ini.</p>
                        <p style="color: var(--muted-foreground);">Semua tugas Anda sudah selesai dikumpulkan, atau pengajar belum memberikan tugas baru.</p>
                    @else
                        <p>Belum ada tugas yang dibuat pada kelas yang Anda ampu.</p>
                    @endif
                </div>
            @endif

        </div>
    </main>
@endsection
