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
            $table->string('createdBy')->nullable()->change();
            $table->string('updatedBy')->nullable()->change();
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->string('createdBy')->nullable()->change();
            $table->string('updatedBy')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('createdBy')->nullable()->change();
            $table->unsignedBigInteger('updatedBy')->nullable()->change();
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->unsignedBigInteger('createdBy')->nullable()->change();
            $table->unsignedBigInteger('updatedBy')->nullable()->change();
        });
    }
};
