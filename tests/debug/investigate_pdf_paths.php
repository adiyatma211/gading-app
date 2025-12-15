<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Get the latest transactions
$transactions = App\Models\transactions::latest()->take(5)->get(['id', 'nomor_faktur', 'pdf_storage_path', 'pdf_storage_path_invoice', 'nota_file', 'nota_file_dua', 'created_at']);

echo "=== Latest 5 Transactions ===\n";
foreach ($transactions as $transaction) {
    echo "ID: {$transaction->id}\n";
    echo "Nomor Faktur: {$transaction->nomor_faktur}\n";
    echo "PDF Storage Path: " . ($transaction->pdf_storage_path ?? 'NULL') . "\n";
    echo "PDF Storage Path Invoice: " . ($transaction->pdf_storage_path_invoice ?? 'NULL') . "\n";
    echo "Nota File: " . ($transaction->nota_file ?? 'NULL') . "\n";
    echo "Nota File Dua: " . ($transaction->nota_file_dua ?? 'NULL') . "\n";
    echo "Created At: {$transaction->created_at}\n";
    echo "--------------------------------\n";
}

// Check if PDFStorageService is being used properly
echo "\n=== Checking PDFStorageService ===\n";

// Get the PDFStorageService class
$reflection = new ReflectionClass('App\Services\PDFStorageService');
$methods = $reflection->getMethods();

echo "PDFStorageService methods:\n";
foreach ($methods as $method) {
    echo "- {$method->getName()}\n";
}

// Check the TransactionsController for PDF storage usage
echo "\n=== Checking TransactionsController ===\n";

$controllerReflection = new ReflectionClass('App\Http\Controllers\TransactionsController');
$controllerMethods = $controllerReflection->getMethods();

echo "TransactionsController methods:\n";
foreach ($controllerMethods as $method) {
    if ($method->getName() !== 'getMiddleware' && $method->getName() !== 'validate' && $method->getName() !== 'validateWithBag') {
        echo "- {$method->getName()}\n";
    }
}

// Check the source code of the store method
if ($controllerReflection->hasMethod('store')) {
    $storeMethod = $controllerReflection->getMethod('store');
    $startLine = $storeMethod->getStartLine();
    $endLine = $storeMethod->getEndLine();

    echo "\n=== Store Method Source (lines {$startLine}-{$endLine}) ===\n";

    $controllerFile = file_get_contents(__DIR__ . '/app/Http/Controllers/TransactionsController.php');
    $lines = explode("\n", $controllerFile);

    for ($i = $startLine - 1; $i < $endLine; $i++) {
        echo ($i + 1) . ": " . $lines[$i] . "\n";
    }
}

// Check for any recent transactions with PDF paths
echo "\n=== Transactions with PDF paths ===\n";
$transactionsWithPdf = App\Models\transactions::whereNotNull('pdf_storage_path')
    ->orWhereNotNull('pdf_storage_path_invoice')
    ->latest()
    ->take(5)
    ->get(['id', 'nomor_faktur', 'pdf_storage_path', 'pdf_storage_path_invoice', 'created_at']);

if ($transactionsWithPdf->isEmpty()) {
    echo "No transactions found with PDF paths stored.\n";
} else {
    foreach ($transactionsWithPdf as $transaction) {
        echo "ID: {$transaction->id}, Nomor Faktur: {$transaction->nomor_faktur}\n";
        echo "PDF Storage Path: " . ($transaction->pdf_storage_path ?? 'NULL') . "\n";
        echo "PDF Storage Path Invoice: " . ($transaction->pdf_storage_path_invoice ?? 'NULL') . "\n";
        echo "Created At: {$transaction->created_at}\n";
        echo "--------------------------------\n";
    }
}
