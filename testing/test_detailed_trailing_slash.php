<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test URLs
$urlWithoutSlash = 'http://127.0.0.1:8000/pdf-storage/THERMAL-20251214-028-29.pdf';
$urlWithSlash = 'http://127.0.0.1:8000/pdf-storage/THERMAL-20251214-028-29.pdf/';

function testUrl($url, $description) {
    echo "Testing $description:\n";
    echo "URL: $url\n";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_NOBODY, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    $redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);

    curl_close($ch);

    echo "Final HTTP Status: $httpCode\n";
    echo "Redirect Count: $redirectCount\n";
    if ($redirectUrl) {
        echo "Redirected to: $redirectUrl\n";
    }

    // Parse response headers
    list($headers, $body) = explode("\r\n\r\n", $response, 2);
    $headerLines = explode("\r\n", $headers);

    foreach ($headerLines as $line) {
        if (strpos($line, 'Location:') === 0) {
            echo "Location header: " . trim(substr($line, 9)) . "\n";
        }
    }

    echo "\n";
    return ['httpCode' => $httpCode, 'redirectUrl' => $redirectUrl, 'redirectCount' => $redirectCount];
}

echo "Detailed analysis of PDF URL with and without trailing slash:\n";
echo "==========================================================\n\n";

$result1 = testUrl($urlWithoutSlash, "URL without trailing slash");
$result2 = testUrl($urlWithSlash, "URL with trailing slash");

// Check if the server is running
echo "Server Status Check:\n";
$ch = curl_init('http://127.0.0.1:8000/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "Server root HTTP Status: $httpCode\n\n";

if ($httpCode == 0) {
    echo "NOTE: The Laravel server doesn't appear to be running on http://127.0.0.1:8000\n";
    echo "Please start the server with: php artisan serve\n";
}
