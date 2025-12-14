<?php
// Test the trailing slash issue directly
echo "Testing trailing slash issue for PDF routes:\n";
echo "==========================================\n\n";

// Simulate the route behavior
function simulateRoute($path) {
    echo "Testing path: '$path'\n";

    // Decode path (as done in the route)
    $decodedPath = urldecode($path);
    echo "  After urldecode: '$decodedPath'\n";

    // Security check (as done in the route)
    $pattern = '/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/(thermal|invoice|THERMAL|INVOICE)\/[^\/]+\.pdf$/';
    $securityCheck = preg_match($pattern, $decodedPath);
    echo "  Security check: " . ($securityCheck ? "PASS" : "FAIL") . "\n";

    if (!$securityCheck) {
        echo "  Result: 403 Forbidden (Security check failed)\n";
        return false;
    }

    // Check if trailing slash affects the pattern
    if (substr($decodedPath, -1) === '/') {
        echo "  Issue: Path ends with slash, pattern won't match\n";
        echo "  Result: 404 Not Found (Pattern mismatch)\n";
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
];

foreach ($testCases as $testCase) {
    simulateRoute($testCase);
    echo "\n";
}

echo "Analysis:\n";
echo "=========\n";
echo "1. The route pattern expects: YYYY/MM/DD/(thermal|invoice)/filename.pdf\n";
echo "2. The pattern ends with \\.pdf$ which means it must end with .pdf\n";
echo "3. When a trailing slash is added, the pattern doesn't match because:\n";
echo "   - The path no longer ends with .pdf (it ends with /)\n";
echo "   - The regex pattern [^\\/]+\\.pdf$ fails to match\n";
echo "4. This causes Laravel to return 404 Not Found\n\n";

echo "The .htaccess rule:\n";
echo "RewriteCond %{REQUEST_FILENAME} !-d\n";
echo "RewriteCond %{REQUEST_URI} (.+)/\\$\n";
echo "RewriteRule ^ %1 [L,R=301]\n\n";
echo "This rule should redirect URLs with trailing slashes to remove them,\n";
echo "but it seems the redirect isn't working properly for this specific route.\n\n";

echo "Root Cause:\n";
echo "==========\n";
echo "The issue is that the route pattern is too strict and doesn't account\n";
echo "for optional trailing slashes. The regex pattern requires the path to\n";
echo "end exactly with .pdf, not .pdf/\n\n";

echo "Solutions:\n";
echo "==========\n";
echo "1. Modify the route pattern to accept optional trailing slash\n";
echo "2. Create a separate route to handle trailing slashes\n";
echo "3. Fix the .htaccess redirect rule\n";
