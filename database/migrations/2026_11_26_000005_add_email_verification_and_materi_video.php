<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tb_user', 'email_verified_at')) {
            Schema::table('tb_user', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable()->after('role');
            });
        }

        if (!Schema::hasColumn('tb_materi', 'video_url')) {
            Schema::table('tb_materi', function (Blueprint $table) {
                $table->string('video_url', 255)->nullable()->after('file_materi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tb_materi', 'video_url')) {
            Schema::table('tb_materi', function (Blueprint $table) {
                $table->dropColumn('video_url');
            });
        }

        if (Schema::hasColumn('tb_user', 'email_verified_at')) {
            Schema::table('tb_user', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }
    }
};
