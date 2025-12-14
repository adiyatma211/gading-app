# Database Migration Analysis & Strategy

## Current Production Database Analysis

Based on the production database dump from `u209222223_kasir_gading.sql`, here's the current state:

### Existing Tables Related to PDF Storage

#### 1. `transactions` Table (Lines 298-322)
**Current Fields:**
```sql
CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `biaya_desain` decimal(15,2) NOT NULL DEFAULT 0.00,
  `diskon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` varchar(255) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `tanggal_ambil` timestamp NULL DEFAULT NULL,
  `tanggal_transaksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `nota_file` varchar(250) DEFAULT NULL,           -- ✅ EXISTS
  `nota_file_dua` varchar(250) DEFAULT NULL,       -- ✅ EXISTS
  `nomor_faktur` varchar(250) DEFAULT NULL,
  `status_pembayaran` varchar(15) DEFAULT NULL,
  `diambil_oleh` varchar(250) DEFAULT NULL,
  `bukti_pengambilan` varchar(255) DEFAULT NULL,
  `tanggal_selesai` timestamp NULL DEFAULT NULL,
  `deleteSts` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` varchar(250) DEFAULT NULL,
  `updatedBy` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2. `historynotas` Table (Lines 129-140)
**Current Fields:**
```sql
CREATE TABLE `historynotas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `nota_file` varchar(255) NOT NULL,
  `tanggal_cetak` timestamp NULL DEFAULT NULL,
  `deleteSts` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` varchar(255) DEFAULT NULL,
  `updatedBy` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Missing Fields for New PDF Storage System

#### 1. Missing in `transactions` Table:
- `thermal_pdf_path` - New structured path for thermal PDFs
- `invoice_pdf_path` - New structured path for invoice PDFs
- `thermal_pdf_filename` - New filename for thermal PDFs
- `invoice_pdf_filename` - New filename for invoice PDFs
- `thermal_pdf_size` - File size for thermal PDFs
- `invoice_pdf_size` - File size for invoice PDFs
- `thermal_pdf_hash` - MD5 hash for integrity check
- `invoice_pdf_hash` - MD5 hash for integrity check
- `thermal_pdf_generated_at` - Generation timestamp
- `invoice_pdf_generated_at` - Generation timestamp
- `thermal_pdf_archived` - Archive status flag
- `invoice_pdf_archived` - Archive status flag
- `deleted_at` - Soft delete timestamp
- Performance indexes

#### 2. Missing Tables:
- `pdf_files` - Dedicated PDF management table
- `pdf_alerts` - Alert system table

## Migration Strategy

### Challenge: Previous Developers Didn't Use Migrations

The production database shows that previous developers made manual changes without proper Laravel migrations. This creates several challenges:

1. **Migration Table Inconsistency**: The `migrations` table may not reflect actual database state
2. **Schema Drift**: Current migration files don't match production database
3. **Deployment Complexity**: Hard to replicate database changes in hosting environments

### Solution: Baseline Migration Strategy

#### Phase 1: Create Baseline Migration
Create a "snapshot" migration that represents the current production state as the starting point.

#### Phase 2: Add New Fields Incrementally
Add only the new fields needed for the PDF storage system.

#### Phase 3: Data Migration
Migrate existing PDF data to new structure while maintaining backward compatibility.

## Implementation Plan

### Step 1: Create Baseline Migration

```php
// database/migrations/2025_12_10_000001_create_baseline_production_structure.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This migration creates the baseline structure matching current production database
     * This will be marked as already run to establish the starting point
     */
    public function up(): void
    {
        // This migration is for documentation purposes only
        // We'll mark it as run in the migrations table without actually running
        // This establishes our baseline for future migrations
    }
    
    public function down(): void
    {
        // No rollback needed for baseline migration
    }
};
```

### Step 2: Add PDF Management Fields

```php
// database/migrations/2025_12_10_000002_add_pdf_management_fields_to_transactions.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // New PDF file paths (structured)
            $table->string('thermal_pdf_path')->nullable()->after('nota_file_dua');
            $table->string('invoice_pdf_path')->nullable()->after('thermal_pdf_path');
            
            // File metadata
            $table->string('thermal_pdf_filename')->nullable()->after('thermal_pdf_path');
            $table->string('invoice_pdf_filename')->nullable()->after('invoice_pdf_path');
            $table->integer('thermal_pdf_size')->nullable()->after('thermal_pdf_filename');
            $table->integer('invoice_pdf_size')->nullable()->after('invoice_pdf_filename');
            $table->string('thermal_pdf_hash')->nullable()->after('thermal_pdf_size');
            $table->string('invoice_pdf_hash')->nullable()->after('invoice_pdf_size');
            
            // File management timestamps
            $table->timestamp('thermal_pdf_generated_at')->nullable()->after('thermal_pdf_hash');
            $table->timestamp('invoice_pdf_generated_at')->nullable()->after('invoice_pdf_hash');
            $table->tinyInteger('thermal_pdf_archived')->default(0)->after('thermal_pdf_generated_at');
            $table->tinyInteger('invoice_pdf_archived')->default(0)->after('invoice_pdf_generated_at');
            
            // Soft delete
            $table->softDeletes()->after('updated_at');
            
            // Performance indexes
            $table->index(['tanggal_transaksi']);
            $table->index(['thermal_pdf_generated_at']);
            $table->index(['invoice_pdf_generated_at']);
            $table->index(['thermal_pdf_archived']);
            $table->index(['invoice_pdf_archived']);
            $table->index(['deleted_at']);
        });
    }

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

### Step 3: Create PDF Files Management Table

```php
// database/migrations/2025_12_10_000003_create_pdf_files_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            
            // Indexes for performance
            $table->index(['file_type']);
            $table->index(['generated_at']);
            $table->index(['archived_at']);
            $table->index(['is_compressed']);
            $table->index(['transaction_id']);
            $table->index(['file_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_files');
    }
};
```

### Step 4: Create PDF Alerts Table

```php
// database/migrations/2025_12_10_000004_create_pdf_alerts_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('level'); // 'info', 'warning', 'error', 'critical'
            $table->string('title');
            $table->text('message');
            $table->json('context')->nullable();
            $table->boolean('acknowledged')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            $table->string('acknowledged_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['level']);
            $table->index(['acknowledged']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_alerts');
    }
};
```

### Step 5: Data Migration Script

```php
// database/migrations/2025_12_10_000005_migrate_existing_pdf_data.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing PDF data from old structure to new structure
        $this->migrateExistingPDFs();
        $this->populatePDFFilesTable();
    }
    
    private function migrateExistingPDFs(): void
    {
        $transactions = DB::table('transactions')
            ->whereNotNull('nota_file')
            ->orWhereNotNull('nota_file_dua')
            ->get();
            
        foreach ($transactions as $transaction) {
            $transactionDate = Carbon::parse($transaction->tanggal_transaksi);
            $transactionId = 'TX' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            
            // Process thermal PDF (nota_file)
            if ($transaction->nota_file) {
                $this->processPDFFile($transaction, 'thermal', $transaction->nota_file, $transactionId, $transactionDate);
            }
            
            // Process invoice PDF (nota_file_dua)
            if ($transaction->nota_file_dua) {
                $this->processPDFFile($transaction, 'invoice', $transaction->nota_file_dua, $transactionId, $transactionDate);
            }
        }
    }
    
    private function processPDFFile($transaction, $type, $filename, $transactionId, $date): void
    {
        try {
            // Check if file exists in old location
            $oldPath = 'nota/' . $filename;
            if (!Storage::disk('public')->exists($oldPath)) {
                Log::warning("PDF file not found during migration", [
                    'transaction_id' => $transaction->id,
                    'filename' => $filename,
                    'type' => $type
                ]);
                return;
            }
            
            // Get file content and metadata
            $content = Storage::disk('public')->get($oldPath);
            $fileSize = strlen($content);
            $fileHash = md5($content);
            
            // Generate new structured path and filename
            $sequence = $this->getDailySequence($date->format('Y-m-d'));
            $newFilename = $this->generateNewFilename($type, $date, $transactionId, $sequence);
            $newPath = $this->generateNewPath($type, $date) . '/' . $newFilename;
            
            // Move file to new location
            Storage::disk('pdfs')->makeDirectory($this->generateNewPath($type, $date));
            Storage::disk('pdfs')->put($newPath, $content);
            
            // Update transactions table with new metadata
            $updateData = [
                "{$type}_pdf_path" => $newPath,
                "{$type}_pdf_filename" => $newFilename,
                "{$type}_pdf_size" => $fileSize,
                "{$type}_pdf_hash" => $fileHash,
                "{$type}_pdf_generated_at" => $date,
            ];
            
            DB::table('transactions')
                ->where('id', $transaction->id)
                ->update($updateData);
            
            // Insert into pdf_files table
            DB::table('pdf_files')->insert([
                'file_type' => $type,
                'filename' => $newFilename,
                'path' => $newPath,
                'file_size' => $fileSize,
                'file_hash' => $fileHash,
                'transaction_id' => $transactionId,
                'generated_at' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info("PDF migrated successfully", [
                'transaction_id' => $transaction->id,
                'old_filename' => $filename,
                'new_filename' => $newFilename,
                'type' => $type
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to migrate PDF", [
                'transaction_id' => $transaction->id,
                'filename' => $filename,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function getDailySequence(string $date): int
    {
        return DB::table('transactions')
            ->whereDate('tanggal_transaksi', $date)
            ->count() + 1;
    }
    
    private function generateNewFilename(string $type, Carbon $date, string $transactionId, int $sequence): string
    {
        $prefix = $type === 'thermal' ? 'TRX' : 'INV';
        $dateStr = $date->format('Ymd');
        $seq = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$dateStr}-{$seq}-{$transactionId}.pdf";
    }
    
    private function generateNewPath(string $type, Carbon $date): string
    {
        $year = $date->format('Y');
        $month = $date->format('m_' . $date->format('F'));
        $day = $date->format('d');
        
        return "{$year}/{$month}/{$day}/{$type}";
    }
    
    private function populatePDFFilesTable(): void
    {
        // This is handled in processPDFFile method
        // Keeping this method for clarity
    }
    
    public function down(): void
    {
        // Rollback would be complex and potentially destructive
        // Recommend backup before running this migration
        throw new \Exception('This migration cannot be rolled back automatically. Please restore from backup.');
    }
};
```

## Hosting Deployment Strategy

### Challenge: Hosting Environment Limitations

Since previous developers didn't use migrations, hosting environments may have:

1. **Manual Database Changes**: Direct SQL modifications without migration tracking
2. **Inconsistent Schemas**: Development vs Production differences
3. **No Migration History**: Unable to run `php artisan migrate` safely

### Solution: Safe Deployment Approach

#### Phase 1: Database Backup
```bash
# Create full database backup
mysqldump -u username -p database_name > backup_before_migration.sql
```

#### Phase 2: Schema Verification
```bash
# Compare current schema with expected schema
php artisan schema:dump --database=mysql > current_schema.sql
```

#### Phase 3: Incremental Deployment
Deploy changes one at a time with verification:

1. **Add New Fields Only** (safe operation)
2. **Create New Tables** (safe operation)
3. **Migrate Data** (requires testing)
4. **Add Indexes** (performance impact)

#### Phase 4: Verification Scripts
```php
// app/Console/Commands/VerifyMigration.php
class VerifyMigration extends Command
{
    protected $signature = 'pdfs:verify-migration';
    
    public function handle()
    {
        $this->info('Verifying PDF storage migration...');
        
        // Check new fields exist
        $hasThermalPath = Schema::hasColumn('transactions', 'thermal_pdf_path');
        $hasInvoicePath = Schema::hasColumn('transactions', 'invoice_pdf_path');
        $hasPDFFilesTable = Schema::hasTable('pdf_files');
        
        $this->line("Thermal PDF Path field: " . ($hasThermalPath ? "✅" : "❌"));
        $this->line("Invoice PDF Path field: " . ($hasInvoicePath ? "✅" : "❌"));
        $this->line("PDF Files table: " . ($hasPDFFilesTable ? "✅" : "❌"));
        
        // Check data integrity
        $orphanedFiles = $this->checkOrphanedFiles();
        $this->line("Orphaned files: " . count($orphanedFiles));
        
        if (count($orphanedFiles) > 0) {
            $this->error("Data integrity issues found!");
            return 1;
        }
        
        $this->info("Migration verification completed successfully!");
        return 0;
    }
}
```

## Risk Mitigation

### 1. Data Loss Prevention
- **Full Backup**: Before any migration
- **Incremental Backups**: After each major step
- **Verification Scripts**: Automated integrity checks
- **Rollback Plan**: Documented rollback procedures

### 2. Downtime Minimization
- **Zero-Downtime Deployment**: Add new fields first (non-breaking)
- **Background Migration**: Process data migration in batches
- **Feature Flags**: Enable new system gradually
- **Fallback Mechanism**: Keep old system functional during transition

### 3. Hosting Compatibility
- **SQL Scripts**: Provide raw SQL for manual execution
- **Step-by-Step Guide**: Detailed instructions for manual deployment
- **Verification Commands**: Scripts to verify each step
- **Support Documentation**: Troubleshooting guide

## Implementation Timeline

### Week 1: Preparation
- [ ] Create full database backup
- [ ] Set up development environment matching production
- [ ] Test migration scripts on staging
- [ ] Prepare rollback procedures

### Week 2: Field Addition
- [ ] Deploy new field migrations (safe operation)
- [ ] Verify new fields exist
- [ ] Update application code to use new fields
- [ ] Test with existing data

### Week 3: Data Migration
- [ ] Run data migration script
- [ ] Verify data integrity
- [ ] Test new PDF generation
- [ ] Update file paths in application

### Week 4: System Integration
- [ ] Deploy new storage service
- [ ] Enable monitoring system
- [ ] Train staff on new system
- [ ] Document new procedures

### Week 5: Cleanup
- [ ] Remove old PDF files (after verification)
- [ ] Update documentation
- [ ] Performance optimization
- [ ] Final verification

This comprehensive migration strategy addresses the challenges of inconsistent migration history while ensuring safe deployment to hosting environments.
