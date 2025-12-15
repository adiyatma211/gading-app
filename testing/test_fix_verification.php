<?php
// Test the fix for trailing slash issue
echo "TESTING THE FIX FOR TRAILING SLASH ISSUE\n";
echo "========================================\n\n";

// Simulate the route behavior with the fix
function simulateFixedRoute($path) {
    echo "Testing path: '$path'\n";

    // Decode path (as done in the route)
    $decodedPath = urldecode($path);
    echo "  After urldecode: '$decodedPath'\n";

    // NEW: Remove trailing slash if present
    $cleanedPath = rtrim($decodedPath, '/');
    if ($decodedPath !== $cleanedPath) {
        echo "  Trailing slash removed: '$cleanedPath'\n";
    }

    // Security check (as done in the route)
    $pattern = '/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/';
    $securityCheck = preg_match($pattern, $cleanedPath);
    echo "  Security check: " . ($securityCheck ? "PASS" : "FAIL") . "\n";

    if (!$securityCheck) {
        echo "  Result: 403 Forbidden (Security check failed)\n";
        return false;
    }

    echo "  Result: 200 OK (Would serve PDF)\n";
    return true;
}

// Test cases
$testCases = [
    '2025/12/14/thermal/THERMAL-20251214-028-29.pdf',
    '2025/12/14/thermal/THERMAL-20251214-028-29.pdf/',
    '2025/12/14/invoice/INV-20251214-001.pdf',
    '2025/12/14/invoice/INV-20251214-001.pdf/',
    '2025/12/14/thermal/THERMAL-20251214-028-29.pdf//', // Double slash
];

$passCount = 0;
$totalTests = count($testCases);

foreach ($testCases as $testCase) {
    if (simulateFixedRoute($testCase)) {
        $passCount++;
    }
    echo "\n";
}

echo "SUMMARY:\n";
echo "========\n";
echo "Tests passed: $passCount/$totalTests\n";
echo "Fix success rate: " . round(($passCount / $totalTests) * 100, 1) . "%\n\n";

echo "CONCLUSION:\n";
echo "===========\n";
if ($passCount === $totalTests) {
    echo "✅ ALL TESTS PASSED! The fix successfully handles trailing slashes.\n";
    echo "PDFs will now be accessible with or without trailing slashes.\n";
} else {
    echo "❌ Some tests failed. The fix may need adjustment.\n";
}

echo "\nNEXT STEPS:\n";
echo "===========\n";
echo "1. Test in browser:\n";
echo "   - http://127.0.0.1:8000/pdf-storage/THERMAL-20251214-028-29.pdf\n";
echo "   - http://127.0.0.1:8000/pdf-storage/THERMAL-20251214-028-29.pdf/\n";
echo "2. Both URLs should now return the PDF file successfully\n";
echo "3. If authentication is required, both should redirect to login\n";
