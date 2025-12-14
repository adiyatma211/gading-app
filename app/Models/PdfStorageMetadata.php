<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PdfStorageMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdfable_type',
        'pdfable_id',
        'file_name',
        'file_path',
        'file_type',
        'file_hash',
        'file_size_bytes',
        'compressed_size_bytes',
        'is_compressed',
        'storage_disk',
        'metadata',
        'archived_at',
        'archive_path',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_compressed' => 'boolean',
        'file_size_bytes' => 'integer',
        'compressed_size_bytes' => 'integer',
        'archived_at' => 'datetime',
    ];

    /**
     * Get the parent model (polymorphic relationship).
     */
    public function pdfable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get human readable file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        return $this->formatBytes($this->file_size_bytes);
    }

    /**
     * Get human readable compressed file size
     */
    public function getFormattedCompressedSizeAttribute(): ?string
    {
        return $this->compressed_size_bytes ? $this->formatBytes($this->compressed_size_bytes) : null;
    }

    /**
     * Get compression ratio percentage
     */
    public function getCompressionRatioAttribute(): ?float
    {
        if (!$this->is_compressed || !$this->compressed_size_bytes) {
            return null;
        }

        return round(($this->file_size_bytes - $this->compressed_size_bytes) / $this->file_size_bytes * 100, 2);
    }

    /**
     * Check if file is archived
     */
    public function getIsArchivedAttribute(): bool
    {
        return !is_null($this->archived_at);
    }

    /**
     * Get file URL
     */
    public function getUrlAttribute(): ?string
    {
        $storageService = app(\App\Services\PDFStorageService::class);
        return $storageService->getPDFUrl($this->file_path);
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Scope to get only active (non-archived) files
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope to get only archived files
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to get only compressed files
     */
    public function scopeCompressed($query)
    {
        return $query->where('is_compressed', true);
    }

    /**
     * Scope to get only uncompressed files
     */
    public function scopeUncompressed($query)
    {
        return $query->where('is_compressed', false);
    }

    /**
     * Scope to filter by file type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Scope to get files older than specified days
     */
    public function scopeOlderThan($query, int $days)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope to get files created within specified days
     */
    public function scopeWithinDays($query, int $days)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
