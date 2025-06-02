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
        Schema::create('harga_produk_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->integer('min_qty')->nullable(); // Untuk tiered pricing
            $table->integer('max_qty')->nullable(); // Untuk tiered pricing
            $table->integer('sisi')->nullable(); // Untuk kartu nama
            $table->boolean('laminasi')->default(false); // Untuk kartu nama
            $table->decimal('harga', 15, 2);
            $table->string('satuan')->nullable(); // "meter", "lembar", "pcs"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_produk');
    }
};
