<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PDFStorageService;
use App\Services\PDFCompressionService;
use App\Models\PdfStorageMetadata;

class PdfStorageStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:stats
                            {--detailed : Show detailed statistics}
                            {--type= : Filter by file type (nota, nota_dua, etc.)}
                            {--older-than= : Show stats for files older than X days}
                            {--recent : Show only recent files (last 7 days)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display PDF storage statistics and information';

    /**
     * Execute the console command.
     */
    public function handle(PDFStorageService $storageService, PDFCompressionService $compressionService): int
    {
        $this->info('📊 PDF Storage Statistics');
        $this->line(str_repeat('=', 50));

        // Get basic storage stats
        $stats = $storageService->getStorageStats();

        if (isset($stats['error'])) {
            $this->error('❌ Error retrieving storage stats: ' . $stats['error']);
            return 1;
        }

        // Display summary
        $this->info('📁 Storage Summary:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Files', number_format($stats['total_files'])],
                ['Archived Files', number_format($stats['archived_files'])],
                ['Compressed Files', number_format($stats['compressed_files'])],
                ['Total Size', $stats['total_size_formatted']],
            ]
        );

        // Display compression stats
        $compressionStats = $compressionService->getCompressionStats();
        if (!isset($compressionStats['error'])) {
            $this->info("\n🗜️  Compression Statistics:");
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Compression Rate', $compressionStats['compression_rate'] . '%'],
                    ['Total Size Saved', $this->formatBytes($compressionStats['total_size_saved'])],
                    ['Average Compression Ratio', $compressionStats['average_compression_ratio'] . '%'],
                    ['Compressed PDFs', number_format($compressionStats['compressed_pdfs'])],
                    ['Uncompressed PDFs', number_format($compressionStats['uncompressed_pdfs'])],
                ]
            );
        }

        // Detailed stats if requested
        if ($this->option('detailed')) {
            $this->displayDetailedStats($stats);
        }

        // Filter by type if specified
        if ($type = $this->option('type')) {
            $this->displayTypeStats($type);
        }

        // Filter by age if specified
        if ($olderThan = $this->option('older-than')) {
            $this->displayAgeStats($olderThan, 'older');
        }

        // Show recent files if requested
        if ($this->option('recent')) {
            $this->displayRecentFiles();
        }

        // Check disk usage
        $this->checkDiskUsage();

        $this->info("\n✅ Statistics retrieved successfully!");
        return 0;
    }

    /**
     * Display detailed statistics
     */
    protected function displayDetailedStats(array $stats): void
    {
        $this->info("\n📈 Detailed Statistics by Type:");

        if (!empty($stats['by_type'])) {
            $typeData = [];
            foreach ($stats['by_type'] as $type => $data) {
                $typeData[] = [
                    'Type' => strtoupper($type),
                    'Count' => number_format($data['count']),
                    'Size' => $this->formatBytes($data['total_size']),
                    'Avg Size' => $this->formatBytes($data['total_size'] / $data['count']),
                ];
            }
            $this->table(['Type', 'Count', 'Total Size', 'Average Size'], $typeData);
        }

        // Display monthly trends
        $this->displayMonthlyTrends();
    }

    /**
     * Display statistics for specific file type
     */
    protected function displayTypeStats(string $type): void
    {
        $this->info("\n📂 Statistics for Type: " . strtoupper($type));

        $query = PdfStorageMetadata::byType($type);

        $total = $query->count();
        $totalSize = $query->sum('file_size_bytes');
        $compressed = $query->compressed()->count();
        $archived = $query->archived()->count();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Files', number_format($total)],
                ['Total Size', $this->formatBytes($totalSize)],
                ['Compressed Files', number_format($compressed)],
                ['Archived Files', number_format($archived)],
                ['Compression Rate', $total > 0 ? round(($compressed / $total) * 100, 2) . '%' : '0%'],
            ]
        );
    }

    /**
     * Display statistics for files by age
     */
    protected function displayAgeStats(int $days, string $type): void
    {
        $this->info("\n📅 Statistics for Files " . ucfirst($type) . " than {$days} days:");

        $query = PdfStorageMetadata::{$type . 'Than'}($days);

        $total = $query->count();
        $totalSize = $query->sum('file_size_bytes');
        $compressed = $query->compressed()->count();
        $archived = $query->archived()->count();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Files', number_format($total)],
                ['Total Size', $this->formatBytes($totalSize)],
                ['Compressed Files', number_format($compressed)],
                ['Archived Files', number_format($archived)],
                ['Compression Rate', $total > 0 ? round(($compressed / $total) * 100, 2) . '%' : '0%'],
            ]
        );
    }

    /**
     * Display recent files
     */
    protected function displayRecentFiles(): void
    {
        $this->info("\n🕐 Recent Files (Last 7 Days):");

        $recentFiles = PdfStorageMetadata::withinDays(7)
            ->with('pdfable')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($recentFiles->isEmpty()) {
            $this->line("No recent files found.");
            return;
        }

        $fileData = [];
        foreach ($recentFiles as $file) {
            $fileData[] = [
                'File Name' => $file->file_name,
                'Type' => strtoupper($file->file_type),
                'Size' => $this->formatBytes($file->file_size_bytes),
                'Compressed' => $file->is_compressed ? 'Yes' : 'No',
                'Created' => $file->created_at->format('Y-m-d H:i'),
                'Transaction ID' => $file->pdfable_id ?? 'N/A',
            ];
        }

        $this->table(
            ['File Name', 'Type', 'Size', 'Compressed', 'Created', 'Transaction ID'],
            $fileData
        );
    }

    /**
     * Display monthly trends
     */
    protected function displayMonthlyTrends(): void
    {
        $this->info("\n📊 Monthly Trends:");

        $monthlyStats = PdfStorageMetadata::selectRaw(
            'DATE_FORMAT(created_at, "%Y-%m") as month,
             COUNT(*) as count,
             SUM(file_size_bytes) as total_size'
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();

        if ($monthlyStats->isEmpty()) {
            $this->line("No monthly data available.");
            return;
        }

        $trendData = [];
        foreach ($monthlyStats as $stat) {
            $trendData[] = [
                'Month' => $stat->month,
                'Files' => number_format($stat->count),
                'Total Size' => $this->formatBytes($stat->total_size),
                'Avg Size' => $this->formatBytes($stat->total_size / $stat->count),
            ];
        }

        $this->table(['Month', 'Files', 'Total Size', 'Average Size'], $trendData);
    }

    /**
     * Check disk usage
     */
    protected function checkDiskUsage(): void
    {
        $this->info("\n💾 Disk Usage:");

        $pdfStoragePath = storage_path('app/pdfs');
        if (is_dir($pdfStoragePath)) {
            $totalSpace = disk_total_space($pdfStoragePath);
            $freeSpace = disk_free_space($pdfStoragePath);
            $usedSpace = $totalSpace - $freeSpace;
            $usagePercentage = ($usedSpace / $totalSpace) * 100;

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Disk Space', $this->formatBytes($totalSpace)],
                    ['Used Space', $this->formatBytes($usedSpace)],
                    ['Free Space', $this->formatBytes($freeSpace)],
                    ['Usage Percentage', round($usagePercentage, 2) . '%'],
                    ['Status', $usagePercentage > 80 ? '⚠️  High' : '✅ Normal'],
                ]
            );

            // Alert if usage is high
            if ($usagePercentage > 80) {
                $this->warn("⚠️  Warning: Disk usage is above 80%. Consider archiving old files or cleaning up.");
            }
        } else {
            $this->line("PDF storage directory not found.");
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
