# Debug and Test Files

This directory contains various test and debugging scripts used during the development and troubleshooting of the PDF storage system in the Gading application.

## File Descriptions

### 1. test_pdf_paths.php
**Purpose**: Tests PDF path storage in transactions table
- Checks if the required columns (pdf_storage_path, pdf_storage_path_invoice) exist in the transactions table
- Retrieves and displays the latest 5 transactions with their PDF path information
- Verifies file existence for local storage paths
- Provides summary statistics about PDF path usage

### 2. investigate_pdf_paths.php
**Purpose**: Investigates PDF path implementation and usage
- Displays the latest 5 transactions with all PDF-related fields
- Lists available methods in PDFStorageService
- Shows TransactionsController methods
- Examines the source code of the store method
- Checks for transactions with PDF paths stored

### 3. detailed_pdf_investigation.php
**Purpose**: Provides comprehensive investigation of PDF storage system
- Checks transactions table for PDF paths
- Examines PDFStorageMetadata table records
- Specifically looks for invoice PDFs in metadata
- Simulates PDFStorageService storePDF calls
- Verifies database schema for PDF-related columns

### 4. test_pdf_path_fix.php
**Purpose**: Tests the PDF path fix implementation
- Uses a sample transaction to test PDF storage
- Tests both thermal and invoice PDF storage
- Verifies that transaction records are updated correctly
- Checks PDFStorageMetadata table for stored records
- Validates path matching between returned values and database

### 5. test_unique_pdf_paths.php
**Purpose**: Tests that different PDF types get unique paths
- Creates unique PDF content for thermal and invoice types
- Tests storage of both PDF types for the same transaction
- Verifies that different file paths are generated
- Ensures metadata records are correctly created
- Validates path uniqueness between thermal and invoice PDFs

### 6. check_pdf_paths_tinker.php
**Purpose**: Quick Tinker script for checking PDF paths
- Designed to be run in Laravel Tinker
- Checks the latest transaction's PDF storage paths
- Displays PDFStorageMetadata records for the transaction
- Shows basic transaction details
- Useful for quick debugging during development

### 7. test_pdf_links_tinker.php
**Purpose**: Comprehensive test for PDF links functionality
- Tests that Nota Satu and Nota Dua links show different PDFs
- Validates file existence using PDFStorageService
- Compares file content using MD5 hashes
- Generates actual URLs used by the application
- Provides detailed test results and recommendations
- Supports both new PDF storage system and legacy file system

## Usage Instructions

### Running Standalone Scripts
For files that include Laravel bootstrap (test_pdf_paths.php, investigate_pdf_paths.php, detailed_pdf_investigation.php, test_pdf_path_fix.php, test_unique_pdf_paths.php):
```bash
php tests/debug/[filename].php
```

### Running Tinker Scripts
For Tinker scripts (check_pdf_paths_tinker.php, test_pdf_links_tinker.php):
```bash
php artisan tinker --execute="include 'tests/debug/[filename].php';"
```

Or run Tinker interactively:
```bash
php artisan tinker
```
Then copy and paste the script content.

## Important Notes

- These files are for development and debugging purposes only
- They should not be used in production environments
- Some scripts create dummy PDF content for testing
- Always backup your database before running test scripts that modify data
- The Tinker scripts are designed to be safe and read-only

## Related Documentation

- `PDF_Links_Test_Script_Documentation.md` - Detailed documentation for the PDF links test script
- `app/Services/PDFStorageService.php` - Main PDF storage service implementation
- `app/Http/Controllers/TransactionsController.php` - Controller handling PDF generation
