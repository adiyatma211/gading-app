<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\PDFStorageService;
use App\Models\PdfStorageMetadata;

class PdfArchiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:archive
                            {--years=2 : Archive PDFs older than X years}
                            {--batch : Run in batch mode with progress bar}
                            {--dry-run : Show what would be archived without actually doing it}
                            {--force : Force archive even if already archived}
                            {--delete : Delete original files after archiving}
                            {--limit=100 : Limit number of PDFs to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive old PDF files to long-term storage';

    /**
     * Execute the console command.
     */
    public function handle(PDFStorageService $storageService): int
    {
        $this->info('📦 PDF Archive Tool');
        $this->line(str_repeat('=', 50));

        $years = (int) $this->option('years');
        $limit = (int) $this->option('limit');
        $delete = $this->option('delete');

        $this->info("📅 Archiving PDFs older than {$years} years");
        $this->info("📊 Processing limit: {$limit} PDFs");

        // Check what files would be archived
        $cutoffDate = now()->subYears($years);
        $query = PdfStorageMetadata::where('created_at', '<', $cutoffDate)
            ->whereNull('archived_at');

        if (!$this->option('force')) {
            $query->whereNull('archived_at');
        }

        $totalToArchive = $query->count();

        if ($totalToArchive === 0) {
            $this->info("✅ No PDFs found older than {$years} years.");
            return 0;
        }

        $this->info("📋 Found {$totalToArchive} PDFs eligible for archiving.");

        if ($this->option('dry-run')) {
            return $this->performDryRun($query, $limit);
        }

        if (!$this->confirm("Are you sure you want to archive {$totalToArchive} PDFs?")) {
            $this->info("❌ Archive operation cancelled.");
            return 0;
        }

        return $this->performArchive($storageService, $query, $limit, $delete);
    }

    /**
     * Perform dry run to show what would be archived
     */
    protected function performDryRun($query, int $limit): int
    {
        $this->warn("🔍 DRY RUN MODE - No files will be moved");

        $pdfs = $query->limit($limit)->get([
            'id', 'file_name', 'file_path', 'file_size_bytes', 'created_at', 'file_type'
        ]);

        $totalSize = 0;
        $typeCounts = [];

        foreach ($pdfs as $pdf) {
            $totalSize += $pdf->file_size_bytes;
            $typeCounts[$pdf->file_type] = ($typeCounts[$pdf->file_type] ?? 0) + 1;
        }

        $this->table(
            ['File Name', 'Type', 'Size', 'Created'],
            $pdfs->map(function ($pdf) {
                return [
                    $pdf->file_name,
                    strtoupper($pdf->file_type),
                    $this->formatBytes($pdf->file_size_bytes),
                    $pdf->created_at->format('Y-m-d H:i'),
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info("📊 Summary:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Files to Archive', number_format($pdfs->count())],
                ['Total Size', $this->formatBytes($totalSize)],
                ['Total Size (MB)', round($totalSize / 1024 / 1024, 2) . ' MB'],
            ]
        );

        if (!empty($typeCounts)) {
            $this->newLine();
            $this->info("📂 By Type:");
            foreach ($typeCounts as $type => $count) {
                $this->line("  - " . strtoupper($type) . ": " . number_format($count) . " files");
            }
        }

        return 0;
    }

    /**
     * Perform the actual archiving
     */
    protected function performArchive(PDFStorageService $storageService, $query, int $limit, bool $delete): int
    {
        $totalToArchive = min($query->count(), $limit);
        $this->info("🔄 Starting archive process for {$totalToArchive} PDFs...");

        $errors = [];
        $archived = 0;
        $totalSizeArchived = 0;

        $this->withProgressBar($totalToArchive, function () use ($storageService, $query, &$errors, &$archived, &$totalSizeArchived, $delete) {
            $pdf = $query->first();
            if (!$pdf) return;

            try {
                if ($this->option('batch')) {
                    // Use batch archive method for better performance
                    $result = $storageService->archiveOldPDFs(1); // Archive one at a time for progress tracking
                    if ($result['success'] && $result['archived_count'] > 0) {
                        $archived++;
                        $totalSizeArchived += $pdf->file_size_bytes;
                    } elseif (!empty($result['errors'])) {
                        $errors = array_merge($errors, $result['errors']);
                    }
                } else {
                    // Individual file archive - use the archiveOldPDFs method with specific parameters
                    $result = $storageService->archiveOldPDFs(0); // Will be handled manually

                    // Since we need to archive a specific file, we'll handle it differently
                    try {
                        $archivePath = 'archived/' . $pdf->file_path;
                        $content = $storageService->getPDF($pdf->file_path);

                        if ($content) {
                            // Move to archive
                            $disk = config('filesystems.pdf_archive_disk', 'pdf_archive');
                            Storage::disk($disk)->put($archivePath, $content);

                            // Update metadata
                            $pdf->archived_at = now();
                            $pdf->archive_path = $archivePath;
                            $pdf->save();

                            // Delete from main storage if requested
                            if ($delete) {
                                $mainDisk = config('filesystems.pdf_storage_disk', 'pdf_storage');
                                Storage::disk($mainDisk)->delete($pdf->file_path);
                            }

                            $archived++;
                            $totalSizeArchived += $pdf->file_size_bytes;
                        } else {
                            $errors[] = [
                                'file_path' => $pdf->file_path,
                                'error' => 'Could not retrieve file content for archiving'
                            ];
                        }
                    } catch (\Exception $e) {
                        $errors[] = [
                            'file_path' => $pdf->file_path,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } catch (\Exception $e) {
                $errors[] = [
                    'file_path' => $pdf->file_path,
                    'error' => $e->getMessage()
                ];
            }
        });

        $this->newLine();
        $this->displayArchiveResults($archived, $totalSizeArchived, $errors);

        return empty($errors) ? 0 : 1;
    }

    /**
     * Display archive results
     */
    protected function displayArchiveResults(int $archived, int $totalSizeArchived, array $errors): void
    {
        $this->info("✅ Archive operation completed!");

        $this->table(
            ['Metric', 'Value'],
            [
                ['Files Archived', number_format($archived)],
                ['Total Size Archived', $this->formatBytes($totalSizeArchived)],
                ['Total Size (MB)', round($totalSizeArchived / 1024 / 1024, 2) . ' MB'],
                ['Errors', count($errors)],
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->warn("⚠️  Errors encountered:");
            foreach ($errors as $error) {
                $this->line("  - {$error['file_path']}: {$error['error']}");
            }
        }

        // Show updated storage stats
        $this->newLine();
        $this->info("📊 Updated Storage Statistics:");

        $activeCount = PdfStorageMetadata::whereNull('archived_at')->count();
        $archivedCount = PdfStorageMetadata::whereNotNull('archived_at')->count();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Active Files', number_format($activeCount)],
                ['Archived Files', number_format($archivedCount)],
                ['Archive Rate', $archivedCount > 0 ? round(($archivedCount / ($activeCount + $archivedCount)) * 100, 2) . '%' : '0%'],
            ]
        );
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
