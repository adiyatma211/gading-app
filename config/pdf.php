<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PDF Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF storage system including compression settings,
    | storage disks, and file management options.
    |
    */

    // Default storage disk for PDF files
    'storage_disk' => env('PDF_STORAGE_DISK', 'pdf_storage'),

    // Archive storage disk for old PDF files
    'archive_disk' => env('PDF_ARCHIVE_DISK', 'pdf_archive'),

    // Public storage disk for accessible PDF files
    'public_disk' => env('PDF_PUBLIC_DISK', 'pdf_public'),

    /*
    |--------------------------------------------------------------------------
    | Compression Settings
    |--------------------------------------------------------------------------
    |
    | Settings for PDF compression using Ghostscript.
    | Available quality presets: screen, ebook, printer, prepress, default
    |
    */

    'compression' => [
        'enabled' => env('PDF_COMPRESSION_ENABLED', true),
        'quality' => env('PDF_COMPRESSION_QUALITY', 'ebook'), // screen, ebook, printer, prepress, default
        'resolution' => env('PDF_COMPRESSION_RESOLUTION', 150), // DPI
        'downsample_images' => env('PDF_COMPRESSION_DOWNSAMPLE_IMAGES', true),
        'auto_compress' => env('PDF_AUTO_COMPRESS', false), // Auto-compress on upload
        'batch_compress_delay' => env('PDF_BATCH_COMPRESS_DELAY', 7), // Days before batch compression
    ],

    /*
    |--------------------------------------------------------------------------
    | Ghostscript Configuration
    |--------------------------------------------------------------------------
    |
    | Path to Ghostscript executable and related settings.
    |
    */

    'ghostscript' => [
        'path' => env('GHOSTSCRIPT_PATH', 'gs'), // 'gs' for Linux/Mac, 'gswin64c.exe' for Windows 64-bit
        'timeout' => env('GHOSTSCRIPT_TIMEOUT', 60), // Seconds
        'memory_limit' => env('GHOSTSCRIPT_MEMORY_LIMIT', '256m'), // Memory limit
    ],

    /*
    |--------------------------------------------------------------------------
    | Archiving Settings
    |--------------------------------------------------------------------------
    |
    | Settings for automatic archiving of old PDF files.
    |
    */

    'archiving' => [
        'enabled' => env('PDF_ARCHIVING_ENABLED', true),
        'archive_after_years' => env('PDF_ARCHIVE_AFTER_YEARS', 2),
        'auto_archive' => env('PDF_AUTO_ARCHIVE', false), // Auto-archive old files
        'delete_after_archive' => env('PDF_DELETE_AFTER_ARCHIVE', false), // Delete from main storage after archiving
    ],

    /*
    |--------------------------------------------------------------------------
    | File Naming Convention
    |--------------------------------------------------------------------------
    |
    | Pattern for PDF file naming.
    | Available variables: {TYPE}, {YYYYMMDD}, {SEQ}, {TXID}, {UUID}
    |
    */

    'naming' => [
        'pattern' => env('PDF_NAMING_PATTERN', '{TYPE}-{YYYYMMDD}-{SEQ}-{TXID}.pdf'),
        'uppercase_type' => env('PDF_UPPERCASE_TYPE', true),
        'pad_sequence' => env('PDF_PAD_SEQUENCE', 3), // Pad sequence number with zeros
    ],

    /*
    |--------------------------------------------------------------------------
    | Directory Structure
    |--------------------------------------------------------------------------
    |
    | Hierarchical directory structure for PDF organization.
    | Available variables: {YYYY}, {MM}, {DD}, {TYPE}
    |
    */

    'directory_structure' => [
        'pattern' => env('PDF_DIRECTORY_PATTERN', '{YYYY}/{MM}/{DD}/{TYPE}/'),
        'create_directories' => env('PDF_CREATE_DIRECTORIES', true),
        'directory_permissions' => env('PDF_DIRECTORY_PERMISSIONS', 0755),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Validation
    |--------------------------------------------------------------------------
    |
    | Validation rules for uploaded PDF files.
    |
    */

    'validation' => [
        'max_file_size' => env('PDF_MAX_FILE_SIZE', '10MB'), // Maximum file size
        'min_file_size' => env('PDF_MIN_FILE_SIZE', '1KB'), // Minimum file size
        'allowed_mime_types' => [
            'application/pdf',
        ],
        'scan_for_malware' => env('PDF_SCAN_MALWARE', false), // Enable malware scanning
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Settings to optimize PDF processing performance.
    |
    */

    'performance' => [
        'chunk_size' => env('PDF_CHUNK_SIZE', 50), // Chunk size for batch operations
        'max_concurrent_processes' => env('PDF_MAX_CONCURRENT_PROCESSES', 2), // Max concurrent compression processes
        'cache_metadata' => env('PDF_CACHE_METADATA', true), // Cache file metadata
        'cache_ttl' => env('PDF_CACHE_TTL', 3600), // Cache TTL in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Alerts
    |--------------------------------------------------------------------------
    |
    | Settings for monitoring PDF storage and sending alerts.
    |
    */

    'monitoring' => [
        'enabled' => env('PDF_MONITORING_ENABLED', true),
        'disk_usage_threshold' => env('PDF_DISK_USAGE_THRESHOLD', 80), // Alert when disk usage exceeds this percentage
        'large_file_threshold' => env('PDF_LARGE_FILE_THRESHOLD', '5MB'), // Alert for files larger than this
        'failed_operation_threshold' => env('PDF_FAILED_OPERATION_THRESHOLD', 5), // Alert after this many failed operations
        'alert_email' => env('PDF_ALERT_EMAIL', null), // Email address for alerts
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related settings for PDF handling.
    |
    */

    'security' => [
        'encrypt_storage' => env('PDF_ENCRYPT_STORAGE', false), // Encrypt stored PDF files
        'access_logging' => env('PDF_ACCESS_LOGGING', true), // Log file access
        'ip_whitelist' => env('PDF_IP_WHITELIST', null), // Comma-separated list of allowed IPs
        'rate_limit' => env('PDF_RATE_LIMIT', '60:1'), // Rate limit for PDF access (requests:minute)
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Settings for third-party integrations.
    |
    */

    'integrations' => [
        'ocr_enabled' => env('PDF_OCR_ENABLED', false), // Enable OCR processing
        'ocr_language' => env('PDF_OCR_LANGUAGE', 'eng'), // OCR language
        'watermark_enabled' => env('PDF_WATERMARK_ENABLED', false), // Enable watermarking
        'watermark_text' => env('PDF_WATERMARK_TEXT', 'GADING PRINT'),
        'backup_enabled' => env('PDF_BACKUP_ENABLED', false), // Enable backup to cloud storage
        'backup_disk' => env('PDF_BACKUP_DISK', 's3'), // Backup storage disk
    ],
];
