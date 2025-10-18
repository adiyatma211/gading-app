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
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->id();

        $table->unsignedBigInteger('transaction_id');
        $table->unsignedBigInteger('tipe_produk_id');

        $table->decimal('panjang', 8, 2)->nullable();
        $table->decimal('lebar', 8, 2)->nullable();
        $table->integer('qty')->nullable();

        $table->string('sisi')->nullable(); // jika enum: bisa diganti ->enum('sisi', ['1', '2'])
        $table->string('laminasi')->nullable(); // bisa juga enum('ya', 'tidak')

        $table->decimal('harga_per_meter', 10, 2);
        $table->decimal('diskon_barang', 10, 2)->default(0);
        $table->decimal('total_harga', 15, 2);

        $table->text('keterangan')->nullable();

        $table->string('createdBy')->nullable();
        $table->string('updatedBy')->nullable();

        $table->tinyInteger('deleteSts')->default(0);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            //
        });
    }
};
