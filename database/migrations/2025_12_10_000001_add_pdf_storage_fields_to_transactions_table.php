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
        Schema::table('transactions', function (Blueprint $table) {
            // Add missing fields from production database first
            $table->string('nota_file')->nullable()->after('bukti_pembayaran');
            $table->string('nota_file_dua')->nullable()->after('nota_file');
            $table->string('nomor_faktur')->nullable()->after('nota_file_dua');
            $table->timestamp('tanggal_ambil')->nullable()->after('tanggal_transaksi');
            $table->string('status_pembayaran')->nullable()->after('tanggal_ambil');
            $table->string('diambil_oleh')->nullable()->after('status_pembayaran');
            $table->string('bukti_pengambilan')->nullable()->after('diambil_oleh');
            $table->timestamp('tanggal_selesai')->nullable()->after('bukti_pengambilan');

            // PDF Storage System Fields
            $table->string('pdf_storage_path')->nullable()->after('bukti_pengambilan');
            $table->string('pdf_storage_type')->default('nota')->after('pdf_storage_path');
            $table->string('pdf_storage_hash')->nullable()->after('pdf_storage_type');
            $table->integer('pdf_storage_size')->nullable()->after('pdf_storage_hash');
            $table->boolean('pdf_is_compressed')->default(false)->after('pdf_storage_size');
            $table->timestamp('pdf_archived_at')->nullable()->after('pdf_is_compressed');
            $table->string('pdf_archive_path')->nullable()->after('pdf_archived_at');

            // Indexes for performance
            $table->index('pdf_storage_type');
            $table->index('pdf_is_compressed');
            $table->index('pdf_archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['pdf_storage_type']);
            $table->dropIndex(['pdf_is_compressed']);
            $table->dropIndex(['pdf_archived_at']);

            $table->dropColumn([
                'nota_file',
                'nota_file_dua',
                'nomor_faktur',
                'tanggal_ambil',
                'status_pembayaran',
                'diambil_oleh',
                'bukti_pengambilan',
                'tanggal_selesai',
                'pdf_storage_path',
                'pdf_storage_type',
                'pdf_storage_hash',
                'pdf_storage_size',
                'pdf_is_compressed',
                'pdf_archived_at',
                'pdf_archive_path'
            ]);
        });
    }
};
