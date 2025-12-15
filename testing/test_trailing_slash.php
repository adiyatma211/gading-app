<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test URLs
$urlWithoutSlash = 'http://127.0.0.1:8000/pdf-storage/THERMAL-20251214-028-29.pdf';
$urlWithSlash = 'http://127.0.0.1:8000/pdf-storage/THERMAL-20251214-028-29.pdf/';

echo "Testing PDF access with and without trailing slash:\n";
echo "==================================================\n\n";

// Test without trailing slash
echo "1. Testing URL without trailing slash:\n";
echo "   URL: $urlWithoutSlash\n";
$ch = curl_init($urlWithoutSlash);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "   HTTP Status: $httpCode\n\n";

// Test with trailing slash
echo "2. Testing URL with trailing slash:\n";
echo "   URL: $urlWithSlash\n";
$ch = curl_init($urlWithSlash);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "   HTTP Status: $httpCode\n\n";

// Analyze the route pattern
echo "3. Route Analysis:\n";
echo "   The route in web.php is defined as: /pdf-storage/{path}\n";
echo "   With where constraint: ->where('path', '.*')\n";
echo "   This should match any character including slashes, but...\n\n";

echo "4. .htaccess Analysis:\n";
echo "   Lines 16-19 in .htaccess show:\n";
echo "   # Redirect Trailing Slashes If Not A Folder...\n";
echo "   RewriteCond %{REQUEST_FILENAME} !-d\n";
echo "   RewriteCond %{REQUEST_URI} (.+)/\$\n";
echo "   RewriteRule ^ %1 [L,R=301]\n\n";
echo "   This rule redirects URLs with trailing slashes to URLs without them,\n";
echo "   but only if the requested filename is not a directory.\n\n";

echo "5. The Problem:\n";
echo "   When a trailing slash is added to the PDF URL:\n";
echo "   - The .htaccess rule tries to redirect it (removing the slash)\n";
echo "   - But Laravel routing doesn't recognize the redirected URL properly\n";
echo "   - Because the route pattern expects a specific path format\n";
echo "   - The redirect changes the URL structure, causing a 404\n\n";
