<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. tb_user
        Schema::create('tb_user', function (Blueprint $table) {
            $table->increments('id_user');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('role', 20)->default('Murid');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. tb_pengajar
        Schema::create('tb_pengajar', function (Blueprint $table) {
            $table->string('nidn', 20)->primary();
            $table->string('nama_pengajar', 100);
            $table->string('email', 100)->unique();
            $table->unsignedInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 3. tb_murid
        Schema::create('tb_murid', function (Blueprint $table) {
            $table->string('nim', 20)->primary();
            $table->string('nama_murid', 100);
            $table->string('email', 100)->unique();
            $table->unsignedInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 4. tb_kelas
        Schema::create('tb_kelas', function (Blueprint $table) {
            $table->increments('id_kelas');
            $table->string('nama_kelas', 100);
            $table->text('deskripsi')->nullable();
            $table->string('nidn', 20)->nullable();
            $table->foreign('nidn')->references('nidn')->on('tb_pengajar')->onDelete('set null');
            $table->timestamps();
        });

        // 5. tb_peserta_kelas
        Schema::create('tb_peserta_kelas', function (Blueprint $table) {
            $table->unsignedInteger('id_kelas');
            $table->string('nim', 20);
            $table->primary(['id_kelas', 'nim']);
            $table->foreign('id_kelas')->references('id_kelas')->on('tb_kelas')->onDelete('cascade');
            $table->foreign('nim')->references('nim')->on('tb_murid')->onDelete('cascade');
        });

        // 6. tb_materi
        Schema::create('tb_materi', function (Blueprint $table) {
            $table->increments('id_materi');
            $table->unsignedInteger('id_kelas');
            $table->string('judul_materi', 150);
            $table->text('deskripsi')->nullable();
            $table->string('file_materi', 255)->nullable();
            $table->timestamp('tgl_upload')->useCurrent();
            $table->foreign('id_kelas')->references('id_kelas')->on('tb_kelas')->onDelete('cascade');
        });

        // 7. tb_tugas
        Schema::create('tb_tugas', function (Blueprint $table) {
            $table->increments('id_tugas');
            $table->unsignedInteger('id_kelas');
            $table->string('judul_tugas', 150);
            $table->text('deskripsi')->nullable();
            $table->string('file_tugas', 255)->nullable();
            $table->dateTime('deadline')->nullable();
            $table->timestamps();
            $table->foreign('id_kelas')->references('id_kelas')->on('tb_kelas')->onDelete('cascade');
        });

        // 8. tb_submission
        Schema::create('tb_submission', function (Blueprint $table) {
            $table->increments('id_submission');
            $table->unsignedInteger('id_tugas');
            $table->string('nim', 20);
            $table->string('file_jawaban', 255)->nullable();
            $table->timestamp('tgl_kumpul')->useCurrent();
            $table->integer('nilai')->nullable();
            $table->text('catatan')->nullable();
            $table->foreign('id_tugas')->references('id_tugas')->on('tb_tugas')->onDelete('cascade');
            $table->foreign('nim')->references('nim')->on('tb_murid')->onDelete('cascade');
        });

        // 9. tb_diskusi (Diskusi internal per kelas)
        Schema::create('tb_diskusi', function (Blueprint $table) {
            $table->increments('id_diskusi');
            $table->unsignedInteger('id_kelas');
            $table->unsignedInteger('id_user');
            $table->string('judul_diskusi', 150);
            $table->text('isi_diskusi');
            $table->timestamp('tgl_post')->useCurrent();
            $table->foreign('id_kelas')->references('id_kelas')->on('tb_kelas')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 10. tb_balasan_diskusi
        Schema::create('tb_balasan_diskusi', function (Blueprint $table) {
            $table->increments('id_balasan');
            $table->unsignedInteger('id_diskusi');
            $table->unsignedInteger('id_user');
            $table->text('isi_balasan');
            $table->timestamp('tgl_balasan')->useCurrent();
            $table->foreign('id_diskusi')->references('id_diskusi')->on('tb_diskusi')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 11. tb_kuis
        Schema::create('tb_kuis', function (Blueprint $table) {
            $table->increments('id_kuis');
            $table->unsignedInteger('id_kelas');
            $table->string('judul_kuis', 150);
            $table->text('deskripsi')->nullable();
            $table->integer('durasi_menit')->default(30);
            $table->timestamp('tgl_dibuat')->useCurrent();
            $table->foreign('id_kelas')->references('id_kelas')->on('tb_kelas')->onDelete('cascade');
        });

        // 12. tb_soal_kuis
        Schema::create('tb_soal_kuis', function (Blueprint $table) {
            $table->increments('id_soal');
            $table->unsignedInteger('id_kuis');
            $table->text('pertanyaan');
            $table->string('opsi_a', 255);
            $table->string('opsi_b', 255);
            $table->string('opsi_c', 255);
            $table->string('opsi_d', 255);
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D']);
            $table->foreign('id_kuis')->references('id_kuis')->on('tb_kuis')->onDelete('cascade');
        });

        // 13. tb_nilai_kuis
        Schema::create('tb_nilai_kuis', function (Blueprint $table) {
            $table->increments('id_nilai');
            $table->unsignedInteger('id_kuis');
            $table->string('nim', 20);
            $table->integer('nilai')->default(0);
            $table->timestamp('tgl_dikerjakan')->useCurrent();
            $table->foreign('id_kuis')->references('id_kuis')->on('tb_kuis')->onDelete('cascade');
            $table->foreign('nim')->references('nim')->on('tb_murid')->onDelete('cascade');
        });

        // 14. tb_notifikasi
        Schema::create('tb_notifikasi', function (Blueprint $table) {
            $table->increments('id_notifikasi');
            $table->unsignedInteger('id_user');
            $table->string('judul', 150);
            $table->text('pesan');
            $table->string('link', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('tgl_dibuat')->useCurrent();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 15. tb_forum_tag
        Schema::create('tb_forum_tag', function (Blueprint $table) {
            $table->increments('id_tag');
            $table->string('nama_tag', 50)->unique();
            $table->string('warna', 7)->default('#FFD700');
            $table->string('icon', 50)->default('bx-code-alt');
        });

        // 16. tb_forum_post
        Schema::create('tb_forum_post', function (Blueprint $table) {
            $table->increments('id_post');
            $table->unsignedInteger('id_user');
            $table->string('judul', 255);
            $table->text('konten');
            $table->enum('tipe', ['pertanyaan', 'diskusi', 'berbagi'])->default('pertanyaan');
            $table->enum('status', ['open', 'solved'])->default('open');
            $table->integer('views')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_announcement')->default(false);
            $table->timestamp('tgl_post')->useCurrent();
            $table->timestamp('tgl_update')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 17. tb_forum_jawaban
        Schema::create('tb_forum_jawaban', function (Blueprint $table) {
            $table->increments('id_jawaban');
            $table->unsignedInteger('id_post');
            $table->unsignedInteger('id_user');
            $table->text('konten');
            $table->boolean('is_solusi')->default(false);
            $table->timestamp('tgl_jawaban')->useCurrent();
            $table->foreign('id_post')->references('id_post')->on('tb_forum_post')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
        });

        // 18. tb_forum_vote
        Schema::create('tb_forum_vote', function (Blueprint $table) {
            $table->increments('id_vote');
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_post')->nullable();
            $table->unsignedInteger('id_jawaban')->nullable();
            $table->enum('tipe_vote', ['up', 'down']);
            $table->timestamp('tgl_vote')->useCurrent();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_post')->references('id_post')->on('tb_forum_post')->onDelete('cascade');
            $table->foreign('id_jawaban')->references('id_jawaban')->on('tb_forum_jawaban')->onDelete('cascade');
            $table->unique(['id_user', 'id_post'], 'unique_vote_post');
            $table->unique(['id_user', 'id_jawaban'], 'unique_vote_jawaban');
        });

        // 19. tb_forum_post_tag
        Schema::create('tb_forum_post_tag', function (Blueprint $table) {
            $table->unsignedInteger('id_post');
            $table->unsignedInteger('id_tag');
            $table->primary(['id_post', 'id_tag']);
            $table->foreign('id_post')->references('id_post')->on('tb_forum_post')->onDelete('cascade');
            $table->foreign('id_tag')->references('id_tag')->on('tb_forum_tag')->onDelete('cascade');
        });

        // 20. tb_forum_simpan
        Schema::create('tb_forum_simpan', function (Blueprint $table) {
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_post');
            $table->timestamp('tgl_simpan')->useCurrent();
            $table->primary(['id_user', 'id_post']);
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_post')->references('id_post')->on('tb_forum_post')->onDelete('cascade');
        });

        // 21. tb_forum_media
        Schema::create('tb_forum_media', function (Blueprint $table) {
            $table->increments('id_media');
            $table->unsignedInteger('id_post')->nullable();
            $table->unsignedInteger('id_jawaban')->nullable();
            $table->string('file_path', 255);
            $table->string('tipe_file', 50)->default('image');
            $table->timestamp('tgl_upload')->useCurrent();
            $table->foreign('id_post')->references('id_post')->on('tb_forum_post')->onDelete('cascade');
            $table->foreign('id_jawaban')->references('id_jawaban')->on('tb_forum_jawaban')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_forum_media');
        Schema::dropIfExists('tb_forum_simpan');
        Schema::dropIfExists('tb_forum_post_tag');
        Schema::dropIfExists('tb_forum_vote');
        Schema::dropIfExists('tb_forum_jawaban');
        Schema::dropIfExists('tb_forum_post');
        Schema::dropIfExists('tb_forum_tag');
        Schema::dropIfExists('tb_notifikasi');
        Schema::dropIfExists('tb_nilai_kuis');
        Schema::dropIfExists('tb_soal_kuis');
        Schema::dropIfExists('tb_kuis');
        Schema::dropIfExists('tb_balasan_diskusi');
        Schema::dropIfExists('tb_diskusi');
        Schema::dropIfExists('tb_submission');
        Schema::dropIfExists('tb_tugas');
        Schema::dropIfExists('tb_materi');
        Schema::dropIfExists('tb_peserta_kelas');
        Schema::dropIfExists('tb_kelas');
        Schema::dropIfExists('tb_murid');
        Schema::dropIfExists('tb_pengajar');
        Schema::dropIfExists('tb_user');
    }
};

