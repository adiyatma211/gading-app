<?php

/**
 * Comprehensive PHP Tinker Script to Test PDF Links
 *
 * This script tests that both Nota Satu and Nota Dua links show different PDFs.
 * It is designed to run in Laravel Tinker environment.
 *
 * Usage:
 * 1. Copy and paste this entire script into Laravel Tinker
 * 2. Or run: php artisan tinker --execute="include 'tests/debug/test_pdf_links_tinker.php';"
 */

echo "========================================\n";
echo "PDF Links Test Script for Nota Satu & Dua\n";
echo "========================================\n\n";

// Initialize counters
$testResults = [
    'total_transactions_tested' => 0,
    'valid_pairs_found' => 0,
    'different_files' => 0,
    'same_files' => 0,
    'file_not_found' => 0,
    'errors' => 0
];

try {
    // Get PDF Storage Service
    $storageService = app(\App\Services\PDFStorageService::class);
    echo "✓ PDF Storage Service initialized\n\n";

    // Step 1: Query recent transactions that have both PDF paths
    echo "Step 1: Finding transactions with both PDF paths...\n";
    $transactions = \App\Models\transactions::whereNotNull('pdf_storage_path')
        ->whereNotNull('pdf_storage_path_invoice')
        ->orderBy('id', 'desc')
        ->limit(20)
        ->get();

    echo "Found {$transactions->count()} transactions with both PDF paths\n\n";

    if ($transactions->count() === 0) {
        echo "⚠️  No transactions found with both PDF paths.\n";
        echo "   Checking for transactions with individual PDF paths...\n";

        $thermalOnly = \App\Models\transactions::whereNotNull('pdf_storage_path')
            ->whereNull('pdf_storage_path_invoice')
            ->count();
        $invoiceOnly = \App\Models\transactions::whereNull('pdf_storage_path')
            ->whereNotNull('pdf_storage_path_invoice')
            ->count();

        echo "   - Thermal only: {$thermalOnly}\n";
        echo "   - Invoice only: {$invoiceOnly}\n\n";

        // Try to find any transactions with old file paths
        $oldSystem = \App\Models\transactions::whereNotNull('nota_file')
            ->whereNotNull('nota_file_dua')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        if ($oldSystem->count() > 0) {
            echo "Found {$oldSystem->count()} transactions with old file system paths\n";
            $transactions = $oldSystem;
        } else {
            echo "❌ No suitable transactions found for testing\n";
            exit;
        }
    }

    // Step 2: Test each transaction
    foreach ($transactions as $transaction) {
        $testResults['total_transactions_tested']++;

        echo "----------------------------------------\n";
        echo "Testing Transaction ID: {$transaction->id}\n";
        echo "Nomor Faktur: " . ($transaction->nomor_faktur ?? 'N/A') . "\n";
        echo "Created: " . ($transaction->created_at ?? 'N/A') . "\n";

        // Get file paths
        $thermalPath = $transaction->pdf_storage_path ?? null;
        $invoicePath = $transaction->pdf_storage_path_invoice ?? null;
        $thermalFile = $transaction->nota_file ?? null;
        $invoiceFile = $transaction->nota_file_dua ?? null;

        echo "\nPaths:\n";
        echo "  Thermal (Nota Satu): " . ($thermalPath ?: 'N/A') . "\n";
        echo "  Invoice (Nota Dua):  " . ($invoicePath ?: 'N/A') . "\n";

        // Check if using new or old system
        $usingNewSystem = $thermalPath && $invoicePath;
        $usingOldSystem = $thermalFile && $invoiceFile;

        if (!$usingNewSystem && !$usingOldSystem) {
            echo "❌ No valid file paths found\n";
            $testResults['errors']++;
            continue;
        }

        $testResults['valid_pairs_found']++;

        // Step 3: Check if files exist in storage system
        echo "\nFile Existence Check:\n";

        if ($usingNewSystem) {
            $thermalExists = $storageService->fileExists($thermalPath);
            $invoiceExists = $storageService->fileExists($invoicePath);

            echo "  Thermal file exists: " . ($thermalExists ? "✓ Yes" : "❌ No") . "\n";
            echo "  Invoice file exists: " . ($invoiceExists ? "✓ Yes" : "❌ No") . "\n";

            if (!$thermalExists || !$invoiceExists) {
                $testResults['file_not_found']++;
                echo "  ⚠️  Skipping file comparison - missing files\n";
                continue;
            }

            // Get file content for comparison
            $thermalContent = $storageService->getPDF($thermalPath);
            $invoiceContent = $storageService->getPDF($invoicePath);

        } else {
            // Old system check
            $thermalExists = file_exists(public_path('nota/' . $thermalFile));
            $invoiceExists = file_exists(public_path('nota/' . $invoiceFile));

            echo "  Thermal file exists: " . ($thermalExists ? "✓ Yes" : "❌ No") . "\n";
            echo "  Invoice file exists: " . ($invoiceExists ? "✓ Yes" : "❌ No") . "\n";

            if (!$thermalExists || !$invoiceExists) {
                $testResults['file_not_found']++;
                echo "  ⚠️  Skipping file comparison - missing files\n";
                continue;
            }

            // Get file content for comparison
            $thermalContent = file_get_contents(public_path('nota/' . $thermalFile));
            $invoiceContent = file_get_contents(public_path('nota/' . $invoiceFile));
        }

        // Step 4: Generate URLs that would be used for both links
        echo "\nURL Generation:\n";

        if ($usingNewSystem) {
            $thermalUrl = url('/pdf-storage/' . urlencode($thermalPath));
            $invoiceUrl = url('/pdf-storage/' . urlencode($invoicePath));
        } else {
            $thermalUrl = asset('nota/' . $thermalFile);
            $invoiceUrl = asset('nota/' . $invoiceFile);
        }

        echo "  Thermal URL: {$thermalUrl}\n";
        echo "  Invoice URL: {$invoiceUrl}\n";

        // Step 5: Verify that the paths are different
        echo "\nPath Comparison:\n";

        if ($usingNewSystem) {
            $pathsDifferent = $thermalPath !== $invoicePath;
            echo "  Paths are different: " . ($pathsDifferent ? "✓ Yes" : "❌ No") . "\n";
        } else {
            $filesDifferent = $thermalFile !== $invoiceFile;
            echo "  Filenames are different: " . ($filesDifferent ? "✓ Yes" : "❌ No") . "\n";
        }

        // Step 6: Test actual file content to confirm they are different PDFs
        echo "\nFile Content Comparison:\n";

        if ($thermalContent && $invoiceContent) {
            $thermalHash = md5($thermalContent);
            $invoiceHash = md5($invoiceContent);

            echo "  Thermal file MD5: {$thermalHash}\n";
            echo "  Invoice file MD5: {$invoiceHash}\n";

            $contentDifferent = $thermalHash !== $invoiceHash;
            echo "  Content is different: " . ($contentDifferent ? "✓ Yes" : "❌ No") . "\n";

            if ($contentDifferent) {
                $testResults['different_files']++;
                echo "  ✓ SUCCESS: Files are different\n";
            } else {
                $testResults['same_files']++;
                echo "  ❌ ISSUE: Files are identical!\n";
            }

            // Additional content analysis
            $thermalSize = strlen($thermalContent);
            $invoiceSize = strlen($invoiceContent);

            echo "  Thermal file size: " . number_format($thermalSize) . " bytes\n";
            echo "  Invoice file size: " . number_format($invoiceSize) . " bytes\n";

            // Check if files are valid PDFs
            $thermalIsPDF = strpos($thermalContent, '%PDF') === 0;
            $invoiceIsPDF = strpos($invoiceContent, '%PDF') === 0;

            echo "  Thermal is valid PDF: " . ($thermalIsPDF ? "✓ Yes" : "❌ No") . "\n";
            echo "  Invoice is valid PDF: " . ($invoiceIsPDF ? "✓ Yes" : "❌ No") . "\n";

        } else {
            echo "  ❌ Could not read file content for comparison\n";
            $testResults['errors']++;
        }

        echo "\n";
    }

    // Step 7: Output comprehensive test results
    echo "========================================\n";
    echo "COMPREHENSIVE TEST RESULTS\n";
    echo "========================================\n";

    echo "Total transactions tested: {$testResults['total_transactions_tested']}\n";
    echo "Valid pairs found: {$testResults['valid_pairs_found']}\n";
    echo "Different files: {$testResults['different_files']}\n";
    echo "Same files (ISSUE): {$testResults['same_files']}\n";
    echo "Files not found: {$testResults['file_not_found']}\n";
    echo "Errors: {$testResults['errors']}\n\n";

    // Calculate success rate
    if ($testResults['valid_pairs_found'] > 0) {
        $successRate = round(($testResults['different_files'] / $testResults['valid_pairs_found']) * 100, 2);
        echo "Success rate: {$successRate}%\n\n";
    }

    // Final verdict
    if ($testResults['same_files'] > 0) {
        echo "❌ CRITICAL ISSUE FOUND: Some Nota Satu and Nota Dua links point to the same file!\n";
        echo "   This needs immediate attention.\n\n";
    } elseif ($testResults['different_files'] > 0 && $testResults['same_files'] === 0) {
        echo "✅ SUCCESS: All tested Nota Satu and Nota Dua links point to different files!\n\n";
    } else {
        echo "⚠️  INCONCLUSIVE: Could not determine if files are different due to missing files or errors.\n\n";
    }

    // Recommendations
    echo "RECOMMENDATIONS:\n";
    if ($testResults['file_not_found'] > 0) {
        echo "  • Fix missing files ({$testResults['file_not_found']} files missing)\n";
    }
    if ($testResults['same_files'] > 0) {
        echo "  • Investigate why some transactions have identical PDF files\n";
        echo "  • Check PDF generation logic in TransactionsController\n";
    }
    if ($testResults['errors'] > 0) {
        echo "  • Fix errors in file processing ({$testResults['errors']} errors)\n";
    }
    if ($testResults['different_files'] > 0 && $testResults['same_files'] === 0) {
        echo "  • System is working correctly\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "Test completed at: " . now()->toDateTimeString() . "\n";
echo "========================================\n";

?>
