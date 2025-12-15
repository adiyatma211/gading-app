<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Create a test user session
$user = App\Models\User::first();
if ($user) {
    // Simulate authentication
    Illuminate\Support\Facades\Auth::login($user);

    $path = '2025/12/14/THERMAL/THERMAL-20251214-028-29.pdf';

    // Test the regex pattern from the route
    $pattern = '/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/';
    $isPatternValid = preg_match($pattern, $path);
    echo "Path pattern validation: " . ($isPatternValid ? "VALID" : "INVALID") . PHP_EOL;

    // Test file existence
    $storageService = app(\App\Services\PDFStorageService::class);
    $fileExists = $storageService->fileExists($path);
    echo "File exists: " . ($fileExists ? "YES" : "NO") . PHP_EOL;

    if ($fileExists) {
        // Try to get the file content
        $fileContent = $storageService->getPDF($path);
        if ($fileContent) {
            echo "File size: " . strlen($fileContent) . " bytes" . PHP_EOL;
            echo "PDF access test: SUCCESS" . PHP_EOL;
        } else {
            echo "PDF access test: FAILED - Could not read file content" . PHP_EOL;
        }
    } else {
        echo "PDF access test: FAILED - File not found" . PHP_EOL;
    }

    // Test the old lowercase pattern
    $lowercasePath = strtolower($path);
    $isOldPatternValid = preg_match('/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice)\/[^\/]+\.pdf$/', $lowercasePath);
    echo "Old pattern (lowercase only) validation: " . ($isOldPatternValid ? "VALID" : "INVALID") . PHP_EOL;

} else {
    echo "No users found in the system" . PHP_EOL;
}
