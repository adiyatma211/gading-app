# PDF Storage System Documentation

## Overview
Sistem penyimpanan PDF yang terintegrasi untuk aplikasi Kasir Gading dengan struktur hierarkis dan manajemen metadata yang lengkap.

## Struktur Database

### Tabel Utama

#### 1. transactions
Tabel transaksi utama dengan field tambahan untuk PDF storage:
- `pdf_storage_path` - Path file PDF di storage baru
- `pdf_storage_type` - Tipe PDF (thermal/invoice)
- `pdf_storage_hash` - Hash MD5 untuk verifikasi integritas
- `pdf_storage_size` - Ukuran file dalam bytes
- `pdf_is_compressed` - Status kompresi PDF
- `pdf_archived_at` - Tanggal arsip
- `pdf_archive_path` - Path file di arsip

#### 2. pdf_storage_metadata
Tabel metadata untuk tracking lengkap file PDF:
- `pdfable_type` - Model yang terkait (polymorphic)
- `pdfable_id` - ID model yang terkait
- `file_name` - Nama file dengan konvensi {TYPE}-{YYYYMMDD}-{SEQ}-{TXID}.pdf
- `file_path` - Path lengkap file
- `file_type` - Tipe file (thermal/invoice)
- `file_hash` - Hash MD5 untuk deteksi duplikat
- `file_size_bytes` - Ukuran file dalam bytes
- `storage_disk` - Disk storage yang digunakan
- `archived_at` - Tanggal arsip
- `archive_path` - Path di arsip
- `metadata` - JSON metadata tambahan

#### 3. roles
Tabel roles pengguna:
- `rolesName` - Nama role (Admin, Kasir, Operator)
- `keterangan` - Deskripsi role
- `deleteSts` - Status soft delete

#### 4. users
Tabel pengguna:
- `name` - Nama lengkap
- `username` - Username unik
- `password` - Password hashed
- `role_id` - Foreign key ke tabel roles
- `deleteSts` - Status soft delete

## Struktur Direktori Storage

```
storage/app/pdfs/
├── 2025/
│   ├── 01_January/
│   │   ├── 01/
│   │   │   ├── thermal/
│   │   │   └── invoice/
│   │   └── 02/
│   └── 02_February/
└── archive/
    ├── 2023/
    └── 2024/
```

## Konvensi Penamaan File

Format: `{TYPE}-{YYYYMMDD}-{SEQ}-{TXID}.pdf`

Contoh:
- `THERMAL-20251210-001-123.pdf`
- `INVOICE-20251210-001-124.pdf`

## API Commands

### 1. Setup Storage
```bash
php artisan pdf:setup-storage --test
```
Membuat struktur direktori dan testing permission.

### 2. Migrate Files
```bash
php artisan pdf:migrate-files --dry-run --batch-size=50
```
Migrasi file dari struktur lama ke baru.

### 3. Compress PDF
```bash
php artisan pdf:compress --all --quality=ebook --resolution=150
```
Kompresi file PDF menggunakan Ghostscript.

### 4. Archive Files
```bash
php artisan pdf:archive --years=2 --batch --delete
```
Arsip file lama ke storage jangka panjang.

### 5. Storage Statistics
```bash
php artisan pdf:stats --detailed --type=thermal
```
Menampilkan statistik lengkap storage.

## Konfigurasi

### Filesystems (config/filesystems.php)
```php
'pdf_storage' => [
    'driver' => 'local',
    'root' => storage_path('app/pdfs'),
    'throw' => false,
],

'pdf_archive' => [
    'driver' => 'local', 
    'root' => storage_path('app/pdfs/archive'),
    'throw' => false,
],

'pdf_public' => [
    'driver' => 'local',
    'root' => public_path('pdfs'),
    'url' => env('APP_URL').'/pdfs',
    'visibility' => 'public',
],
```

### PDF Configuration (config/pdf.php)
- Pengaturan kompresi dengan Ghostscript
- Kebijakan arsip (2 tahun)
- Konvensi penamaan file
- Monitoring dan alerting

## User Credentials

### Default Users
1. **Administrator**
   - Username: `admin`
   - Password: `admin123`
   - Role: Admin

2. **Kasir**
   - Username: `kasir`
   - Password: `kasir123`
   - Role: Kasir

3. **Operator**
   - Username: `operator`
   - Password: `operator123`
   - Role: Operator

## Integrasi dengan Aplikasi

### TransactionsController
- Menggunakan `PDFStorageService` untuk generate dan store PDF
- Mapping tipe file: `nota` → `thermal`, `nota_dua` → `invoice`
- Metadata tracking otomatis
- Backward compatibility dengan file lama

### PDFStorageService
- Hierarchical path generation
- Duplicate detection dengan hash MD5
- File integrity verification
- Automatic archiving
- Comprehensive error handling dan logging

## Keamanan

### File Integrity
- MD5 hash verification
- Storage checksum validation
- Corruption detection dan recovery

### Access Control
- Role-based permissions
- File ownership tracking
- Audit trail lengkap

## Monitoring

### Logging
- Semua operasi PDF di-log
- Error tracking dengan stack trace
- Performance metrics collection

### Statistics
- Total files dan size per type
- Compression ratio tracking
- Archive status monitoring
- Disk usage analysis
- Monthly trend analysis

## Best Practices

### 1. File Management
- Gunakan hierarchical structure untuk organisasi
- Implement duplicate detection
- Regular archiving untuk old files
- Compression untuk space optimization

### 2. Database Management
- Gunakan polymorphic relationships untuk flexibility
- Implement soft deletes
- Proper indexing untuk performance
- Metadata enrichment untuk traceability

### 3. Security
- Hash verification untuk integrity
- Role-based access control
- Regular backup procedures
- Audit trail maintenance

## Troubleshooting

### Common Issues
1. **Permission Denied**: Pastikan storage directories memiliki permission 755
2. **Disk Full**: Monitor disk usage dan archive old files
3. **Corruption**: Check MD5 hashes dan restore dari backup
4. **Performance**: Implement compression dan indexing

### Solutions
- Gunakan `php artisan pdf:setup-storage --test` untuk testing
- Monitor dengan `php artisan pdf:stats --detailed`
- Cleanup dengan `php artisan pdf:archive --years=1 --delete`

## Deployment

### Production Setup
1. Run `php artisan migrate:fresh --seed`
2. Execute `php artisan pdf:setup-storage`
3. Test dengan `php artisan pdf:migrate-files --dry-run`
4. Monitor dengan `php artisan pdf:stats`

### Backup Strategy
- Daily backup dari storage directory
- Database backup termasuk metadata
- Version control untuk configuration files
- Disaster recovery plan

---

*Dokumentasi ini mencakup seluruh aspek sistem PDF storage yang telah diimplementasikan untuk aplikasi Kasir Gading.*
