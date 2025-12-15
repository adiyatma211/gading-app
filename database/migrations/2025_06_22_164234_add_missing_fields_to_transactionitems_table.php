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
            if (!Schema::hasColumn('transaction_items', 'id')) {
                $table->id();
            }

            if (!Schema::hasColumn('transaction_items', 'transaction_id')) {
                $table->unsignedBigInteger('transaction_id');
            }
            if (!Schema::hasColumn('transaction_items', 'tipe_produk_id')) {
                $table->unsignedBigInteger('tipe_produk_id')->nullable();
            }

            if (!Schema::hasColumn('transaction_items', 'panjang')) {
                $table->decimal('panjang', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('transaction_items', 'lebar')) {
                $table->decimal('lebar', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('transaction_items', 'qty')) {
                $table->integer('qty')->nullable();
            }

            if (!Schema::hasColumn('transaction_items', 'sisi')) {
                $table->string('sisi')->nullable();
            }
            if (!Schema::hasColumn('transaction_items', 'laminasi')) {
                $table->string('laminasi')->nullable();
            }

            if (!Schema::hasColumn('transaction_items', 'harga_per_meter')) {
                $table->decimal('harga_per_meter', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('transaction_items', 'diskon_barang')) {
                $table->decimal('diskon_barang', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('transaction_items', 'total_harga')) {
                $table->decimal('total_harga', 15, 2)->nullable();
            }

            if (!Schema::hasColumn('transaction_items', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }

            if (!Schema::hasColumn('transaction_items', 'createdBy')) {
                $table->string('createdBy')->nullable();
            }
            if (!Schema::hasColumn('transaction_items', 'updatedBy')) {
                $table->string('updatedBy')->nullable();
            }

            if (!Schema::hasColumn('transaction_items', 'deleteSts')) {
                $table->tinyInteger('deleteSts')->default(0);
            }

            if (!Schema::hasColumn('transaction_items', 'created_at') && !Schema::hasColumn('transaction_items', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            // No-op: intentionally left blank to avoid dropping columns inadvertently
        });
    }
};
