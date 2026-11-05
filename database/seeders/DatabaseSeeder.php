<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('tb_user')->truncate();
        DB::table('tb_pengajar')->truncate();
        DB::table('tb_murid')->truncate();
        DB::table('tb_kelas')->truncate();
        DB::table('tb_peserta_kelas')->truncate();
        DB::table('tb_materi')->truncate();
        DB::table('tb_tugas')->truncate();
        DB::table('tb_kuis')->truncate();
        DB::table('tb_soal_kuis')->truncate();
        DB::table('tb_nilai_kuis')->truncate();
        DB::table('tb_forum_tag')->truncate();
        DB::table('tb_forum_post')->truncate();
        DB::table('tb_forum_post_tag')->truncate();
        DB::table('tb_forum_jawaban')->truncate();
        DB::table('tb_forum_vote')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $defaultPassword = Hash::make('password123');

        // 1. tb_user
        DB::table('tb_user')->insert([
            ['id_user' => 1, 'username' => 'admin', 'password' => $defaultPassword, 'role' => 'Admin'],
            ['id_user' => 2, 'username' => 'pimpinan', 'password' => $defaultPassword, 'role' => 'Pimpinan'],
            ['id_user' => 13, 'username' => 'dosen_rina', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 14, 'username' => 'dosen_andi', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 15, 'username' => 'dosen_siti', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 16, 'username' => 'dosen_eko', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 17, 'username' => 'dosen_dewi', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 18, 'username' => 'dosen_fajar', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 19, 'username' => 'dosen_hendra', 'password' => $defaultPassword, 'role' => 'Pengajar'],
            ['id_user' => 20, 'username' => 'student_adit', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 21, 'username' => 'student_bagus', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 22, 'username' => 'student_citra', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 23, 'username' => 'student_dina', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 24, 'username' => 'student_fahri', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 25, 'username' => 'student_gita', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 26, 'username' => 'student_heri', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 27, 'username' => 'student_indah', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 28, 'username' => 'student_joko', 'password' => $defaultPassword, 'role' => 'Murid'],
            ['id_user' => 29, 'username' => 'student_kania', 'password' => $defaultPassword, 'role' => 'Murid'],
        ]);

        // 2. tb_pengajar
        DB::table('tb_pengajar')->insert([
            ['nidn' => '0401018801', 'nama_pengajar' => 'Rina Wijaya, M.T.', 'email' => 'rina.wijaya@dosen.local', 'id_user' => 13],
            ['nidn' => '0401018902', 'nama_pengajar' => 'Andi Hidayat, M.Cs.', 'email' => 'andi.hidayat@dosen.local', 'id_user' => 14],
            ['nidn' => '0401019003', 'nama_pengajar' => 'Siti Rahma, M.Kom.', 'email' => 'siti.rahma@dosen.local', 'id_user' => 15],
            ['nidn' => '0401019104', 'nama_pengajar' => 'Eko Prasetyo, M.T.', 'email' => 'eko.prasetyo@dosen.local', 'id_user' => 16],
            ['nidn' => '0401019205', 'nama_pengajar' => 'Dewi Lestari, M.Kom.', 'email' => 'dewi.lestari@dosen.local', 'id_user' => 17],
            ['nidn' => '0401019306', 'nama_pengajar' => 'Fajar Nugroho, M.Cs.', 'email' => 'fajar.nugroho@dosen.local', 'id_user' => 18],
            ['nidn' => '0401019407', 'nama_pengajar' => 'Hendra Wijaya, Ph.D.', 'email' => 'hendra.wijaya@dosen.local', 'id_user' => 19],
        ]);

        // 3. tb_murid
        DB::table('tb_murid')->insert([
            ['nim' => '2201010011', 'nama_murid' => 'Adit Pratama', 'email' => 'adit@student.local', 'id_user' => 20],
            ['nim' => '2201010012', 'nama_murid' => 'Bagus Saputra', 'email' => 'bagus@student.local', 'id_user' => 21],
            ['nim' => '2201010013', 'nama_murid' => 'Citra Lestari', 'email' => 'citra@student.local', 'id_user' => 22],
            ['nim' => '2201010014', 'nama_murid' => 'Dina Ananda', 'email' => 'dina@student.local', 'id_user' => 23],
            ['nim' => '2201010015', 'nama_murid' => 'Fahri Ramadhan', 'email' => 'fahri@student.local', 'id_user' => 24],
            ['nim' => '2201010016', 'nama_murid' => 'Gita Cahyani', 'email' => 'gita@student.local', 'id_user' => 25],
            ['nim' => '2201010017', 'nama_murid' => 'Heri Setiawan', 'email' => 'heri@student.local', 'id_user' => 26],
            ['nim' => '2201010018', 'nama_murid' => 'Indah Permata', 'email' => 'indah@student.local', 'id_user' => 27],
            ['nim' => '2201010019', 'nama_murid' => 'Joko Susilo', 'email' => 'joko@student.local', 'id_user' => 28],
            ['nim' => '2201010020', 'nama_murid' => 'Kania Dewi', 'email' => 'kania@student.local', 'id_user' => 29],
        ]);

        // 4. tb_kelas
        DB::table('tb_kelas')->insert([
            ['id_kelas' => 8, 'nama_kelas' => 'Pemrograman Python untuk Data Science', 'deskripsi' => 'Mempelajari dasar-dasar pemrograman Python, pengolahan data menggunakan Pandas dan NumPy, hingga visualisasi data menggunakan Matplotlib dan Seaborn.', 'nidn' => '0401018801'],
            ['id_kelas' => 9, 'nama_kelas' => 'Pengembangan Aplikasi Mobile Android dengan Kotlin', 'deskripsi' => 'Membangun aplikasi Android modern menggunakan Kotlin, Jetpack Compose, arsitektur MVVM, dan integrasi REST API.', 'nidn' => '0401018902'],
            ['id_kelas' => 10, 'nama_kelas' => 'Desain dan Pemrograman Basis Data Lanjut', 'deskripsi' => 'Konsep perancangan basis data relasional tingkat lanjut, normalisasi, optimasi query SQL, indexing, stored procedure, dan transaksi ACID.', 'nidn' => '0401019003'],
            ['id_kelas' => 11, 'nama_kelas' => 'Pemrograman Web Modern dengan React & Tailwind', 'deskripsi' => 'Mengembangkan aplikasi Single Page Application (SPA) yang interaktif dan responsif menggunakan React 18, React Router, Tailwind CSS, dan state management.', 'nidn' => '0401019104'],
            ['id_kelas' => 12, 'nama_kelas' => 'Keamanan Jaringan dan Kriptografi', 'deskripsi' => 'Prinsip-prinsip keamanan sistem komputer, protokol enkripsi simetris & asimetris, hashing, deteksi intrusi, dan analisis kerentanan sistem.', 'nidn' => '0401019205'],
            ['id_kelas' => 13, 'nama_kelas' => 'Struktur Data & Algoritma Tingkat Lanjut', 'deskripsi' => 'Implementasi dan analisis kompleksitas (Big O) dari berbagai struktur data seperti Tree, Graph, Trie, serta algoritma pencarian dan pengurutan tingkat lanjut menggunakan C++.', 'nidn' => '0401019306'],
            ['id_kelas' => 14, 'nama_kelas' => 'Dasar-Dasar Kecerdasan Buatan & Machine Learning', 'deskripsi' => 'Pengantar teori dan implementasi praktis algoritma Machine Learning populer seperti Regresi Linier, Decision Tree, Random Forest, dan K-Means Clustering.', 'nidn' => '0401019407'],
            ['id_kelas' => 15, 'nama_kelas' => 'Pemrograman Berorientasi Objek (OOP) dengan Java', 'deskripsi' => 'Pemahaman mendalam mengenai paradigma OOP meliputi Class, Object, Inheritance, Polymorphism, Encapsulation, dan Abstraction menggunakan bahasa Java.', 'nidn' => '0401018801'],
            ['id_kelas' => 16, 'nama_kelas' => 'Arsitektur Perangkat Lunak & Clean Code', 'deskripsi' => 'Mempelajari best practice penulisan kode bersih, penerapan SOLID principles, Design Patterns, dan arsitektur modular pada sistem skala besar.', 'nidn' => '0401018902'],
            ['id_kelas' => 17, 'nama_kelas' => 'Pemrograman Backend dengan Node.js & Express', 'deskripsi' => 'Membangun RESTful API yang scalable menggunakan Node.js, framework Express.js, autentikasi JWT, middleware, dan koneksi ke database NoSQL & SQL.', 'nidn' => '0401019104'],
        ]);

        // 5. tb_peserta_kelas
        DB::table('tb_peserta_kelas')->insert([
            ['id_kelas' => 8, 'nim' => '2201010011'],
            ['id_kelas' => 8, 'nim' => '2201010012'],
            ['id_kelas' => 8, 'nim' => '2201010013'],
            ['id_kelas' => 9, 'nim' => '2201010011'],
            ['id_kelas' => 9, 'nim' => '2201010014'],
            ['id_kelas' => 10, 'nim' => '2201010012'],
            ['id_kelas' => 10, 'nim' => '2201010015'],
            ['id_kelas' => 11, 'nim' => '2201010011'],
            ['id_kelas' => 11, 'nim' => '2201010013'],
            ['id_kelas' => 11, 'nim' => '2201010016'],
            ['id_kelas' => 12, 'nim' => '2201010017'],
            ['id_kelas' => 13, 'nim' => '2201010018'],
            ['id_kelas' => 14, 'nim' => '2201010019'],
            ['id_kelas' => 15, 'nim' => '2201010020'],
            ['id_kelas' => 16, 'nim' => '2201010011'],
            ['id_kelas' => 17, 'nim' => '2201010012'],
        ]);

        // 6. tb_materi
        DB::table('tb_materi')->insert([
            ['id_materi' => 14, 'id_kelas' => 8, 'judul_materi' => 'Pengenalan Python & Setup Environment', 'deskripsi' => 'Pengantar sintaks dasar Python, tipe data, variabel, kontrol alur, dan cara mengonfigurasi Jupyter Notebook di VS Code.', 'file_materi' => 'python_intro.pdf'],
            ['id_materi' => 15, 'id_kelas' => 8, 'judul_materi' => 'Manipulasi Data Menggunakan Pandas', 'deskripsi' => 'Mempelajari DataFrame, Series, indexing, slicing, membaca file CSV/Excel, serta penanganan missing values.', 'file_materi' => 'pandas_dasar.pdf'],
            ['id_materi' => 16, 'id_kelas' => 9, 'judul_materi' => 'Kotlin Fundamentals & Null Safety', 'deskripsi' => 'Memahami keunggulan Kotlin: val vs var, type inference, nullable types, safe call operator, dan Elvis operator.', 'file_materi' => 'kotlin_null_safety.pdf'],
            ['id_materi' => 17, 'id_kelas' => 10, 'judul_materi' => 'Normalisasi Database (1NF sampai 3NF)', 'deskripsi' => 'Langkah-langkah sistematis memecah tabel abnormal menjadi bentuk normal untuk meminimalisasi redundansi data.', 'file_materi' => 'normalisasi_database.pdf'],
            ['id_materi' => 18, 'id_kelas' => 11, 'judul_materi' => 'Komponen React, Props, dan State Hook', 'deskripsi' => 'Membangun reusable functional component, mengalirkan data via props, serta mengelola reaktivitas menggunakan useState.', 'file_materi' => 'react_hooks.pdf'],
            ['id_materi' => 19, 'id_kelas' => 12, 'judul_materi' => 'Kriptografi Kunci Publik & Hashing', 'deskripsi' => 'Perbedaan enkripsi simetris (AES) dan asimetris (RSA), konsep tanda tangan digital, serta fungsi hash SHA-256.', 'file_materi' => 'kriptografi_dasar.pdf'],
            ['id_materi' => 20, 'id_kelas' => 13, 'judul_materi' => 'Binary Search Tree & Graph Traversal', 'deskripsi' => 'Konsep pohon biner seimbang, operasi insert/delete/search, serta algoritma penjelajahan graf BFS dan DFS.', 'file_materi' => 'tree_graph_algo.pdf'],
            ['id_materi' => 21, 'id_kelas' => 14, 'judul_materi' => 'Pengantar Machine Learning: Supervised vs Unsupervised', 'deskripsi' => 'Perbedaan Machine Learning dengan pemrograman tradisional, konsep data training/testing, serta Supervised vs Unsupervised.', 'file_materi' => 'intro_ml.pdf'],
            ['id_materi' => 22, 'id_kelas' => 15, 'judul_materi' => 'Enkapsulasi, Inheritansi, dan Polimorfisme', 'deskripsi' => 'Memahami tiga pilar utama OOP: membatasi akses data (enkapsulasi), pewarisan kelas (inheritansi), dan banyak bentuk objek (polimorfisme).', 'file_materi' => 'konsep_oop.pdf'],
            ['id_materi' => 23, 'id_kelas' => 16, 'judul_materi' => 'Pola Desain MVC & Repository Pattern', 'deskripsi' => 'Membahas arsitektur Model-View-Controller (MVC) serta memisahkan logika akses data menggunakan Repository Pattern.', 'file_materi' => 'pattern_mvc_repo.pdf'],
            ['id_materi' => 24, 'id_kelas' => 17, 'judul_materi' => 'RESTful API Concept & Express.js Router', 'deskripsi' => 'Mempelajari metode HTTP (GET, POST, PUT, DELETE), kode status HTTP, dan pengaturan routing menggunakan Express Router.', 'file_materi' => 'express_router.pdf'],
        ]);

        // 7. tb_tugas
        $now = Carbon::now();
        DB::table('tb_tugas')->insert([
            ['id_tugas' => 3, 'id_kelas' => 8, 'judul_tugas' => 'Tugas 1: Membuat Kalkulator Sederhana', 'deskripsi' => 'Buatlah program Python interaktif CLI untuk melakukan operasi matematika dasar (tambah, kurang, kali, bagi) berdasarkan input user.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 4, 'id_kelas' => 9, 'judul_tugas' => 'Tugas 1: Membuat Layout Profil Sederhana', 'deskripsi' => 'Buatlah layout profil pengguna menggunakan Jetpack Compose, yang terdiri dari foto profil, nama, deskripsi diri, dan tombol aksi.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 5, 'id_kelas' => 10, 'judul_tugas' => 'Tugas 1: Merancang ERD Toko Online', 'deskripsi' => 'Rancanglah Entity Relationship Diagram (ERD) untuk sistem e-commerce sederhana dengan entitas Customer, Order, Product, dan Payment.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 6, 'id_kelas' => 11, 'judul_tugas' => 'Tugas 1: Membuat Todo-List App', 'deskripsi' => 'Buatlah aplikasi catatan tugas (Todo-List) sederhana berbasis React yang mendukung tambah data, centang selesai, dan hapus data.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 7, 'id_kelas' => 12, 'judul_tugas' => 'Tugas 1: Analisis Kerentanan SQL Injection', 'deskripsi' => 'Tulis laporan analisis mengenai bahaya SQL Injection, bagaimana penyerang mengeksploitasinya, dan langkah mitigasi menggunakan Prepared Statements.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 8, 'id_kelas' => 13, 'judul_tugas' => 'Tugas 1: Implementasi Linked List di C++', 'deskripsi' => 'Tuliskan kode program lengkap menggunakan C++ untuk membuat Single Linked List beserta fungsi insertNode, deleteNode, dan printList.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 9, 'id_kelas' => 14, 'judul_tugas' => 'Tugas 1: Implementasi K-Nearest Neighbors', 'deskripsi' => 'Gunakan library scikit-learn Python untuk melatih model K-Nearest Neighbors (KNN) menggunakan dataset Iris, lalu ukur akurasinya.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 10, 'id_kelas' => 15, 'judul_tugas' => 'Tugas 1: Membuat Simulasi Sistem Bank', 'deskripsi' => 'Buatlah program simulasi perbankan menggunakan prinsip OOP. Harus memiliki class Account dengan enkapsulasi saldo, deposit(), dan withdraw().', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 11, 'id_kelas' => 16, 'judul_tugas' => 'Tugas 1: Implementasi Singleton & Factory Pattern', 'deskripsi' => 'Implementasikan pola desain Singleton dan Factory Pattern dalam bahasa pemrograman pilihan Anda (PHP / Java / JS). Sertakan contoh kasus penggunaannya.', 'deadline' => $now->copy()->addDays(7)],
            ['id_tugas' => 12, 'id_kelas' => 17, 'judul_tugas' => 'Tugas 1: Membuat REST API CRUD Product', 'deskripsi' => 'Buatlah RESTful API sederhana dengan Node.js & Express untuk menyimpan data Product (id, nama, harga, stok). Harus mendukung operasi CRUD lengkap.', 'deadline' => $now->copy()->addDays(7)],
        ]);

        // 8. Sample Kuis & Soal
        $kuisId = DB::table('tb_kuis')->insertGetId([
            'id_kelas' => 8,
            'judul_kuis' => 'Kuis 1: Dasar Sintaks Python & Variabel',
            'deskripsi' => 'Uji pemahaman Anda tentang tipe data dasar, percabangan, dan list comprehension di Python.',
            'durasi_menit' => 15,
            'tgl_dibuat' => $now,
        ]);

        DB::table('tb_soal_kuis')->insert([
            [
                'id_kuis' => $kuisId,
                'pertanyaan' => 'Manakah tipe data di Python yang bersifat Immutable (tidak dapat diubah)?',
                'opsi_a' => 'List',
                'opsi_b' => 'Tuple',
                'opsi_c' => 'Dictionary',
                'opsi_d' => 'Set',
                'jawaban_benar' => 'B'
            ],
            [
                'id_kuis' => $kuisId,
                'pertanyaan' => 'Fungsi built-in mana yang digunakan untuk mengetahui jumlah elemen dalam array/list?',
                'opsi_a' => 'count()',
                'opsi_b' => 'size()',
                'opsi_c' => 'len()',
                'opsi_d' => 'length()',
                'jawaban_benar' => 'C'
            ],
            [
                'id_kuis' => $kuisId,
                'pertanyaan' => 'Keyword apa yang digunakan untuk mendefinisikan fungsi di Python?',
                'opsi_a' => 'function',
                'opsi_b' => 'func',
                'opsi_c' => 'define',
                'opsi_d' => 'def',
                'jawaban_benar' => 'D'
            ],
        ]);

        // 9. Forum Tags
        DB::table('tb_forum_tag')->insert([
            ['id_tag' => 1, 'nama_tag' => 'JavaScript', 'warna' => '#F7DF1E', 'icon' => 'bxl-javascript'],
            ['id_tag' => 2, 'nama_tag' => 'Python', 'warna' => '#3776AB', 'icon' => 'bxl-python'],
            ['id_tag' => 3, 'nama_tag' => 'PHP', 'warna' => '#777BB4', 'icon' => 'bxl-php'],
            ['id_tag' => 4, 'nama_tag' => 'React', 'warna' => '#61DAFB', 'icon' => 'bxl-react'],
            ['id_tag' => 5, 'nama_tag' => 'Vue.js', 'warna' => '#4FC08D', 'icon' => 'bxl-vuejs'],
            ['id_tag' => 6, 'nama_tag' => 'Database & SQL', 'warna' => '#00758F', 'icon' => 'bxs-data'],
            ['id_tag' => 7, 'nama_tag' => 'PostgreSQL', 'warna' => '#336791', 'icon' => 'bxs-data'],
            ['id_tag' => 8, 'nama_tag' => 'Flutter', 'warna' => '#02569B', 'icon' => 'bxl-flutter'],
            ['id_tag' => 9, 'nama_tag' => 'Kotlin', 'warna' => '#7F52FF', 'icon' => 'bxl-android'],
            ['id_tag' => 10, 'nama_tag' => 'CSS & UI', 'warna' => '#264DE4', 'icon' => 'bxl-css3'],
            ['id_tag' => 11, 'nama_tag' => 'Git & GitHub', 'warna' => '#F05032', 'icon' => 'bxl-git'],
            ['id_tag' => 12, 'nama_tag' => 'Docker & DevOps', 'warna' => '#2496ED', 'icon' => 'bxl-docker'],
            ['id_tag' => 13, 'nama_tag' => 'Cyber Security', 'warna' => '#FF4C4C', 'icon' => 'bx-shield-quarter'],
            ['id_tag' => 14, 'nama_tag' => 'Frontend', 'warna' => '#FF6B6B', 'icon' => 'bx-layout'],
            ['id_tag' => 15, 'nama_tag' => 'Backend', 'warna' => '#4ECDC4', 'icon' => 'bx-server'],
            ['id_tag' => 16, 'nama_tag' => 'Machine Learning', 'warna' => '#FFBE0B', 'icon' => 'bx-brain'],
            ['id_tag' => 17, 'nama_tag' => 'Algoritma & Struktur Data', 'warna' => '#8338EC', 'icon' => 'bx-code-block'],
            ['id_tag' => 18, 'nama_tag' => 'Tips & Tutorial', 'warna' => '#3A86FF', 'icon' => 'bx-bulb'],
            ['id_tag' => 19, 'nama_tag' => 'Career & Job', 'warna' => '#06D6A0', 'icon' => 'bx-briefcase'],
            ['id_tag' => 20, 'nama_tag' => 'Diskusi Umum', 'warna' => '#FFD700', 'icon' => 'bx-conversation'],
        ]);

        // 10. Forum Posts
        DB::table('tb_forum_post')->insert([
            [
                'id_post' => 1,
                'id_user' => 13,
                'judul' => 'Cara Optimasi Query SQL dengan Indexing yang Benar untuk Jutaan Record',
                'konten' => '<p>Halo semua! Seringkali aplikasi kita mulai lambat saat database sudah mencapai ratusan ribu hingga jutaan baris data. Masalah utamanya biasanya ada di query yang melakukan <em>Full Table Scan</em>.</p><p>Berikut tips praktis indexing yang wajib dipahami:</p><ol><li><strong>Identifikasi kolom WHERE dan JOIN:</strong> Buat index pada kolom yang paling sering difilter.</li><li><strong>Gunakan Composite Index:</strong> Jika memfilter 2 kolom sekaligus.</li><li><strong>Hindari Over-indexing:</strong> Setiap index mempercepat SELECT tapi memperlambat INSERT/UPDATE.</li><li><strong>Gunakan EXPLAIN:</strong> Selalu cek query plan dengan perintah <code>EXPLAIN SELECT ...</code></li></ol>',
                'tipe' => 'berbagi',
                'status' => 'open',
                'views' => 312,
                'is_pinned' => 1,
                'is_announcement' => 0,
                'tgl_post' => $now->copy()->subDays(2),
            ],
            [
                'id_post' => 2,
                'id_user' => 20,
                'judul' => 'Kenapa useEffect di React 18 terpanggil dua kali saat mode development?',
                'konten' => '<p>Halo teman-teman dev! Saya baru belajar React 18 dan perhatiin kalau <code>useEffect</code> selalu run 2 kali saat pertama kali mount di local server.</p><p>Apakah ini bug atau memang behaviour resmi React 18? Bagaimana cara mengatasinya agar API tidak tertembak dua kali?</p>',
                'tipe' => 'pertanyaan',
                'status' => 'solved',
                'views' => 198,
                'is_pinned' => 0,
                'is_announcement' => 0,
                'tgl_post' => $now->copy()->subHours(18),
            ],
            [
                'id_post' => 6,
                'id_user' => 14,
                'judul' => '[PENGUMUMAN] Hackathon Mingguan #12 Telah Dibuka: Bikin Sorting Visualizer JS & Canvas!',
                'konten' => '<p>Tantangan terbuka untuk seluruh mahasiswa! Tanpa library tambahan. Buat kontrol slider kecepatan animasi dan tombol pause.</p><p><strong>Deadline: 3 hari</strong></p><p>Source code terbaik akan direview di YouTube DevForum.</p>',
                'tipe' => 'berbagi',
                'status' => 'open',
                'views' => 89,
                'is_pinned' => 1,
                'is_announcement' => 1,
                'tgl_post' => $now->copy()->subHours(6),
            ]
        ]);

        // 11. Relasi Post - Tag
        DB::table('tb_forum_post_tag')->insert([
            ['id_post' => 1, 'id_tag' => 6],
            ['id_post' => 1, 'id_tag' => 7],
            ['id_post' => 1, 'id_tag' => 15],
            ['id_post' => 2, 'id_tag' => 4],
            ['id_post' => 2, 'id_tag' => 1],
            ['id_post' => 2, 'id_tag' => 14],
            ['id_post' => 6, 'id_tag' => 1],
            ['id_post' => 6, 'id_tag' => 17],
        ]);

        // 12. Jawaban Forum
        DB::table('tb_forum_jawaban')->insert([
            [
                'id_jawaban' => 1,
                'id_post' => 1,
                'id_user' => 16,
                'konten' => '<p>Mantap penjelasannya! Tambahan: gunakan <code>pg_stat_statements</code> untuk monitoring query yang paling sering dipanggil.</p>',
                'is_solusi' => 0,
                'tgl_jawaban' => $now->copy()->subHour(),
            ],
            [
                'id_jawaban' => 2,
                'id_post' => 2,
                'id_user' => 15,
                'konten' => '<p>Di React 18 StrictMode memang sengaja double-invoke useEffect di development untuk memastikan cleanup berjalan dengan benar. Gunakan AbortController saat fetch.</p>',
                'is_solusi' => 1,
                'tgl_jawaban' => $now->copy()->subMinutes(20),
            ]
        ]);

        // 13. Votes
        DB::table('tb_forum_vote')->insert([
            ['id_vote' => 1, 'id_user' => 20, 'id_post' => 1, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 2, 'id_user' => 21, 'id_post' => 1, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 3, 'id_user' => 20, 'id_post' => 2, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
        ]);
    }
}
