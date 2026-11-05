@extends('layouts.app')

@section('title', 'Profil Akun - E-Learning')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1000px; margin: 0 auto; padding-top: 20px;">
            
            <h2>Informasi Akun Pengguna</h2>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: {{ $tipe_pesan === 'success' ? '#a8e6cf' : 'var(--primary)' }}; color: {{ $tipe_pesan === 'success' ? '#000' : '#fff' }}; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif
            
            <!-- Kotak Utama Identitas -->
            <div class="neo-box" style="max-width: 650px;">
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px; border-bottom: var(--border-width) solid var(--border); padding-bottom: 20px;">
                    <div style="width: 75px; height: 75px; background-color: var(--secondary); border: var(--border-width) solid var(--border); box-shadow: var(--shadow-offset-x) var(--shadow-offset-y) 0px var(--shadow-color); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800; color: #000;">
                        {{ strtoupper(substr($nama_lengkap, 0, 1)) }}
                    </div>
                    <div>
                        <h3 style="margin-bottom: 6px; font-size: 1.5rem;">{{ $nama_lengkap }}</h3>
                        <span class="badge-kode" style="margin-bottom: 0;">Role: {{ $role }}</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div>
                        <label style="font-size: 0.85em; color: var(--muted-foreground); font-weight: bold; text-transform: uppercase;">Nomor Induk (NIM/NIDN)</label>
                        <div style="font-size: 1.1em; font-weight: bold; margin-top: 4px;">{{ $nomor_induk }}</div>
                    </div>
                    <div>
                        <label style="font-size: 0.85em; color: var(--muted-foreground); font-weight: bold; text-transform: uppercase;">Username Login</label>
                        <div style="font-size: 1.1em; font-weight: bold; margin-top: 4px;">{{ $username }}</div>
                    </div>
                    <div>
                        <label style="font-size: 0.85em; color: var(--muted-foreground); font-weight: bold; text-transform: uppercase;">Alamat Email</label>
                        <div style="font-size: 1.1em; font-weight: bold; margin-top: 4px;">{{ $email }}</div>
                    </div>
                    <div>
                        <label style="font-size: 0.85em; color: var(--muted-foreground); font-weight: bold; text-transform: uppercase;">Status Akun</label>
                        <div style="font-size: 1.1em; font-weight: bold; margin-top: 4px; color: #2ecc71;">Aktif</div>
                    </div>
                </div>

                <!-- Tombol Buka/Tutup Form Ganti Password -->
                <div style="border-top: 2px dashed var(--border); padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: bold; font-size: 0.95em;">Ingin mengamankan akun Anda?</span>
                    <button type="button" onclick="togglePasswordForm()" class="neo-btn" style="background-color: var(--accent); color: var(--accent-foreground); padding: 8px 16px; font-size: 0.85rem;">
                        🔒 Ubah Password
                    </button>
                </div>
            </div>

            <!-- Kotak Form Ganti Password (Tersembunyi secara default) -->
            <div id="form-password" class="neo-box" style="max-width: 650px; display: {{ !empty($pesan) && $tipe_pesan === 'error' ? 'block' : 'none' }}; border-left: 8px solid var(--primary);">
                <h3 style="border-bottom: 3px solid var(--border); padding-bottom: 10px; margin-bottom: 20px; text-transform: uppercase;">Formulir Ganti Password</h3>
                
                <form method="POST" action="{{ route('profil.password') }}">
                    @csrf
                    <div class="neo-form-group">
                        <label class="neo-label">Password Lama</label>
                        <input type="password" name="password_lama" class="neo-input" placeholder="Masukkan password saat ini..." required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Password Baru</label>
                        <input type="password" name="password_baru" class="neo-input" placeholder="Masukkan password baru..." required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="neo-input" placeholder="Ketik ulang password baru..." required>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" name="ganti_password" value="1" class="neo-btn" style="flex: 1;">Simpan Password Baru</button>
                        <button type="button" onclick="togglePasswordForm()" class="neo-btn" style="background-color: var(--muted); color: var(--foreground);">Batal</button>
                    </div>
                </form>
            </div>

        </div>
    </main>
@endsection

@section('scripts')
<script>
function togglePasswordForm() {
    var form = document.getElementById('form-password');
    if (form.style.display === 'none') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    } else {
        form.style.display = 'none';
    }
}
</script>
@endsection
