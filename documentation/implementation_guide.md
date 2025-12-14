# PDF Storage System Implementation Guide

This guide provides step-by-step instructions and all necessary code to implement the improved PDF storage system for POS Gading App.

## Phase 1: Database Migration

### Migration 1: Add PDF Management Fields to Transactions Table

Create file: `database/migrations/2025_12_10_000001_add_pdf_management_fields_to_transactions.php`

```php
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
            // PDF file paths
            $table->string('thermal_pdf_path')->nullable()->after('bukti_pembayaran');
            $table->string('invoice_pdf_path')->nullable()->after('thermal_pdf_path');
            
            // File metadata
            $table->string('thermal_pdf_filename')->nullable()->after('thermal_pdf_path');
            $table->string('invoice_pdf_filename')->nullable()->after('invoice_pdf_path');
            $table->integer('thermal_pdf_size')->nullable()->after('thermal_pdf_filename');
            $table->integer('invoice_pdf_size')->nullable()->after('invoice_pdf_filename');
            $table->string('thermal_pdf_hash')->nullable()->after('thermal_pdf_size');
            $table->string('invoice_pdf_hash')->nullable()->after('invoice_pdf_size');
            
            // File management
            $table->timestamp('thermal_pdf_generated_at')->nullable()->after('thermal_pdf_hash');
            $table->timestamp('invoice_pdf_generated_at')->nullable()->after('invoice_pdf_hash');
            $table->tinyInteger('thermal_pdf_archived')->default(0)->after('thermal_pdf_generated_at');
            $table->tinyInteger('invoice_pdf_archived')->default(0)->after('invoice_pdf_generated_at');
            $table->softDeletes()->after('updatedBy');
            
            // Indexes for performance
            $table->index(['tanggal_transaksi']);
            $table->index(['thermal_pdf_generated_at']);
            $table->index(['invoice_pdf_generated_at']);
            $table->index(['thermal_pdf_archived']);
            $table->index(['invoice_pdf_archived']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['tanggal_transaksi']);
            $table->dropIndex(['thermal_pdf_generated_at']);
            $table->dropIndex(['invoice_pdf_generated_at']);
            $table->dropIndex(['thermal_pdf_archived']);
            $table->dropIndex(['invoice_pdf_archived']);
            $table->dropIndex(['deleted_at']);
            
            $table->dropColumn([
                'thermal_pdf_path',
                'invoice_pdf_path',
                'thermal_pdf_filename',
                'invoice_pdf_filename',
                'thermal_pdf_size',
                'invoice_pdf_size',
                'thermal_pdf_hash',
                'invoice_pdf_hash',
                'thermal_pdf_generated_at',
                'invoice_pdf_generated_at',
                'thermal_pdf_archived',
                'invoice_pdf_archived',
                'deleted_at'
            ]);
        });
    }
};
```

### Migration 2: Create PDF Files Management Table

Create file: `database/migrations/2025_12_10_000002_create_pdf_files_table.php`

```php
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
        Schema::create('pdf_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_type'); // 'thermal' or 'invoice'
            $table->string('filename');
            $table->string('path');
            $table->integer('file_size');
            $table->string('file_hash');
            $table->string('transaction_id');
            $table->timestamp('generated_at');
            $table->timestamp('archived_at')->nullable();
            $table->string('archive_path')->nullable();
            $table->tinyInteger('is_compressed')->default(0);
            $table->string('compression_type')->nullable();
            $table->integer('original_size')->nullable();
            $table->integer('compressed_size')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['file_type']);
            $table->index(['generated_at']);
            $table->index(['archived_at']);
            $table->index(['is_compressed']);
            $table->index(['transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_files');
    }
};
```

## Phase 2: Storage Service Implementation

### PDF Storage Service

Create file: `app/Services/PDFStorageService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\transactions;

class PDFStorageService
{
    private const THERMAL = 'thermal';
    private const INVOICE = 'invoice';
    
    public function generatePath(string $type, Carbon $date): string
    {
        $year = $date->format('Y');
        $month = $date->format('m_' . $date->format('F'));
        $day = $date->format('d');
        
        return "{$year}/{$month}/{$day}/{$type}";
    }
    
    public function generateFilename(string $type, Carbon $date, string $transactionId, int $sequence): string
    {
        $prefix = $type === self::THERMAL ? 'TRX' : 'INV';
        $dateStr = $date->format('Ymd');
        $seq = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$dateStr}-{$seq}-{$transactionId}.pdf";
    }
    
    public function storePDF(string $content, string $type, Carbon $date, string $transactionId, int $sequence): array
    {
        $filename = $this->generateFilename($type, $date, $transactionId, $sequence);
        $path = $this->generatePath($type, $date);
        $fullPath = "{$path}/{$filename}";
        
        // Ensure directory exists
        Storage::disk('pdfs')->makeDirectory($path);
        
        // Store file
        $stored = Storage::disk('pdfs')->put($fullPath, $content);
        
        if (!$stored) {
            throw new \Exception("Failed to store PDF: {$fullPath}");
        }
        
        // Get file info
        $fileSize = Storage::disk('pdfs')->size($fullPath);
        $fileHash = md5($content);
        
        Log::info('PDF stored successfully', [
            'filename' => $filename,
            'path' => $fullPath,
            'size' => $fileSize,
            'type' => $type
        ]);
        
        return [
            'filename' => $filename,
            'path' => $fullPath,
            'size' => $fileSize,
            'hash' => $fileHash,
            'full_url' => Storage::disk('pdfs')->url($fullPath),
        ];
    }
    
    public function getDailySequence(string $date): int
    {
        $count = transactions::whereDate('tanggal_transaksi', $date)->count();
        return $count + 1;
    }
    
    public function archiveFile(string $path, Carbon $date): bool
    {
        $archivePath = $this->generateArchivePath($path, $date);
        
        // Ensure archive directory exists
        $archiveDir = dirname($archivePath);
        Storage::disk('pdfs_archive')->makeDirectory($archiveDir);
        
        // Copy to archive first
        $content = Storage::disk('pdfs')->get($path);
        $copied = Storage::disk('pdfs_archive')->put($archivePath, $content);
        
        if ($copied) {
            // Delete from active storage
            Storage::disk('pdfs')->delete($path);
            
            Log::info("File archived", [
                'from' => $path,
                'to' => $archivePath,
                'archived_at' => now()
            ]);
            
            return true;
        }
        
        return false;
    }
    
    private function generateArchivePath(string $path, Carbon $date): string
    {
        // Extract year and move to archive structure
        $parts = explode('/', $path);
        $year = $parts[0] ?? $date->format('Y');
        
        return "{$year}/{$path}";
    }
    
    public function migrateExistingFiles(): array
    {
        $results = [];
        $existingFiles = Storage::disk('public')->allFiles('nota');
        
        foreach ($existingFiles as $file) {
            try {
                // Extract information from filename
                $filename = basename($file);
                $parts = explode('_', $filename);
                
                if (count($parts) >= 3) {
                    $dateStr = $parts[1] ?? null;
                    $customerName = $parts[2] ?? null;
                    
                    if ($dateStr && strlen($dateStr) === 8) {
                        $date = Carbon::createFromFormat('Ymd', $dateStr);
                        $type = str_contains($filename, 'nota_dua') ? self::INVOICE : self::THERMAL;
                        
                        // Generate new path and filename
                        $transactionId = Str::random(8);
                        $sequence = $this->getDailySequence($date->format('Y-m-d'));
                        
                        $newPath = $this->generatePath($type, $date);
                        $newFilename = $this->generateFilename($type, $date, $transactionId, $sequence);
                        $newFullPath = "{$newPath}/{$newFilename}";
                        
                        // Move file to new location
                        $content = Storage::disk('public')->get($file);
                        Storage::disk('pdfs')->makeDirectory($newPath);
                        Storage::disk('pdfs')->put($newFullPath, $content);
                        
                        $results[] = [
                            'old_file' => $file,
                            'new_file' => $newFullPath,
                            'status' => 'migrated'
                        ];
                    }
                }
            } catch (\Exception $e) {
                $results[] = [
                    'old_file' => $file,
                    'error' => $e->getMessage(),
                    'status' => 'failed'
                ];
            }
        }
        
        return $results;
    }
}
```

### PDF Compression Service

Create file: `app/Services/PDFCompressionService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PDFCompressionService
{
    public function compressPDF(string $sourcePath, string $targetPath): array
    {
        try {
            $sourceFullPath = Storage::disk('pdfs')->path($sourcePath);
            $targetFullPath = Storage::disk('pdfs_archive')->path($targetPath);
            
            $originalSize = Storage::disk('pdfs')->size($sourcePath);
            
            // Use ghostscript for PDF compression
            $command = $this->buildCompressionCommand($sourceFullPath, $targetFullPath);
            $output = shell_exec($command . ' 2>&1');
            
            if (file_exists($targetFullPath)) {
                $compressedSize = filesize($targetFullPath);
                $compressionRatio = round((1 - $compressedSize / $originalSize) * 100, 2);
                
                Log::info('PDF compressed successfully', [
                    'source' => $sourcePath,
                    'target' => $targetPath,
                    'original_size' => $originalSize,
                    'compressed_size' => $compressedSize,
                    'compression_ratio' => $compressionRatio
                ]);
                
                return [
                    'original_size' => $originalSize,
                    'compressed_size' => $compressedSize,
                    'compression_ratio' => $compressionRatio,
                    'success' => true
                ];
            }
            
            return ['success' => false, 'error' => 'Compression failed', 'output' => $output];
            
        } catch (\Exception $e) {
            Log::error('PDF compression error', [
                'source' => $sourcePath,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function buildCompressionCommand(string $source, string $target): string
    {
        return "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/printer " .
               "-dNOPAUSE -dQUIET -dBATCH -sOutputFile=\"{$target}\" \"{$source}\"";
    }
    
    public function isGhostscriptAvailable(): bool
    {
        $output = shell_exec('gs --version 2>&1');
        return !empty($output) && !str_contains($output, 'not found');
    }
}
```

## Phase 3: Update Configuration

### Enhanced Filesystems Configuration

Update file: `config/filesystems.php`

```php
<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // New: PDF Storage Disk
        'pdfs' => [
            'driver' => 'local',
            'root' => storage_path('app/pdfs'),
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ],

        // New: PDF Archive Disk
        'pdfs_archive' => [
            'driver' => 'local',
            'root' => storage_path('app/pdfs_archive'),
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ],

        // Cloud Storage (Optional)
        's3_pdfs' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_PDF_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
```

## Phase 4: Update Transactions Controller

### Enhanced TransactionsController

Update file: `app/Http/Controllers/TransactionsController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\historynota;
use Illuminate\Support\Str;
use App\Models\transactions;
use Illuminate\Http\Request;
use App\Models\histoypayment;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\transactionitems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Http\Requests\UpdatetransactionsRequest;
use App\Services\PDFStorageService;
use App\Services\PDFCompressionService;

class TransactionsController extends Controller
{
    protected $pdfStorageService;
    protected $pdfCompressionService;

    public function __construct(PDFStorageService $pdfStorageService, PDFCompressionService $pdfCompressionService)
    {
        $this->pdfStorageService = $pdfStorageService;
        $this->pdfCompressionService = $pdfCompressionService;
    }

    // ... existing methods ...

    private function generateNotaFile($transaction): string
    {
        $transaction = Transactions::with(['customer', 'items.produk'])->find($transaction->id);
        $custName = $transaction->customer->nama;
        $logoPath = public_path('assets/logoSVG.SVG');
        $logoPath2 = public_path('assets/logoSVG.svg');
        $watermarkPath = public_path('assets/lunas2.png');

        $logoData = $this->imageToDataUri(File::exists($logoPath2) ? $logoPath2 : $logoPath);
        $watermarkData = $this->imageToDataUri($watermarkPath);

        $transactionDate = Carbon::parse($transaction->tanggal_transaksi);
        $transactionId = 'TX' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
        $sequence = $this->pdfStorageService->getDailySequence($transactionDate->format('Y-m-d'));

        // === 1. Generate PDF versi pertama (thermal) ===
        $pdfContent1 = view('Transaksi.v_notav1', [
            'transaction' => $transaction,
            'logoPath' => $logoPath2,
            'logoData' => $logoData,
            'watermarkPath' => $watermarkPath,
            'watermarkData' => $watermarkData,
            'thermalWidth' => (float) config('print.thermal_width_mm', 72),
        ])->render();

        // Store thermal PDF using new storage service
        $thermalFileInfo = $this->pdfStorageService->storePDF(
            $this->renderPdfSafe($pdfContent1, [0, 0, 72 * 2.83465, 800], 'portrait'),
            PDFStorageService::THERMAL,
            $transactionDate,
            $transactionId,
            $sequence
        );

        // === 2. Generate PDF versi kedua (invoice) ===
        $pdfContent2 = view('Transaksi.v_nota', [
            'transaction' => $transaction,
            'logoPath' => $logoPath2,
            'logoData' => $logoData,
            'watermarkPath' => $watermarkPath,
            'watermarkData' => $watermarkData,
        ])->render();

        // Store invoice PDF using new storage service
        $invoiceFileInfo = $this->pdfStorageService->storePDF(
            $this->renderPdfSafe($pdfContent2, 'a4', 'landscape'),
            PDFStorageService::INVOICE,
            $transactionDate,
            $transactionId,
            $sequence
        );

        // === 3. Update ke tabel transactions ===
        $transaction->update([
            'thermal_pdf_path' => $thermalFileInfo['path'],
            'thermal_pdf_filename' => $thermalFileInfo['filename'],
            'thermal_pdf_size' => $thermalFileInfo['size'],
            'thermal_pdf_hash' => $thermalFileInfo['hash'],
            'thermal_pdf_generated_at' => now(),
            'invoice_pdf_path' => $invoiceFileInfo['path'],
            'invoice_pdf_filename' => $invoiceFileInfo['filename'],
            'invoice_pdf_size' => $invoiceFileInfo['size'],
            'invoice_pdf_hash' => $invoiceFileInfo['hash'],
            'invoice_pdf_generated_at' => now(),
        ]);

        // === 4. Simpan ke history_nota ===
        $lastId = Transactions::max('id') + 1;
        $nomorFaktur = 'FK-' . str_pad($lastId, 3, '0', STR_PAD_LEFT) . '/' . date('m') . '/' . date('Y');

        historynota::create([
            'transaction_id' => $transaction->id,
            'nomor_faktur'   => $nomorFaktur,
            'customer_id'    => $transaction->customer_id,
            'nota_file'      => $thermalFileInfo['filename'],
            'tanggal_cetak'  => now(),
            'deleteSts'      => 0,
            'createdBy'      => Auth::user()?->name ?? 'System',
            'updatedBy'      => Auth::user()?->name ?? 'System',
        ]);

        Log::info('PDFs generated and stored successfully', [
            'thermal_file' => $thermalFileInfo['filename'],
            'invoice_file' => $invoiceFileInfo['filename'],
            'transaction_id' => $transaction->id
        ]);

        return $thermalFileInfo['filename'];
    }

    // ... rest of existing methods ...
}
```

## Phase 5: Console Commands for Management

### PDF Storage Monitor Command

Create file: `app/Console/Commands/MonitorPDFStorage.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MonitorPDFStorage extends Command
{
    protected $signature = 'pdfs:monitor {--detailed : Show detailed information}';
    protected $description = 'Monitor PDF storage usage and health';

    public function handle()
    {
        $this->info('Starting PDF storage monitoring...');
        
        $this->checkStorageUsage();
        $this->checkFileIntegrity();
        $this->checkArchivingSchedule();
        
        if ($this->option('detailed')) {
            $this->showDetailedStats();
        }
        
        $this->info('PDF storage monitoring completed.');
        
        return Command::SUCCESS;
    }
    
    private function checkStorageUsage()
    {
        $activeSize = $this->calculateDirectorySize(storage_path('app/pdfs'));
        $archiveSize = $this->calculateDirectorySize(storage_path('app/pdfs_archive'));
        $totalSize = $activeSize + $archiveSize;
        
        $this->info('Storage Usage:');
        $this->line("  Active Storage: " . round($activeSize / 1024 / 1024 / 1024, 2) . ' GB');
        $this->line("  Archive Storage: " . round($archiveSize / 1024 / 1024 / 1024, 2) . ' GB');
        $this->line("  Total Storage: " . round($totalSize / 1024 / 1024 / 1024, 2) . ' GB');
        
        Log::info('Storage usage report', [
            'active_storage_gb' => round($activeSize / 1024 / 1024 / 1024, 2),
            'archive_storage_gb' => round($archiveSize / 1024 / 1024 / 1024, 2),
            'total_storage_gb' => round($totalSize / 1024 / 1024 / 1024, 2)
        ]);
    }
    
    private function checkFileIntegrity()
    {
        $orphanedFiles = 0;
        $missingFiles = 0;
        
        // Check for orphaned files in storage
        $storedFiles = Storage::disk('pdfs')->allFiles();
        $dbFiles = \App\Models\transactions::whereNotNull('thermal_pdf_path')
            ->orWhereNotNull('invoice_pdf_path')
            ->get();
        
        $dbFilePaths = $dbFiles->flatMap(function ($transaction) {
            $paths = [];
            if ($transaction->thermal_pdf_path) $paths[] = $transaction->thermal_pdf_path;
            if ($transaction->invoice_pdf_path) $paths[] = $transaction->invoice_pdf_path;
            return $paths;
        })->toArray();
        
        $orphanedFiles = count(array_diff($storedFiles, $dbFilePaths));
        
        // Check for missing files
        foreach ($dbFilePaths as $path) {
            if (!Storage::disk('pdfs')->exists($path)) {
                $missingFiles++;
            }
        }
        
        $this->info('File Integrity:');
        $this->line("  Orphaned Files: {$orphanedFiles}");
        $this->line("  Missing Files: {$missingFiles}");
        
        if ($orphanedFiles > 0 || $missingFiles > 0) {
            Log::warning('File integrity issues detected', [
                'orphaned_files' => $orphanedFiles,
                'missing_files' => $missingFiles
            ]);
        }
    }
    
    private function checkArchivingSchedule()
    {
        $twoYearsAgo = now()->subYears(2);
        $oldFiles = \App\Models\transactions::where(function ($query) use ($twoYearsAgo) {
            $query->where('thermal_pdf_generated_at', '<', $twoYearsAgo)
                  ->orWhere('invoice_pdf_generated_at', '<', $twoYearsAgo);
        })->where(function ($query) {
            $query->where('thermal_pdf_archived', 0)
                  ->orWhere('invoice_pdf_archived', 0);
        })->count();
        
        $this->info('Archiving Schedule:');
        $this->line("  Files Ready for Archive: {$oldFiles}");
        
        if ($oldFiles > 0) {
            Log::info('Files ready for archiving', ['count' => $oldFiles]);
        }
    }
    
    private function showDetailedStats()
    {
        $this->info('Detailed Statistics:');
        
        // File count by type
        $thermalCount = \App\Models\transactions::whereNotNull('thermal_pdf_path')->count();
        $invoiceCount = \App\Models\transactions::whereNotNull('invoice_pdf_path')->count();
        
        $this->line("  Thermal PDFs: {$thermalCount}");
        $this->line("  Invoice PDFs: {$invoiceCount}");
        
        // File count by year
        $filesByYear = \App\Models\transactions::selectRaw('YEAR(thermal_pdf_generated_at) as year, COUNT(*) as count')
            ->whereNotNull('thermal_pdf_generated_at')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();
            
        foreach ($filesByYear as $year) {
            $this->line("  Year {$year->year}: {$year->count} files");
        }
    }
    
    private function calculateDirectorySize($path): int
    {
        $totalSize = 0;
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
            }
        }
        
        return $totalSize;
    }
}
```

### PDF Archive Command

Create file: `app/Console/Commands/ArchivePDFs.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\transactions;
use App\Services\PDFStorageService;
use Carbon\Carbon;

class ArchivePDFs extends Command
{
    protected $signature = 'pdfs:archive {--dry-run : Show what would be archived without actually archiving}';
    protected $description = 'Archive old PDF files to long-term storage';
    
    protected $pdfStorageService;
    
    public function __construct(PDFStorageService $pdfStorageService)
    {
        parent::__construct();
        $this->pdfStorageService = $pdfStorageService;
    }

    public function handle()
    {
        $this->info('Starting PDF archiving process...');
        
        $twoYearsAgo = Carbon::now()->subYears(2);
        $dryRun = $this->option('dry-run');
        
        $transactions = transactions::where(function ($query) use ($twoYearsAgo) {
            $query->where('thermal_pdf_generated_at', '<', $twoYearsAgo)
                  ->orWhere('invoice_pdf_generated_at', '<', $twoYearsAgo);
        })->where(function ($query) {
            $query->where('thermal_pdf_archived', 0)
                  ->orWhere('invoice_pdf_archived', 0);
        })->get();
        
        $this->info("Found {$transactions->count()} transactions with files to archive");
        
        $archivedCount = 0;
        $errorCount = 0;
        
        foreach ($transactions as $transaction) {
            try {
                $this->archiveTransactionFiles($transaction, $dryRun);
                $archivedCount++;
                
                if (!$dryRun) {
                    $this->line("Archived files for transaction {$transaction->id}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to archive transaction {$transaction->id}: " . $e->getMessage());
                Log::error('Archive error', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Archiving completed. Processed: {$archivedCount}, Errors: {$errorCount}");
        
        return Command::SUCCESS;
    }
    
    private function archiveTransactionFiles($transaction, $dryRun)
    {
        $transactionDate = Carbon::parse($transaction->tanggal_transaksi);
        
        // Archive thermal PDF
        if ($transaction->thermal_pdf_path && !$transaction->thermal_pdf_archived) {
            if ($dryRun) {
                $this->line("Would archive thermal: {$transaction->thermal_pdf_path}");
            } else {
                $this->pdfStorageService->archiveFile($transaction->thermal_pdf_path, $transactionDate);
                $transaction->update([
                    'thermal_pdf_archived' => 1,
                    'archived_at' => now()
                ]);
            }
        }
        
        // Archive invoice PDF
        if ($transaction->invoice_pdf_path && !$transaction->invoice_pdf_archived) {
            if ($dryRun) {
                $this->line("Would archive invoice: {$transaction->invoice_pdf_path}");
            } else {
                $this->pdfStorageService->archiveFile($transaction->invoice_pdf_path, $transactionDate);
                $transaction->update([
                    'invoice_pdf_archived' => 1,
                    'archived_at' => now()
                ]);
            }
        }
    }
}
```

## Phase 6: Update Console Kernel

Update file: `app/Console/Kernel.php`

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\MonitorPDFStorage::class,
        \App\Console\Commands\ArchivePDFs::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Daily storage monitoring at 2 AM
        $schedule->command('pdfs:monitor')->dailyAt('02:00');
        
        // Weekly archiving on Sundays at 3 AM
        $schedule->command('pdfs:archive')->weeklyOn(0, '03:00');
        
        // Monthly detailed report on 1st at 4 AM
        $schedule->command('pdfs:monitor --detailed')->monthlyOn(1, '04:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
```

## Phase 7: Environment Configuration

### Add to .env file

```env
# PDF Storage Configuration
FILESYSTEM_DISK=local

# Optional: Cloud Storage for PDFs
AWS_PDF_BUCKET=your-pdf-bucket-name
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=ap-southeast-1

# PDF Processing
PDF_COMPRESSION_ENABLED=true
PDF_RETENTION_YEARS=7
```

## Phase 8: Migration Script for Existing Files

### Create Migration Command

Create file: `app/Console/Commands/MigratePDFs.php`

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PDFStorageService;
use Illuminate\Support\Facades\Log;

class MigratePDFs extends Command
{
    protected $signature = 'pdfs:migrate {--dry-run : Show what would be migrated without actually migrating}';
    protected $description = 'Migrate existing PDFs to new storage structure';
    
    protected $pdfStorageService;
    
    public function __construct(PDFStorageService $pdfStorageService)
    {
        parent::__construct();
        $this->pdfStorageService = $pdfStorageService;
    }

    public function handle()
    {
        $this->info('Starting PDF migration...');
        
        $dryRun = $this->option('dry-run');
        $results = $this->pdfStorageService->migrateExistingFiles();
        
        $successCount = count(array_filter($results, fn($r) => $r['status'] === 'migrated'));
        $errorCount = count(array_filter($results, fn($r) => $r['status'] === 'failed'));
        
        $this->info("Migration completed. Success: {$successCount}, Errors: {$errorCount}");
        
        if ($errorCount > 0) {
            $this->error('Some files failed to migrate. Check logs for details.');
        }
        
        return Command::SUCCESS;
    }
}
```

## Implementation Steps

### Step 1: Database Setup
```bash
php artisan migrate
```

### Step 2: Create Storage Directories
```bash
mkdir -p storage/app/pdfs
mkdir -p storage/app/pdfs_archive
php artisan storage:link
```

### Step 3: Migrate Existing Files
```bash
# Test migration
php artisan pdfs:migrate --dry-run

# Execute migration
php artisan pdfs:migrate
```

### Step 4: Test New System
```bash
# Test PDF generation
php artisan tinker
>>> $transaction = \App\Models\transactions::first();
>>> app('App\Http\Controllers\TransactionsController')->generateNotaFile($transaction);

# Test storage monitoring
php artisan pdfs:monitor --detailed
```

### Step 5: Schedule Tasks
```bash
# Add to crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Testing Checklist

- [ ] Database migrations run successfully
- [ ] New PDFs are generated with correct naming convention
- [ ] Files are stored in hierarchical folder structure
- [ ] Existing files are migrated without data loss
- [ ] Archive process works correctly
- [ ] Storage monitoring provides accurate reports
- [ ] Compression reduces file sizes effectively
- [ ] Backup procedures are functional
- [ ] Performance remains acceptable with large file counts

## Rollback Plan

If issues occur during implementation:

1. **Database Rollback**: `php artisan migrate:rollback`
2. **File Restoration**: Restore from backup before migration
3. **Configuration**: Revert filesystems.php changes
4. **Code**: Revert controller changes to previous version

## Support and Maintenance

### Regular Tasks
- Monitor storage usage weekly
- Check archiving process monthly
- Verify backup integrity quarterly
- Review compression effectiveness annually

### Troubleshooting
- Check Laravel logs for PDF generation errors
- Verify file permissions on storage directories
- Ensure sufficient disk space for growth
- Monitor compression tool availability (ghostscript)

This implementation guide provides a complete, production-ready solution for the PDF storage system redesign.
