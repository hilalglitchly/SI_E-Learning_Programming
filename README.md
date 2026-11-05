# dYs? E-Learning Programming (Laravel 10 Edition)

Sistem Informasi E-Learning dengan arsitektur **Laravel 10 (MVC)** dan tema antarmuka **Neo-Brutalism**.

---

## 🚀 Cara Menjalankan Proyek

1. **Pastikan MySQL di Laragon aktif.**
2. Buka terminal di folder project ini:
   ```bash
   cd C:\laragon\www\SI_E-Learning_Laravel
   ```
3. Database `db_elearning_laravel` sudah dibuat dan dimigrasi secara otomatis.
   Jika ingin melakukan reset & re-seed data dari awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   Buka di browser: **`http://127.0.0.1:8000`** atau melalui Virtual Host Laragon: **`http://si_e-learning_laravel.test`**.

---

## 🔑 Akun Demo Pengujian

Semua akun memiliki password default: `password123`

| Role | Username | Nama | Hak Akses |
|---|---|---|---|
| **Murid** | `student_adit` | Adit Pratama | Mengikuti kelas, kumpul tugas, kerjakan kuis, forum diskusi |
| **Pengajar** | `dosen_rina` | Rina Wijaya, M.T. | Mengelola kelas, upload materi, nilai tugas, buat kuis |
| **Admin** | `admin` | Administrator | Manajemen seluruh pengguna, kelola tugas, laporan sistem |
| **Pimpinan** | `pimpinan` | Pimpinan Institusi | Memantau seluruh rekapitulasi & laporan akademik |

---

## 🛠️ Fitur yang Berhasil Dimigrasi

- [x] **Autentikasi Multi-Role** (Admin, Pengajar, Murid, Pimpinan) menggunakan `Illuminate\Support\Facades\Auth` dan Role Middleware kustom.
- [x] **Manajemen Kelas & Kurikulum** (Katalog, Detail Ruang Kelas, Enrollment Mahasiswa, Modul Materi).
- [x] **Tugas & Penilaian** (Filter status pengerjaan, upload berkas jawaban, form grading guru).
- [x] **Engine Kuis Online** (Bank soal pilihan ganda, countdown timer interaktif, kalkulasi nilai otomatis).
- [x] **E-FORVM Diskusi Global** (Filter tags, upvote/downvote AJAX, mark solved answer, simpan/bookmark thread).
- [x] **Laporan & Rekapitulasi** (Cetak PDF/Print friendly, statistik aktivitas pembelajaran).
- [x] **UI Neo-Brutalism Khas** (Blade Components, Custom Cursor sound, Dark Mode support tanpa FOUC).
