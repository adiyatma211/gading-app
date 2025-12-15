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
        if (!Schema::hasColumn('produks', 'tipe_produk')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->enum('tipe_produk', ['per_meter', 'tiered', 'flat', 'custom'])->nullable()->after('nama_produk');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('produks', 'tipe_produk')) {
            Schema::table('produks', function (Blueprint $table) {
                $table->dropColumn('tipe_produk');
            });
        }
    }
};
