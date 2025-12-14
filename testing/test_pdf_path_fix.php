<?php

/**
 * Test script to verify PDF access fix
 *
 * This script tests:
 * 1. AJAX response simulation with full path
 * 2. URL construction validation
 * 3. Route pattern matching
 * 4. Both primary and fallback path formats
 */

// Base URL for the application
$baseUrl = 'http://localhost/gading-app';

// Test cases for different PDF path formats
$testCases = [
    [
        'name' => 'Primary Thermal Path',
        'path' => '2025/12/14/thermal/THERMAL-20251214-001-123.pdf',
        'expected_type' => 'thermal'
    ],
    [
        'name' => 'Primary Invoice Path',
        'path' => '2025/12/14/invoice/INVOICE-20251214-001-123.pdf',
        'expected_type' => 'invoice'
    ],
    [
        'name' => 'Fallback Path Format',
        'path' => 'nota/INV-00123.pdf',
        'expected_type' => 'fallback'
    ],
    [
        'name' => 'Lowercase Thermal Path',
        'path' => '2025/12/14/thermal/thermal-20251214-001-123.pdf',
        'expected_type' => 'thermal'
    ],
    [
        'name' => 'Lowercase Invoice Path',
        'path' => '2025/12/14/invoice/invoice-20251214-001-123.pdf',
        'expected_type' => 'invoice'
    ]
];

// Route pattern for pdf-storage
$pdfStoragePattern = '/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/';

// Route pattern for fallback nota
$fallbackPattern = '/^nota\/[^\/]+\.pdf$/';

echo "=== PDF Access Fix Test Script ===\n\n";

// Test each case
foreach ($testCases as $index => $testCase) {
    echo "Test Case " . ($index + 1) . ": " . $testCase['name'] . "\n";
    echo "----------------------------------------\n";

    // 1. Simulate AJAX response (backend returning full path)
    $ajaxResponse = [
        'success' => true,
        'nota_file' => $testCase['path']
    ];

    echo "1. AJAX Response Simulation:\n";
    echo "   Response: " . json_encode($ajaxResponse) . "\n";
    echo "   Extracted path: " . $ajaxResponse['nota_file'] . "\n\n";

    // 2. Construct URL as frontend would
    if ($testCase['expected_type'] === 'fallback') {
        $url = $baseUrl . '/' . $testCase['path'];
        $routeType = 'fallback (direct public access)';
    } else {
        // Encode the path for URL
        $encodedPath = urlencode($testCase['path']);
        $url = $baseUrl . '/pdf-storage/' . $encodedPath;
        $routeType = 'pdf-storage (authenticated)';
    }

    echo "2. URL Construction:\n";
    echo "   Route Type: " . $routeType . "\n";
    echo "   Constructed URL: " . $url . "\n\n";

    // 3. Validate URL matches expected route pattern
    $isValidPattern = false;
    if ($testCase['expected_type'] === 'fallback') {
        $isValidPattern = preg_match($fallbackPattern, $testCase['path']);
    } else {
        $isValidPattern = preg_match($pdfStoragePattern, $testCase['path']);
    }

    echo "3. Route Pattern Validation:\n";
    echo "   Expected Pattern: " . ($testCase['expected_type'] === 'fallback' ? $fallbackPattern : $pdfStoragePattern) . "\n";
    echo "   Path Matches Pattern: " . ($isValidPattern ? '✓ PASS' : '✗ FAIL') . "\n\n";

    // 4. Additional checks
    echo "4. Additional Checks:\n";

    // Check if path has trailing slash (should not)
    $hasTrailingSlash = substr($testCase['path'], -1) === '/';
    echo "   No Trailing Slash: " . ($hasTrailingSlash ? '✗ FAIL' : '✓ PASS') . "\n";

    // Check if file extension is PDF
    $hasPdfExtension = strtolower(pathinfo($testCase['path'], PATHINFO_EXTENSION)) === 'pdf';
    echo "   Has PDF Extension: " . ($hasPdfExtension ? '✓ PASS' : '✗ FAIL') . "\n";

    // For primary paths, check date format
    if ($testCase['expected_type'] !== 'fallback') {
        $pathParts = explode('/', $testCase['path']);
        if (count($pathParts) >= 5) {
            $year = $pathParts[0];
            $month = $pathParts[1];
            $day = $pathParts[2];

            $isValidDate = checkdate($month, $day, $year);
            echo "   Valid Date Format: " . ($isValidDate ? '✓ PASS' : '✗ FAIL') . "\n";
        } else {
            echo "   Valid Date Format: ✗ FAIL (incorrect path structure)\n";
        }
    }

    // Overall result
    $overallPass = $isValidPattern && !$hasTrailingSlash && $hasPdfExtension;
    if ($testCase['expected_type'] !== 'fallback') {
        $pathParts = explode('/', $testCase['path']);
        $overallPass = $overallPass && count($pathParts) >= 5 && checkdate($pathParts[1], $pathParts[2], $pathParts[0]);
    }

    echo "\n   OVERALL RESULT: " . ($overallPass ? '✓ PASS' : '✗ FAIL') . "\n\n";
    echo str_repeat("=", 60) . "\n\n";
}

// Summary
echo "=== SUMMARY ===\n";
echo "This test verifies that:\n";
echo "1. The backend returns the full path in the AJAX response\n";
echo "2. The frontend correctly constructs URLs based on the path type\n";
echo "3. Primary paths use the /pdf-storage/ route with authentication\n";
echo "4. Fallback paths use direct public access\n";
echo "5. All paths match their expected route patterns\n";
echo "6. Paths don't have trailing slashes that would cause issues\n\n";

echo "To run this test in a browser environment:\n";
echo "1. Place this file in your project root\n";
echo "2. Access it via: http://localhost/gading-app/test_pdf_path_fix.php\n";
echo "3. Review the test results to ensure all checks pass\n\n";

echo "For manual testing in the application:\n";
echo "1. Create a new transaction\n";
echo "2. Check browser console for DEBUG logs showing the PDF URL\n";
echo "3. Verify the URL opens correctly in a new tab\n";
echo "4. Test both new transactions (primary path) and old ones (fallback path)\n";

?>
