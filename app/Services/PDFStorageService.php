<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\PdfStorageMetadata;
use App\Models\transactions;

class PDFStorageService
{
    /**
     * Storage disk name for PDF files
     */
    protected $disk = 'pdf_storage';

    /**
     * Archive disk name for old PDF files
     */
    protected $archiveDisk = 'pdf_archive';

    /**
     * Generate hierarchical storage path based on type and date
     * Format: YYYY/MM/DD/Type/
     *
     * @param string $type The type of PDF (thermal, invoice)
     * @param \Carbon\Carbon|null $date The date for path, defaults to current date
     * @return string The generated path
     */
    public function generatePath($type, $date = null)
    {
        $date = $date ?: Carbon::now();
        $type = strtoupper($type);

        return sprintf('%s/%s/%s/%s/',
            $date->format('Y'),
            $date->format('m'),
            $date->format('d'),
            $type
        );
    }

    /**
     * Generate unique filename with convention: {TYPE}-{YYYYMMDD}-{SEQ}-{TXID}.pdf
     *
     * @param string $type The type of PDF (thermal, invoice)
     * @param int $transactionId The transaction ID
     * @param \Carbon\Carbon|null $date The date for filename, defaults to current date
     * @return string The generated filename
     */
    public function generateFilename($type, $transactionId, $date = null)
    {
        $date = $date ?: Carbon::now();
        $type = strtoupper($type);
        $dateStr = $date->format('Ymd');

        // Get sequence number for this type and date
        $sequence = $this->getSequenceNumber($type, $date);

        return sprintf('%s-%s-%03d-%d.pdf',
            $type,
            $dateStr,
            $sequence,
            $transactionId
        );
    }

    /**
     * Get next sequence number for a given type and date
     *
     * @param string $type The type of PDF
     * @param \Carbon\Carbon $date The date
     * @return int The next sequence number
     */
    protected function getSequenceNumber($type, $date)
    {
        $dateStr = $date->format('Y-m-d');

        // Count existing files for this type and date
        $count = PdfStorageMetadata::where('file_type', strtolower($type))
            ->whereDate('created_at', $dateStr)
            ->count();

        return $count + 1;
    }

    /**
     * Store PDF with metadata and return file information
     *
     * @param string $pdfContent The PDF content as binary string
     * @param string $type The type of PDF (thermal, invoice)
     * @param int $transactionId The transaction ID
     * @param \Carbon\Carbon|null $date The date for storage, defaults to current date
     * @return array Information about the stored file
     */
    public function storePDF($pdfContent, $type, $transactionId, $date = null)
    {
        try {
            $date = $date ?: Carbon::now();
            $type = strtolower($type);

            // Validate type
            if (!in_array($type, ['thermal', 'invoice'])) {
                throw new \InvalidArgumentException("Invalid PDF type: {$type}. Must be 'thermal' or 'invoice'.");
            }

            // Generate path and filename
            $path = $this->generatePath($type, $date);
            $filename = $this->generateFilename($type, $transactionId, $date);
            $fullPath = $path . $filename;

            // Calculate file hash for integrity verification
            $fileHash = md5($pdfContent);
            $fileSize = strlen($pdfContent);

            // Check for duplicate files
            $existingFile = PdfStorageMetadata::where('file_hash', $fileHash)->first();
            if ($existingFile) {
                Log::info('Duplicate PDF detected, reusing existing file', [
                    'existing_file' => $existingFile->file_path,
                    'transaction_id' => $transactionId,
                    'file_hash' => $fileHash
                ]);

                return [
                    'success' => true,
                    'file_path' => $existingFile->file_path,
                    'file_name' => $existingFile->file_name,
                    'file_size' => $existingFile->file_size_bytes,
                    'file_hash' => $existingFile->file_hash,
                    'is_duplicate' => true,
                    'message' => 'Duplicate file detected, using existing reference'
                ];
            }

            // Ensure directory exists
            $this->ensureDirectoryExists($path);

            // Store file
            Storage::disk($this->disk)->put($fullPath, $pdfContent);

            // Verify file was stored correctly
            if (!Storage::disk($this->disk)->exists($fullPath)) {
                throw new \Exception("Failed to store PDF file at: {$fullPath}");
            }

            // Verify file integrity
            $storedContent = Storage::disk($this->disk)->get($fullPath);
            if (md5($storedContent) !== $fileHash) {
                // Clean up corrupted file
                Storage::disk($this->disk)->delete($fullPath);
                throw new \Exception("File integrity verification failed for: {$fullPath}");
            }

            // Save metadata to database
            $metadata = PdfStorageMetadata::create([
                'pdfable_type' => transactions::class,
                'pdfable_id' => $transactionId,
                'file_name' => $filename,
                'file_path' => $fullPath,
                'file_type' => $type,
                'file_hash' => $fileHash,
                'file_size_bytes' => $fileSize,
                'storage_disk' => $this->disk,
                'metadata' => [
                    'original_transaction_id' => $transactionId,
                    'storage_date' => $date->toDateTimeString(),
                    'verified' => true
                ]
            ]);

            // Update transaction record with PDF storage info
            $transaction = transactions::find($transactionId);
            if ($transaction) {
                if ($type === 'thermal') {
                    $transaction->pdf_storage_path = $fullPath;
                } elseif ($type === 'invoice') {
                    $transaction->pdf_storage_path_invoice = $fullPath;
                }
                $transaction->pdf_storage_type = $type;
                $transaction->pdf_storage_hash = $fileHash;
                $transaction->pdf_storage_size = $fileSize;
                $transaction->save();
            }

            Log::info('PDF stored successfully', [
                'transaction_id' => $transactionId,
                'file_path' => $fullPath,
                'file_size' => $fileSize,
                'file_hash' => $fileHash
            ]);

            return [
                'success' => true,
                'file_path' => $fullPath,
                'file_name' => $filename,
                'file_size' => $fileSize,
                'file_hash' => $fileHash,
                'is_duplicate' => false,
                'metadata_id' => $metadata->id
            ];

        } catch (\Exception $e) {
            Log::error('Failed to store PDF', [
                'transaction_id' => $transactionId,
                'type' => $type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Move old files to archive
     *
     * @param string $filePath The path to file to archive
     * @return array Result of archive operation
     */
    public function archiveFile($filePath)
    {
        try {
            // Check if file exists in main storage
            if (!Storage::disk($this->disk)->exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }

            // Get metadata for this file
            $metadata = PdfStorageMetadata::where('file_path', $filePath)->first();
            if (!$metadata) {
                throw new \Exception("No metadata found for file: {$filePath}");
            }

            // Generate archive path
            $archivePath = 'archived/' . ltrim($filePath, '/');

            // Ensure archive directory exists
            $archiveDir = dirname($archivePath);
            $this->ensureArchiveDirectoryExists($archiveDir);

            // Move file to archive
            $fileContent = Storage::disk($this->disk)->get($filePath);
            Storage::disk($this->archiveDisk)->put($archivePath, $fileContent);

            // Verify file was archived correctly
            if (!Storage::disk($this->archiveDisk)->exists($archivePath)) {
                throw new \Exception("Failed to archive file to: {$archivePath}");
            }

            // Update metadata
            $metadata->archived_at = Carbon::now();
            $metadata->archive_path = $archivePath;
            $metadata->save();

            // Update transaction record
            if ($metadata->pdfable_type === transactions::class) {
                $transaction = transactions::find($metadata->pdfable_id);
                if ($transaction) {
                    $transaction->pdf_archived_at = Carbon::now();
                    $transaction->pdf_archive_path = $archivePath;
                    $transaction->save();
                }
            }

            // Optionally delete from main storage (commented out for safety)
            // Storage::disk($this->disk)->delete($filePath);

            Log::info('File archived successfully', [
                'original_path' => $filePath,
                'archive_path' => $archivePath,
                'metadata_id' => $metadata->id
            ]);

            return [
                'success' => true,
                'original_path' => $filePath,
                'archive_path' => $archivePath,
                'archived_at' => $metadata->archived_at->toDateTimeString()
            ];

        } catch (\Exception $e) {
            Log::error('Failed to archive file', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Migrate existing PDFs from old structure to new hierarchical structure
     *
     * @return array Result of migration operation
     */
    public function migrateExistingFiles()
    {
        try {
            $migratedCount = 0;
            $errorCount = 0;
            $errors = [];

            // Get all transactions with PDF files in old format
            $transactions = transactions::whereNotNull('nota_file')
                ->orWhereNotNull('nota_file_dua')
                ->get();

            Log::info('Starting PDF migration', [
                'total_transactions' => $transactions->count()
            ]);

            foreach ($transactions as $transaction) {
                try {
                    // Process main nota file
                    if ($transaction->nota_file) {
                        $result = $this->migrateSingleFile($transaction, 'nota', $transaction->nota_file);
                        if ($result['success']) {
                            $migratedCount++;
                        } else {
                            $errorCount++;
                            $errors[] = [
                                'transaction_id' => $transaction->id,
                                'file' => $transaction->nota_file,
                                'error' => $result['error']
                            ];
                        }
                    }

                    // Process secondary nota file
                    if ($transaction->nota_file_dua) {
                        $result = $this->migrateSingleFile($transaction, 'nota_dua', $transaction->nota_file_dua);
                        if ($result['success']) {
                            $migratedCount++;
                        } else {
                            $errorCount++;
                            $errors[] = [
                                'transaction_id' => $transaction->id,
                                'file' => $transaction->nota_file_dua,
                                'error' => $result['error']
                            ];
                        }
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ];

                    Log::error('Failed to migrate transaction files', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('PDF migration completed', [
                'migrated_count' => $migratedCount,
                'error_count' => $errorCount
            ]);

            return [
                'success' => true,
                'migrated_count' => $migratedCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            Log::error('PDF migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Migrate a single file from old structure to new structure
     *
     * @param transactions $transaction The transaction model
     * @param string $type The type of file (nota, nota_dua)
     * @param string $oldFilename The old filename
     * @return array Result of migration
     */
    protected function migrateSingleFile($transaction, $type, $oldFilename)
    {
        try {
            // Determine old file path
            $oldPath = 'nota/' . $oldFilename;

            // Check if file exists in public storage
            if (!Storage::disk('public')->exists($oldPath)) {
                throw new \Exception("Old file not found: {$oldPath}");
            }

            // Get file content
            $fileContent = Storage::disk('public')->get($oldPath);

            // Determine new type (map old types to new ones)
            $newType = ($type === 'nota') ? 'thermal' : 'invoice';

            // Get transaction date for proper path generation
            $transactionDate = $transaction->created_at ?? Carbon::now();

            // Store using new system
            $result = $this->storePDF($fileContent, $newType, $transaction->id, $transactionDate);

            if ($result['success']) {
                Log::info('File migrated successfully', [
                    'transaction_id' => $transaction->id,
                    'old_path' => $oldPath,
                    'new_path' => $result['file_path']
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to migrate single file', [
                'transaction_id' => $transaction->id,
                'old_filename' => $oldFilename,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Ensure directory exists in storage
     *
     * @param string $path The directory path
     * @return void
     */
    protected function ensureDirectoryExists($path)
    {
        $fullPath = Storage::disk($this->disk)->path($path);

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }
    }

    /**
     * Ensure archive directory exists
     *
     * @param string $path The directory path
     * @return void
     */
    protected function ensureArchiveDirectoryExists($path)
    {
        $fullPath = Storage::disk($this->archiveDisk)->path($path);

        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }
    }

    /**
     * Get PDF file content
     *
     * @param string $filePath The file path
     * @return string|null The file content or null if not found
     */
    public function getPDF($filePath)
    {
        try {
            // Check main storage first
            if (Storage::disk($this->disk)->exists($filePath)) {
                return Storage::disk($this->disk)->get($filePath);
            }

            // Check if file is archived
            $metadata = PdfStorageMetadata::where('file_path', $filePath)
                ->whereNotNull('archived_at')
                ->first();

            if ($metadata && $metadata->archive_path) {
                return Storage::disk($this->archiveDisk)->get($metadata->archive_path);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Failed to get PDF', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Check if file exists
     *
     * @param string $filePath The file path
     * @return bool True if file exists
     */
    public function fileExists($filePath)
    {
        try {
            // Check main storage first
            if (Storage::disk($this->disk)->exists($filePath)) {
                return true;
            }

            // Check if file is archived
            $metadata = PdfStorageMetadata::where('file_path', $filePath)
                ->whereNotNull('archived_at')
                ->first();

            if ($metadata && $metadata->archive_path) {
                return Storage::disk($this->archiveDisk)->exists($metadata->archive_path);
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to check file existence', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Delete PDF file and metadata
     *
     * @param string $filePath The file path
     * @return bool True if deletion was successful
     */
    public function deletePDF($filePath)
    {
        try {
            DB::beginTransaction();

            $metadata = PdfStorageMetadata::where('file_path', $filePath)->first();
            if (!$metadata) {
                DB::rollBack();
                return false;
            }

            // Delete from main storage
            if (Storage::disk($this->disk)->exists($filePath)) {
                Storage::disk($this->disk)->delete($filePath);
            }

            // Delete from archive if exists
            if ($metadata->archive_path && Storage::disk($this->archiveDisk)->exists($metadata->archive_path)) {
                Storage::disk($this->archiveDisk)->delete($metadata->archive_path);
            }

            // Update transaction record
            if ($metadata->pdfable_type === transactions::class) {
                $transaction = transactions::find($metadata->pdfable_id);
                if ($transaction) {
                    $transaction->pdf_storage_path = null;
                    $transaction->pdf_storage_type = null;
                    $transaction->pdf_storage_hash = null;
                    $transaction->pdf_storage_size = null;
                    $transaction->pdf_archived_at = null;
                    $transaction->pdf_archive_path = null;
                    $transaction->save();
                }
            }

            // Delete metadata
            $metadata->delete();

            DB::commit();

            Log::info('PDF deleted successfully', [
                'file_path' => $filePath
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete PDF', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get storage statistics
     *
     * @return array Storage statistics
     */
    public function getStorageStats()
    {
        try {
            $stats = [
                'total_files' => 0,
                'total_size' => 0,
                'by_type' => [
                    'thermal' => ['count' => 0, 'size' => 0],
                    'invoice' => ['count' => 0, 'size' => 0]
                ],
                'by_year' => [],
                'archived_files' => 0,
                'compressed_files' => 0,
                'recent_files' => []
            ];

            // Get all PDF metadata
            $metadata = PdfStorageMetadata::all();

            foreach ($metadata as $file) {
                $stats['total_files']++;
                $stats['total_size'] += $file->file_size_bytes;

                // Group by type
                $type = $file->file_type;
                if (isset($stats['by_type'][$type])) {
                    $stats['by_type'][$type]['count']++;
                    $stats['by_type'][$type]['size'] += $file->file_size_bytes;
                }

                // Group by year
                $year = $file->created_at->format('Y');
                if (!isset($stats['by_year'][$year])) {
                    $stats['by_year'][$year] = ['count' => 0, 'size' => 0];
                }
                $stats['by_year'][$year]['count']++;
                $stats['by_year'][$year]['size'] += $file->file_size_bytes;

                // Count archived and compressed files
                if ($file->archived_at) {
                    $stats['archived_files']++;
                }

                // Get recent files (last 10)
                if (count($stats['recent_files']) < 10) {
                    $stats['recent_files'][] = [
                        'id' => $file->id,
                        'file_name' => $file->file_name,
                        'file_type' => $file->file_type,
                        'file_size' => $file->file_size_bytes,
                        'created_at' => $file->created_at->toDateTimeString()
                    ];
                }
            }

            // Get compression stats from transactions table
            $compressedCount = DB::table('transactions')
                ->where('pdf_is_compressed', true)
                ->count();
            $stats['compressed_files'] = $compressedCount;

            // Format sizes
            $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);
            foreach ($stats['by_type'] as $type => &$data) {
                $data['size_formatted'] = $this->formatBytes($data['size']);
            }
            foreach ($stats['by_year'] as $year => &$data) {
                $data['size_formatted'] = $this->formatBytes($data['size']);
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get storage stats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format bytes to human readable format
     *
     * @param int $bytes Number of bytes
     * @return string Formatted size
     */
    protected function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
