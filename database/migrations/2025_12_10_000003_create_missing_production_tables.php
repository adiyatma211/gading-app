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
        // Create harga_produk table if not exists
        if (!Schema::hasTable('harga_produk')) {
            Schema::create('harga_produk', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('produk_id');
                $table->integer('min_qty')->nullable();
                $table->integer('max_qty')->nullable();
                $table->integer('sisi')->nullable();
                $table->boolean('laminasi')->default(0);
                $table->decimal('harga', 15, 2);
                $table->string('satuan')->nullable();
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
            });
        }

        // Create harga_produk_new table if not exists
        if (!Schema::hasTable('harga_produk_new')) {
            Schema::create('harga_produk_new', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('produk_id');
                $table->integer('min_qty')->nullable();
                $table->integer('max_qty')->nullable();
                $table->integer('sisi')->nullable();
                $table->boolean('laminasi')->default(0);
                $table->decimal('harga', 15, 2);
                $table->string('satuan')->nullable();
                $table->integer('diskon')->nullable();
                $table->timestamps();

                $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
            });
        }

        // Create cache_locks table if not exists
        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        // Create job_batches table if not exists
        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        // Create sessions table if not exists
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity');

                $table->index('user_id');
                $table->index('last_activity');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_produk');
        Schema::dropIfExists('harga_produk_new');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('sessions');
    }
};
