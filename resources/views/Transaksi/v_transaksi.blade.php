@extends('layouts.base')
@section('content')
    <!-- STYLING -->
    <style>
        /* Steps indicator */
        .steps .progress {
            background-color: #e9ecef;
            border-radius: 10px;
        }

        .steps .step {
            text-align: center;
            font-weight: 500;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .steps .step.active {
            color: #0d6efd;
            font-weight: 600;
        }

        /* Wrapper setiap item MMT */
        .mmt-item {
            background-color: #fff;
            border: 1px solid #d0d7de;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 1.2rem 1rem 0.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            transition: all 0.3s ease;
        }

        /* Badge kecil pojok kiri */
        .mmt-item .badge {
            position: absolute;
            top: -0.6rem;
            left: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            background-color: #0d6efd;
            color: white;
            font-size: 1rem;
            font-weight: 500;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            width: auto;
            max-width: max-content;
            height: auto;
            z-index: 2;
        }

        /* Ringkasan Data Customer */
        .nasabah-summary {
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 1rem;
        }

        .nasabah-summary p {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        /* Total akhir (readonly field) */
        #total {
            font-weight: bold;
            font-size: 1.3rem;
            background-color: #f1f3f5;
            border: none;
            border-radius: 0 0.375rem 0.375rem 0;
            text-align: right;
        }

        /* Awalan "Rp" pada total */
        .input-group-text {
            background-color: #198754;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 0.375rem 0 0 0.375rem;
        }

        /* Tombol hapus (per item MMT) */
        .btn-remove-item {
            font-size: 0.8rem;
            padding: 0.4rem 0.75rem;
        }

        /* Tombol tambah item */
        #btnAddItem {
            font-size: 0.85rem;
            padding: 0.45rem 0.85rem;
            border-radius: 0.5rem;
        }


        /* Styling untuk field keterangan */
        textarea[name*="[keterangan]"] {
            resize: vertical;
            /* Memungkinkan pengguna mengubah ukuran vertikal */
            min-height: 60px;
            /* Tinggi minimal */
        }

        .payment-section {
            display: flex;
            align-items: center;
            /* Menyelaraskan elemen secara vertikal */
            gap: 20px;
            /* Jarak antara kolom */
        }

        .payment-section .col-md-4,
        .payment-section .col-md-6 {
            display: flex;
            flex-direction: column;
            /* Mengatur elemen di dalam kolom secara vertikal */
        }

        .payment-section .d-flex.align-items-center {
            align-items: center;
            /* Menyelaraskan ikon dan teks secara vertikal */
        }
    </style>
    <div class="page-heading">
        <h3><i class="bi bi-receipt-cutoff me-2"></i>Form Transaksi</h3>
    </div>
    <section class="section">
        <!-- Step Indicator -->
        <div class="steps mb-4">
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary" role="progressbar" id="progressBar" style="width: 33.33%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <div class="step active" id="step1-indicator">
                    <i class="bi bi-person-circle"></i> Data Customer
                </div>
                <div class="step" id="step2-indicator">
                    <i class="bi bi-cart4"></i> Transaksi MMT
                </div>
                <div class="step" id="step3-indicator">
                    <i class="bi bi-cash-stack"></i> Pembayaran
                </div>
            </div>
        </div>

        <!-- Step 1: Form Data Customer -->
        <div class="card shadow-sm border-primary" id="nasabahForm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Data Customer</h5>
            </div>
            <div class="card-body">
                <form id="formNasabah">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" id="nama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" name="telepon" id="telepon" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email (Opsional)</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pelanggan</label>
                            <select class="form-select" name="jenis_pelanggan" id="jenis_pelanggan">
                                <option value="baru">Pelanggan Baru</option>
                                <option value="lama">Pelanggan Lama</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-right-circle"></i> Lanjut ke Form Transaksi
                    </button>
                </form>
            </div>
        </div>

        <!-- Step 2: Form Transaksi MMT -->
        <div class="card shadow-sm border-primary" id="transaksiForm" style="display: none;">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-cart4 me-2"></i>Transaksi Pemesanan MMT</h5>
            </div>
            <div class="card-body">
                <div class="nasabah-summary mb-1 mt-4">
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-person-badge me-1"></i>Data Customer
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditNasabah">
                                <i class="bi bi-pencil-square"></i> Edit Data
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Nama Lengkap</small>
                                            <strong id="summary-nama">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Nomor Telepon</small>
                                            <strong id="summary-telepon">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Email</small>
                                            <strong id="summary-email">-</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-tag-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Jenis Pelanggan</small>
                                            <strong id="summary-jenis">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Alamat</small>
                                            <strong id="summary-alamat">-</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="formTransaksi">
                    @csrf
                    <input type="hidden" name="nama_nasabah" id="hidden_nama">
                    <input type="hidden" name="telepon_nasabah" id="hidden_telepon">
                    <input type="hidden" name="email_nasabah" id="hidden_email">
                    <input type="hidden" name="jenis_pelanggan" id="hidden_jenis_pelanggan">
                    <input type="hidden" name="alamat_nasabah" id="hidden_alamat">
                    <div id="mmtItemsContainer">
                        <div class="mmt-item row g-3">
                            <span class="badge">Prodak 1</span>
                            <div class="col-md-3">
                                <label class="form-label">Tipe MMT</label>
                                <select class="form-select" name="items[0][tipe]">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach ($showProdak as $produk)
                                        <option value="{{ $produk->id }}" data-harga="{{ $produk->total_harga }}"
                                            data-diskon="{{ $produk->diskon }}">
                                            {{ $produk->nama_bahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Panjang (m)</label>
                                <input type="number" step="0.1" class="form-control" name="items[0][panjang]"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lebar (m)</label>
                                <input type="number" step="0.1" class="form-control" name="items[0][lebar]"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Harga/m</label>
                                <input type="text" class="form-control rupiah-input" name="items[0][harga]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Diskon Barang/m</label>
                                <input type="text" class="form-control rupiah-input" name="items[0][diskonbarang]"
                                    disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Keterangan (Opsional)</label>
                                <textarea class="form-control" name="items[0][keterangan]" rows="2"
                                    placeholder="Tambahkan catatan untuk produk ini..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mb-4">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddItem">
                            <i class="bi bi-plus-circle"></i> Tambah Item
                        </button>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Biaya Desain (Opsional)</label>
                            <input type="text" name="biaya_desain" id="biaya_desain"
                                class="form-control rupiah-input " value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Diskon (Rp)</label>
                            <input type="text" name="diskon" id="diskon" class="form-control  rupiah-input"
                                value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Ambil / Selesai</label>
                            <input type="date" name="tanggal_ambil" id="tanggal_ambil" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <input type="hidden" id="total_raw" value="0">
                        <label class="form-label fs-5 fw-bold">Total Akhir</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="total" class="form-control bg-light" readonly>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="btnBackToNasabah">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-success flex-grow-1" id="btnNextToPembayaran">
                            <i class="bi bi-check2-circle"></i> Lanjut ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Step 3: Form Pembayaran -->
        <div class="card shadow-sm border-primary" id="pembayaranForm" style="display: none;">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-cash-stack me-2"></i>Pembayaran</h5>
            </div>
            <div class="card-body">
                <!-- Nota Transaksi -->
                <div class="nota-summary mb-4">
                    <div class="card border-primary shadow-sm">
                        <div
                            class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-receipt me-1"></i>Nota Transaksi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Nama Lengkap</small>
                                            <strong id="nota-nama">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Nomor Telepon</small>
                                            <strong id="nota-telepon">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Email</small>
                                            <strong id="nota-email">-</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-tag-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Jenis Pelanggan</small>
                                            <strong id="nota-jenis">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <div>
                                            <small class="text-muted d-block">Alamat</small>
                                            <strong id="nota-alamat">-</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Detail Produk:</label>
                                    <ul id="nota-produk-list" class="list-unstyled">
                                        <!-- Daftar produk akan ditampilkan di sini -->
                                    </ul>
                                    <ul class="list-unstyled mt-3">
                                        <li>
                                            <strong>Tanggal Selesai/Diambil:</strong>
                                            <span id="nota-tanggal-selesai">-</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <div>

                                            <p class="mb-1"><strong>Biaya Desain:</strong> Rp <span
                                                    id="nota-biaya-desain">0</span></p>
                                            <p class="mb-1"><strong>Diskon:</strong> Rp <span id="nota-diskon">0</span>
                                            </p>
                                            <p class="mb-1"><strong>Subtotal:</strong> Rp <span
                                                    id="nota-subtotal">0</span></p>
                                            <!-- Tambahkan baris untuk DP -->
                                            <p class="mb-1" id="nota-dp-row" style="display: none;">
                                                <strong>Down Payment (DP):</strong> Rp <span id="nota-dp">0</span>
                                            </p>
                                            <hr>
                                            <p class="mb-0"><strong>Total Akhir:</strong> Rp <span
                                                    id="nota-total">0</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pilihan Metode Pembayaran -->
                <div class="row g-3 mb-4 payment-section">
                    <div class="col-md-4">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select" name="metode_pembayaran" id="metode_pembayaran" required>
                            <option value="tunai">Tunai</option>
                            <option value="transfer_bank">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Pembayaran</label>
                        <select class="form-select" name="status_pembayaran" id="status_pembayaran" required>
                            <option value="lunas">Lunas</option>
                            <option value="dp">DP</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="buktiContainer" style="display: none;">
                        <label class="form-label">Bukti Pembayaran</label>
                        <input type="file" class="form-control" name="bukti_pembayaran" id="bukti_pembayaran"
                            accept="image/*">
                        <small class="text-muted">Unggah bukti pembayaran (format gambar).</small>
                    </div>
                    <div class="col-md-4" id="dpContainer" style="display: none;">
                        <label class="form-label">Down Payment (DP) 30%</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" name="dp" id="dp"
                                placeholder="Masukkan DP" required>
                        </div>
                        <div id="dpWarning" style="color: red; font-size: 0.9em; display: none;"></div>
                        <small class="text-muted">DP wajib 50% dari total jika total ≥ Rp 300,000.</small>
                    </div>
                    <!-- Penempatan Pesan "Pembayaran Wajib Lunas" -->
                    <div id="pembayaranLunas" class="col-md-4 d-flex align-items-start">
                        <div class="d-flex align-items-center text-danger" style="gap: 8px; padding-top: 10px;">
                            <i class="fas fa-exclamation-circle" id="alert-icon" style="font-size: 20px;"></i>
                            <p class="mb-0" id="alert-text" style="font-size: 16px; font-weight: 500; display: none;">
                                Pembayaran wajib lunas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btnBackToTransaksi">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <button type="submit" class="btn btn-success flex-grow-1" id="btnSelesaiTransaksi">
                        <i class="bi bi-check2-circle"></i> Selesai & Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi format ke Rupiah
            function formatRupiah(angka, prefix = 'Rp ') {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return prefix + rupiah;
            }

            document.querySelectorAll('select[name^="items["]').forEach(select => {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const harga = selectedOption.getAttribute('data-harga') || '0';
                    const diskon = selectedOption.getAttribute('data-diskon') || '0';

                    // Ambil input harga dan diskon di baris yang sama
                    const row = this.closest('.mmt-item');
                    const hargaInput = row.querySelector('input[name$="[harga]"]');
                    const diskonInput = row.querySelector('input[name$="[diskonbarang]"]');
                    if (hargaInput) {
                        hargaInput.value = formatRupiah(harga);
                    }
                    // Tampilkan Diskon ke Field Diskon Barang/m (di dalam baris yang sama)
                    if (diskonInput) {
                        diskonInput.value = formatRupiah(diskon);
                    }
                });
            });
        });
    </script>

    <script>
        function formatRupiah(angka, prefix = 'Rp ') {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const rupiahInputs = document.querySelectorAll('.rupiah-input');
            rupiahInputs.forEach(input => {
                input.addEventListener('keyup', function(e) {
                    this.value = formatRupiah(this.value);
                });
            });
        });
    </script>

    <script>
        let index = 1;

        // Menghitung total saat ada perubahan input
        $(document).on('input', 'input', function() {
            calculateTotal();
        });

        // Fungsi untuk menghitung total
        function calculateTotal() {
            let subtotal = 0;
            $('.mmt-item').each(function() {
                const panjang = parseFloat($(this).find('input[name$="[panjang]"]').val()) || 0;
                const lebar = parseFloat($(this).find('input[name$="[lebar]"]').val()) || 0;

                // Ambil angka dari input harga (hilangkan "Rp", titik, dll)
                let rawHarga = $(this).find('input[name$="[harga]"]').val() || '0';
                let harga = parseFloat(rawHarga.replace(/[^0-9]/g, '')) || 0;

                subtotal += panjang * lebar * harga;
            });

            const desain = parseFloat($('#biaya_desain').val().replace(/[^0-9]/g, '')) || 0;
            const diskon = parseFloat($('#diskon').val().replace(/[^0-9]/g, '')) || 0;

            const totalAkhir = subtotal + desain - diskon;

            // Update ke input hidden dan input tampilan
            $('#total_raw').val(totalAkhir);
            $('#total').val(totalAkhir.toLocaleString('id-ID'));

            // Update juga DP
            updateDpField();

            return totalAkhir;
        }


        // Menambah item MMT baru
        $('#btnAddItem').click(function() {
            const html = `
    <div class="mmt-item row g-3" style="display: none;">
        <span class="badge">Prodak ${index + 1}</span>
        <div class="col-md-3">
            <label class="form-label">Tipe MMT</label>
            <select class="form-select tipe-produk" name="items[${index}][tipe]" required>
                <option value="">-- Pilih Produk --</option>
                @foreach ($showProdak as $produk)
                    <option value="{{ $produk->id }}" data-harga="{{ $produk->total_harga }}"   data-diskon="{{ $produk->diskon }}" >
                        {{ $produk->nama_bahan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Panjang (m)</label>
            <input type="number" step="0.1" class="form-control" name="items[${index}][panjang]" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Lebar (m)</label>
            <input type="number" step="0.1" class="form-control" name="items[${index}][lebar]" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Harga/m</label>
            <input type="text" class="form-control rupiah-input harga-input" name="items[${index}][harga]" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Diskon Barang/m</label>
            <input type="text" class="form-control rupiah-input diskon-input" name="items[${index}][diskonbarang]"
                disabled>
        </div>

        <div class="col-md-3">
            <label class="form-label">Keterangan (Opsional)</label>
            <textarea class="form-control" name="items[${index}][keterangan]" rows="2" placeholder="Tambahkan catatan untuk produk ini..."></textarea>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-outline-danger btn-remove-item w-100">
                <i class="bi bi-x-circle"></i> Hapus
            </button>
        </div>
    </div>
    `;
            $('#mmtItemsContainer').append(html);
            $('#mmtItemsContainer .mmt-item').last().fadeIn(300);
            index++;

            // Attach event listener untuk harga otomatis saat item baru dibuat
            attachDropdownChangeHandler();
        });

        function attachDropdownChangeHandler() {
            $('.form-select[name^="items["]').off('change').on('change', function() {
                const harga = $(this).find(':selected').data('harga') || 0;
                const diskonharga = $(this).find(':selected').data('diskon') || 0;

                const row = $(this).closest('.mmt-item');
                const hargaInput = row.find('input[name$="[harga]"]');
                const diskonInput = row.find('input[name$="[diskonbarang]"]');

                // Debugging: Cek Nilai
                console.log("Harga:", harga);
                console.log("Diskon:", diskonharga);
                console.log("Harga Input:", hargaInput.length ? "Ditemukan" : "Tidak Ditemukan");
                console.log("Diskon Input:", diskonInput.length ? "Ditemukan" : "Tidak Ditemukan");

                // Set nilai harga dan diskon
                if (hargaInput.length) {
                    hargaInput.val(formatRupiah(harga.toString()));
                }

                if (diskonInput.length) {
                    diskonInput.prop('disabled', false); // Aktifkan jika disabled
                    diskonInput.val(formatRupiah(diskonharga.toString()));
                }
            });
        }
        // Menghapus item MMT
        $(document).on('click', '.btn-remove-item', function() {
            $(this).closest('.mmt-item').fadeOut(200, function() {
                $(this).remove();
                calculateTotal();
            });
        });

        // Menyimpan Data Customer dan menampilkan form transaksi
        $('#formNasabah').submit(function(e) {
            e.preventDefault();
            // Ambil data dari form nasabah
            const nama = $('#nama').val();
            const telepon = $('#telepon').val();
            const email = $('#email').val() || '-';
            const jenisPelanggan = $('#jenis_pelanggan').val();
            const jenisPelangganText = $('#jenis_pelanggan option:selected').text();
            const alamat = $('#alamat').val();
            // Validasi
            if (!nama || !telepon || !alamat) {
                Swal.fire('Error', 'Harap isi semua Data Customer yang diperlukan', 'error');
                return;
            }
            // Isi data di form ringkasan
            $('#summary-nama').text(nama);
            $('#summary-telepon').text(telepon);
            $('#summary-email').text(email);
            $('#summary-jenis').text(jenisPelangganText);
            $('#summary-alamat').text(alamat);
            // Isi data di hidden input
            $('#hidden_nama').val(nama);
            $('#hidden_telepon').val(telepon);
            $('#hidden_email').val(email);
            $('#hidden_jenis_pelanggan').val(jenisPelanggan);
            $('#hidden_alamat').val(alamat);
            // Ubah tampilan
            $('#nasabahForm').fadeOut(300, function() {
                $('#transaksiForm').fadeIn(300);
                $('#step1-indicator').removeClass('active');
                $('#step2-indicator').addClass('active');
                $('#progressBar').css('width', '66.66%');
            });
        });

        // Kembali ke form nasabah
        $('#btnBackToNasabah, #btnEditNasabah').click(function() {
            $('#transaksiForm').fadeOut(300, function() {
                $('#nasabahForm').fadeIn(300);
                $('#step2-indicator').removeClass('active');
                $('#step1-indicator').addClass('active');
                $('#progressBar').css('width', '33.33%');
            });
        });

        // Navigasi ke form pembayaran
        $('#btnNextToPembayaran').click(function() {
            // Validasi form transaksi sebelum lanjut
            let isValid = true;
            $('.mmt-item').each(function() {
                const panjang = $(this).find('input[name$="[panjang]"]').val();
                const lebar = $(this).find('input[name$="[lebar]"]').val();
                const harga = $(this).find('input[name$="[harga]"]').val();
                if (!panjang || !lebar || !harga) {
                    isValid = false;
                    return false;
                }
            });
            if (!isValid) {
                Swal.fire('Error', 'Harap isi semua data MMT dengan benar', 'error');
                return;
            }

            // Isi data nota
            $('#nota-nama').text($('#summary-nama').text());
            $('#nota-telepon').text($('#summary-telepon').text());
            $('#nota-email').text($('#summary-email').text());
            $('#nota-jenis').text($('#summary-jenis').text());
            $('#nota-alamat').text($('#summary-alamat').text());

            // Tampilkan daftar produk dengan keterangan
            const produkList = [];
            $('.mmt-item').each(function() {
                const tipe = $(this).find('select[name$="[tipe]"]').val();
                const panjang = parseFloat($(this).find('input[name$="[panjang]"]').val()) || 0;
                const lebar = parseFloat($(this).find('input[name$="[lebar]"]').val()) || 0;

                const rawHarga = $(this).find('input[name$="[harga]"]').val() || '0';
                const harga = parseFloat(rawHarga.replace(/[^0-9]/g, '')) || 0;

                const keterangan = $(this).find('textarea[name$="[keterangan]"]').val() || '-';
                const subtotal = panjang * lebar * harga;
                // Format produk dengan keterangan
                produkList.push(`
            ${tipe} (${panjang}m x ${lebar}m) - Rp ${subtotal.toLocaleString('id-ID')}
            <small class="text-muted d-block">Keterangan: ${keterangan}</small>
        `);
            });
            $('#nota-produk-list').html(`<li>${produkList.join('</li><li>')}</li>`);
            // Ambil nilai tanggal dari halaman pertama
            const tanggalSelesai = $('#tanggal_ambil')
                .val(); // Ganti #tanggal-selesai-input sesuai ID input tanggal

            // Format tanggal jika ada, atau tampilkan pesan default jika kosong
            const tanggalSelesaiFormatted = tanggalSelesai ?
                new Date(tanggalSelesai).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                }) :
                '-';

            // Tambahkan tanggal selesai/diambil ke dalam nota
            $('#nota-tanggal-selesai').html(tanggalSelesaiFormatted);

            // Hitung total awal
            const subtotal = parseFloat($('#total').val().replace(/\./g, '').replace(/,/g, '')) || 0;
            const rawBiayaDesain = $('#biaya_desain').val() || '0';

            const biayaDesain = parseFloat(rawBiayaDesain.replace(/[^0-9]/g, '')) || 0;

            const rawDiskon = $('#diskon').val() || '0';
            const diskon = parseFloat(rawDiskon.replace(/[^0-9]/g, '')) || 0;

            // Hitung total sebelum DP
            const totalSebelumDp = subtotal;

            // Ambil nilai DP dari input field atau hitung 30% jika kosong
            let rawDp = $('#dp').val().replace(/\./g, '').replace(/,/g, '');
            let dpValue = parseInt(rawDp, 10) || 0;

            // Jika DP kosong, hitung 30%
            if (dpValue === 0 && totalSebelumDp >= 300000) {
                dpValue = Math.round(totalSebelumDp * 0.5);
            }

            // Hitung total akhir setelah dikurangi DP
            const totalAkhir = totalSebelumDp - dpValue;

            // Update nilai di nota
            $('#nota-subtotal').text(subtotal.toLocaleString('id-ID'));
            $('#nota-biaya-desain').text(biayaDesain.toLocaleString('id-ID'));
            $('#nota-diskon').text(diskon.toLocaleString('id-ID'));
            $('#nota-dp').text(dpValue.toLocaleString('id-ID')); // Tampilkan nilai DP
            $('#nota-total').text(totalAkhir.toLocaleString('id-ID')); // Tampilkan total akhir
            const alertIcon = $('#alert-icon');
            const statusPembayaranSelect = $('#status_pembayaran');
            const alertText = $('#alert-text');
            // Tampilkan baris DP jika total ≥ 100.000
            if (totalSebelumDp >= 300000) {
                $('#pembayaranLunas').hide(); // Sembunyikan semua
                alertIcon.hide(); // Pastikan ikon disembunyikan
                alertText.hide();
                statusPembayaranSelect.prop('disabled', false);
                $('#nota-dp-row').fadeIn(200); // Tampilkan baris DP
            } else {
                statusPembayaranSelect.val('lunas');
                statusPembayaranSelect.prop('disabled', true);
                $('#pembayaranLunas').show(); // Menampilkan div luar
                alertIcon.show(); // Menampilkan ikon
                alertText.show();
                $('#nota-dp-row').fadeOut(200); // Sembunyikan baris DP jika total < 100.000
            }

            // Ubah tampilan ke halaman pembayaran
            $('#transaksiForm').fadeOut(300, function() {
                $('#pembayaranForm').fadeIn(300);
                $('#step2-indicator').removeClass('active');
                $('#step3-indicator').addClass('active');
                $('#progressBar').css('width', '100%');
            });
        });

        // Kembali ke form transaksi
        $('#btnBackToTransaksi').click(function() {
            $('#pembayaranForm').fadeOut(300, function() {
                $('#transaksiForm').fadeIn(300);
                $('#step3-indicator').removeClass('active');
                $('#step2-indicator').addClass('active');
                $('#progressBar').css('width', '66.66%');
            });
        });

        // Submit transaksi
        $('#btnSelesaiTransaksi').click(function() {
            const metodePembayaran = $('#metode_pembayaran').val();
            const status_pembayaran = $('#status_pembayaran').val();
            const buktiPembayaran = $('#bukti_pembayaran')[0]?.files[0];
            const totalAkhir = parseFloat($('#total').val().replace(/\./g, '').replace(/,/g, '')) || 0;
            const dp = parseFloat($('#dp').val().replace(/\./g, '').replace(/,/g, '')) || 0;


            // Debugging logs
            console.log('DP:', dp);
            console.log('Total Akhir:', totalAkhir);

            // Validasi DP jika total ≥ 100 ribu
            if (totalAkhir >= 300000) {
                const requiredDp = Math.round(totalAkhir * 0.5);
                if (!dp || dp < requiredDp) {
                    Swal.fire('Error',
                        `Harap isi DP minimal Rp ${requiredDp.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}.`,
                        'error');
                    return;
                }
            }

            // Validasi bukti pembayaran untuk transfer bank atau QRIS
            if ((metodePembayaran === 'transfer_bank' || metodePembayaran === 'qris') && !buktiPembayaran) {
                Swal.fire('Error', 'Harap unggah bukti pembayaran untuk metode ini.', 'error');
                return;
            }
            // Ambil semua data untuk payload
            const customerData = {
                nama: $('#hidden_nama').val(),
                telepon: $('#hidden_telepon').val(),
                email: $('#hidden_email').val(),
                jenis_pelanggan: $('#hidden_jenis_pelanggan').val(),
                alamat: $('#hidden_alamat').val(),
            };

            const transaksiItems = [];
            $('.mmt-item').each(function() {
                transaksiItems.push({
                    tipe: $(this).find('select[name$="[tipe]"]').val(),
                    panjang: $(this).find('input[name$="[panjang]"]').val(),
                    lebar: $(this).find('input[name$="[lebar]"]').val(),
                    harga: $(this).find('input[name$="[harga]"]').val().replace(/[^0-9]/g, ''),
                    keterangan: $(this).find('textarea[name$="[keterangan]"]').val()
                });
            });

            const transaksiSummary = {
                total: parseFloat($('#total').val().replace(/\./g, '').replace(/,/g, '')) || 0,
                subtotal: parseFloat($('#nota-total').text().replace(/\./g, '').replace(/,/g, '')) || 0,
                biaya_desain: parseFloat($('#nota-biaya-desain').text().replace(/\./g, '').replace(/,/g, '')) ||
                    0,
                diskon: parseFloat($('#nota-diskon').text().replace(/\./g, '').replace(/,/g, '')) || 0,
                dp: parseFloat($('#nota-dp').text().replace(/\./g, '').replace(/,/g, '')) || 0,
                metode_pembayaran: $('#metode_pembayaran').val(),
                status_pembayaran: $('#status_pembayaran').val(),
                bukti_pembayaran: $('#bukti_pembayaran')[0]?.files[0]?.name || null,
                tanggal_ambil: $('#tanggal_ambil').val()
            };

            const fullPayload = {
                customer: customerData,
                transaksi: transaksiItems,
                summary: transaksiSummary
            };

            // Ini untuk cek di Console Browser
            console.log('Payload Transaksi Lengkap:', fullPayload);
            // Tampilkan pesan sukses
            Swal.fire({
                title: 'Apakah kamu yakin untuk menyimpan transaksi ini?',
                text: 'Pastikan data sudah benar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim AJAX ke server
                    $.ajax({
                        url: "{{ route('transaksi.store') }}",
                        method: 'POST',
                        data: fullPayload,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // consol.log(response);

                            // Setelah sukses simpan
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Transaksi berhasil disimpan. Mau lihat nota sekarang?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Lihat Nota',
                                cancelButtonText: 'Selesai'
                            }).then((nextAction) => {
                                if (nextAction.isConfirmed) {
                                    // Arahkan ke halaman cetak nota
                                    const fileName = response.nota_file;
                                    const url = '/nota/' + fileName;

                                    window.open(url, '_blank');
                                    // Setelah buka tab baru, langsung reset halaman/form
                                    resetAllForms();
                                } else {
                                    // Kalau tidak, reset form biasa
                                    resetAllForms();
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat menyimpan.', 'error');
                        }
                    });
                }
            });

        });

        function updateDpField() {
            let rawTotal = $('#total').val().replace(/\./g, '').replace(/[^0-9]/g, '');
            let totalAkhir = parseInt(rawTotal, 10) || 0;

            const dpField = $('#dpContainer');
            const dpInput = $('#dp');
            const dpWarning = $('#dpWarning');
            const lunasMessageContainer = $('#lunasMessageContainer');

            if (totalAkhir >= 300000) {
                // Tampilkan field DP
                dpField.fadeIn(200);
                lunasMessageContainer.fadeOut(200); // Sembunyikan pesan lunas

                // Hitung DP minimal 50%
                let dpMin = Math.round(totalAkhir * 0.5);
                let formattedDp = dpMin.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                // Isi DP ke 50% hanya jika belum pernah diubah manual
                if (!dpInput.data('manually-changed')) {
                    dpInput.val(formattedDp);
                }

                dpInput.data('min-dp', dpMin);

                // Validasi input DP
                dpInput.off('input').on('input', function() {
                    let inputValue = $(this).val().replace(/[^0-9]/g, '');
                    let newDp = parseInt(inputValue, 10) || 0;
                    let currentMinDp = $(this).data('min-dp');

                    // Tandai bahwa pengguna telah mengubah DP
                    dpInput.data('manually-changed', true);

                    // Jika dihapus atau diisi kurang dari 50%
                    if (inputValue === "") {
                        dpWarning.text("DP tidak boleh kosong").fadeIn(200);
                    } else if (newDp < currentMinDp) {
                        dpWarning.text(`DP minimal ${formattedDp}`).fadeIn(200);
                    } else {
                        dpWarning.fadeOut(200);
                    }
                });

            } else {
                // Sembunyikan field DP jika total < 300.000
                dpField.fadeOut(200);
                dpInput.val('').removeAttr('required');
                dpWarning.fadeOut(200);
                dpInput.data('manually-changed', false);

                // Tampilkan pesan "Pembayaran wajib lunas"
                lunasMessageContainer.fadeIn(200);
            }
        }
        // Format angka saat user mengetik
        $(document).on('input', '#dp', function() {
            // Ambil nilai DP yang dimasukkan oleh pengguna
            let rawDp = $(this).val();
            let cleanedDp = rawDp.replace(/[^0-9]/g, ''); // Hanya angka
            let dpValue = parseInt(cleanedDp, 10) || 0;

            // Format angka ke IDR dengan pemisah ribuan manual
            if (dpValue > 0) {
                let formattedDp = dpValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                $(this).val(formattedDp);
            } else {
                $(this).val(''); // Kosongkan jika DP = 0
            }

            // Update nilai di nota langsung
            $('#nota-dp').text(dpValue.toLocaleString('id-ID'));

            // Hitung ulang total akhir
            calculateTotalWithDp(dpValue);
        });

        // Fungsi untuk menghitung total akhir dengan mempertimbangkan DP
        function calculateTotalWithDp(dp) {
            // Ambil subtotal dari input total
            const subtotal = parseFloat($('#total').val().replace(/\./g, '').replace(/,/g, '')) || 0;
            const biayaDesain = parseFloat($('#biaya_desain').val()) || 0;
            const diskon = parseFloat($('#diskon').val()) || 0;

            // Hitung total akhir dengan mempertimbangkan DP
            const totalAkhir = subtotal + biayaDesain - diskon - dp;

            // Update total akhir di UI
            $('#totalAkhir').val(totalAkhir.toLocaleString('id-ID'));

            // Jika di halaman nota, update juga nilai total akhir di nota
            $('#nota-total').text(totalAkhir.toLocaleString('id-ID'));
        }



        $(document).on('input', '#total', function() {
            updateDpField();
        });

        function resetAllForms() {
            // Reset form jika elemen adalah form
            if ($('#formNasabah').is('form')) $('#formNasabah')[0].reset();
            if ($('#formTransaksi').is('form')) $('#formTransaksi')[0].reset();
            if ($('#pembayaranForm').is('form')) $('#pembayaranForm')[0].reset();

            // Reset input file
            $('#bukti_pembayaran').val('');
            $('.preview-image').attr('src', '').hide(); // Hapus preview gambar jika ada

            // Reset field DP
            $('#dp').val('');

            // Reset dropdown ke nilai default
            $('#metode_pembayaran').val('tunai');
            $('#status_pembayaran').val('lunas');

            // Sembunyikan container opsional
            $('#buktiContainer').hide();
            $('#dpContainer').hide();
            $('#pembayaranLunas').hide();
            $('#alert-text').hide();

            // Reset index produk
            let index = 0;

            // Generate ulang satu item MMT dengan produk pertama
            let html = `
        <div class="mmt-item row g-3">
            <span class="badge">Prodak ${index + 1}</span>
            <div class="col-md-3">
                <label class="form-label">Tipe MMT</label>
                <select class="form-select tipe-produk" name="items[${index}][tipe]" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($showProdak as $produk)
                        <option value="{{ $produk->id }}" data-harga="{{ $produk->total_harga }}">
                            {{ $produk->nama_bahan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Panjang (m)</label>
                <input type="number" step="0.1" class="form-control" name="items[${index}][panjang]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Lebar (m)</label>
                <input type="number" step="0.1" class="form-control" name="items[${index}][lebar]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Harga/m</label>
                <input type="text" class="form-control rupiah-input harga-input" name="items[${index}][harga]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Diskon Barang/m</label>
                <input type="text" class="form-control rupiah-input" name="items[${index}][diskonbarang]"
                    disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label">Keterangan (Opsional)</label>
                <textarea class="form-control" name="items[${index}][keterangan]" rows="2" placeholder="Tambahkan catatan untuk produk ini..."></textarea>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-remove-item w-100">
                    <i class="bi bi-x-circle"></i> Hapus
                </button>
            </div>
        </div>
    `;

            $('#mmtItemsContainer').html(html);
            $('#mmtItemsContainer .mmt-item').last().fadeIn(300);

            // Attach handler
            attachDropdownChangeHandler();

            // Reset total
            $('#total').val('0');

            // Navigasi step: kembali ke langkah 1
            $('#transaksiForm').hide();
            $('#pembayaranForm').hide();
            $('#nasabahForm').show();

            $('#step3-indicator').removeClass('active');
            $('#step2-indicator').removeClass('active');
            $('#step1-indicator').addClass('active');
            $('#progressBar').css('width', '33.33%');

            // Set tanggal ambil ke hari ini
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_ambil').val(today);
        }



        // Set tanggal hari ini saat halaman dimuat
        $(document).ready(function() {
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_ambil').val(today);

            // Tampilkan/menyembunyikan input bukti pembayaran berdasarkan metode pembayaran
            $('#metode_pembayaran').change(function() {
                const metode = $(this).val();
                if (metode === 'transfer_bank' || metode === 'qris') {
                    $('#buktiContainer').fadeIn(200);
                    $('#bukti_pembayaran').attr('required', true); // Wajibkan unggah bukti
                } else {
                    $('#buktiContainer').fadeOut(200);
                    $('#bukti_pembayaran').removeAttr('required'); // Hapus keharusan unggah bukti
                }
            });
        });
    </script>
@endsection
