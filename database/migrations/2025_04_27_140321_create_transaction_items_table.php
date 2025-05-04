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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->unsignedBigInteger('tipe_produk_id')->nullable(); // Sesuai ID produk (opsional)
            $table->decimal('panjang', 8, 2)->default(0);
            $table->decimal('lebar', 8, 2)->default(0);
            $table->decimal('harga_per_meter', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('createdBy')->nullable();
            $table->unsignedBigInteger('updatedBy')->nullable();
            $table->tinyInteger('deleteSts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
