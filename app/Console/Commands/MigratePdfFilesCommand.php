<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\transactions;
use App\Models\PdfStorageMetadata;
use App\Services\PDFStorageService;
use Carbon\Carbon;

class MigratePdfFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:migrate-files
                            {--dry-run : Show what will be migrated without actually doing it}
                            {--batch-size=50 : Number of files to process in each batch}
                            {--force : Force migration even if files already exist in new system}
                            {--start-from=0 : Start from specific transaction ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing PDF files from old structure to new hierarchical storage system';

    /**
     * @var PDFStorageService
     */
    protected $storageService;

    /**
     * Migration statistics
     */
    protected $stats = [
        'total_transactions' => 0,
        'processed' => 0,
        'migrated' => 0,
        'skipped' => 0,
        'failed' => 0,
        'errors' => []
    ];

    /**
     * Execute the console command.
     */
    public function handle(PDFStorageService $storageService): int
    {
        $this->storageService = $storageService;
        $dryRun = $this->option('dry-run');
        $batchSize = $this->option('batch-size');
        $startFrom = $this->option('start-from');
        $force = $this->option('force');

        $this->info('PDF Files Migration');
        $this->line(str_repeat('=', 50));

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be actually migrated');
        }

        // Get transactions with PDF files
        $query = transactions::where(function ($q) {
            $q->whereNotNull('nota_file')
              ->orWhereNotNull('nota_file_dua');
        })->where('id', '>=', $startFrom)
        ->orderBy('id');

        $this->stats['total_transactions'] = $query->count();
        $this->info("Found {$this->stats['total_transactions']} transactions with PDF files");

        if ($this->stats['total_transactions'] === 0) {
            $this->info('No PDF files to migrate.');
            return 0;
        }

        // Process in batches
        $query->chunk($batchSize, function ($transactions) use ($dryRun, $force) {
            $this->processBatch($transactions, $dryRun, $force);
        });

        $this->displayResults();

        return $this->stats['failed'] > 0 ? 1 : 0;
    }

    /**
     * Process a batch of transactions
     */
    protected function processBatch($transactions, bool $dryRun, bool $force): void
    {
        $this->line("\nProcessing batch of " . $transactions->count() . " transactions...");

        foreach ($transactions as $transaction) {
            $this->stats['processed']++;

            try {
                $this->processTransaction($transaction, $dryRun, $force);
            } catch (\Exception $e) {
                $this->stats['failed']++;
                $this->stats['errors'][] = [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage()
                ];

                $this->error("Failed to process transaction {$transaction->id}: " . $e->getMessage());
                Log::error('PDF migration failed', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Show progress
            if ($this->stats['processed'] % 10 === 0) {
                $this->showProgress();
            }
        }
    }

    /**
     * Process a single transaction
     */
    protected function processTransaction($transaction, bool $dryRun, bool $force): void
    {
        // Check if already migrated
        if (!$force && $this->isAlreadyMigrated($transaction)) {
            $this->stats['skipped']++;
            $this->line("   [SKIP] Transaction {$transaction->id} already migrated");
            return;
        }

        $migratedFiles = 0;

        // Process nota_file (thermal receipt)
        if ($transaction->nota_file) {
            if ($this->migrateFile($transaction, 'nota_file', 'thermal', $dryRun)) {
                $migratedFiles++;
            }
        }

        // Process nota_file_dua (invoice)
        if ($transaction->nota_file_dua) {
            if ($this->migrateFile($transaction, 'nota_file_dua', 'invoice', $dryRun)) {
                $migratedFiles++;
            }
        }

        if ($migratedFiles > 0) {
            $this->stats['migrated']++;
            $this->line("   [OK] Transaction {$transaction->id} - {$migratedFiles} files migrated");
        }
    }

    /**
     * Migrate a single file
     */
    protected function migrateFile($transaction, string $field, string $type, bool $dryRun): bool
    {
        $oldFilename = $transaction->$field;
        $oldPath = public_path('nota/' . $oldFilename);

        // Check if old file exists
        if (!File::exists($oldPath)) {
            $this->warn("   [WARN] Old file not found: {$oldFilename}");
            return false;
        }

        if ($dryRun) {
            $this->line("   [DRY] Would migrate: {$oldPath} -> {$type}");
            return true;
        }

        try {
            // Get file content
            $fileContent = File::get($oldPath);

            // Store using new service
            $transactionDate = $transaction->created_at ?? Carbon::now();
            $result = $this->storageService->storePDF(
                $fileContent,
                $type,
                $transaction->id,
                $transactionDate
            );

            if (!$result['success']) {
                $this->error("   [ERROR] Failed to store {$type} PDF: " . $result['error']);
                return false;
            }

            // Update transaction with new file info
            if ($type === 'thermal') {
                $transaction->pdf_storage_path = $result['file_path'];
                $transaction->pdf_storage_type = $type;
                $transaction->pdf_storage_hash = $result['file_hash'];
                $transaction->pdf_storage_size = $result['file_size'];
            }

            $transaction->save();

            Log::info('PDF file migrated successfully', [
                'transaction_id' => $transaction->id,
                'field' => $field,
                'type' => $type,
                'old_path' => $oldPath,
                'new_path' => $result['file_path'],
                'file_size' => $result['file_size']
            ]);

            return true;

        } catch (\Exception $e) {
            $this->error("   [ERROR] Failed to migrate {$oldPath}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if transaction is already migrated
     */
    protected function isAlreadyMigrated($transaction): bool
    {
        return !empty($transaction->pdf_storage_path) &&
               !empty($transaction->pdf_storage_hash);
    }

    /**
     * Show migration progress
     */
    protected function showProgress(): void
    {
        $percentage = round(($this->stats['processed'] / $this->stats['total_transactions']) * 100, 1);
        $this->line("   Progress: {$this->stats['processed']}/{$this->stats['total_transactions']} ({$percentage}%)");
    }

    /**
     * Display migration results
     */
    protected function displayResults(): void
    {
        $this->line("\n" . str_repeat('=', 50));
        $this->info('Migration Results:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Transactions', $this->stats['total_transactions']],
                ['Processed', $this->stats['processed']],
                ['Migrated', $this->stats['migrated']],
                ['Skipped', $this->stats['skipped']],
                ['Failed', $this->stats['failed']],
            ]
        );

        if (!empty($this->stats['errors'])) {
            $this->warn("\nErrors encountered:");
            foreach ($this->stats['errors'] as $error) {
                $this->line("  - Transaction {$error['transaction_id']}: {$error['error']}");
            }
        }

        $successRate = $this->stats['processed'] > 0
            ? round(($this->stats['migrated'] / $this->stats['processed']) * 100, 1)
            : 0;

        $this->info("\nMigration completed with {$successRate}% success rate.");

        if (!$this->option('dry-run')) {
            $this->info('Don\'t forget to run: php artisan pdf:stats to verify migration.');
        }
    }
}
