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
        Schema::create('history_payments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('telepon');
            $table->string('email')->nullable();
            $table->string('jenis_pelanggan')->nullable();
            $table->text('alamat');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('biaya_desain', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('dp', 15, 2)->default(0);

            $table->string('metode_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->integer('jumlah_item')->default(0);

            $table->timestamp('tanggal_transaksi')->useCurrent();

            // Field tambahan standar tracking
            $table->tinyInteger('deleteSts')->default(0);
            $table->string('createdBy')->nullable();
            $table->string('updatedBy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histoy_payments');
    }
};
