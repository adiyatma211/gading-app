<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PDFCompressionService;
use App\Services\PDFStorageService;
use App\Models\PdfStorageMetadata;

class PdfCompressCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:compress
                            {--id= : Compress specific PDF by metadata ID}
                            {--batch : Run batch compression for old PDFs}
                            {--all : Compress all uncompressed PDFs}
                            {--limit=50 : Limit number of PDFs to process in batch mode}
                            {--older-than=7 : Process PDFs older than X days (batch mode)}
                            {--quality=ebook : Compression quality (screen, ebook, printer, prepress, default)}
                            {--resolution=150 : Resolution in DPI}
                            {--force : Force re-compression even if already compressed}
                            {--dry-run : Show what would be compressed without actually doing it}';

    /**
     * Properties for tracking compression results
     */
    private $errors = [];
    private $successful = 0;
    private $totalSizeSaved = 0;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress PDF files using Ghostscript';

    /**
     * Execute the console command.
     */
    public function handle(PDFCompressionService $compressionService, PDFStorageService $storageService): int
    {
        $this->info('🗜️  PDF Compression Tool');
        $this->line(str_repeat('=', 50));

        // Check Ghostscript availability
        $gsCheck = $compressionService->checkGhostscriptAvailability();
        if (!$gsCheck['available']) {
            $this->error('❌ Ghostscript is not available: ' . $gsCheck['error']);
            $this->line('Please install Ghostscript and ensure it\'s in your PATH.');
            $this->line('Download from: https://www.ghostscript.com/');
            return 1;
        }

        $this->info('✅ Ghostscript is available: ' . $gsCheck['version']);

        // Determine compression mode
        if ($id = $this->option('id')) {
            return $this->compressSinglePDF($id, $compressionService);
        } elseif ($this->option('all')) {
            return $this->compressAllPDFs($compressionService);
        } elseif ($this->option('batch')) {
            return $this->batchCompressPDFs($compressionService);
        } else {
            $this->error('❌ Please specify a compression mode: --id, --batch, or --all');
            $this->line('Use --help for more information.');
            return 1;
        }
    }

    /**
     * Compress a single PDF by ID
     */
    protected function compressSinglePDF(int $id, PDFCompressionService $compressionService): int
    {
        $this->info("📄 Compressing PDF ID: {$id}");

        try {
            $pdfMetadata = PdfStorageMetadata::findOrFail($id);

            if (!$this->option('force') && $pdfMetadata->is_compressed) {
                $this->warn("⚠️  PDF is already compressed. Use --force to re-compress.");
                return 0;
            }

            if ($this->option('dry-run')) {
                $this->line("🔍 DRY RUN: Would compress PDF '{$pdfMetadata->file_name}' (Size: {$pdfMetadata->formatted_file_size})");
                return 0;
            }

            $result = null;
            $this->withProgressBar(1, function () use ($compressionService, $id, &$result) {
                $result = $compressionService->compressAndUpdatePDF($id, $this->getCompressionOptions());
            });

            if ($result['success']) {
                $this->newLine();
                $this->info("✅ PDF compressed successfully!");
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Original Size', $this->formatBytes($result['original_size'])],
                        ['Compressed Size', $this->formatBytes($result['compressed_size'])],
                        ['Compression Ratio', $result['compression_ratio'] . '%'],
                        ['Space Saved', $this->formatBytes($result['size_saved'])],
                    ]
                );
                return 0;
            } else {
                $this->newLine();
                $this->error("❌ Compression failed: " . $result['error']);
                return 1;
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->error("❌ PDF with ID {$id} not found.");
            return 1;
        } catch (\Exception $e) {
            $this->error("❌ Error compressing PDF: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Compress all uncompressed PDFs
     */
    protected function compressAllPDFs(PDFCompressionService $compressionService): int
    {
        $query = PdfStorageMetadata::where('is_compressed', false);

        if (!$this->option('force')) {
            $query->whereNull('archived_at'); // Skip archived files
        }

        $totalPDFs = $query->count();

        if ($totalPDFs === 0) {
            $this->info("✅ No uncompressed PDFs found.");
            return 0;
        }

        $this->info("📊 Found {$totalPDFs} uncompressed PDFs to process.");

        if ($this->option('dry-run')) {
            $pdfs = $query->limit(10)->get(['id', 'file_name', 'file_size_bytes']);
            $this->line("🔍 DRY RUN: Would compress up to {$totalPDFs} PDFs. First 10:");
            foreach ($pdfs as $pdf) {
                $this->line("  - {$pdf->file_name} ({$pdf->formatted_file_size})");
            }
            return 0;
        }

        $errors = [];
        $successful = 0;
        $totalSizeSaved = 0;

        $this->withProgressBar($totalPDFs, function () use ($compressionService, $query, &$errors, &$successful, &$totalSizeSaved) {
            $pdf = $query->first();
            if ($pdf) {
                $result = $compressionService->compressAndUpdatePDF($pdf->id, $this->getCompressionOptions());
                if (!$result['success']) {
                    $errors[] = [
                        'id' => $pdf->id,
                        'file_name' => $pdf->file_name,
                        'error' => $result['error']
                    ];
                } else {
                    $successful++;
                    $totalSizeSaved += $result['size_saved'];
                }
            }
        });

        $this->errors = $errors;
        $this->successful = $successful;
        $this->totalSizeSaved = $totalSizeSaved;

        $this->newLine();
        $this->displayCompressionResults();

        return empty($this->errors) ? 0 : 1;
    }

    /**
     * Batch compress old PDFs
     */
    protected function batchCompressPDFs(PDFCompressionService $compressionService): int
    {
        $limit = (int) $this->option('limit');
        $olderThan = (int) $this->option('older-than');

        $this->info("🔄 Batch compressing PDFs older than {$olderThan} days (limit: {$limit})");

        if ($this->option('dry-run')) {
            $query = PdfStorageMetadata::where('is_compressed', false)
                ->olderThan($olderThan)
                ->limit($limit);

            $count = $query->count();
            $this->line("🔍 DRY RUN: Would compress {$count} PDFs older than {$olderThan} days.");
            return 0;
        }

        $result = $compressionService->batchCompressPDFs($limit, $olderThan);

        if (!$result['success']) {
            $this->error("❌ Batch compression failed: " . $result['error']);
            return 1;
        }

        $this->info("✅ Batch compression completed!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Processed', number_format($result['processed'])],
                ['Successful', number_format($result['successful'])],
                ['Failed', number_format($result['failed'])],
                ['Total Size Saved', $this->formatBytes($result['total_size_saved'])],
                ['Success Rate', $result['processed'] > 0 ? round(($result['successful'] / $result['processed']) * 100, 2) . '%' : '0%'],
            ]
        );

        if (!empty($result['errors'])) {
            $this->newLine();
            $this->warn("⚠️  Errors encountered:");
            foreach ($result['errors'] as $error) {
                $this->line("  - ID {$error['pdf_id']}: {$error['error']}");
            }
        }

        return $result['failed'] > 0 ? 1 : 0;
    }

    /**
     * Get compression options from command line arguments
     */
    protected function getCompressionOptions(): array
    {
        return [
            'quality' => $this->option('quality'),
            'resolution' => (int) $this->option('resolution'),
            'downsample_images' => true,
        ];
    }

    /**
     * Display compression results
     */
    protected function displayCompressionResults(): void
    {
        $successful = $this->successful ?? 0;
        $totalSizeSaved = $this->totalSizeSaved ?? 0;
        $errors = $this->errors ?? [];

        $this->info("✅ Compression completed!");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Successful', number_format($successful)],
                ['Failed', number_format(count($errors))],
                ['Total Size Saved', $this->formatBytes($totalSizeSaved)],
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->warn("⚠️  Errors encountered:");
            foreach ($errors as $error) {
                $this->line("  - ID {$error['id']} ({$error['file_name']}): {$error['error']}");
            }
        }
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
}
