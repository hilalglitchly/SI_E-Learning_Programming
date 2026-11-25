@extends('layouts.app')

@section('title', 'Pendaftaran Kelas - E-Learning')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1400px; margin: 0 auto; padding-top: 20px;">
            
            @if (!empty($pesan))
                <div class="neo-box" style="background-color: var(--accent); color: var(--accent-foreground);">
                    <strong>Info:</strong> {{ $pesan }}
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                <h2 style="margin: 0;">Kelas yang Tersedia</h2>
                
                <!-- FITUR LIVE SEARCH -->
                <div style="flex-grow: 1; max-width: 400px;">
                    <input type="text" id="searchKelas" class="neo-input" placeholder="🔍 Ketik nama kelas atau pengajar..." style="margin-bottom: 0; background-color: var(--card);">
                </div>
            </div>

            @if (count($kelasTersedia) > 0)
                <div class="neo-grid" id="kelasGrid">
                    @foreach ($kelasTersedia as $kelas)
                        <div class="neo-card kelas-item" style="display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 10px;">
                                    <span class="badge-kode" style="margin: 0;">KODE: KLS-{{ $kelas['id_kelas'] }}</span>
                                    @if (!empty($kelas['is_premium']))
                                        <span class="badge-kode" style="margin: 0; background-color: #7C3AED; color: #fff; border: 2px solid #000;">👑 PREMIUM (PRO)</span>
                                    @else
                                        <span class="badge-kode" style="margin: 0; background-color: #BBF7D0; color: #000; border: 2px solid #000;">✓ GRATIS</span>
                                    @endif
                                </div>
                                <h3 class="nama-kelas">{{ $kelas['nama_kelas'] }}</h3>
                                <div class="nama-pengajar" style="font-size: 0.9em; margin-bottom: 12px; color: var(--foreground);">
                                    👨‍🏫 Pengajar: <strong>{{ $kelas['nama_pengajar'] ?? 'Belum ditentukan' }}</strong>
                                </div>
                                <p class="deskripsi-kelas" style="font-size: 0.95em; margin-bottom: 20px; color: var(--muted-foreground);">
                                    {{ $kelas['deskripsi'] }}
                                </p>
                            </div>
                            
                            @if (!empty($kelas['is_premium']) && empty($hasActivePro))
                                <a href="{{ route('langganan.index') }}" class="neo-btn" style="margin-top: auto; width: 100%; text-align: center; text-decoration: none; display: block; background-color: #7C3AED; color: #fff;">
                                    🔒 Aktifkan Pro untuk Gabung &rarr;
                                </a>
                            @else
                                <form method="POST" action="{{ route('enroll.store') }}" data-kelas-nama="{{ $kelas['nama_kelas'] }}" style="margin-top: auto;" onsubmit="event.preventDefault(); NeoConfirm('Gabung Kelas', 'Yakin ingin bergabung ke kelas ' + this.dataset.kelasNama + '?', () => this.submit());">
                                    @csrf
                                    <input type="hidden" name="id_kelas" value="{{ $kelas['id_kelas'] }}">
                                    <button type="submit" class="neo-btn" style="width: 100%; background-color: var(--accent);">Gabung Kelas &rarr;</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
                <!-- Pesan jika hasil pencarian kosong -->
                <div id="noResultMsg" class="neo-box" style="display: none; text-align: center;">
                    <p style="font-weight: bold;">Kelas yang Anda cari tidak ditemukan.</p>
                </div>
            @else
                <div class="neo-box">
                    <p>Tidak ada kelas baru yang tersedia saat ini, atau Anda sudah mengambil semua kelas yang ada.</p>
                    <br>
                    <a href="{{ route('dashboard') }}" class="neo-btn">Kembali ke Dashboard</a>
                </div>
            @endif

        </div>
    </main>
@endsection

@section('scripts')
<script>
// Script Real-Time Search Filter
document.getElementById('searchKelas')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('.kelas-item');
    let visibleCount = 0;

    cards.forEach(card => {
        let title = card.querySelector('.nama-kelas').innerText.toLowerCase();
        let teacher = card.querySelector('.nama-pengajar').innerText.toLowerCase();
        let desc = card.querySelector('.deskripsi-kelas').innerText.toLowerCase();

        if (title.includes(filter) || teacher.includes(filter) || desc.includes(filter)) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    let noResultMsg = document.getElementById('noResultMsg');
    if (noResultMsg) {
        noResultMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
});
</script>
@endsection

