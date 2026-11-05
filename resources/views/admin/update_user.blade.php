@extends('layouts.app')

@section('title', 'Update Pengguna - Admin E-Learning')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="neo-layout-container">
        <div class="neo-content-inner" style="max-width: 1000px; margin: 0 auto; padding-top: 20px;">
            <div style="margin-bottom: 20px;">
                <a href="{{ route('dashboard') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); font-size: 0.85rem; padding: 8px 16px;">&larr; Kembali ke Dashboard</a>
            </div>
            
            <h2>Edit Data {{ $role_target }}</h2>

            @if (!empty($pesan))
                <div class="neo-box" style="background-color: var(--primary); color: #fff; font-weight: bold;">
                    {{ $pesan }}
                </div>
            @endif
            
            <div class="neo-box" style="max-width: 650px;">
                <form method="POST" action="{{ route('admin.users.update', $id_target) }}">
                    @csrf
                    <div class="neo-form-group">
                        <label class="neo-label">Hak Akses (Role)</label>
                        <input type="text" class="neo-input" value="{{ $role_target }}" disabled style="background-color: var(--muted); cursor: not-allowed; font-weight: bold;">
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">{{ $role_target === 'Pengajar' ? 'NIDN' : 'NIM' }} (Tidak dapat diubah)</label>
                        <input type="text" class="neo-input" value="{{ $nomor_induk }}" disabled style="background-color: var(--muted); cursor: not-allowed; font-weight: bold;">
                    </div>

                    <hr style="border: 0; border-top: 3px dashed var(--border); margin: 24px 0;">

                    <div class="neo-form-group">
                        <label class="neo-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="neo-input" value="{{ $nama_lengkap }}" required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Alamat Email</label>
                        <input type="email" name="email" class="neo-input" value="{{ $email }}" required>
                    </div>

                    <hr style="border: 0; border-top: 3px dashed var(--border); margin: 24px 0;">

                    <div class="neo-form-group">
                        <label class="neo-label">Username Login</label>
                        <input type="text" name="username" class="neo-input" value="{{ $username_target }}" required>
                    </div>

                    <div class="neo-form-group">
                        <label class="neo-label">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="neo-input" placeholder="Kosongkan jika tidak ingin mengganti password">
                        <small style="color: var(--muted-foreground); font-weight: bold;">*Isi kolom ini hanya jika ingin mereset password pengguna.</small>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="submit" name="update_user" value="1" class="neo-btn" style="flex: 1; font-size: 1.05rem; padding: 14px; background-color: var(--secondary); color: #000;">💾 Simpan Perubahan</button>
                        <a href="{{ route('dashboard') }}" class="neo-btn" style="background-color: var(--muted); color: var(--foreground); text-align: center; padding: 14px 24px;">Batal</a>
                    </div>
                </form>
            </div>

        </div>
    </main>
@endsection

