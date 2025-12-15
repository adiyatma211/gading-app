<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Detailed PDF Path Investigation ===\n\n";

// 1. Check the transactions table for PDF paths
echo "1. Checking transactions table:\n";
$transactions = App\Models\transactions::latest()->take(5)->get(['id', 'nomor_faktur', 'pdf_storage_path', 'pdf_storage_path_invoice', 'nota_file', 'nota_file_dua']);

foreach ($transactions as $transaction) {
    echo "ID: {$transaction->id}\n";
    echo "Nomor Faktur: {$transaction->nomor_faktur}\n";
    echo "PDF Storage Path: " . ($transaction->pdf_storage_path ?? 'NULL') . "\n";
    echo "PDF Storage Path Invoice: " . ($transaction->pdf_storage_path_invoice ?? 'NULL') . "\n";
    echo "Nota File: " . ($transaction->nota_file ?? 'NULL') . "\n";
    echo "Nota File Dua: " . ($transaction->nota_file_dua ?? 'NULL') . "\n";
    echo "--------------------------------\n";
}

// 2. Check the PDFStorageMetadata table
echo "\n2. Checking PDFStorageMetadata table:\n";
$metadataRecords = App\Models\PdfStorageMetadata::latest()->take(10)->get();

foreach ($metadataRecords as $metadata) {
    echo "ID: {$metadata->id}\n";
    echo "PDFable Type: {$metadata->pdfable_type}\n";
    echo "PDFable ID: {$metadata->pdfable_id}\n";
    echo "File Name: {$metadata->file_name}\n";
    echo "File Path: {$metadata->file_path}\n";
    echo "File Type: {$metadata->file_type}\n";
    echo "Created At: {$metadata->created_at}\n";
    echo "--------------------------------\n";
}

// 3. Check for invoice PDFs in the metadata
echo "\n3. Checking for invoice PDFs in metadata:\n";
$invoicePdfs = App\Models\PdfStorageMetadata::where('file_type', 'invoice')->get();

if ($invoicePdfs->isEmpty()) {
    echo "No invoice PDFs found in metadata table.\n";
} else {
    foreach ($invoicePdfs as $pdf) {
        echo "Invoice PDF ID: {$pdf->id}\n";
        echo "Transaction ID: {$pdf->pdfable_id}\n";
        echo "File Path: {$pdf->file_path}\n";
        echo "File Name: {$pdf->file_name}\n";
        echo "Created At: {$pdf->created_at}\n";
        echo "--------------------------------\n";

        // Check if the corresponding transaction has the invoice path
        $transaction = App\Models\transactions::find($pdf->pdfable_id);
        if ($transaction) {
            echo "Transaction PDF Storage Path Invoice: " . ($transaction->pdf_storage_path_invoice ?? 'NULL') . "\n";
            echo "Match: " . (($transaction->pdf_storage_path_invoice === $pdf->file_path) ? "YES" : "NO") . "\n";
        }
        echo "================================\n";
    }
}

// 4. Check if the PDFStorageService is being called correctly
echo "\n4. Simulating PDFStorageService storePDF calls:\n";

// Get a sample transaction
$sampleTransaction = App\Models\transactions::first();
if ($sampleTransaction) {
    echo "Sample Transaction ID: {$sampleTransaction->id}\n";

    // Create a dummy PDF content
    $dummyPdfContent = "%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n174\n%%EOF";

    // Test storing a thermal PDF
    echo "\nTesting thermal PDF storage:\n";
    $storageService = app(App\Services\PDFStorageService::class);
    $thermalResult = $storageService->storePDF($dummyPdfContent, 'thermal', $sampleTransaction->id, $sampleTransaction->created_at);
    echo "Thermal Result: " . json_encode($thermalResult, JSON_PRETTY_PRINT) . "\n";

    // Test storing an invoice PDF
    echo "\nTesting invoice PDF storage:\n";
    $invoiceResult = $storageService->storePDF($dummyPdfContent, 'invoice', $sampleTransaction->id, $sampleTransaction->created_at);
    echo "Invoice Result: " . json_encode($invoiceResult, JSON_PRETTY_PRINT) . "\n";

    // Check if the transaction was updated correctly
    echo "\nChecking transaction after PDF storage:\n";
    $updatedTransaction = App\Models\transactions::find($sampleTransaction->id);
    echo "PDF Storage Path: " . ($updatedTransaction->pdf_storage_path ?? 'NULL') . "\n";
    echo "PDF Storage Path Invoice: " . ($updatedTransaction->pdf_storage_path_invoice ?? 'NULL') . "\n";
}

// 5. Check the database schema for the pdf_storage_path_invoice column
echo "\n5. Checking database schema:\n";
$schema = DB::select("DESCRIBE transactions");
foreach ($schema as $column) {
    if (strpos($column->Field, 'pdf') !== false) {
        echo "Column: {$column->Field}, Type: {$column->Type}, Null: {$column->Null}, Default: {$column->Default}\n";
    }
}
