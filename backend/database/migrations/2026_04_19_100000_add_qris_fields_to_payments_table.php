<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('qris_invoice', 100)->nullable()->after('payment_proof');
            $table->text('qris_url')->nullable()->after('qris_invoice');
            $table->dateTime('expired_at')->nullable()->after('qris_url');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['qris_invoice', 'qris_url', 'expired_at']);
        });
    }
};
