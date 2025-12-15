# Dokumentasi Sistem PDF Storage - Aplikasi Kasir Gading

## Daftar Dokumentasi

Berikut adalah daftar lengkap dokumentasi untuk sistem PDF storage yang telah diimplementasikan:

### 📋 1. Desain Sistem
**File**: [`pdf_storage_system_design.md`](pdf_storage_system_design.md)
- Arsitektur sistem PDF storage
- Struktur database dan relasi
- Konvensi penamaan file
- Alur kerja sistem

### 📋 2. Analisis Migrasi Database
**File**: [`database_migration_analysis.md`](database_migration_analysis.md)
- Analisis perbandingan database produksi vs development
- Identifikasi masalah migrasi
- Rekomendasi perbaikan

### 📋 3. Rencana Aksi Resolusi Masalah
**File**: [`pdf_problem_resolution_action_plan.md`](pdf_problem_resolution_action_plan.md)
- Langkah-langkah penyelesaian masalah
- Prioritas perbaikan
- Timeline implementasi

### 📋 4. Dokumentasi Sistem Lengkap
**File**: [`pdf_storage_documentation.md`](pdf_storage_documentation.md)
- Dokumentasi lengkap implementasi sistem
- Struktur database terbaru
- API commands dan penggunaan
- Konfigurasi sistem
- Kredensial user

### 📋 5. Panduan Implementasi
**File**: [`implementation_guide.md`](implementation_guide.md)
- Panduan langkah demi langkah implementasi
- Best practices dan rekomendasi
- Troubleshooting umum

### 📋 6. Sistem Monitoring & Maintenance
**File**: [`monitoring_maintenance_system.md`](monitoring_maintenance_system.md)
- Strategi monitoring sistem
- Jadwal maintenance
- Alerting dan notifikasi
- Performance metrics

## 🚀 Quick Start Guide

### Setup Awal
```bash
# 1. Fresh install database
php artisan migrate:fresh --seed

# 2. Setup PDF storage directories
php artisan pdf:setup-storage --test

# 3. Test system integration
php artisan pdf:stats --detailed
```

### User Credentials Default
| Role | Username | Password |
|-------|----------|----------|
| Admin | admin | admin123 |
| Kasir | kasir | kasir123 |
| Operator | operator | operator123 |

## 📚 Struktur Kode

### Commands
- [`SetupPdfStorageCommand`](../app/Console/Commands/SetupPdfStorageCommand.php)
- [`MigratePdfFilesCommand`](../app/Console/Commands/MigratePdfFilesCommand.php)
- [`PdfCompressCommand`](../app/Console/Commands/PdfCompressCommand.php)
- [`PdfArchiveCommand`](../app/Console/Commands/PdfArchiveCommand.php)
- [`PdfStorageStatsCommand`](../app/Console/Commands/PdfStorageStatsCommand.php)

### Services
- [`PDFStorageService`](../app/Services/PDFStorageService.php)
- [`PDFCompressionService`](../app/Services/PDFCompressionService.php)

### Models
- [`PdfStorageMetadata`](../app/Models/PdfStorageMetadata.php)

### Migrations
- [`000001_add_pdf_storage_fields_to_transactions_table`](../database/migrations/2025_12_10_000001_add_pdf_storage_fields_to_transactions_table.php)
- [`000002_create_pdf_storage_metadata_table`](../database/migrations/2025_12_10_000002_create_pdf_storage_metadata_table.php)
- [`000003_create_missing_production_tables`](../database/migrations/2025_12_10_000003_create_missing_production_tables.php)

### Seeders
- [`UsersSeeder`](../database/seeders/UsersSeeder.php)
- [`RolesSeeder`](../database/seeders/RolesSeeder.php)

### Controllers
- [`TransactionsController`](../app/Http/Controllers/TransactionsController.php)

## 🔍 Konfigurasi Penting

### Environment Variables
Pastikan `.env` memiliki konfigurasi:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gading
DB_USERNAME=root
DB_PASSWORD=

# PDF Storage Configuration
PDF_GHOSTSCRIPT_PATH="gs"
PDF_COMPRESSION_ENABLED=true
PDF_ARCHIVE_YEARS=2
```

### Storage Permissions
Pastikan direktori memiliki permission yang benar:
```bash
chmod -R 755 storage/app/pdfs/
chmod -R 755 storage/app/pdfs/archive/
```

## 🛠️ Troubleshooting

### Masalah Umum dan Solusi

1. **Permission Denied**
   - Masalah: Tidak bisa write ke storage
   - Solusi: `chmod -R 755 storage/app/pdfs/`

2. **Migration Gagal**
   - Masalah: Table already exists
   - Solusi: `php artisan migrate:fresh --seed`

3. **PDF Tidak Ter-generate**
   - Masalah: Path atau permission salah
   - Solusi: `php artisan pdf:setup-storage --test`

4. **Compression Error**
   - Masalah: Ghostscript tidak ditemukan
   - Solusi: Install Ghostscript atau update path di config

## 📞 Support

Untuk bantuan teknis, hubungi development team dengan menyertakan:
- Error log dari `storage/logs/laravel.log`
- Screenshoot error message
- Langkah-langkah reproduksi masalah

---

*Dokumentasi ini diperbarui terakhir: 10 Desember 2025*
