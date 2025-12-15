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
            // Add missing fields that don't exist in the original table
            $table->integer('qty')->nullable()->after('lebar');
            $table->integer('sisi')->nullable()->after('qty');
            $table->string('laminasi')->nullable()->after('sisi');
            $table->integer('diskon_barang')->nullable()->default(0)->after('harga_per_meter');
            $table->integer('total_harga')->nullable()->after('diskon_barang');

            // Modify existing fields to match production database
            $table->decimal('panjang', 8, 2)->default(0.00)->change();
            $table->decimal('lebar', 8, 2)->default(0.00)->change();
            $table->decimal('harga_per_meter', 15, 2)->default(0.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['qty', 'sisi', 'laminasi', 'diskon_barang', 'total_harga']);
        });
    }
};
