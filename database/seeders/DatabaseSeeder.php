<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        $tables = [
            'tb_user', 'tb_pengajar', 'tb_murid', 'tb_kelas', 'tb_peserta_kelas',
            'tb_materi', 'tb_tugas', 'tb_submission', 'tb_diskusi', 'tb_balasan_diskusi',
            'tb_kuis', 'tb_soal_kuis', 'tb_nilai_kuis', 'tb_notifikasi', 'tb_langganan',
            'tb_forum_tag', 'tb_forum_post', 'tb_forum_post_tag', 'tb_forum_jawaban',
            'tb_forum_vote', 'tb_forum_simpan', 'tb_forum_media',
        ];
        foreach ($tables as $t) {
            DB::table($t)->truncate();
        }

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $defaultPassword = Hash::make('password123');
        $now = Carbon::now();

        // 1. tb_user (1 Admin + 1 Pimpinan + 12 Pengajar + 20 Murid = 34 Pengguna)
        $pengajarProfiles = [
            ['id_user' => 13, 'username' => 'dosen_rina',   'nidn' => '0401018801', 'nama' => 'Rina Wijaya, M.T.',        'email' => 'rina.wijaya@dosen.local'],
            ['id_user' => 14, 'username' => 'dosen_andi',   'nidn' => '0401018902', 'nama' => 'Andi Hidayat, M.Cs.',      'email' => 'andi.hidayat@dosen.local'],
            ['id_user' => 15, 'username' => 'dosen_siti',   'nidn' => '0401019003', 'nama' => 'Siti Rahma, M.Kom.',       'email' => 'siti.rahma@dosen.local'],
            ['id_user' => 16, 'username' => 'dosen_eko',    'nidn' => '0401019104', 'nama' => 'Eko Prasetyo, M.T.',       'email' => 'eko.prasetyo@dosen.local'],
            ['id_user' => 17, 'username' => 'dosen_dewi',   'nidn' => '0401019205', 'nama' => 'Dewi Lestari, M.Kom.',     'email' => 'dewi.lestari@dosen.local'],
            ['id_user' => 18, 'username' => 'dosen_fajar',  'nidn' => '0401019306', 'nama' => 'Fajar Nugroho, M.Cs.',     'email' => 'fajar.nugroho@dosen.local'],
            ['id_user' => 19, 'username' => 'dosen_hendra', 'nidn' => '0401019407', 'nama' => 'Hendra Wijaya, Ph.D.',     'email' => 'hendra.wijaya@dosen.local'],
            ['id_user' => 30, 'username' => 'dosen_bima',   'nidn' => '0401019508', 'nama' => 'Bima Sakti, M.Kom.',       'email' => 'bima.sakti@dosen.local'],
            ['id_user' => 31, 'username' => 'dosen_nadia',  'nidn' => '0401019609', 'nama' => 'Nadia Kusuma, M.T.',       'email' => 'nadia.kusuma@dosen.local'],
            ['id_user' => 32, 'username' => 'dosen_rehan',  'nidn' => '0401019710', 'nama' => 'Rehan Mahendra, M.Cs.',    'email' => 'rehan.mahendra@dosen.local'],
            ['id_user' => 33, 'username' => 'dosen_tari',   'nidn' => '0401019811', 'nama' => 'Tari Wulandari, M.Kom.',   'email' => 'tari.wulandari@dosen.local'],
            ['id_user' => 34, 'username' => 'dosen_yusuf',  'nidn' => '0401019912', 'nama' => 'Yusuf Alamsyah, M.T.I.',   'email' => 'yusuf.alamsyah@dosen.local'],
        ];

        $muridProfiles = [
            ['id_user' => 20, 'username' => 'student_adit',   'nim' => '2201010011', 'nama' => 'Adit Pratama',     'email' => 'adit@student.local'],
            ['id_user' => 21, 'username' => 'student_bagus',  'nim' => '2201010012', 'nama' => 'Bagus Saputra',    'email' => 'bagus@student.local'],
            ['id_user' => 22, 'username' => 'student_citra',  'nim' => '2201010013', 'nama' => 'Citra Lestari',    'email' => 'citra@student.local'],
            ['id_user' => 23, 'username' => 'student_dina',   'nim' => '2201010014', 'nama' => 'Dina Ananda',      'email' => 'dina@student.local'],
            ['id_user' => 24, 'username' => 'student_fahri',  'nim' => '2201010015', 'nama' => 'Fahri Ramadhan',   'email' => 'fahri@student.local'],
            ['id_user' => 25, 'username' => 'student_gita',   'nim' => '2201010016', 'nama' => 'Gita Cahyani',     'email' => 'gita@student.local'],
            ['id_user' => 26, 'username' => 'student_heri',   'nim' => '2201010017', 'nama' => 'Heri Setiawan',    'email' => 'heri@student.local'],
            ['id_user' => 27, 'username' => 'student_indah',  'nim' => '2201010018', 'nama' => 'Indah Permata',    'email' => 'indah@student.local'],
            ['id_user' => 28, 'username' => 'student_joko',   'nim' => '2201010019', 'nama' => 'Joko Susilo',      'email' => 'joko@student.local'],
            ['id_user' => 29, 'username' => 'student_kania',  'nim' => '2201010020', 'nama' => 'Kania Dewi',       'email' => 'kania@student.local'],
            ['id_user' => 35, 'username' => 'student_lutfi',  'nim' => '2201010021', 'nama' => 'Lutfi Hakim',      'email' => 'lutfi@student.local'],
            ['id_user' => 36, 'username' => 'student_maya',   'nim' => '2201010022', 'nama' => 'Maya Sari',        'email' => 'maya@student.local'],
            ['id_user' => 37, 'username' => 'student_naufal', 'nim' => '2201010023', 'nama' => 'Naufal Rizky',     'email' => 'naufal@student.local'],
            ['id_user' => 38, 'username' => 'student_olivia', 'nim' => '2201010024', 'nama' => 'Olivia Putri',     'email' => 'olivia@student.local'],
            ['id_user' => 39, 'username' => 'student_putra',  'nim' => '2201010025', 'nama' => 'Putra Mahendra',   'email' => 'putra@student.local'],
            ['id_user' => 40, 'username' => 'student_qori',   'nim' => '2201010026', 'nama' => 'Qori Azzahra',     'email' => 'qori@student.local'],
            ['id_user' => 41, 'username' => 'student_rafi',   'nim' => '2201010027', 'nama' => 'Rafi Kurniawan',   'email' => 'rafi@student.local'],
            ['id_user' => 42, 'username' => 'student_salma',  'nim' => '2201010028', 'nama' => 'Salma Nabila',     'email' => 'salma@student.local'],
            ['id_user' => 43, 'username' => 'student_tiara',  'nim' => '2201010029', 'nama' => 'Tiara Anggraini',  'email' => 'tiara@student.local'],
            ['id_user' => 44, 'username' => 'student_umar',   'nim' => '2201010030', 'nama' => 'Umar Faruq',       'email' => 'umar@student.local'],
        ];

        $userRows = [
            ['id_user' => 1, 'username' => 'admin',    'password' => $defaultPassword, 'role' => 'Admin', 'email_verified_at' => $now],
            ['id_user' => 2, 'username' => 'pimpinan', 'password' => $defaultPassword, 'role' => 'Pimpinan', 'email_verified_at' => $now],
        ];
        foreach ($pengajarProfiles as $p) {
            $userRows[] = ['id_user' => $p['id_user'], 'username' => $p['username'], 'password' => $defaultPassword, 'role' => 'Pengajar', 'email_verified_at' => $now];
        }
        foreach ($muridProfiles as $m) {
            $userRows[] = ['id_user' => $m['id_user'], 'username' => $m['username'], 'password' => $defaultPassword, 'role' => 'Murid', 'email_verified_at' => $now];
        }
        DB::table('tb_user')->insert($userRows);

        // 2. tb_pengajar (12 Pengajar)
        DB::table('tb_pengajar')->insert(array_map(fn($p) => [
            'nidn' => $p['nidn'],
            'nama_pengajar' => $p['nama'],
            'email' => $p['email'],
            'id_user' => $p['id_user'],
        ], $pengajarProfiles));

        // 3. tb_murid (20 Murid)
        DB::table('tb_murid')->insert(array_map(fn($m) => [
            'nim' => $m['nim'],
            'nama_murid' => $m['nama'],
            'email' => $m['email'],
            'id_user' => $m['id_user'],
        ], $muridProfiles));

        // 4. tb_kelas (30 Kelas Pemrograman Relevan 2025/2026: 18 Gratis + 12 Pro)
        $courses = [
            [
                'id_kelas' => 1, 'nidn' => '0401018801', 'is_premium' => false,
                'nama_kelas' => 'Python untuk Data Science & Analisis Modern',
                'deskripsi' => 'Pelajari Python modern, pengolahan dataset dengan Pandas & Polars, NumPy, serta visualisasi analitik interaktif untuk kebutuhan data profesional.',
                'materi' => [
                    ['Sintaks Python Modern & Virtual Environment', 'Memahami tipe data, list comprehension, type hints, manajemen paket dengan uv/pip, dan setup VS Code.', 'python_modern_env.pdf'],
                    ['Analisis Data dengan Pandas & Visualisasi', 'Membersihkan data kotor, agregasi groupby, pivot table, serta membuat grafik analitik dengan Matplotlib & Seaborn.', 'python_pandas_viz.pdf'],
                ],
                'tugas' => ['Analisis Dataset Penjualan E-Commerce', 'Lakukan pembersihan data CSV, hitung tren penjualan bulanan dengan Pandas, dan sajikan 3 grafik insight utama.'],
            ],
            [
                'id_kelas' => 2, 'nidn' => '0401018902', 'is_premium' => false,
                'nama_kelas' => 'Android Modern dengan Kotlin & Jetpack Compose',
                'deskripsi' => 'Bangun aplikasi Android deklaratif menggunakan Kotlin Coroutines, Jetpack Compose, state management MVVM, dan konsumsi REST API.',
                'materi' => [
                    ['Kotlin Null Safety & Coroutines Dasar', 'Penggunaan val/var, data class, sealed class, null safety, dan eksekusi asynchronous aman memakai Coroutines.', 'kotlin_coroutines.pdf'],
                    ['UI Deklaratif dengan Jetpack Compose & MVVM', 'Menyusun Composable reusable, mengelola StateFlow, navigasi layar, dan menghubungkan ViewModel ke Retrofit.', 'jetpack_compose_mvvm.pdf'],
                ],
                'tugas' => ['Aplikasi Katalog Film Jetpack Compose', 'Buat aplikasi daftar film dengan layar detail, pencarian real-time, dan arsitektur MVVM berbasis Compose.'],
            ],
            [
                'id_kelas' => 3, 'nidn' => '0401019003', 'is_premium' => false,
                'nama_kelas' => 'PostgreSQL, Desain Database & Optimasi Query',
                'deskripsi' => 'Kuasai normalisasi 1NF–3NF, relasi foreign key, transaksi ACID, B-Tree & GIN indexing, serta pembacaan EXPLAIN ANALYZE untuk jutaan baris.',
                'materi' => [
                    ['Perancangan ERD & Normalisasi 1NF hingga 3NF', 'Memecah anomali insert/update/delete, memilih surrogate vs natural key, dan merancang relasi M:N yang bersih.', 'postgres_normalisasi.pdf'],
                    ['Indexing, Query Plan & Transaksi ACID', 'Menganalisis bottleneck dengan EXPLAIN ANALYZE, membuat composite index, serta mencegah race condition dengan row locking.', 'postgres_indexing_acid.pdf'],
                ],
                'tugas' => ['Perancangan Skema & Optimasi Query Marketplace', 'Buat ERD ternormalisasi 3NF lengkap dengan DDL PostgreSQL serta studi perbandingan query sebelum dan sesudah indexing.'],
            ],
            [
                'id_kelas' => 4, 'nidn' => '0401019104', 'is_premium' => false,
                'nama_kelas' => 'React 19, TypeScript & Tailwind CSS',
                'deskripsi' => 'Kembangkan antarmuka web interaktif bertipe aman menggunakan React 19, Hooks modern, TypeScript strict mode, dan styling responsif Tailwind CSS.',
                'materi' => [
                    ['Komponen React, TypeScript Props & Hooks', 'Membuat komponen reusable bertipe ketat, mengelola state/effect secara bersih, dan menghindari render berlebih.', 'react_ts_hooks.pdf'],
                    ['Routing, Data Fetching & Layout Tailwind CSS', 'Integrasi TanStack Query / fetch state, form handling, dark mode, serta membangun desain responsif mobile-first.', 'react_tailwind_layout.pdf'],
                ],
                'tugas' => ['Dashboard Manajemen Tugas dengan React + TS', 'Bangun aplikasi Kanban/Todo interaktif dengan filter status, pencarian instan, dan penyimpanan lokal.'],
            ],
            [
                'id_kelas' => 5, 'nidn' => '0401019205', 'is_premium' => true,
                'nama_kelas' => 'Web Security, OWASP Top 10 & Kriptografi',
                'deskripsi' => 'Pelajari pencegahan nyata SQL Injection, XSS, CSRF, IDOR, broken auth, hardening HTTP headers, hashing password modern, dan enkripsi TLS.',
                'materi' => [
                    ['Mitigasi OWASP Top 10: SQLi, XSS, CSRF & IDOR', 'Studi kasus eksploitasi dan pertahanan berlapis memakai prepared statements, output escaping, CSP, dan otorisasi server-side.', 'owasp_top10_defense.pdf'],
                    ['Kriptografi Praktis, Hashing & Secure Session', 'Perbedaan enkripsi simetris AES-GCM, asimetris RSA/Ed25519, hashing Argon2/Bcrypt, serta keamanan cookie sesi.', 'kriptografi_praktis.pdf'],
                ],
                'tugas' => ['Audit & Hardening Aplikasi Web Rentan', 'Temukan minimal 5 celah OWASP pada contoh aplikasi web, tuliskan dampaknya, lalu susun patch perbaikannya.'],
            ],
            [
                'id_kelas' => 6, 'nidn' => '0401019306', 'is_premium' => false,
                'nama_kelas' => 'Struktur Data & Algoritma Interview Coding (C++)',
                'deskripsi' => 'Pahami kompleksitas Big O, HashMap, Stack/Queue, Tree, Heap, Graph BFS/DFS, hingga Dynamic Programming untuk persiapan coding interview.',
                'materi' => [
                    ['Kompleksitas Big O, Array, Sliding Window & HashMap', 'Teknik two-pointer, sliding window, prefix sum, dan analisis waktu/memori untuk soal teknis industri.', 'dsa_big_o_patterns.pdf'],
                    ['Binary Tree, Graph Traversal & Dynamic Programming', 'Implementasi BST, traversal BFS/DFS, shortest path Dijkstra, serta pola memoization dan tabulation DP.', 'dsa_tree_graph_dp.pdf'],
                ],
                'tugas' => ['Solusi 5 Masalah Algoritma Teroptimasi', 'Implementasikan solusi optimal C++ untuk masalah sliding window, validasi BST, dan shortest path lengkap dengan analisis Big O.'],
            ],
            [
                'id_kelas' => 7, 'nidn' => '0401019407', 'is_premium' => true,
                'nama_kelas' => 'Machine Learning Praktis dengan Python & Scikit-Learn',
                'deskripsi' => 'Bangun pipeline ML end-to-end mulai dari feature engineering, regresi, klasifikasi, clustering, cross-validation, hingga evaluasi model produksi.',
                'materi' => [
                    ['Supervised Learning: Regresi, Decision Tree & Ensemble', 'Membangun model prediksi dengan Linear/Logistic Regression, Random Forest, dan Gradient Boosting di Scikit-Learn.', 'ml_supervised_pipeline.pdf'],
                    ['Evaluasi Model, Cross-Validation & Clustering', 'Menghindari data leakage, membaca confusion matrix, ROC-AUC, hyperparameter tuning, serta K-Means clustering.', 'ml_eval_tuning.pdf'],
                ],
                'tugas' => ['Model Prediksi Churn Pelanggan', 'Latih dan bandingkan minimal 3 algoritma klasifikasi pada dataset churn lengkap dengan pipeline preprocessing dan metrik F1-score.'],
            ],
            [
                'id_kelas' => 8, 'nidn' => '0401018801', 'is_premium' => false,
                'nama_kelas' => 'Java OOP & Spring Boot REST API Dasar',
                'deskripsi' => 'Kuasai pilar OOP Java modern (records, streams, collections) lalu bangun REST API enterprise dengan Spring Boot, Spring Data JPA, dan validasi.',
                'materi' => [
                    ['Pilar OOP Java Modern, Generics & Stream API', 'Enkapsulasi, polimorfisme, interface, Java Records, Optional, dan pemrosesan koleksi deklaratif dengan Stream.', 'java_oop_modern.pdf'],
                    ['Membangun REST API dengan Spring Boot & JPA', 'Dependency injection, controller, service layer, repository JPA, DTO validation, dan global exception handler.', 'spring_boot_rest.pdf'],
                ],
                'tugas' => ['REST API Sistem Inventaris Spring Boot', 'Buat layanan CRUD produk dan stok memakai Spring Boot, JPA, validasi input, serta dokumentasi endpoint.'],
            ],
            [
                'id_kelas' => 9, 'nidn' => '0401018902', 'is_premium' => false,
                'nama_kelas' => 'Clean Code, SOLID & Software Architecture',
                'deskripsi' => 'Tingkatkan kualitas rekayasa perangkat lunak dengan prinsip SOLID, refactoring aman, Design Patterns praktis, dan arsitektur modular.',
                'materi' => [
                    ['Prinsip SOLID, Refactoring & Code Smells', 'Mendeteksi coupling berlebihan, memecah god class, penamaan ekspresif, serta teknik refactoring bertahap.', 'clean_code_solid.pdf'],
                    ['Design Patterns & Modular Layered Architecture', 'Penerapan Strategy, Factory, Repository, Dependency Injection, dan pemisahan domain logic dari framework.', 'software_arch_patterns.pdf'],
                ],
                'tugas' => ['Refactoring Modul Legacy Menjadi Modular', 'Refactor modul perhitungan transaksi yang kaku menjadi desain berbasis Strategy Pattern dengan unit test terpisah.'],
            ],
            [
                'id_kelas' => 10, 'nidn' => '0401019104', 'is_premium' => true,
                'nama_kelas' => 'Backend Scalable dengan Node.js, TypeScript & Express',
                'deskripsi' => 'Rancang REST API produksi menggunakan Node.js, TypeScript, Express, autentikasi JWT + refresh token, validasi Zod, dan integrasi ORM Prisma.',
                'materi' => [
                    ['Arsitektur Express + TypeScript & Validasi Zod', 'Struktur folder service-controller, middleware error terpusat, validasi payload ketat, dan logging terstruktur.', 'node_ts_express.pdf'],
                    ['Autentikasi JWT, RBAC & Integrasi Prisma ORM', 'Implementasi access/refresh token rotation, role-based access control, rate limiting, dan migrasi database Prisma.', 'node_jwt_prisma.pdf'],
                ],
                'tugas' => ['API Manajemen Proyek dengan Auth JWT & RBAC', 'Bangun REST API proyek & tugas dengan role Admin/Member, validasi Zod, dan penyimpanan database via Prisma.'],
            ],
            [
                'id_kelas' => 11, 'nidn' => '0401019508', 'is_premium' => true,
                'nama_kelas' => 'Full-Stack Web Modern dengan Next.js App Router',
                'deskripsi' => 'Pelajari Server Components, Server Actions, caching/revalidation, autentikasi, optimasi SEO, dan deployment full-stack di Next.js modern.',
                'materi' => [
                    ['App Router, Server Components & Streaming SSR', 'Membedakan Server vs Client Component, nested layout, loading boundary, dan optimasi Core Web Vitals.', 'nextjs_app_router.pdf'],
                    ['Server Actions, Mutasi Data & Revalidasi Cache', 'Menangani form mutasi langsung di server, validasi skema, proteksi rute, serta strategi ISR dan cache tags.', 'nextjs_server_actions.pdf'],
                ],
                'tugas' => ['Aplikasi Job Board Full-Stack Next.js', 'Buat portal lowongan kerja dengan filter URL searchParams, detail dinamis SEO-friendly, dan form posting Server Action.'],
            ],
            [
                'id_kelas' => 12, 'nidn' => '0401019003', 'is_premium' => false,
                'nama_kelas' => 'Laravel 11 & Livewire: Bangun Aplikasi Web Cepat',
                'deskripsi' => 'Kuasai ekosistem PHP modern dengan Laravel 11, Eloquent ORM, Blade/Livewire reaktif, Queue, Policy otorisasi, dan Feature Testing PHPUnit.',
                'materi' => [
                    ['Eloquent Relasi, Migrasi & Otorisasi Policy', 'Mengelola relasi hasMany/belongsToMany tanpa N+1 query, Form Request validation, serta Gate & Policy.', 'laravel_eloquent_policy.pdf'],
                    ['Interaktivitas Real-Time dengan Livewire & Testing', 'Membangun pencarian, pagination, dan form reaktif tanpa menulis banyak JS, dilengkapi automated Feature Test.', 'laravel_livewire_test.pdf'],
                ],
                'tugas' => ['Modul Manajemen Event dengan Laravel', 'Buat fitur pendaftaran event lengkap dengan kuota peserta, validasi anti-duplikat, dan minimal 3 Feature Test PHPUnit.'],
            ],
            [
                'id_kelas' => 13, 'nidn' => '0401019609', 'is_premium' => true,
                'nama_kelas' => 'Go (Golang) untuk High-Performance Microservices',
                'deskripsi' => 'Bangun layanan backend super cepat dan hemat memori dengan Go, goroutines, channels, context cancellation, gRPC/HTTP API, dan graceful shutdown.',
                'materi' => [
                    ['Fundamental Go, Interface & Concurrency Goroutines', 'Struct, composition, error handling idiomatik, worker pool dengan channel, dan pencegahan race condition.', 'golang_concurrency.pdf'],
                    ['HTTP/gRPC Service, Context & Graceful Shutdown', 'Membangun API dengan net/http atau Fiber/Chi, connection pooling PostgreSQL, timeout context, dan Docker multi-stage.', 'golang_microservices.pdf'],
                ],
                'tugas' => ['Layanan URL Shortener & Analytics Berbasis Go', 'Buat API pemendek URL berkinerja tinggi di Go dengan worker pool pencatat klik dan unit test race detector.'],
            ],
            [
                'id_kelas' => 14, 'nidn' => '0401019710', 'is_premium' => false,
                'nama_kelas' => 'Flutter & Dart: Aplikasi Cross-Platform iOS & Android',
                'deskripsi' => 'Kembangkan satu basis kode untuk Android & iOS dengan Dart null-safety, widget tree Flutter, Riverpod/BLoC state management, dan penyimpanan lokal.',
                'materi' => [
                    ['Dart Modern & Penyusunan Widget Layout Flutter', 'Async/await Dart, Stateless vs StatefulWidget, responsive layout, theming Light/Dark, dan navigasi GoRouter.', 'flutter_widgets_nav.pdf'],
                    ['State Management Riverpod & Konsumsi REST API', 'Mengelola state asynchronous, caching lokal, penanganan loading/error state, serta build APK rilis.', 'flutter_riverpod_api.pdf'],
                ],
                'tugas' => ['Aplikasi Pencatat Keuangan Pribadi Flutter', 'Bangun aplikasi expense tracker dengan ringkasan saldo bulanan, filter kategori, dan penyimpanan lokal persisten.'],
            ],
            [
                'id_kelas' => 15, 'nidn' => '0401019811', 'is_premium' => false,
                'nama_kelas' => 'Docker, Containerization & Compose untuk Developer',
                'deskripsi' => 'Hilangkan masalah "jalan di laptop saya" dengan Dockerfile multi-stage, Docker Compose multi-service, networking, volume, dan keamanan image.',
                'materi' => [
                    ['Image, Container & Dockerfile Multi-Stage Efisien', 'Cara kerja layer caching, mengecilkan ukuran image produksi, non-root user, dan manajemen .dockerignore.', 'docker_multistage.pdf'],
                    ['Orkestrasi Lokal dengan Docker Compose & Volume', 'Menjalankan App + MySQL/PostgreSQL + Redis + Nginx dalam satu stack Compose dengan healthcheck dan named volume.', 'docker_compose_stack.pdf'],
                ],
                'tugas' => ['Containerisasi Stack Web + Database + Redis', 'Tulis Dockerfile multi-stage dan docker-compose.yml lengkap dengan healthcheck serta persistensi data database.'],
            ],
            [
                'id_kelas' => 16, 'nidn' => '0401019811', 'is_premium' => true,
                'nama_kelas' => 'Kubernetes & Cloud Native Deployment',
                'deskripsi' => 'Kelola deployment skala produksi dengan Pod, Deployment, Service, Ingress TLS, ConfigMap/Secret, HPA autoscaling, dan zero-downtime rollout.',
                'materi' => [
                    ['Arsitektur Cluster, Deployment, Service & Ingress', 'Menulis manifest YAML deklaratif, liveness/readiness probe, rolling update, serta routing trafik lewat Ingress.', 'k8s_core_workloads.pdf'],
                    ['Secrets, Autoscaling (HPA) & Observability Dasar', 'Mengelola konfigurasi rahasia, resource requests/limits, Horizontal Pod Autoscaler, dan inspeksi log/metrik.', 'k8s_scaling_ops.pdf'],
                ],
                'tugas' => ['Manifest Deployment Aplikasi Stateless di Kubernetes', 'Susun manifest Deployment, Service, Ingress, dan HPA dengan probe kesehatan dan batas CPU/memori yang aman.'],
            ],
            [
                'id_kelas' => 17, 'nidn' => '0401019912', 'is_premium' => false,
                'nama_kelas' => 'CI/CD Otomatis dengan GitHub Actions & DevOps Dasar',
                'deskripsi' => 'Otomatiskan linting, automated testing, security scan, build artifact, dan deployment ke server setiap kali push atau Pull Request dibuat.',
                'materi' => [
                    ['Workflow GitHub Actions: Lint, Test & Matrix Build', 'Menyusun pipeline YAML, caching dependensi Composer/npm, service container DB untuk test, dan branch protection.', 'gh_actions_ci.pdf'],
                    ['Continuous Deployment, Secrets & Rollback Aman', 'Deploy otomatis via SSH/Docker Registry, manajemen environment secrets, dan strategi rollback cepat saat gagal.', 'gh_actions_cd.pdf'],
                ],
                'tugas' => ['Pipeline CI/CD Lengkap untuk Aplikasi Web', 'Buat workflow GitHub Actions yang menjalankan linter, PHPUnit/Jest, dan validasi build pada setiap Pull Request.'],
            ],
            [
                'id_kelas' => 18, 'nidn' => '0401019407', 'is_premium' => true,
                'nama_kelas' => 'AI Engineering: LLM API, RAG & Prompt Engineering',
                'deskripsi' => 'Bangun aplikasi berbasis AI modern: structured output, function/tool calling, embeddings, vector search (RAG), evaluasi akurasi, dan guardrails.',
                'materi' => [
                    ['LLM API, Structured Output & Tool Calling', 'Merancang system prompt deterministik, memaksa output JSON schema valid, streaming respons, dan memanggil fungsi eksternal.', 'ai_llm_tool_calling.pdf'],
                    ['Retrieval-Augmented Generation (RAG) & Evaluasi', 'Chunking dokumen, embedding vector, pencarian hibrida, sitasi sumber anti-halusinasi, serta kontrol biaya token.', 'ai_rag_architecture.pdf'],
                ],
                'tugas' => ['Asisten Q&A Dokumen Berbasis RAG', 'Bangun prototipe RAG sederhana yang menjawab pertanyaan dari dokumen PDF/Markdown disertai kutipan sumber yang akurat.'],
            ],
            [
                'id_kelas' => 19, 'nidn' => '0401019407', 'is_premium' => true,
                'nama_kelas' => 'Deep Learning & Computer Vision dengan PyTorch',
                'deskripsi' => 'Pahami arsitektur neural network, backpropagation, CNN, transfer learning, object detection, dan optimasi inferensi model menggunakan PyTorch.',
                'materi' => [
                    ['Tensor, Autograd & Training Loop PyTorch', 'Membangun model nn.Module dari nol, DataLoader, loss function, optimizer AdamW, serta pelacakan overfitting.', 'pytorch_fundamentals.pdf'],
                    ['Transfer Learning CNN & Klasifikasi Citra', 'Augmentasi dataset gambar, fine-tuning pretrained backbone (ResNet/EfficientNet), dan ekspor model ONNX.', 'pytorch_cv_transfer.pdf'],
                ],
                'tugas' => ['Klasifikasi Citra dengan Transfer Learning', 'Latih classifier gambar multi-kelas memakai PyTorch transfer learning dan laporkan akurasi validasi serta confusion matrix.'],
            ],
            [
                'id_kelas' => 20, 'nidn' => '0401019508', 'is_premium' => false,
                'nama_kelas' => 'FastAPI & Async Python untuk Backend AI',
                'deskripsi' => 'Buat REST API Python berperforma tinggi dengan FastAPI, validasi Pydantic v2, dokumentasi OpenAPI otomatis, async SQLAlchemy, dan integrasi model AI.',
                'materi' => [
                    ['Routing FastAPI, Pydantic v2 & Dependency Injection', 'Skema request/response otomatis, validasi tipe ketat, OpenAPI Swagger interaktif, dan dependency reusable.', 'fastapi_pydantic.pdf'],
                    ['Async Database, Background Tasks & Model Serving', 'Koneksi async DB, mengeksekusi tugas berat di background worker, serta membungkus endpoint inferensi ML/LLM.', 'fastapi_async_serving.pdf'],
                ],
                'tugas' => ['API Inferensi & Pencatatan Riwayat FastAPI', 'Bangun layanan FastAPI dengan 2 endpoint prediksi/analisis, validasi Pydantic v2, dan tes otomatis memakai Pytest.'],
            ],
            [
                'id_kelas' => 21, 'nidn' => '0401019710', 'is_premium' => false,
                'nama_kelas' => 'Vue 3, Composition API & Nuxt Full-Stack',
                'deskripsi' => 'Pelajari reaktivitas Vue 3 modern dengan `<script setup>`, state management Pinia, routing dinamis, hingga SSR dan API routes menggunakan Nuxt.',
                'materi' => [
                    ['Vue 3 Composition API, Reactivity & Pinia', 'Penggunaan ref, reactive, computed, composable reusable, serta manajemen state global terstruktur dengan Pinia.', 'vue3_composition_pinia.pdf'],
                    ['SSR, Nitro Server Routes & SEO dengan Nuxt', 'File-based routing, data fetching universal useFetch, middleware autentikasi, dan optimasi bundle produksi.', 'nuxt_fullstack.pdf'],
                ],
                'tugas' => ['Katalog Produk Interaktif Vue 3 + Pinia', 'Implementasikan daftar produk dengan keranjang belanja Pinia, filter kategori, dan kalkulasi total real-time.'],
            ],
            [
                'id_kelas' => 22, 'nidn' => '0401019912', 'is_premium' => false,
                'nama_kelas' => 'HTML5, CSS3 Modern, Grid & Responsive Design',
                'deskripsi' => 'Fondasi wajib web developer: HTML semantik aksesibel, Flexbox, CSS Grid, Container Queries, Custom Properties, dark mode, dan WCAG AA.',
                'materi' => [
                    ['HTML5 Semantik, Form Modern & Aksesibilitas WCAG', 'Struktur landmark main/nav/article, label & autofill form, kontras warna AA, serta navigasi keyboard.', 'html5_semantic_a11y.pdf'],
                    ['CSS Grid, Flexbox, Clamp & Container Queries', 'Membangun layout responsif tanpa framework, tipografi fluid dengan clamp(), variabel CSS, dan tema gelap/terang.', 'css_modern_layout.pdf'],
                ],
                'tugas' => ['Landing Page Responsif Multi-Breakpoint', 'Bangun halaman portofolio responsif memakai CSS Grid & Flexbox yang rapi di desktop, tablet, dan layar 360px.'],
            ],
            [
                'id_kelas' => 23, 'nidn' => '0401019104', 'is_premium' => false,
                'nama_kelas' => 'JavaScript Modern (ES2024+) & Asynchronous DOM',
                'deskripsi' => 'Pahami cara kerja event loop, closure, ES modules, Promise, async/await, Fetch API, serta manipulasi DOM bersih tanpa library berat.',
                'materi' => [
                    ['ES2024+, Closure, Scope & Array Methods', 'Destructuring, spread/rest, optional chaining, map/filter/reduce, serta pemahaman mendalam execution context.', 'js_es2024_core.pdf'],
                    ['Event Loop, Async/Await & Fetch API Praktis', 'Menangani request paralel dengan Promise.allSettled, AbortController, error handling jaringan, dan DOM delegation.', 'js_async_dom.pdf'],
                ],
                'tugas' => ['Aplikasi Pencari Repositori GitHub Vanilla JS', 'Buat pencarian repositori berbasis Fetch API dengan debounce input, loading/error state, dan pembatalan request lama.'],
            ],
            [
                'id_kelas' => 24, 'nidn' => '0401019508', 'is_premium' => false,
                'nama_kelas' => 'TypeScript Mendalam untuk Aplikasi Skala Besar',
                'deskripsi' => 'Tulis kode JavaScript bebas bug runtime dengan discriminated unions, generics, utility types, type narrowing, dan konfigurasi strict tsconfig.',
                'materi' => [
                    ['Type System, Narrowing & Discriminated Unions', 'Memodelkan domain bisnis secara akurat agar state mustahil tidak bisa dikompilasi oleh TypeScript.', 'ts_narrowing_unions.pdf'],
                    ['Generics, Utility Types & Type-Safe API Client', 'Membuat helper generik reusable, memanfaatkan Pick/Omit/Record/ReturnType, serta validasi data runtime.', 'ts_generics_utility.pdf'],
                ],
                'tugas' => ['Library API Client Type-Safe dengan TypeScript', 'Bangun wrapper HTTP generik dengan tipe response terstruktur (Result<T, E>) dan 0 penggunaan tipe any.'],
            ],
            [
                'id_kelas' => 25, 'nidn' => '0401019306', 'is_premium' => false,
                'nama_kelas' => 'Git, GitHub Collaboration & Open-Source Workflow',
                'deskripsi' => 'Kuasai alur kerja tim profesional: branching, Pull Request, code review, menyelesaikan merge conflict, rebase interaktif, dan semantic commits.',
                'materi' => [
                    ['Branching, Pull Request & Resolusi Merge Conflict', 'Alur feature branch, commit atomik, review diff yang efektif, dan langkah tenang membereskan konflik kode.', 'git_branch_pr.pdf'],
                    ['Rebase, Stash, Cherry-Pick & Pemulihan Reflog', 'Merapikan histori commit, menyelamatkan perubahan dengan stash/reflog, serta kontribusi fork ke proyek open-source.', 'git_advanced_workflow.pdf'],
                ],
                'tugas' => ['Simulasi Kolaborasi PR & Resolusi Konflik', 'Buat 2 feature branch yang mengubah file sama, selesaikan merge conflict secara bersih, dan dokumentasikan PR-nya.'],
            ],
            [
                'id_kelas' => 26, 'nidn' => '0401019609', 'is_premium' => true,
                'nama_kelas' => 'Rust Dasar: Memory Safety & Systems Programming',
                'deskripsi' => 'Pelajari ownership, borrowing, lifetime, pattern matching, error handling Result/Option, hingga membangun CLI & web service cepat dengan Rust.',
                'materi' => [
                    ['Ownership, Borrowing & Lifetimes Tanpa Garbage Collector', 'Cara compiler Rust menjamin keamanan memori saat kompilasi, slice, enum, dan pattern matching menyeluruh.', 'rust_ownership_borrowing.pdf'],
                    ['Cargo, Trait, Concurrency & Membangun Tool CLI', 'Menyusun proyek Cargo, memakai trait generik, error handling dengan thiserror/anyhow, dan parsing argumen CLI.', 'rust_cli_traits.pdf'],
                ],
                'tugas' => ['CLI Log Analyzer Cepat Berbasis Rust', 'Buat tool CLI Rust yang membaca file log besar, menghitung frekuensi error/status HTTP, dan mencetak ringkasan dalam milidetik.'],
            ],
            [
                'id_kelas' => 27, 'nidn' => '0401019205', 'is_premium' => false,
                'nama_kelas' => 'Automated Testing: Unit, Integration & Playwright E2E',
                'deskripsi' => 'Kirim rilis kode dengan percaya diri memakai piramida pengujian: unit test cepat, API integration test, mocking aman, dan browser E2E Playwright.',
                'materi' => [
                    ['Unit & Integration Testing dengan PHPUnit / Vitest', 'Menulis tes deterministik, Arrange-Act-Assert, test database terisolasi, dan menghindari mock yang rapuh.', 'testing_unit_integration.pdf'],
                    ['End-to-End Browser Testing dengan Playwright', 'Mengotomasi alur login, checkout, dan form kritis memakai role locator aksesibel serta integrasi ke CI.', 'testing_playwright_e2e.pdf'],
                ],
                'tugas' => ['Suite Uji Alur Autentikasi & Checkout', 'Tulis minimal 5 integration/E2E test mencakup skenario sukses, input tidak valid, dan pencegahan akses tanpa izin.'],
            ],
            [
                'id_kelas' => 28, 'nidn' => '0401019609', 'is_premium' => true,
                'nama_kelas' => 'Redis, Caching, Queue & System Design Dasar',
                'deskripsi' => 'Pahami cara menangani lonjakan trafik dengan pola cache-aside, rate limiting, message queue background job, load balancing, dan database scaling.',
                'materi' => [
                    ['Strategi Caching Redis, TTL & Rate Limiting', 'Pola cache-aside, write-through, mencegah cache stampede, serta implementasi sliding-window rate limiter.', 'redis_caching_ratelimit.pdf'],
                    ['Message Queue, Async Worker & Arsitektur Skala Besar', 'Memindahkan proses berat (email/export/webhook) ke antrean idempoten, retry backoff, dan pengenalan read replica.', 'system_design_queues.pdf'],
                ],
                'tugas' => ['Desain Arsitektur Sistem Flash Sale', 'Buat dokumen arsitektur dan prototipe caching/queue untuk menangani 5.000 request per detik tanpa overselling stok.'],
            ],
            [
                'id_kelas' => 29, 'nidn' => '0401019811', 'is_premium' => true,
                'nama_kelas' => 'Cloud Architecture AWS & Linux Server Administration',
                'deskripsi' => 'Kelola server produksi Linux, Nginx reverse proxy + SSL otomatis, systemd, firewall UFW, serta layanan inti cloud (EC2, S3, RDS, IAM).',
                'materi' => [
                    ['Administrasi Linux VPS, Nginx Reverse Proxy & HTTPS', 'Hardening SSH key, firewall UFW, konfigurasi Nginx untuk Laravel/Node, dan sertifikat TLS otomatis Certbot.', 'linux_nginx_tls.pdf'],
                    ['Layanan Inti Cloud: Compute, Object Storage & IAM', 'Prinsip least-privilege IAM, menyimpan file upload privat di S3/R2, managed database RDS, serta backup otomatis.', 'cloud_aws_fundamentals.pdf'],
                ],
                'tugas' => ['Checklist & Konfigurasi Deploy VPS Produksi', 'Tulis konfigurasi Nginx + HTTPS + skrip backup database harian serta langkah hardening SSH untuk server produksi.'],
            ],
            [
                'id_kelas' => 30, 'nidn' => '0401019912', 'is_premium' => true,
                'nama_kelas' => 'GraphQL, WebSockets & Real-Time API Engineering',
                'deskripsi' => 'Rancang API modern beyond REST: skema GraphQL bebas over-fetching, DataLoader anti N+1, serta komunikasi dua arah real-time via WebSockets.',
                'materi' => [
                    ['Skema GraphQL, Resolver & Optimasi DataLoader', 'Mendefinisikan Query, Mutation, pagination cursor-based, dan menghilangkan N+1 query memakai batching DataLoader.', 'graphql_dataloader.pdf'],
                    ['WebSockets, Server-Sent Events & Fitur Real-Time', 'Memilih SSE vs WebSocket, autentikasi handshake, room broadcasting untuk chat/notifikasi, dan penanganan reconnect.', 'realtime_websockets_sse.pdf'],
                ],
                'tugas' => ['Aplikasi Live Notification & Chat Room', 'Implementasikan fitur notifikasi/chat real-time dengan autentikasi pengguna, indikator mengetik, dan auto-reconnect.'],
            ],
        ];

        $kelasRows = [];
        $materiRows = [];
        $tugasRows = [];
        $materiId = 1;
        $tugasId = 1;

        foreach ($courses as $c) {
            $kelasRows[] = [
                'id_kelas' => $c['id_kelas'],
                'nama_kelas' => $c['nama_kelas'],
                'deskripsi' => $c['deskripsi'],
                'nidn' => $c['nidn'],
                'is_premium' => $c['is_premium'],
            ];

            foreach ($c['materi'] as $mIdx => $m) {
                $materiRows[] = [
                    'id_materi' => $materiId++,
                    'id_kelas' => $c['id_kelas'],
                    'judul_materi' => $m[0],
                    'deskripsi' => $m[1],
                    'file_materi' => $m[2],
                    'tgl_upload' => $now->copy()->subDays(30 - $c['id_kelas'])->addHours($mIdx * 6),
                ];
            }

            $tugasRows[] = [
                'id_tugas' => $tugasId++,
                'id_kelas' => $c['id_kelas'],
                'judul_tugas' => 'Tugas 1: ' . $c['tugas'][0],
                'deskripsi' => $c['tugas'][1],
                'deadline' => $now->copy()->addDays(5 + ($c['id_kelas'] % 10)),
            ];
        }

        DB::table('tb_kelas')->insert($kelasRows);
        DB::table('tb_materi')->insert($materiRows);
        DB::table('tb_tugas')->insert($tugasRows);

        // 5. tb_peserta_kelas (Sebaran realistis 20 murid di kelas Gratis & Pro)
        $pesertaRows = [];
        $seenEnroll = [];
        $addEnroll = function (int $kelasId, string $nim) use (&$pesertaRows, &$seenEnroll) {
            $key = "{$kelasId}:{$nim}";
            if (!isset($seenEnroll[$key])) {
                $seenEnroll[$key] = true;
                $pesertaRows[] = ['id_kelas' => $kelasId, 'nim' => $nim];
            }
        };

        $freeIds = array_values(array_map(fn($c) => $c['id_kelas'], array_filter($courses, fn($c) => !$c['is_premium'])));
        $proIds = array_values(array_map(fn($c) => $c['id_kelas'], array_filter($courses, fn($c) => $c['is_premium'])));

        // Setiap murid ambil 2–3 kelas gratis
        foreach ($muridProfiles as $idx => $m) {
            $addEnroll($freeIds[$idx % count($freeIds)], $m['nim']);
            $addEnroll($freeIds[($idx + 3) % count($freeIds)], $m['nim']);
            if ($idx % 2 === 0) {
                $addEnroll($freeIds[($idx + 7) % count($freeIds)], $m['nim']);
            }
        }
        // Murid dengan langganan Pro aktif (student_bagus, student_citra, student_lutfi, student_maya) ikut kelas Pro
        $proNims = ['2201010012', '2201010013', '2201010021', '2201010022'];
        foreach ($proIds as $idx => $kId) {
            $addEnroll($kId, $proNims[$idx % count($proNims)]);
        }
        DB::table('tb_peserta_kelas')->insert($pesertaRows);

        // 6. Contoh Langganan Pro Aktif & Pending (hanya jika tb_paket_langganan sudah ada)
        if (DB::table('tb_paket_langganan')->where('id_paket', 1)->exists()) {
            DB::table('tb_langganan')->insert([
                [
                    'kode_invoice' => 'INV-SEED-PRO001',
                    'id_user' => 21, // student_bagus
                    'id_paket' => 1,
                    'nominal' => 49000,
                    'status' => 'active',
                    'bukti_bayar' => null,
                    'catatan_admin' => 'Langganan aktif (data contoh seeder).',
                    'tgl_mulai' => $now->copy()->subDays(5),
                    'tgl_berakhir' => $now->copy()->addDays(25),
                    'verified_by' => 1,
                    'verified_at' => $now->copy()->subDays(5),
                    'created_at' => $now->copy()->subDays(5),
                ],
                [
                    'kode_invoice' => 'INV-SEED-PRO002',
                    'id_user' => 22, // student_citra
                    'id_paket' => 2,
                    'nominal' => 490000,
                    'status' => 'active',
                    'bukti_bayar' => null,
                    'catatan_admin' => 'Langganan tahunan aktif (data contoh seeder).',
                    'tgl_mulai' => $now->copy()->subDays(12),
                    'tgl_berakhir' => $now->copy()->addDays(353),
                    'verified_by' => 1,
                    'verified_at' => $now->copy()->subDays(12),
                    'created_at' => $now->copy()->subDays(12),
                ],
            ]);
        }

        // 7. Sample Kuis & Soal (3 Kuis lintas kelas)
        $kuis1 = DB::table('tb_kuis')->insertGetId([
            'id_kelas' => 1,
            'judul_kuis' => 'Kuis 1: Dasar Sintaks Python & Pandas',
            'deskripsi' => 'Uji pemahaman Anda tentang tipe data dasar, tuple/list, dan fungsi built-in di Python.',
            'durasi_menit' => 15,
            'tgl_dibuat' => $now,
        ]);
        $kuis2 = DB::table('tb_kuis')->insertGetId([
            'id_kelas' => 3,
            'judul_kuis' => 'Kuis 1: Normalisasi & Indexing PostgreSQL',
            'deskripsi' => 'Evaluasi konsep 1NF–3NF, primary key, dan analisis EXPLAIN ANALYZE.',
            'durasi_menit' => 15,
            'tgl_dibuat' => $now,
        ]);
        $kuis3 = DB::table('tb_kuis')->insertGetId([
            'id_kelas' => 4,
            'judul_kuis' => 'Kuis 1: React 19 Hooks & TypeScript',
            'deskripsi' => 'Uji pemahaman state management, efek samping, dan typing props di React.',
            'durasi_menit' => 15,
            'tgl_dibuat' => $now,
        ]);

        DB::table('tb_soal_kuis')->insert([
            [
                'id_kuis' => $kuis1,
                'pertanyaan' => 'Manakah tipe data di Python yang bersifat Immutable (tidak dapat diubah)?',
                'opsi_a' => 'List', 'opsi_b' => 'Tuple', 'opsi_c' => 'Dictionary', 'opsi_d' => 'Set', 'jawaban_benar' => 'B',
            ],
            [
                'id_kuis' => $kuis1,
                'pertanyaan' => 'Fungsi built-in mana yang digunakan untuk mengetahui jumlah elemen dalam list Python?',
                'opsi_a' => 'count()', 'opsi_b' => 'size()', 'opsi_c' => 'len()', 'opsi_d' => 'length()', 'jawaban_benar' => 'C',
            ],
            [
                'id_kuis' => $kuis1,
                'pertanyaan' => 'Keyword apa yang digunakan untuk mendefinisikan fungsi di Python?',
                'opsi_a' => 'function', 'opsi_b' => 'func', 'opsi_c' => 'define', 'opsi_d' => 'def', 'jawaban_benar' => 'D',
            ],
            [
                'id_kuis' => $kuis2,
                'pertanyaan' => 'Perintah PostgreSQL apa yang menampilkan rencana eksekusi beserta waktu nyata query?',
                'opsi_a' => 'DESCRIBE QUERY', 'opsi_b' => 'EXPLAIN ANALYZE', 'opsi_c' => 'SHOW PLAN', 'opsi_d' => 'TRACE SELECT', 'jawaban_benar' => 'B',
            ],
            [
                'id_kuis' => $kuis2,
                'pertanyaan' => 'Syarat utama agar sebuah tabel memenuhi Normal Form Ketiga (3NF) adalah...',
                'opsi_a' => 'Tidak ada primary key', 'opsi_b' => 'Semua kolom bertipe TEXT', 'opsi_c' => 'Sudah 2NF dan tidak ada ketergantungan transitif antar kolom non-key', 'opsi_d' => 'Memiliki minimal 3 tabel pivot', 'jawaban_benar' => 'C',
            ],
            [
                'id_kuis' => $kuis3,
                'pertanyaan' => 'Mengapa array dependency pada useEffect penting diisi dengan akurat?',
                'opsi_a' => 'Agar komponen selalu re-render tanpa henti', 'opsi_b' => 'Untuk mengontrol kapan efek dijalankan ulang saat nilai terkait berubah', 'opsi_c' => 'Untuk menggantikan TypeScript compiler', 'opsi_d' => 'Agar CSS Tailwind dimuat lebih cepat', 'jawaban_benar' => 'B',
            ],
        ]);

        // 8. Forum Tags
        DB::table('tb_forum_tag')->insert([
            ['id_tag' => 1,  'nama_tag' => 'JavaScript',              'warna' => '#F7DF1E', 'icon' => 'bxl-javascript'],
            ['id_tag' => 2,  'nama_tag' => 'Python',                  'warna' => '#3776AB', 'icon' => 'bxl-python'],
            ['id_tag' => 3,  'nama_tag' => 'PHP',                     'warna' => '#777BB4', 'icon' => 'bxl-php'],
            ['id_tag' => 4,  'nama_tag' => 'React',                   'warna' => '#61DAFB', 'icon' => 'bxl-react'],
            ['id_tag' => 5,  'nama_tag' => 'Vue.js',                  'warna' => '#4FC08D', 'icon' => 'bxl-vuejs'],
            ['id_tag' => 6,  'nama_tag' => 'Database & SQL',          'warna' => '#00758F', 'icon' => 'bxs-data'],
            ['id_tag' => 7,  'nama_tag' => 'PostgreSQL',              'warna' => '#336791', 'icon' => 'bxs-data'],
            ['id_tag' => 8,  'nama_tag' => 'Flutter',                 'warna' => '#02569B', 'icon' => 'bxl-flutter'],
            ['id_tag' => 9,  'nama_tag' => 'Kotlin',                  'warna' => '#7F52FF', 'icon' => 'bxl-android'],
            ['id_tag' => 10, 'nama_tag' => 'CSS & UI',                'warna' => '#264DE4', 'icon' => 'bxl-css3'],
            ['id_tag' => 11, 'nama_tag' => 'Git & GitHub',            'warna' => '#F05032', 'icon' => 'bxl-git'],
            ['id_tag' => 12, 'nama_tag' => 'Docker & DevOps',         'warna' => '#2496ED', 'icon' => 'bxl-docker'],
            ['id_tag' => 13, 'nama_tag' => 'Cyber Security',          'warna' => '#DC2626', 'icon' => 'bx-shield-quarter'],
            ['id_tag' => 14, 'nama_tag' => 'Frontend',                'warna' => '#FF6B6B', 'icon' => 'bx-layout'],
            ['id_tag' => 15, 'nama_tag' => 'Backend',                 'warna' => '#047857', 'icon' => 'bx-server'],
            ['id_tag' => 16, 'nama_tag' => 'AI & Machine Learning',   'warna' => '#D97706', 'icon' => 'bx-brain'],
            ['id_tag' => 17, 'nama_tag' => 'Algoritma & Struktur Data', 'warna' => '#6D28D9', 'icon' => 'bx-code-block'],
            ['id_tag' => 18, 'nama_tag' => 'Tips & Tutorial',         'warna' => '#2563EB', 'icon' => 'bx-bulb'],
            ['id_tag' => 19, 'nama_tag' => 'Career & Job',            'warna' => '#059669', 'icon' => 'bx-briefcase'],
            ['id_tag' => 20, 'nama_tag' => 'Diskusi Umum',            'warna' => '#FFD700', 'icon' => 'bx-conversation'],
        ]);

        // 9. Forum Posts (8 Diskusi Relevan)
        DB::table('tb_forum_post')->insert([
            [
                'id_post' => 1, 'id_user' => 13,
                'judul' => 'Cara Optimasi Query SQL dengan Indexing yang Benar untuk Jutaan Record',
                'konten' => '<p>Halo semua! Seringkali aplikasi kita mulai lambat saat database sudah mencapai ratusan ribu hingga jutaan baris data. Masalah utamanya biasanya ada di query yang melakukan <em>Full Table Scan</em>.</p><ol><li><strong>Identifikasi kolom WHERE dan JOIN:</strong> Buat index pada kolom yang paling sering difilter.</li><li><strong>Gunakan Composite Index:</strong> Jika memfilter 2 kolom sekaligus.</li><li><strong>Gunakan EXPLAIN ANALYZE:</strong> Selalu cek query plan sebelum menambah index.</li></ol>',
                'tipe' => 'berbagi', 'status' => 'open', 'views' => 312, 'is_pinned' => 1, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subDays(3),
            ],
            [
                'id_post' => 2, 'id_user' => 20,
                'judul' => 'Kenapa useEffect di React terpanggil dua kali saat mode development?',
                'konten' => '<p>Halo teman-teman dev! Saya baru belajar React dan perhatiin kalau <code>useEffect</code> selalu run 2 kali saat pertama kali mount di local server. Bagaimana cara mengatasinya agar fetch API tidak tertembak dua kali?</p>',
                'tipe' => 'pertanyaan', 'status' => 'solved', 'views' => 198, 'is_pinned' => 0, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subHours(20),
            ],
            [
                'id_post' => 3, 'id_user' => 14,
                'judul' => '[PENGUMUMAN] Challenge Mingguan: Bangun Sorting Visualizer Tanpa Library!',
                'konten' => '<p>Tantangan terbuka untuk seluruh mahasiswa! Buat visualisasi algoritma sorting memakai HTML Canvas + JavaScript murni lengkap dengan kontrol kecepatan dan tombol pause.</p>',
                'tipe' => 'berbagi', 'status' => 'open', 'views' => 124, 'is_pinned' => 1, 'is_announcement' => 1,
                'tgl_post' => $now->copy()->subDays(2),
            ],
            [
                'id_post' => 4, 'id_user' => 21,
                'judul' => 'Bagaimana mencegah Memory Leak saat memakai Event Listener di React/Vue?',
                'konten' => '<p>Halo semua, saya sedang membuat dashboard real-time dengan chart. Setiap kali komponen di-unmount dan re-mount, penggunaan RAM terus naik. Bagaimana best practice cleanup event listener dan interval yang benar?</p>',
                'tipe' => 'pertanyaan', 'status' => 'open', 'views' => 145, 'is_pinned' => 0, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subHours(5),
            ],
            [
                'id_post' => 5, 'id_user' => 22,
                'judul' => 'Solusi error CORS Policy saat memanggil API Laravel dari frontend Vite localhost?',
                'konten' => '<p>Saya dapat error <code>blocked by CORS policy</code> ketika frontend Vite di port 5173 memanggil API Laravel di port 8000. Konfigurasi apa saja yang wajib dicek di <code>config/cors.php</code>?</p>',
                'tipe' => 'pertanyaan', 'status' => 'open', 'views' => 92, 'is_pinned' => 0, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subHours(3),
            ],
            [
                'id_post' => 6, 'id_user' => 30,
                'judul' => 'Tips Memilih Chunk Size pada Pipeline RAG agar Jawaban LLM Tidak Halusinasi',
                'konten' => '<p>Saat membangun sistem RAG untuk dokumen PDF teknis, chunk terlalu kecil menghilangkan konteks, sedangkan terlalu besar membuat retrieval tidak presisi. Coba mulai dari 400–600 token dengan overlap 15% lalu evaluasi recall-nya.</p>',
                'tipe' => 'berbagi', 'status' => 'open', 'views' => 176, 'is_pinned' => 0, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subHours(2),
            ],
            [
                'id_post' => 7, 'id_user' => 35,
                'judul' => 'Docker Compose: Bagaimana memastikan container App menunggu MySQL benar-benar siap?',
                'konten' => '<p>Meskipun sudah pakai <code>depends_on</code>, container Laravel saya kadang gagal migrasi karena MySQL masih proses inisialisasi. Apakah harus pakai <code>healthcheck</code> dengan <code>condition: service_healthy</code>?</p>',
                'tipe' => 'pertanyaan', 'status' => 'solved', 'views' => 134, 'is_pinned' => 0, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subMinutes(55),
            ],
            [
                'id_post' => 8, 'id_user' => 36,
                'judul' => 'Diskusi: Kapan lebih tepat memakai Go dibanding Node.js/Laravel untuk backend?',
                'konten' => '<p>Buat teman-teman yang sudah terjun ke proyek nyata, faktor apa yang paling menentukan saat memilih stack backend antara Laravel, Node.js/TypeScript, atau Go?</p>',
                'tipe' => 'diskusi', 'status' => 'open', 'views' => 88, 'is_pinned' => 0, 'is_announcement' => 0,
                'tgl_post' => $now->copy()->subMinutes(20),
            ],
        ]);

        // 10. Relasi Post - Tag
        DB::table('tb_forum_post_tag')->insert([
            ['id_post' => 1, 'id_tag' => 6],  ['id_post' => 1, 'id_tag' => 7],  ['id_post' => 1, 'id_tag' => 15],
            ['id_post' => 2, 'id_tag' => 4],  ['id_post' => 2, 'id_tag' => 1],  ['id_post' => 2, 'id_tag' => 14],
            ['id_post' => 3, 'id_tag' => 1],  ['id_post' => 3, 'id_tag' => 17],
            ['id_post' => 4, 'id_tag' => 1],  ['id_post' => 4, 'id_tag' => 4],  ['id_post' => 4, 'id_tag' => 14],
            ['id_post' => 5, 'id_tag' => 3],  ['id_post' => 5, 'id_tag' => 15],
            ['id_post' => 6, 'id_tag' => 2],  ['id_post' => 6, 'id_tag' => 16], ['id_post' => 6, 'id_tag' => 18],
            ['id_post' => 7, 'id_tag' => 12], ['id_post' => 7, 'id_tag' => 6],
            ['id_post' => 8, 'id_tag' => 15], ['id_post' => 8, 'id_tag' => 20],
        ]);

        // 11. Jawaban Forum
        DB::table('tb_forum_jawaban')->insert([
            [
                'id_jawaban' => 1, 'id_post' => 1, 'id_user' => 16,
                'konten' => '<p>Mantap penjelasannya! Tambahan: gunakan <code>pg_stat_statements</code> untuk memantau query paling lambat secara real-time di produksi.</p>',
                'is_solusi' => 0, 'tgl_jawaban' => $now->copy()->subHours(2),
            ],
            [
                'id_jawaban' => 2, 'id_post' => 2, 'id_user' => 15,
                'konten' => '<p>Di React StrictMode memang sengaja double-invoke <code>useEffect</code> di mode dev untuk mendeteksi kebocoran cleanup. Gunakan <code>AbortController</code> di fungsi return cleanup saat melakukan fetch.</p>',
                'is_solusi' => 1, 'tgl_jawaban' => $now->copy()->subHours(12),
            ],
            [
                'id_jawaban' => 3, 'id_post' => 7, 'id_user' => 33,
                'konten' => '<p>Benar! Tambahkan <code>healthcheck: test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]</code> di service MySQL lalu pasang <code>depends_on: db: condition: service_healthy</code> di service app.</p>',
                'is_solusi' => 1, 'tgl_jawaban' => $now->copy()->subMinutes(30),
            ],
        ]);

        // 12. Votes
        DB::table('tb_forum_vote')->insert([
            ['id_vote' => 1,  'id_user' => 20, 'id_post' => 1, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 2,  'id_user' => 21, 'id_post' => 1, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 3,  'id_user' => 20, 'id_post' => 2, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 4,  'id_user' => 13, 'id_post' => 4, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 5,  'id_user' => 14, 'id_post' => 6, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 6,  'id_user' => 15, 'id_post' => 6, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 7,  'id_user' => 22, 'id_post' => 7, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
            ['id_vote' => 8,  'id_user' => 35, 'id_post' => 8, 'id_jawaban' => null, 'tipe_vote' => 'up', 'tgl_vote' => $now],
        ]);
    }
}
