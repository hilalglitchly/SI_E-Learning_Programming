@extends('layouts.app')

@section('title', 'Tambah Pengguna - Admin E-Learning')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1000px; margin: 0 auto; padding-top: 20px;">
            <div style="margin-bottom: 20px;">
                <a href="{{ route('dashboard') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); font-size: 0.85rem; padding: 8px 16px;">&larr; Kembali ke Dashboard</a>
            </div>
            
            <h2>Formulir Pendaftaran Pengguna</h2>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: {{ $tipe_pesan === 'success' ? '#a8e6cf' : 'var(--primary)' }}; color: {{ $tipe_pesan === 'success' ? '#000' : '#fff' }}; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif
            
            <div class="neo-box" style="max-width: 650px;">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <!-- Pilihan Role -->
                    <div class="neo-form-group">
                        <label class="neo-label">Pilih Hak Akses (Role)</label>
                        <select id="role_pilihan" name="role_pilihan" class="neo-input" style="font-weight: bold;" onchange="ubahLabelInduk()" required>
                            <option value="Murid" {{ old('role_pilihan') === 'Murid' ? 'selected' : '' }}>🎓 Murid (Mahasiswa)</option>
                            <option value="Pengajar" {{ old('role_pilihan') === 'Pengajar' ? 'selected' : '' }}>👨‍🏫 Pengajar (Dosen)</option>
                        </select>
                    </div>

                    <hr style="border: 0; border-top: 3px dashed var(--border); margin: 24px 0;">

                    <!-- Data Profil Akademik -->
                    <div class="neo-form-group">
                        <label id="label_induk" class="neo-label">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" id="input_induk" name="nomor_induk" class="neo-input" placeholder="Contoh: 220101001" value="{{ old('nomor_induk') }}" required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Nama Lengkap (Beserta Gelar jika Pengajar)</label>
                        <input type="text" name="nama_lengkap" class="neo-input" placeholder="Contoh: Budi Santoso" value="{{ old('nama_lengkap') }}" required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Alamat Email</label>
                        <input type="email" name="email" class="neo-input" placeholder="Contoh: budi@kampus.ac.id" value="{{ old('email') }}" required>
                    </div>

                    <hr style="border: 0; border-top: 3px dashed var(--border); margin: 24px 0;">

                    <!-- Data Kredensial Login -->
                    <div class="neo-form-group">
                        <label class="neo-label">Username untuk Login</label>
                        <input type="text" name="username" class="neo-input" placeholder="Contoh: budi_mhs" value="{{ old('username') }}" required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Password</label>
                        <input type="text" name="password" class="neo-input" value="{{ old('password', '12345') }}" required>
                        <small style="color: var(--muted-foreground); font-weight: bold;">*Default password disarankan: 12345 (dapat diubah oleh user nanti)</small>
                    </div>

                    <button type="submit" name="simpan_user" value="1" class="neo-btn" style="width: 100%; font-size: 1.1rem; padding: 14px;">+ Daftarkan Pengguna</button>
                </form>
            </div>

        </div>
    </main>
@endsection

@section('scripts')
<script>
// Script dinamis untuk mengubah label NIM / NIDN sesuai role yang dipilih
function ubahLabelInduk() {
    var role = document.getElementById('role_pilihan').value;
    var label = document.getElementById('label_induk');
    var input = document.getElementById('input_induk');

    if (role === 'Pengajar') {
        label.innerText = 'Nomor Induk Dosen Nasional (NIDN)';
        input.placeholder = 'Contoh: 0411028801';
    } else {
        label.innerText = 'Nomor Induk Mahasiswa (NIM)';
        input.placeholder = 'Contoh: 220101001';
    }
}
document.addEventListener('DOMContentLoaded', ubahLabelInduk);
</script>
@endsection

