<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_nilai_kuis', function (Blueprint $table) {
            $table->unique(['id_kuis', 'nim'], 'unique_nilai_kuis_murid');
        });

        Schema::table('tb_submission', function (Blueprint $table) {
            $table->unique(['id_tugas', 'nim'], 'unique_submission_tugas_murid');
        });
    }

    public function down(): void
    {
        Schema::table('tb_nilai_kuis', function (Blueprint $table) {
            $table->dropUnique('unique_nilai_kuis_murid');
        });

        Schema::table('tb_submission', function (Blueprint $table) {
            $table->dropUnique('unique_submission_tugas_murid');
        });
    }
};
