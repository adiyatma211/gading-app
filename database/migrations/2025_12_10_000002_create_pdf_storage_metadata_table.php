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
        Schema::create('pdf_storage_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('pdfable'); // polymorphic relation to transactions or other models
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->default('nota'); // nota, nota_dua, archived, etc.
            $table->string('file_hash')->unique(); // SHA-256 hash for integrity
            $table->integer('file_size_bytes');
            $table->integer('compressed_size_bytes')->nullable();
            $table->boolean('is_compressed')->default(false);
            $table->string('storage_disk')->default('pdf_storage');
            $table->json('metadata')->nullable(); // additional metadata
            $table->timestamp('archived_at')->nullable();
            $table->string('archive_path')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['pdfable_type', 'pdfable_id'], 'pdfable_index');
            $table->index('file_type');
            $table->index('is_compressed');
            $table->index('archived_at');
            $table->index('file_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_storage_metadata');
    }
};
