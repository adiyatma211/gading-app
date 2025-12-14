<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Testing Unique PDF Path Fix ===\n\n";

// Get the first transaction to test with
$transaction = App\Models\transactions::first();
if (!$transaction) {
    echo "No transactions found to test with.\n";
    exit;
}

echo "Testing with Transaction ID: {$transaction->id}\n";

// Create unique PDF content for thermal
$thermalPdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 44\n>>\nstream\nBT\n/F1 12 Tf\n72 720 Td\n(Thermal Receipt) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000195 00000 n\ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n299\n%%EOF";

// Create unique PDF content for invoice
$invoicePdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n/Contents 4 0 R\n>>\nendobj\n4 0 obj\n<<\n/Length 44\n>>\nstream\nBT\n/F1 12 Tf\n72 720 Td\n(Invoice Document) Tj\nET\nendstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n0000000195 00000 n\ntrailer\n<<\n/Size 5\n/Root 1 0 R\n>>\nstartxref\n299\n%%EOF";

// Test storing a thermal PDF
echo "\n1. Testing thermal PDF storage:\n";
$storageService = app(App\Services\PDFStorageService::class);
$thermalResult = $storageService->storePDF($thermalPdfContent, 'thermal', $transaction->id, $transaction->created_at);
echo "Thermal Result: " . json_encode($thermalResult, JSON_PRETTY_PRINT) . "\n";

// Test storing an invoice PDF
echo "\n2. Testing invoice PDF storage:\n";
$invoiceResult = $storageService->storePDF($invoicePdfContent, 'invoice', $transaction->id, $transaction->created_at);
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
    if (!$thermalMatch) {
        echo "  Expected: {$thermalResult['file_path']}\n";
        echo "  Actual: " . ($updatedTransaction->pdf_storage_path ?? 'NULL') . "\n";
    }
}

if ($invoiceResult['success']) {
    $invoiceMatch = ($updatedTransaction->pdf_storage_path_invoice === $invoiceResult['file_path']);
    echo "Invoice path match: " . ($invoiceMatch ? "YES" : "NO") . "\n";
    if (!$invoiceMatch) {
        echo "  Expected: {$invoiceResult['file_path']}\n";
        echo "  Actual: " . ($updatedTransaction->pdf_storage_path_invoice ?? 'NULL') . "\n";
    }
}

// Check the PDFStorageMetadata table
echo "\n5. Checking PDFStorageMetadata table:\n";
$metadataRecords = App\Models\PdfStorageMetadata::where('pdfable_id', $transaction->id)->get();
foreach ($metadataRecords as $metadata) {
    echo "Type: {$metadata->file_type}, Path: {$metadata->file_path}\n";
}

echo "\n=== Test Complete ===\n";
