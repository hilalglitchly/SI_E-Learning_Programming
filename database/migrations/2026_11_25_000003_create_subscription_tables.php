<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_kelas', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('nidn');
        });

        Schema::create('tb_paket_langganan', function (Blueprint $table) {
            $table->increments('id_paket');
            $table->string('nama_paket', 80);
            $table->unsignedInteger('durasi_hari');
            $table->unsignedInteger('harga');
            $table->string('deskripsi', 255)->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('tb_langganan', function (Blueprint $table) {
            $table->increments('id_langganan');
            $table->string('kode_invoice', 40)->unique();
            $table->unsignedInteger('id_user');
            $table->unsignedInteger('id_paket');
            $table->unsignedInteger('nominal');
            $table->string('status', 20)->default('pending');
            $table->string('bukti_bayar', 255)->nullable();
            $table->string('catatan_admin', 255)->nullable();
            $table->timestamp('tgl_mulai')->nullable();
            $table->timestamp('tgl_berakhir')->nullable();
            $table->unsignedInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_paket')->references('id_paket')->on('tb_paket_langganan')->onDelete('restrict');
            $table->foreign('verified_by')->references('id_user')->on('tb_user')->onDelete('set null');
            $table->index(['id_user', 'status', 'tgl_berakhir'], 'idx_langganan_user_status_exp');
        });

        // Default fixed plans (available immediately after migrate without running db:seed)
        DB::table('tb_paket_langganan')->insert([
            [
                'id_paket' => 1,
                'nama_paket' => 'Pro Bulanan',
                'durasi_hari' => 30,
                'harga' => 49000,
                'deskripsi' => 'Akses seluruh kelas Premium, materi, tugas, dan kuis selama 30 hari.',
                'is_active' => true,
            ],
            [
                'id_paket' => 2,
                'nama_paket' => 'Pro Tahunan',
                'durasi_hari' => 365,
                'harga' => 490000,
                'deskripsi' => 'Hemat 2 bulan — akses seluruh kelas Premium selama 365 hari penuh.',
                'is_active' => true,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_langganan');
        Schema::dropIfExists('tb_paket_langganan');
        Schema::table('tb_kelas', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });
    }
};
