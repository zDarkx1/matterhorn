<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
        $table->string('invoice_no', 50)->unique();
        $table->dateTime('start_date');
        $table->dateTime('end_date');
        $table->dateTime('return_date')->nullable();
        $table->decimal('total_price', 12, 2);
        $table->decimal('fine_amount', 12, 2)->default(0);
        $table->enum('status', ['booked', 'active', 'returned', 'canceled', 'overdue'])->default('booked');
        $table->text('guarantee_info')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
