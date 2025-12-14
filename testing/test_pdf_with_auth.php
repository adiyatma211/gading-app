<?php
require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test parameters
$testPath = '2025/12/14/THERMAL/THERMAL-20251214-028-29.pdf';
$url = '/pdf-storage/' . $testPath;

echo "=== Testing PDF Access with Authentication ===\n\n";

// 1. Test the regex pattern
$pattern = '/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/';
$patternMatch = preg_match($pattern, $testPath);
echo "Path pattern validation: " . ($patternMatch ? "VALID" : "INVALID") . "\n";
echo "Test path: $testPath\n\n";

// 2. Test file existence through storage service
$storageService = app(\App\Services\PDFStorageService::class);
$fileExists = $storageService->fileExists($testPath);
echo "File exists in storage: " . ($fileExists ? "YES" : "NO") . "\n";

if ($fileExists) {
    try {
        $fileContent = $storageService->getPDF($testPath);
        $fileSize = strlen($fileContent);
        echo "File size: $fileSize bytes\n";
    } catch (Exception $e) {
        echo "Error reading file: " . $e->getMessage() . "\n";
    }
}

// 3. Test route simulation
echo "\n=== Simulating Route Request ===\n";
try {
    // Create a request to the PDF route
    $request = Illuminate\Http\Request::create($url, 'GET');

    // Mock authentication by creating a user
    $user = new App\Models\User();
    $user->id = 1;
    $user->name = 'Test User';
    $user->email = 'test@example.com';

    // Authenticate the user for this request
    Illuminate\Support\Facades\Auth::login($user);

    echo "User authenticated: YES\n";
    echo "Request URL: $url\n";

    // Try to get the response from the route
    $response = $app->handle($request);

    echo "Response status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() === 200) {
        echo "PDF access: SUCCESS\n";
        echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
        echo "Content-Disposition: " . $response->headers->get('Content-Disposition') . "\n";
    } else {
        echo "PDF access: FAILED\n";
        echo "Response content: " . $response->getContent() . "\n";
    }

} catch (Exception $e) {
    echo "Route simulation error: " . $e->getMessage() . "\n";
}

// 4. Test lowercase version
echo "\n=== Testing Lowercase Version ===\n";
$lowercasePath = '2025/12/14/thermal/THERMAL-20251214-028-29.pdf';
$lowercaseMatch = preg_match($pattern, $lowercasePath);
echo "Lowercase path pattern validation: " . ($lowercaseMatch ? "VALID" : "INVALID") . "\n";
echo "Lowercase test path: $lowercasePath\n";

echo "\n=== Test Complete ===\n";
