<?php

// This script will be executed within PHP Tinker
// Run these commands one by one in PHP Tinker

echo "=== Checking latest transaction ===\n";
$latest = App\Models\transactions::latest()->first();
echo "Latest transaction ID: " . $latest->id . "\n";

echo "\n=== Checking PDF storage paths ===\n";
echo "PDF Storage Path: " . ($latest->pdf_storage_path ?? 'NULL') . "\n";
echo "PDF Storage Path Invoice: " . ($latest->pdf_storage_path_invoice ?? 'NULL') . "\n";

echo "\n=== Checking PDF Storage Metadata ===\n";
$metadata = App\Models\PdfStorageMetadata::where('pdfable_id', $latest->id)->get();
echo "Found " . $metadata->count() . " metadata records\n";

foreach ($metadata as $meta) {
    echo "Type: " . $meta->pdf_type . ", Path: " . $meta->file_path . "\n";
}

echo "\n=== Transaction details ===\n";
echo "ID: " . $latest->id . "\n";
echo "Nomor Faktur: " . $latest->nomor_faktur . "\n";
echo "Created At: " . $latest->created_at . "\n";

?>
