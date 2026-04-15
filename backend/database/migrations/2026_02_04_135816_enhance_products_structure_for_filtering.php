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
        Schema::table('products', function (Blueprint $table) {
            $table->enum('gender', ['unisex', 'pria', 'wanita', 'anak'])->default('unisex')->after('category');
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('size');
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
