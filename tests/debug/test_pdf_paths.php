<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing PDF Path Storage in Transactions ===\n\n";

// Check if the columns exist
$columns = Schema::getColumnListing('transactions');
$hasPdfStoragePath = in_array('pdf_storage_path', $columns);
$hasPdfStoragePathInvoice = in_array('pdf_storage_path_invoice', $columns);

echo "Column existence check:\n";
echo "- pdf_storage_path column exists: " . ($hasPdfStoragePath ? "YES" : "NO") . "\n";
echo "- pdf_storage_path_invoice column exists: " . ($hasPdfStoragePathInvoice ? "YES" : "NO") . "\n\n";

if (!$hasPdfStoragePath || !$hasPdfStoragePathInvoice) {
    echo "ERROR: Required columns are missing in the transactions table!\n";
    exit(1);
}

// Get the latest 5 transactions
$transactions = DB::table('transactions')
    ->select([
        'id',
        'nomor_faktur',
        'created_at',
        'pdf_storage_path',
        'pdf_storage_path_invoice',
        'nota_file',
        'nota_file_dua'
    ])
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

echo "Checking latest 5 transactions:\n";
echo str_repeat("-", 100) . "\n";
printf("%-6s %-15s %-20s %-20s %-15s %-15s\n",
    "ID", "Nomor Faktur", "Created At", "pdf_storage_path", "pdf_storage_path_inv", "Both Paths");
echo str_repeat("-", 100) . "\n";

$bothPathsCount = 0;
$totalTransactions = 0;

foreach ($transactions as $transaction) {
    $totalTransactions++;

    $hasPath1 = !empty($transaction->pdf_storage_path);
    $hasPath2 = !empty($transaction->pdf_storage_path_invoice);
    $hasBoth = $hasPath1 && $hasPath2;

    if ($hasBoth) {
        $bothPathsCount++;
    }

    $path1Status = $hasPath1 ? "✓" : "✗";
    $path2Status = $hasPath2 ? "✓" : "✗";
    $bothStatus = $hasBoth ? "✓" : "✗";

    printf("%-6d %-15s %-20s %-20s %-15s %-15s\n",
        $transaction->id,
        substr($transaction->nomor_faktur ?? 'N/A', 0, 15),
        substr($transaction->created_at ?? 'N/A', 0, 20),
        $path1Status,
        $path2Status,
        $bothStatus
    );
}

echo str_repeat("-", 100) . "\n\n";

// Summary
echo "SUMMARY:\n";
echo "- Total transactions checked: {$totalTransactions}\n";
echo "- Transactions with both PDF paths: {$bothPathsCount}\n";
echo "- Success rate: " . round(($bothPathsCount / max($totalTransactions, 1)) * 100, 2) . "%\n\n";

// Check for detailed paths of the first transaction with both paths
$detailTransaction = DB::table('transactions')
    ->select([
        'id',
        'nomor_faktur',
        'pdf_storage_path',
        'pdf_storage_path_invoice',
        'nota_file',
        'nota_file_dua'
    ])
    ->whereNotNull('pdf_storage_path')
    ->whereNotNull('pdf_storage_path_invoice')
    ->orderBy('id', 'desc')
    ->first();

if ($detailTransaction) {
    echo "DETAILED VIEW (Transaction ID: {$detailTransaction->id}):\n";
    echo "- Nomor Faktur: {$detailTransaction->nomor_faktur}\n";
    echo "- Thermal PDF (pdf_storage_path): {$detailTransaction->pdf_storage_path}\n";
    echo "- Invoice PDF (pdf_storage_path_invoice): {$detailTransaction->pdf_storage_path_invoice}\n";
    echo "- Legacy nota_file: {$detailTransaction->nota_file}\n";
    echo "- Legacy nota_file_dua: {$detailTransaction->nota_file_dua}\n\n";
}

// Check if files actually exist (for local storage)
echo "FILE EXISTENCE CHECK:\n";
$localPathTransactions = DB::table('transactions')
    ->select(['id', 'pdf_storage_path', 'pdf_storage_path_invoice'])
    ->whereNotNull('pdf_storage_path')
    ->whereNotNull('pdf_storage_path_invoice')
    ->where('pdf_storage_path', 'like', 'nota/%')
    ->orWhere('pdf_storage_path_invoice', 'like', 'nota/%')
    ->orderBy('id', 'desc')
    ->limit(3)
    ->get();

foreach ($localPathTransactions as $trans) {
    echo "\nTransaction ID: {$trans->id}\n";

    $thermalPath = public_path($trans->pdf_storage_path);
    $invoicePath = public_path($trans->pdf_storage_path_invoice);

    echo "- Thermal PDF path: {$trans->pdf_storage_path}\n";
    echo "  File exists: " . (file_exists($thermalPath) ? "YES" : "NO") . "\n";
    if (file_exists($thermalPath)) {
        echo "  File size: " . round(filesize($thermalPath) / 1024, 2) . " KB\n";
    }

    echo "- Invoice PDF path: {$trans->pdf_storage_path_invoice}\n";
    echo "  File exists: " . (file_exists($invoicePath) ? "YES" : "NO") . "\n";
    if (file_exists($invoicePath)) {
        echo "  File size: " . round(filesize($invoicePath) / 1024, 2) . " KB\n";
    }
}

echo "\n=== Test completed ===\n";
