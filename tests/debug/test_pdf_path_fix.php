<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Testing PDF Path Fix ===\n\n";

// Get the first transaction to test with
$transaction = App\Models\transactions::first();
if (!$transaction) {
    echo "No transactions found to test with.\n";
    exit;
}

echo "Testing with Transaction ID: {$transaction->id}\n";

// Create a dummy PDF content
$dummyPdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n174\n%%EOF";

// Test storing a thermal PDF
echo "\n1. Testing thermal PDF storage:\n";
$storageService = app(App\Services\PDFStorageService::class);
$thermalResult = $storageService->storePDF($dummyPdfContent, 'thermal', $transaction->id, $transaction->created_at);
echo "Thermal Result: " . json_encode($thermalResult, JSON_PRETTY_PRINT) . "\n";

// Test storing an invoice PDF
echo "\n2. Testing invoice PDF storage:\n";
$invoiceResult = $storageService->storePDF($dummyPdfContent, 'invoice', $transaction->id, $transaction->created_at);
echo "Invoice Result: " . json_encode($invoiceResult, JSON_PRETTY_PRINT) . "\n";

// Check if the transaction was updated correctly
echo "\n3. Checking transaction after PDF storage:\n";
$updatedTransaction = App\Models\transactions::find($transaction->id);
echo "PDF Storage Path: " . ($updatedTransaction->pdf_storage_path ?? 'NULL') . "\n";
echo "PDF Storage Path Invoice: " . ($updatedTransaction->pdf_storage_path_invoice ?? 'NULL') . "\n";

// Verify the paths match what was returned
echo "\n4. Verification:\n";
if ($thermalResult['success']) {
    $thermalMatch = ($updatedTransaction->pdf_storage_path === $thermalResult['file_path']);
    echo "Thermal path match: " . ($thermalMatch ? "YES" : "NO") . "\n";
}

if ($invoiceResult['success']) {
    $invoiceMatch = ($updatedTransaction->pdf_storage_path_invoice === $invoiceResult['file_path']);
    echo "Invoice path match: " . ($invoiceMatch ? "YES" : "NO") . "\n";
}

// Check the PDFStorageMetadata table
echo "\n5. Checking PDFStorageMetadata table:\n";
$metadataRecords = App\Models\PdfStorageMetadata::where('pdfable_id', $transaction->id)->get();
foreach ($metadataRecords as $metadata) {
    echo "Type: {$metadata->file_type}, Path: {$metadata->file_path}\n";
}

echo "\n=== Test Complete ===\n";
