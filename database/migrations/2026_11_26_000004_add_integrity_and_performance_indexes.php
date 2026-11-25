<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enforce strict 1:1 user-to-profile integrity
        Schema::table('tb_murid', function (Blueprint $table) {
            $table->unique('id_user', 'unique_murid_id_user');
        });

        Schema::table('tb_pengajar', function (Blueprint $table) {
            $table->unique('id_user', 'unique_pengajar_id_user');
        });

        // Composite & filter indexes for hot queries
        Schema::table('tb_notifikasi', function (Blueprint $table) {
            $table->index(['id_user', 'is_read', 'tgl_dibuat'], 'idx_notifikasi_user_read_tgl');
        });

        Schema::table('tb_forum_post', function (Blueprint $table) {
            $table->index(['is_pinned', 'tgl_post'], 'idx_forum_post_pinned_tgl');
            $table->index(['status', 'tipe'], 'idx_forum_post_status_tipe');
        });

        Schema::table('tb_kelas', function (Blueprint $table) {
            $table->index('is_premium', 'idx_kelas_is_premium');
        });

        Schema::table('tb_tugas', function (Blueprint $table) {
            $table->index(['id_kelas', 'deadline'], 'idx_tugas_kelas_deadline');
        });

        Schema::table('tb_materi', function (Blueprint $table) {
            $table->index(['id_kelas', 'tgl_upload'], 'idx_materi_kelas_tgl');
        });

        Schema::table('tb_diskusi', function (Blueprint $table) {
            $table->index(['id_kelas', 'tgl_post'], 'idx_diskusi_kelas_tgl');
        });
    }

    public function down(): void
    {
        // Recreate single-column FK backing indexes before dropping composite/unique indexes (MySQL 8.0 InnoDB)
        Schema::table('tb_diskusi', function (Blueprint $table) {
            $table->index('id_kelas', 'tb_diskusi_id_kelas_foreign');
            $table->dropIndex('idx_diskusi_kelas_tgl');
        });

        Schema::table('tb_materi', function (Blueprint $table) {
            $table->index('id_kelas', 'tb_materi_id_kelas_foreign');
            $table->dropIndex('idx_materi_kelas_tgl');
        });

        Schema::table('tb_tugas', function (Blueprint $table) {
            $table->index('id_kelas', 'tb_tugas_id_kelas_foreign');
            $table->dropIndex('idx_tugas_kelas_deadline');
        });

        Schema::table('tb_kelas', function (Blueprint $table) {
            $table->dropIndex('idx_kelas_is_premium');
        });

        Schema::table('tb_forum_post', function (Blueprint $table) {
            $table->dropIndex('idx_forum_post_status_tipe');
            $table->dropIndex('idx_forum_post_pinned_tgl');
        });

        Schema::table('tb_notifikasi', function (Blueprint $table) {
            $table->index('id_user', 'tb_notifikasi_id_user_foreign');
            $table->dropIndex('idx_notifikasi_user_read_tgl');
        });

        Schema::table('tb_pengajar', function (Blueprint $table) {
            $table->index('id_user', 'tb_pengajar_id_user_foreign');
            $table->dropUnique('unique_pengajar_id_user');
        });

        Schema::table('tb_murid', function (Blueprint $table) {
            $table->index('id_user', 'tb_murid_id_user_foreign');
            $table->dropUnique('unique_murid_id_user');
        });
    }
};
