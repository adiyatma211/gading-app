# PDF Storage Monitoring & Maintenance System

## Overview

This document outlines the comprehensive monitoring and maintenance system for the PDF storage solution, ensuring optimal performance, data integrity, and proactive issue resolution.

## Monitoring Architecture

### 1. System Health Monitoring

#### Storage Health Metrics

```php
// app/Services/PDFMonitoringService.php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PDFMonitoringService
{
    public function getSystemHealthReport(): array
    {
        return [
            'storage' => $this->getStorageMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'performance' => $this->getPerformanceMetrics(),
            'integrity' => $this->getIntegrityMetrics(),
            'alerts' => $this->getActiveAlerts()
        ];
    }
    
    private function getStorageMetrics(): array
    {
        $activePath = storage_path('app/pdfs');
        $archivePath = storage_path('app/pdfs_archive');
        
        $metrics = [
            'active_storage' => [
                'total_size_gb' => round($this->calculateDirectorySize($activePath) / 1024 / 1024 / 1024, 2),
                'file_count' => $this->countFiles($activePath),
                'oldest_file' => $this->getOldestFileDate($activePath),
                'newest_file' => $this->getNewestFileDate($activePath),
            ],
            'archive_storage' => [
                'total_size_gb' => round($this->calculateDirectorySize($archivePath) / 1024 / 1024 / 1024, 2),
                'file_count' => $this->countFiles($archivePath),
                'oldest_file' => $this->getOldestFileDate($archivePath),
                'newest_file' => $this->getNewestFileDate($archivePath),
            ],
            'disk_usage' => [
                'total_gb' => round(disk_total_space('/') / 1024 / 1024 / 1024, 2),
                'free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2),
                'usage_percentage' => round((1 - disk_free_space('/') / disk_total_space('/')) * 100, 2)
            ]
        ];
        
        // Add growth projections
        $metrics['growth_projection'] = $this->calculateGrowthProjection();
        
        return $metrics;
    }
    
    private function getDatabaseMetrics(): array
    {
        return [
            'transactions' => [
                'total_count' => DB::table('transactions')->count(),
                'with_pdfs' => DB::table('transactions')
                    ->whereNotNull('thermal_pdf_path')
                    ->orWhereNotNull('invoice_pdf_path')
                    ->count(),
                'thermal_pdfs' => DB::table('transactions')
                    ->whereNotNull('thermal_pdf_path')->count(),
                'invoice_pdfs' => DB::table('transactions')
                    ->whereNotNull('invoice_pdf_path')->count(),
            ],
            'pdf_files' => [
                'total_records' => DB::table('pdf_files')->count(),
                'thermal_count' => DB::table('pdf_files')
                    ->where('file_type', 'thermal')->count(),
                'invoice_count' => DB::table('pdf_files')
                    ->where('file_type', 'invoice')->count(),
                'compressed_count' => DB::table('pdf_files')
                    ->where('is_compressed', 1)->count(),
                'archived_count' => DB::table('pdf_files')
                    ->whereNotNull('archived_at')->count(),
            ],
            'performance' => [
                'avg_file_size_kb' => round(DB::table('pdf_files')
                    ->avg('file_size') / 1024, 2),
                'total_storage_used_gb' => round(DB::table('pdf_files')
                    ->sum('file_size') / 1024 / 1024 / 1024, 2),
            ]
        ];
    }
    
    private function getPerformanceMetrics(): array
    {
        return [
            'generation_speed' => $this->getPDFGenerationMetrics(),
            'storage_speed' => $this->getStorageSpeedMetrics(),
            'query_performance' => $this->getQueryPerformanceMetrics(),
        ];
    }
    
    private function getIntegrityMetrics(): array
    {
        return [
            'orphaned_files' => $this->findOrphanedFiles(),
            'missing_files' => $this->findMissingFiles(),
            'corrupted_files' => $this->findCorruptedFiles(),
            'duplicate_files' => $this->findDuplicateFiles(),
        ];
    }
    
    private function getActiveAlerts(): array
    {
        $alerts = [];
        
        // Storage alerts
        $diskUsage = $this->getStorageMetrics()['disk_usage']['usage_percentage'];
        if ($diskUsage > 90) {
            $alerts[] = [
                'type' => 'critical',
                'message' => "Disk usage is critically high: {$diskUsage}%",
                'action' => 'immediate_cleanup_required'
            ];
        } elseif ($diskUsage > 80) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Disk usage is high: {$diskUsage}%",
                'action' => 'schedule_cleanup'
            ];
        }
        
        // File integrity alerts
        $missingFiles = count($this->findMissingFiles());
        if ($missingFiles > 0) {
            $alerts[] = [
                'type' => 'error',
                'message' => "{$missingFiles} files are missing from storage",
                'action' => 'investigate_missing_files'
            ];
        }
        
        return $alerts;
    }
    
    private function calculateGrowthProjection(): array
    {
        // Calculate daily growth over last 30 days
        $dailyGrowth = DB::table('transactions')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $avgDailyGrowth = $dailyGrowth->avg('count') ?? 0;
        $avgFileSizeKB = DB::table('pdf_files')->avg('file_size') / 1024 ?? 100;
        
        return [
            'avg_daily_transactions' => round($avgDailyGrowth, 2),
            'avg_daily_growth_mb' => round($avgDailyGrowth * 2 * $avgFileSizeKB / 1024, 2), // 2 PDFs per transaction
            'monthly_projection_gb' => round($avgDailyGrowth * 30 * 2 * $avgFileSizeKB / 1024 / 1024, 2),
            'yearly_projection_gb' => round($avgDailyGrowth * 365 * 2 * $avgFileSizeKB / 1024 / 1024, 2),
        ];
    }
    
    // Helper methods for file operations
    private function calculateDirectorySize($path): int
    {
        $totalSize = 0;
        if (is_dir($path)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                }
            }
        }
        return $totalSize;
    }
    
    private function countFiles($path): int
    {
        $count = 0;
        if (is_dir($path)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $count++;
                }
            }
        }
        return $count;
    }
    
    private function getOldestFileDate($path): ?string
    {
        $oldestTime = null;
        if (is_dir($path)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $fileTime = $file->getMTime();
                    if ($oldestTime === null || $fileTime < $oldestTime) {
                        $oldestTime = $fileTime;
                    }
                }
            }
        }
        return $oldestTime ? date('Y-m-d H:i:s', $oldestTime) : null;
    }
    
    private function getNewestFileDate($path): ?string
    {
        $newestTime = null;
        if (is_dir($path)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $fileTime = $file->getMTime();
                    if ($newestTime === null || $fileTime > $newestTime) {
                        $newestTime = $fileTime;
                    }
                }
            }
        }
        return $newestTime ? date('Y-m-d H:i:s', $newestTime) : null;
    }
    
    private function findOrphanedFiles(): array
    {
        $storedFiles = Storage::disk('pdfs')->allFiles();
        $dbFiles = DB::table('transactions')
            ->whereNotNull('thermal_pdf_path')
            ->orWhereNotNull('invoice_pdf_path')
            ->get(['thermal_pdf_path', 'invoice_pdf_path']);
            
        $dbFilePaths = $dbFiles->flatMap(function ($transaction) {
            $paths = [];
            if ($transaction->thermal_pdf_path) $paths[] = $transaction->thermal_pdf_path;
            if ($transaction->invoice_pdf_path) $paths[] = $transaction->invoice_pdf_path;
            return $paths;
        })->toArray();
        
        return array_diff($storedFiles, $dbFilePaths);
    }
    
    private function findMissingFiles(): array
    {
        $dbFiles = DB::table('transactions')
            ->whereNotNull('thermal_pdf_path')
            ->orWhereNotNull('invoice_pdf_path')
            ->get(['thermal_pdf_path', 'invoice_pdf_path']);
            
        $missing = [];
        foreach ($dbFiles as $transaction) {
            if ($transaction->thermal_pdf_path && !Storage::disk('pdfs')->exists($transaction->thermal_pdf_path)) {
                $missing[] = $transaction->thermal_pdf_path;
            }
            if ($transaction->invoice_pdf_path && !Storage::disk('pdfs')->exists($transaction->invoice_pdf_path)) {
                $missing[] = $transaction->invoice_pdf_path;
            }
        }
        
        return $missing;
    }
    
    private function findCorruptedFiles(): array
    {
        $corrupted = [];
        $dbFiles = DB::table('pdf_files')->get();
        
        foreach ($dbFiles as $file) {
            $filePath = $file->path;
            if (Storage::disk('pdfs')->exists($filePath)) {
                $content = Storage::disk('pdfs')->get($filePath);
                $currentHash = md5($content);
                
                if ($currentHash !== $file->file_hash) {
                    $corrupted[] = [
                        'path' => $filePath,
                        'expected_hash' => $file->file_hash,
                        'actual_hash' => $currentHash
                    ];
                }
            }
        }
        
        return $corrupted;
    }
    
    private function findDuplicateFiles(): array
    {
        $duplicates = [];
        $dbFiles = DB::table('pdf_files')->get()->groupBy('file_hash');
        
        foreach ($dbFiles as $hash => $files) {
            if ($files->count() > 1) {
                $duplicates[] = [
                    'hash' => $hash,
                    'files' => $files->pluck('path')->toArray()
                ];
            }
        }
        
        return $duplicates;
    }
    
    private function getPDFGenerationMetrics(): array
    {
        // Measure average PDF generation time over last 24 hours
        $recentFiles = DB::table('pdf_files')
            ->where('generated_at', '>=', now()->subDay())
            ->get();
            
        if ($recentFiles->isEmpty()) {
            return ['avg_generation_time_ms' => 0];
        }
        
        // This would require additional timing data to be captured during generation
        // For now, return basic metrics
        return [
            'files_generated_24h' => $recentFiles->count(),
            'avg_generation_time_ms' => 150, // Placeholder - would need actual timing data
        ];
    }
    
    private function getStorageSpeedMetrics(): array
    {
        return [
            'avg_write_speed_mbps' => $this->measureWriteSpeed(),
            'avg_read_speed_mbps' => $this->measureReadSpeed(),
        ];
    }
    
    private function getQueryPerformanceMetrics(): array
    {
        return [
            'avg_query_time_ms' => DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value,
            'slow_queries_count' => DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value,
        ];
    }
    
    private function measureWriteSpeed(): float
    {
        $testData = str_repeat('x', 1024 * 1024); // 1MB test data
        $startTime = microtime(true);
        
        $testFile = 'speed_test_' . time() . '.tmp';
        Storage::disk('pdfs')->put($testFile, $testData);
        Storage::disk('pdfs')->delete($testFile);
        
        $endTime = microtime(true);
        $timeTaken = $endTime - $startTime;
        
        return round(1 / $timeTaken, 2); // MB/s
    }
    
    private function measureReadSpeed(): float
    {
        // Create test file first
        $testData = str_repeat('x', 1024 * 1024); // 1MB test data
        $testFile = 'speed_test_' . time() . '.tmp';
        Storage::disk('pdfs')->put($testFile, $testData);
        
        $startTime = microtime(true);
        Storage::disk('pdfs')->get($testFile);
        $endTime = microtime(true);
        
        Storage::disk('pdfs')->delete($testFile);
        
        $timeTaken = $endTime - $startTime;
        return round(1 / $timeTaken, 2); // MB/s
    }
}
```

### 2. Alert System

#### Alert Configuration

```php
// config/pdf_alerts.php
return [
    'thresholds' => [
        'disk_usage_warning' => 80, // percentage
        'disk_usage_critical' => 90, // percentage
        'missing_files_threshold' => 10, // count
        'generation_time_warning' => 500, // milliseconds
        'generation_time_critical' => 1000, // milliseconds
    ],
    
    'notifications' => [
        'email' => [
            'enabled' => env('PDF_ALERTS_EMAIL_ENABLED', true),
            'to' => explode(',', env('PDF_ALERTS_EMAIL_TO', 'admin@example.com')),
            'from' => env('PDF_ALERTS_EMAIL_FROM', 'alerts@example.com'),
        ],
        'slack' => [
            'enabled' => env('PDF_ALERTS_SLACK_ENABLED', false),
            'webhook_url' => env('PDF_ALERTS_SLACK_WEBHOOK'),
            'channel' => env('PDF_ALERTS_SLACK_CHANNEL', '#alerts'),
        ],
        'database' => [
            'enabled' => true,
            'retention_days' => 30,
        ],
    ],
    
    'cooldown' => [
        'disk_usage' => 3600, // seconds
        'missing_files' => 1800, // seconds
        'performance' => 900, // seconds
    ],
];
```

#### Alert Service

```php
// app/Services/PDFAlertService.php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PDFSystemAlert;
use Illuminate\Support\Facades\Http;

class PDFAlertService
{
    private $config;
    private $lastAlertTimes = [];
    
    public function __construct()
    {
        $this->config = config('pdf_alerts');
    }
    
    public function checkAndSendAlerts(array $healthReport): void
    {
        $this->checkDiskUsage($healthReport['storage']['disk_usage']);
        $this->checkMissingFiles($healthReport['integrity']['missing_files']);
        $this->checkPerformance($healthReport['performance']);
        $this->checkCorruptedFiles($healthReport['integrity']['corrupted_files']);
    }
    
    private function checkDiskUsage(array $diskUsage): void
    {
        $usagePercentage = $diskUsage['usage_percentage'];
        
        if ($usagePercentage >= $this->config['thresholds']['disk_usage_critical']) {
            $this->sendAlert('critical', 'Disk Usage Critical', 
                "Disk usage is at {$usagePercentage}%. Immediate action required.",
                ['disk_usage' => $diskUsage]);
        } elseif ($usagePercentage >= $this->config['thresholds']['disk_usage_warning']) {
            $this->sendAlert('warning', 'Disk Usage High',
                "Disk usage is at {$usagePercentage}%. Consider cleanup soon.",
                ['disk_usage' => $diskUsage]);
        }
    }
    
    private function checkMissingFiles(array $missingFiles): void
    {
        $count = count($missingFiles);
        
        if ($count >= $this->config['thresholds']['missing_files_threshold']) {
            $this->sendAlert('error', 'Missing Files Detected',
                "{$count} files are missing from storage.",
                ['missing_files' => $missingFiles]);
        }
    }
    
    private function checkPerformance(array $performance): void
    {
        $genTime = $performance['generation_speed']['avg_generation_time_ms'] ?? 0;
        
        if ($genTime >= $this->config['thresholds']['generation_time_critical']) {
            $this->sendAlert('critical', 'Performance Degradation',
                "PDF generation time is {$genTime}ms, which exceeds critical threshold.",
                ['performance' => $performance]);
        } elseif ($genTime >= $this->config['thresholds']['generation_time_warning']) {
            $this->sendAlert('warning', 'Performance Warning',
                "PDF generation time is {$genTime}ms, which exceeds warning threshold.",
                ['performance' => $performance]);
        }
    }
    
    private function checkCorruptedFiles(array $corruptedFiles): void
    {
        $count = count($corruptedFiles);
        
        if ($count > 0) {
            $this->sendAlert('error', 'Corrupted Files Detected',
                "{$count} files have integrity issues.",
                ['corrupted_files' => $corruptedFiles]);
        }
    }
    
    private function sendAlert(string $level, string $title, string $message, array $context = []): void
    {
        $alertKey = md5($title . $message);
        
        // Check cooldown
        if ($this->isInCooldown($alertKey)) {
            return;
        }
        
        $this->lastAlertTimes[$alertKey] = now();
        
        // Store in database
        $this->storeAlert($level, $title, $message, $context);
        
        // Send email
        if ($this->config['notifications']['email']['enabled']) {
            $this->sendEmailAlert($level, $title, $message, $context);
        }
        
        // Send Slack notification
        if ($this->config['notifications']['slack']['enabled']) {
            $this->sendSlackAlert($level, $title, $message, $context);
        }
        
        // Log alert
        Log::channel('pdf_alerts')->{$level}($title, [
            'message' => $message,
            'context' => $context
        ]);
    }
    
    private function isInCooldown(string $alertKey): bool
    {
        if (!isset($this->lastAlertTimes[$alertKey])) {
            return false;
        }
        
        $lastSent = $this->lastAlertTimes[$alertKey];
        $cooldownPeriod = $this->config['cooldown']['disk_usage']; // Default cooldown
        
        return $lastSent->diffInSeconds(now()) < $cooldownPeriod;
    }
    
    private function storeAlert(string $level, string $title, string $message, array $context): void
    {
        DB::table('pdf_alerts')->insert([
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'context' => json_encode($context),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    private function sendEmailAlert(string $level, string $title, string $message, array $context): void
    {
        try {
            $recipients = $this->config['notifications']['email']['to'];
            
            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new PDFSystemAlert($level, $title, $message, $context));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email alert', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
        }
    }
    
    private function sendSlackAlert(string $level, string $title, string $message, array $context): void
    {
        try {
            $webhookUrl = $this->config['notifications']['slack']['webhook_url'];
            $channel = $this->config['notifications']['slack']['channel'];
            
            $color = match($level) {
                'critical' => 'danger',
                'error' => 'danger',
                'warning' => 'warning',
                default => 'good'
            };
            
            $payload = [
                'channel' => $channel,
                'attachments' => [
                    [
                        'color' => $color,
                        'title' => $title,
                        'text' => $message,
                        'fields' => [
                            [
                                'title' => 'Level',
                                'value' => strtoupper($level),
                                'short' => true
                            ],
                            [
                                'title' => 'Time',
                                'value' => now()->format('Y-m-d H:i:s'),
                                'short' => true
                            ]
                        ],
                        'footer' => 'PDF Storage System',
                        'ts' => now()->timestamp
                    ]
                ]
            ];
            
            Http::post($webhookUrl, $payload);
        } catch (\Exception $e) {
            Log::error('Failed to send Slack alert', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
        }
    }
}
```

### 3. Automated Maintenance Tasks

#### Maintenance Service

```php
// app/Services/PDFMaintenanceService.php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PDFMaintenanceService
{
    private $pdfStorageService;
    private $pdfCompressionService;
    
    public function __construct(PDFStorageService $pdfStorageService, PDFCompressionService $pdfCompressionService)
    {
        $this->pdfStorageService = $pdfStorageService;
        $this->pdfCompressionService = $pdfCompressionService;
    }
    
    public function performDailyMaintenance(): array
    {
        $results = [];
        
        $results['cleanup'] = $this->performCleanup();
        $results['optimization'] = $this->performOptimization();
        $results['integrity_check'] = $this->performIntegrityCheck();
        $results['backup_verification'] = $this->verifyBackups();
        
        Log::info('Daily maintenance completed', ['results' => $results]);
        
        return $results;
    }
    
    public function performWeeklyMaintenance(): array
    {
        $results = [];
        
        $results['archiving'] = $this->performArchiving();
        $results['compression'] = $this->performCompression();
        $results['index_rebuild'] = $this->rebuildIndexes();
        $results['statistics_update'] = $this->updateStatistics();
        
        Log::info('Weekly maintenance completed', ['results' => $results]);
        
        return $results;
    }
    
    public function performMonthlyMaintenance(): array
    {
        $results = [];
        
        $results['deep_cleanup'] = $this->performDeepCleanup();
        $results['archive_verification'] = $this->verifyArchives();
        $results['performance_analysis'] = $this->analyzePerformance();
        $results['capacity_planning'] = $this->updateCapacityPlanning();
        
        Log::info('Monthly maintenance completed', ['results' => $results]);
        
        return $results;
    }
    
    private function performCleanup(): array
    {
        $cleanup = [];
        
        // Clean up temporary files
        $tempFiles = Storage::disk('local')->allFiles('temp');
        $deletedTemp = 0;
        
        foreach ($tempFiles as $file) {
            if (Storage::disk('local')->lastModified($file) < now()->subDay()->timestamp) {
                Storage::disk('local')->delete($file);
                $deletedTemp++;
            }
        }
        
        $cleanup['temp_files_deleted'] = $deletedTemp;
        
        // Clean up old logs
        $logRetention = config('pdf_alerts.notifications.database.retention_days', 30);
        $deletedLogs = DB::table('pdf_alerts')
            ->where('created_at', '<', now()->subDays($logRetention))
            ->delete();
            
        $cleanup['old_logs_deleted'] = $deletedLogs;
        
        return $cleanup;
    }
    
    private function performOptimization(): array
    {
        $optimization = [];
        
        // Optimize database tables
        $tables = ['transactions', 'pdf_files', 'pdf_alerts'];
        $optimizedTables = 0;
        
        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $optimizedTables++;
            } catch (\Exception $e) {
                Log::warning("Failed to optimize table {$table}", ['error' => $e->getMessage()]);
            }
        }
        
        $optimization['optimized_tables'] = $optimizedTables;
        
        // Clear application cache
        \Cache::flush();
        $optimization['cache_cleared'] = true;
        
        return $optimization;
    }
    
    private function performIntegrityCheck(): array
    {
        $integrity = [];
        
        // Check for orphaned files
        $orphanedFiles = $this->findOrphanedFiles();
        $integrity['orphaned_files_found'] = count($orphanedFiles);
        
        // Check for missing files
        $missingFiles = $this->findMissingFiles();
        $integrity['missing_files_found'] = count($missingFiles);
        
        // Check file hashes
        $corruptedFiles = $this->findCorruptedFiles();
        $integrity['corrupted_files_found'] = count($corruptedFiles);
        
        return $integrity;
    }
    
    private function verifyBackups(): array
    {
        $backup = [];
        
        // Check if backup directory exists and is accessible
        $backupPath = storage_path('app/backups');
        $backup['backup_directory_exists'] = is_dir($backupPath);
        $backup['backup_directory_writable'] = is_writable($backupPath);
        
        // Check latest backup
        if (is_dir($backupPath)) {
            $latestBackup = $this->getLatestBackup($backupPath);
            $backup['latest_backup_date'] = $latestBackup;
            $backup['backup_age_hours'] = $latestBackup ? now()->diffInHours($latestBackup) : null;
        }
        
        return $backup;
    }
    
    private function performArchiving(): array
    {
        $archiving = [];
        
        // Archive files older than 2 years
        $twoYearsAgo = now()->subYears(2);
        $oldTransactions = DB::table('transactions')
            ->where(function ($query) use ($twoYearsAgo) {
                $query->where('thermal_pdf_generated_at', '<', $twoYearsAgo)
                      ->orWhere('invoice_pdf_generated_at', '<', $twoYearsAgo);
            })
            ->where(function ($query) {
                $query->where('thermal_pdf_archived', 0)
                      ->orWhere('invoice_pdf_archived', 0);
            })
            ->get();
        
        $archivedFiles = 0;
        $errors = 0;
        
        foreach ($oldTransactions as $transaction) {
            try {
                $this->archiveTransactionFiles($transaction);
                $archivedFiles++;
            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to archive transaction files', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $archiving['transactions_processed'] = $oldTransactions->count();
        $archiving['files_archived'] = $archivedFiles;
        $archiving['errors'] = $errors;
        
        return $archiving;
    }
    
    private function performCompression(): array
    {
        $compression = [];
        
        if (!$this->pdfCompressionService->isGhostscriptAvailable()) {
            $compression['ghostscript_available'] = false;
            return $compression;
        }
        
        $compression['ghostscript_available'] = true;
        
        // Compress archived files that aren't already compressed
        $archivedFiles = DB::table('pdf_files')
            ->whereNotNull('archived_at')
            ->where('is_compressed', 0)
            ->limit(100) // Process in batches
            ->get();
        
        $compressedFiles = 0;
        $totalSpaceSaved = 0;
        $errors = 0;
        
        foreach ($archivedFiles as $file) {
            try {
                $result = $this->pdfCompressionService->compressPDF(
                    $file->path,
                    $file->path . '.compressed'
                );
                
                if ($result['success']) {
                    // Replace original with compressed version
                    Storage::disk('pdfs_archive')->delete($file->path);
                    Storage::disk('pdfs_archive')->move($file->path . '.compressed', $file->path);
                    
                    // Update database
                    DB::table('pdf_files')
                        ->where('id', $file->id)
                        ->update([
                            'is_compressed' => 1,
                            'compression_type' => 'ghostscript',
                            'original_size' => $result['original_size'],
                            'compressed_size' => $result['compressed_size']
                        ]);
                    
                    $compressedFiles++;
                    $totalSpaceSaved += $result['original_size'] - $result['compressed_size'];
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to compress PDF', [
                    'file_id' => $file->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $compression['files_processed'] = $archivedFiles->count();
        $compression['files_compressed'] = $compressedFiles;
        $compression['space_saved_mb'] = round($totalSpaceSaved / 1024 / 1024, 2);
        $compression['errors'] = $errors;
        
        return $compression;
    }
    
    private function rebuildIndexes(): array
    {
        $indexes = [];
        
        // Rebuild database indexes for better performance
        $indexCommands = [
            'ALTER TABLE transactions DROP INDEX idx_transactions_tanggal_transaksi',
            'ALTER TABLE transactions ADD INDEX idx_transactions_tanggal_transaksi (tanggal_transaksi)',
            'ALTER TABLE pdf_files DROP INDEX idx_pdf_files_generated_at',
            'ALTER TABLE pdf_files ADD INDEX idx_pdf_files_generated_at (generated_at)',
        ];
        
        $rebuiltIndexes = 0;
        
        foreach ($indexCommands as $command) {
            try {
                DB::statement($command);
                $rebuiltIndexes++;
            } catch (\Exception $e) {
                Log::warning("Failed to rebuild index", ['command' => $command, 'error' => $e->getMessage()]);
            }
        }
        
        $indexes['rebuilt_indexes'] = $rebuiltIndexes;
        
        return $indexes;
    }
    
    private function updateStatistics(): array
    {
        $stats = [];
        
        // Update file statistics
        $totalFiles = DB::table('pdf_files')->count();
        $totalSize = DB::table('pdf_files')->sum('file_size');
        $avgSize = $totalFiles > 0 ? $totalSize / $totalFiles : 0;
        
        // Store in cache for quick access
        \Cache::put('pdf_stats', [
            'total_files' => $totalFiles,
            'total_size_gb' => round($totalSize / 1024 / 1024 / 1024, 2),
            'avg_size_kb' => round($avgSize / 1024, 2),
            'updated_at' => now()
        ], 3600); // Cache for 1 hour
        
        $stats['updated_statistics'] = true;
        
        return $stats;
    }
    
    private function performDeepCleanup(): array
    {
        $cleanup = [];
        
        // Remove duplicate files
        $duplicates = $this->findDuplicateFiles();
        $duplicatesRemoved = 0;
        
        foreach ($duplicates as $duplicate) {
            // Keep the first file, remove the rest
            $filesToRemove = array_slice($duplicate['files'], 1);
            foreach ($filesToRemove as $file) {
                Storage::disk('pdfs')->delete($file);
                DB::table('pdf_files')->where('path', $file)->delete();
                $duplicatesRemoved++;
            }
        }
        
        $cleanup['duplicate_files_removed'] = $duplicatesRemoved;
        
        // Clear old cache entries
        $cacheCleared = \Cache::flush();
        $cleanup['cache_entries_cleared'] = $cacheCleared;
        
        return $cleanup;
    }
    
    private function verifyArchives(): array
    {
        $verification = [];
        
        // Check archive integrity
        $archivedFiles = DB::table('pdf_files')
            ->whereNotNull('archived_at')
            ->get();
        
        $verifiedFiles = 0;
        $corruptedArchives = 0;
        
        foreach ($archivedFiles as $file) {
            if (Storage::disk('pdfs_archive')->exists($file->path)) {
                $content = Storage::disk('pdfs_archive')->get($file->path);
                $currentHash = md5($content);
                
                if ($currentHash === $file->file_hash) {
                    $verifiedFiles++;
                } else {
                    $corruptedArchives++;
                }
            } else {
                $corruptedArchives++;
            }
        }
        
        $verification['total_archived_files'] = $archivedFiles->count();
        $verification['verified_files'] = $verifiedFiles;
        $verification['corrupted_archives'] = $corruptedArchives;
        
        return $verification;
    }
    
    private function analyzePerformance(): array
    {
        $analysis = [];
        
        // Analyze query performance
        $slowQueries = DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value;
        $analysis['slow_queries_count'] = $slowQueries;
        
        // Analyze storage performance
        $writeSpeed = $this->measureWriteSpeed();
        $readSpeed = $this->measureReadSpeed();
        
        $analysis['write_speed_mbps'] = $writeSpeed;
        $analysis['read_speed_mbps'] = $readSpeed;
        
        // Performance recommendations
        $recommendations = [];
        
        if ($writeSpeed < 10) {
            $recommendations[] = 'Consider upgrading storage for better write performance';
        }
        
        if ($readSpeed < 50) {
            $recommendations[] = 'Consider implementing caching for frequently accessed files';
        }
        
        if ($slowQueries > 100) {
            $recommendations[] = 'Review and optimize slow database queries';
        }
        
        $analysis['recommendations'] = $recommendations;
        
        return $analysis;
    }
    
    private function updateCapacityPlanning(): array
    {
        $planning = [];
        
        // Calculate current growth rate
        $monthlyGrowth = $this->calculateMonthlyGrowthRate();
        $planning['monthly_growth_gb'] = round($monthlyGrowth / 1024 / 1024 / 1024, 2);
        
        // Project storage needs for next 12 months
        $currentStorage = $this->calculateDirectorySize(storage_path('app/pdfs'));
        $projectedYearly = $currentStorage + ($monthlyGrowth * 12);
        
        $planning['current_storage_gb'] = round($currentStorage / 1024 / 1024 / 1024, 2);
        $planning['projected_yearly_gb'] = round($projectedYearly / 1024 / 1024 / 1024, 2);
        
        // Storage recommendations
        $recommendations = [];
        
        if ($projectedYearly > ($this->getAvailableDiskSpace() * 0.8)) {
            $recommendations[] = 'Consider expanding storage capacity within the next 6 months';
        }
        
        $planning['recommendations'] = $recommendations;
        
        return $planning;
    }
    
    // Helper methods
    private function archiveTransactionFiles($transaction): void
    {
        $transactionDate = Carbon::parse($transaction->tanggal_transaksi);
        
        if ($transaction->thermal_pdf_path && !$transaction->thermal_pdf_archived) {
            $this->pdfStorageService->archiveFile($transaction->thermal_pdf_path, $transactionDate);
            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update(['thermal_pdf_archived' => 1]);
        }
        
        if ($transaction->invoice_pdf_path && !$transaction->invoice_pdf_archived) {
            $this->pdfStorageService->archiveFile($transaction->invoice_pdf_path, $transactionDate);
            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update(['invoice_pdf_archived' => 1]);
        }
    }
    
    private function getLatestBackup(string $backupPath): ?Carbon
    {
        $files = glob($backupPath . '/*');
        $latestFile = null;
        $latestTime = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $fileTime = filemtime($file);
                if ($fileTime > $latestTime) {
                    $latestTime = $fileTime;
                    $latestFile = $file;
                }
            }
        }
        
        return $latestFile ? Carbon::createFromTimestamp($latestTime) : null;
    }
    
    private function calculateMonthlyGrowthRate(): int
    {
        // Calculate average monthly growth over last 3 months
        $threeMonthsAgo = now()->subMonths(3);
        
        $monthlyData = DB::table('pdf_files')
            ->selectRaw('YEAR(generated_at) as year, MONTH(generated_at) as month, SUM(file_size) as total_size')
            ->where('generated_at', '>=', $threeMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get();
        
        if ($monthlyData->count() < 2) {
            return 0;
        }
        
        $sizes = $monthlyData->pluck('total_size')->toArray();
        $growth = ($sizes[0] - $sizes[count($sizes) - 1]) / (count($sizes) - 1);
        
        return max(0, $growth);
    }
    
    private function getAvailableDiskSpace(): int
    {
        return disk_free_space('/');
    }
    
    private function measureWriteSpeed(): float
    {
        $testData = str_repeat('x', 1024 * 1024); // 1MB test data
        $startTime = microtime(true);
        
        $testFile = 'speed_test_' . time() . '.tmp';
        Storage::disk('pdfs')->put($testFile, $testData);
        Storage::disk('pdfs')->delete($testFile);
        
        $endTime = microtime(true);
        $timeTaken = $endTime - $startTime;
        
        return round(1 / $timeTaken, 2); // MB/s
    }
    
    private function measureReadSpeed(): float
    {
        $testData = str_repeat('x', 1024 * 1024); // 1MB test data
        $testFile = 'speed_test_' . time() . '.tmp';
        Storage::disk('pdfs')->put($testFile, $testData);
        
        $startTime = microtime(true);
        Storage::disk('pdfs')->get($testFile);
        $endTime = microtime(true);
        
        Storage::disk('pdfs')->delete($testFile);
        
        $timeTaken = $endTime - $startTime;
        return round(1 / $timeTaken, 2); // MB/s
    }
    
    // Reuse methods from monitoring service
    private function findOrphanedFiles(): array { /* implementation from PDFMonitoringService */ }
    private function findMissingFiles(): array { /* implementation from PDFMonitoringService */ }
    private function findCorruptedFiles(): array { /* implementation from PDFMonitoringService */ }
    private function findDuplicateFiles(): array { /* implementation from PDFMonitoringService */ }
    private function calculateDirectorySize($path): int { /* implementation from PDFMonitoringService */ }
}
```

### 4. Dashboard and Reporting

#### Monitoring Dashboard Controller

```php
// app/Http/Controllers/PDFMonitoringController.php
namespace App\Http\Controllers;

use App\Services\PDFMonitoringService;
use App\Services\PDFMaintenanceService;
use App\Services\PDFAlertService;
use Illuminate\Http\Request;

class PDFMonitoringController extends Controller
{
    private $monitoringService;
    private $maintenanceService;
    private $alertService;
    
    public function __construct(
        PDFMonitoringService $monitoringService,
        PDFMaintenanceService $maintenanceService,
        PDFAlertService $alertService
    ) {
        $this->monitoringService = $monitoringService;
        $this->maintenanceService = $maintenanceService;
        $this->alertService = $alertService;
    }
    
    public function dashboard()
    {
        $healthReport = $this->monitoringService->getSystemHealthReport();
        
        return view('pdfs.dashboard', compact('healthReport'));
    }
    
    public function storageMetrics()
    {
        $metrics = $this->monitoringService->getStorageMetrics();
        
        return response()->json($metrics);
    }
    
    public function performanceMetrics()
    {
        $metrics = $this->monitoringService->getPerformanceMetrics();
        
        return response()->json($metrics);
    }
    
    public function integrityReport()
    {
        $report = $this->monitoringService->getIntegrityMetrics();
        
        return response()->json($report);
    }
    
    public function runMaintenance(Request $request)
    {
        $type = $request->get('type', 'daily');
        
        switch ($type) {
            case 'daily':
                $results = $this->maintenanceService->performDailyMaintenance();
                break;
            case 'weekly':
                $results = $this->maintenanceService->performWeeklyMaintenance();
                break;
            case 'monthly':
                $results = $this->maintenanceService->performMonthlyMaintenance();
                break;
            default:
                return response()->json(['error' => 'Invalid maintenance type'], 400);
        }
        
        return response()->json($results);
    }
    
    public function alerts()
    {
        $alerts = DB::table('pdf_alerts')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return view('pdfs.alerts', compact('alerts'));
    }
    
    public function statistics()
    {
        $stats = [
            'daily_transactions' => $this->getDailyTransactionStats(),
            'monthly_growth' => $this->getMonthlyGrowthStats(),
            'file_type_distribution' => $this->getFileTypeDistribution(),
            'storage_trends' => $this->getStorageTrends(),
        ];
        
        return response()->json($stats);
    }
    
    private function getDailyTransactionStats(): array
    {
        return DB::table('transactions')
            ->selectRaw('DATE(tanggal_transaksi) as date, COUNT(*) as count')
            ->where('tanggal_transaksi', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
    
    private function getMonthlyGrowthStats(): array
    {
        return DB::table('pdf_files')
            ->selectRaw('YEAR(generated_at) as year, MONTH(generated_at) as month, SUM(file_size) as total_size')
            ->where('generated_at', '>=', now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->toArray();
    }
    
    private function getFileTypeDistribution(): array
    {
        return DB::table('pdf_files')
            ->selectRaw('file_type, COUNT(*) as count, SUM(file_size) as total_size')
            ->groupBy('file_type')
            ->get()
            ->toArray();
    }
    
    private function getStorageTrends(): array
    {
        // This would require historical storage data to be collected
        // For now, return current storage breakdown
        return [
            'active_storage' => $this->monitoringService->getStorageMetrics()['active_storage'],
            'archive_storage' => $this->monitoringService->getStorageMetrics()['archive_storage'],
        ];
    }
}
```

### 5. Scheduled Tasks Configuration

#### Updated Console Kernel

```php
// app/Console/Kernel.php (updated)
protected function schedule(Schedule $schedule)
{
    // Monitoring tasks
    $schedule->command('pdfs:monitor')->dailyAt('02:00');
    $schedule->command('pdfs:monitor --detailed')->monthlyOn(1, '04:00');
    
    // Maintenance tasks
    $schedule->call(function () {
        app(PDFMaintenanceService::class)->performDailyMaintenance();
    })->dailyAt('03:00');
    
    $schedule->call(function () {
        app(PDFMaintenanceService::class)->performWeeklyMaintenance();
    })->weeklyOn(1, '05:00'); // Mondays at 5 AM
    
    $schedule->call(function () {
        app(PDFMaintenanceService::class)->performMonthlyMaintenance();
    })->monthlyOn(1, '06:00'); // 1st of month at 6 AM
    
    // Archiving tasks
    $schedule->command('pdfs:archive')->weeklyOn(0, '03:00'); // Sundays at 3 AM
    
    // Alert checking
    $schedule->call(function () {
        $healthReport = app(PDFMonitoringService::class)->getSystemHealthReport();
        app(PDFAlertService::class)->checkAndSendAlerts($healthReport);
    })->hourly();
}
```

## Implementation Checklist

### Phase 1: Monitoring Setup
- [ ] Create PDFMonitoringService
- [ ] Create PDFAlertService
- [ ] Set up alert configuration
- [ ] Create database migration for pdf_alerts table
- [ ] Test monitoring endpoints

### Phase 2: Maintenance System
- [ ] Create PDFMaintenanceService
- [ ] Implement daily/weekly/monthly tasks
- [ ] Set up compression service integration
- [ ] Test maintenance procedures

### Phase 3: Dashboard and Reporting
- [ ] Create PDFMonitoringController
- [ ] Build dashboard views
- [ ] Implement reporting endpoints
- [ ] Set up real-time monitoring

### Phase 4: Automation
- [ ] Configure scheduled tasks
- [ ] Set up alert notifications
- [ ] Implement backup verification
- [ ] Test automated responses

### Phase 5: Testing and Validation
- [ ] Test all monitoring scenarios
- [ ] Validate alert system
- [ ] Verify maintenance procedures
- [ ] Performance testing under load

## Success Metrics

### System Health
- Storage utilization < 80%
- File integrity > 99.9%
- Alert response time < 5 minutes
- Maintenance completion rate > 95%

### Performance
- PDF generation time < 500ms
- Storage read/write speed > 50MB/s
- Database query time < 100ms
- Dashboard load time < 2 seconds

### Reliability
- System uptime > 99.5%
- Backup success rate > 99%
- Archive integrity > 99.9%
- False positive rate < 1%

This comprehensive monitoring and maintenance system ensures the PDF storage solution remains reliable, performant, and scalable over time.
