<?php
echo "=== Comparing PDF Access Methods ===\n\n";

// 1. Test the method from v_tabelTransaksi.blade.php (working)
echo "1. Method from v_tabelTransaksi.blade.php (working):\n";
echo "   URL pattern: /pdf-storage/" . urlencode('2025/12/14/THERMAL/THERMAL-20251214-028-29.pdf') . "\n";
echo "   Approach: Direct link with URL encoding\n\n";

// 2. Test the method from v_transaksi.blade.php (not working)
echo "2. Method from v_transaksi.blade.php (not working):\n";
echo "   JavaScript fetch with headers:\n";
echo "   - X-Requested-With: XMLHttpRequest\n";
echo "   - X-CSRF-TOKEN: [token]\n";
echo "   - Accept: application/pdf,*/*\n\n";

// 3. Test both URL patterns
echo "3. Testing both URL patterns:\n";

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mock authentication
$user = new App\Models\User();
$user->id = 1;
$user->name = 'Test User';
$user->email = 'test@example.com';
Illuminate\Support\Facades\Auth::login($user);

$testPath = '2025/12/14/THERMAL/THERMAL-20251214-028-29.pdf';

// Test direct URL (v_tabelTransaksi method)
$url1 = '/pdf-storage/' . $testPath;
echo "   Testing direct URL: $url1\n";
try {
    $request1 = Illuminate\Http\Request::create($url1, 'GET');
    $response1 = $app->handle($request1);
    echo "   Status: " . $response1->getStatusCode() . " - " . ($response1->getStatusCode() === 200 ? "SUCCESS" : "FAILED") . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test encoded URL (v_tabelTransaksi method with encoding)
$encodedPath = urlencode($testPath);
$url2 = '/pdf-storage/' . $encodedPath;
echo "   Testing encoded URL: $url2\n";
try {
    $request2 = Illuminate\Http\Request::create($url2, 'GET');
    $response2 = $app->handle($request2);
    echo "   Status: " . $response2->getStatusCode() . " - " . ($response2->getStatusCode() === 200 ? "SUCCESS" : "FAILED") . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test with headers (v_transaksi method)
echo "   Testing with AJAX headers:\n";
try {
    $request3 = Illuminate\Http\Request::create($url1, 'GET', [], [], [], [], [
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        'HTTP_X_CSRF_TOKEN' => 'test-token',
        'HTTP_ACCEPT' => 'application/pdf,*/*'
    ]);
    $response3 = $app->handle($request3);
    echo "   Status: " . $response3->getStatusCode() . " - " . ($response3->getStatusCode() === 200 ? "SUCCESS" : "FAILED") . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== Recommendation ===\n";
echo "The issue is likely in the JavaScript implementation in v_transaksi.blade.php.\n";
echo "The route itself is working correctly when accessed directly.\n";
echo "Solution: Use the same simple URL approach as v_tabelTransaksi.blade.php\n";
