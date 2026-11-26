<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_langganan', function (Blueprint $table) {
            $table->string('snap_token', 120)->nullable()->after('status');
            $table->string('payment_url', 255)->nullable()->after('snap_token');
            $table->string('payment_type', 40)->nullable()->after('payment_url');
            $table->string('gateway_transaction_id', 100)->nullable()->after('payment_type');
            $table->string('gateway_status', 30)->nullable()->after('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('tb_langganan', function (Blueprint $table) {
            $table->dropColumn([
                'snap_token',
                'payment_url',
                'payment_type',
                'gateway_transaction_id',
                'gateway_status',
            ]);
        });
    }
};
