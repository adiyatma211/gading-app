# PDF Links Test Script Documentation

## Overview

The `tests/debug/test_pdf_links_tinker.php` script is a comprehensive PHP Tinker script designed to test that both Nota Satu and Nota Dua links show different PDFs in the Gading application.

## Purpose

This script verifies that:
1. Transactions have both PDF paths (thermal and invoice)
2. Both PDF files exist in the storage system
3. The generated URLs are correct
4. The file paths are different
5. The actual file content is different (different PDFs)
6. Files are valid PDFs

## Features

### Comprehensive Testing
- Tests both new PDF storage system and legacy file system
- Validates file existence using PDFStorageService
- Compares file content using MD5 hashes
- Generates actual URLs that would be used in the application
- Provides detailed output for each test case

### Error Handling
- Gracefully handles missing files
- Provides clear error messages
- Tracks different types of failures separately
- Offers actionable recommendations

### Reporting
- Detailed test results for each transaction
- Summary statistics with success rate
- Clear pass/fail indicators
- Recommendations based on findings

## Usage

### Method 1: Direct Tinker Execution
```bash
php artisan tinker --execute="include 'tests/debug/test_pdf_links_tinker.php';"
```

### Method 2: Copy-Paste into Tinker
1. Open Laravel Tinker: `php artisan tinker`
2. Copy and paste the entire script content
3. Press Enter to execute

## Script Flow

1. **Initialization**: Sets up counters and initializes PDFStorageService
2. **Query Transactions**: Finds transactions with both PDF paths
3. **File Existence Check**: Verifies files exist in storage
4. **URL Generation**: Creates the actual URLs used by the application
5. **Path Comparison**: Confirms paths are different
6. **Content Comparison**: Uses MD5 hashes to verify files are different
7. **Validation**: Checks if files are valid PDFs
8. **Reporting**: Provides comprehensive results and recommendations

## Test Results

The script provides the following metrics:

- **Total transactions tested**: Number of transactions processed
- **Valid pairs found**: Transactions with both PDF paths
- **Different files**: Files that are correctly different
- **Same files (ISSUE)**: Files that are identical (problem)
- **Files not found**: Missing files
- **Errors**: Processing errors
- **Success rate**: Percentage of correctly different files

## Example Output

```
========================================
PDF Links Test Script for Nota Satu & Dua
========================================

✓ PDF Storage Service initialized

Step 1: Finding transactions with both PDF paths...
Found 1 transactions with both PDF paths

----------------------------------------
Testing Transaction ID: 1
Nomor Faktur: GD-MMT-01-20251210
Created: 2025-12-10 16:59:02

Paths:
  Thermal (Nota Satu): 2025/12/10/THERMAL/THERMAL-20251210-003-1.pdf
  Invoice (Nota Dua):  2025/12/10/INVOICE/INVOICE-20251210-002-1.pdf

File Existence Check:
  Thermal file exists: ✓ Yes
  Invoice file exists: ✓ Yes

URL Generation:
  Thermal URL: http://localhost/pdf-storage/2025%2F12%2F10%2FTHERMAL%2FTHERMAL-20251210-003-1.pdf
  Invoice URL: http://localhost/pdf-storage/2025%2F12%2F10%2FINVOICE%2FINVOICE-20251210-002-1.pdf

Path Comparison:
  Paths are different: ✓ Yes

File Content Comparison:
  Thermal file MD5: 8aba43d43b41a32122a3845cd807d234
  Invoice file MD5: 823185ddee369fa289c18d7aec65fff1
  Content is different: ✓ Yes
  ✓ SUCCESS: Files are different
  Thermal file size: 455 bytes
  Invoice file size: 456 bytes
  Thermal is valid PDF: ✓ Yes
  Invoice is valid PDF: ✓ Yes

========================================
COMPREHENSIVE TEST RESULTS
========================================
Total transactions tested: 1
Valid pairs found: 1
Different files: 1
Same files (ISSUE): 0
Files not found: 0
Errors: 0

Success rate: 100%

✅ SUCCESS: All tested Nota Satu and Nota Dua links point to different files!

RECOMMENDATIONS:
  • System is working correctly
```

## Technical Details

### File Systems Supported
1. **New System**: Uses PDFStorageService with hierarchical storage
2. **Legacy System**: Uses public/nota/ directory

### URL Generation
- New System: `/pdf-storage/{encoded_path}`
- Legacy System: `/nota/{filename}`

### Validation Methods
- **File Existence**: PDFStorageService::fileExists() or file_exists()
- **Content Comparison**: MD5 hash comparison
- **PDF Validation**: Checks for `%PDF` header

## Troubleshooting

### Common Issues

1. **No transactions found**
   - Check if transactions have both pdf_storage_path and pdf_storage_path_invoice
   - Verify PDF generation is working correctly

2. **Files not found**
   - Check storage configuration
   - Verify PDFStorageService is working
   - Check file permissions

3. **Same files detected**
   - Investigate PDF generation logic in TransactionsController
   - Check if thermal and invoice views are different

### Debugging Tips

1. **Check individual transactions**:
   ```php
   $transaction = \App\Models\transactions::find(1);
   dd($transaction->pdf_storage_path, $transaction->pdf_storage_path_invoice);
   ```

2. **Verify storage service**:
   ```php
   $service = app(\App\Services\PDFStorageService::class);
   $exists = $service->fileExists('path/to/file.pdf');
   ```

3. **Check file content directly**:
   ```php
   $content = $service->getPDF('path/to/file.pdf');
   echo substr($content, 0, 100); // First 100 characters
   ```

## Integration with Application

This script complements the existing PDF system:

- **PDFStorageService**: Used for file operations
- **TransactionsController**: Generates the PDFs being tested
- **Routes**: Tests the actual URLs used by the application
- **Views**: Validates the PDF generation process

## Future Enhancements

Potential improvements to the script:

1. **Batch Testing**: Test all transactions, not just recent ones
2. **Performance Metrics**: Add timing information
3. **Visual Comparison**: Include PDF preview options
4. **Automated Reporting**: Save results to database or file
5. **Scheduled Testing**: Integrate with Laravel scheduler

## Conclusion

This script provides a robust method to verify that the Nota Satu and Nota Dua functionality is working correctly, ensuring users receive different PDF files as intended. It's designed to be run regularly as part of system maintenance or after making changes to the PDF generation system.
