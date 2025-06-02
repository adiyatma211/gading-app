@extends('layouts.base')

@section('content')
    <!-- STYLING -->
    <style>
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
            z-index: 2;
        }

        .nasabah-summary {
            border-bottom: 1px dashed #dee2e6;
            padding-bottom: 1rem;
        }

        .nasabah-summary p {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        #total {
            font-weight: bold;
            font-size: 1.3rem;
            background-color: #f1f3f5;
            border: none;
            border-radius: 0 0.375rem 0.375rem 0;
            text-align: right;
        }

        .input-group-text {
            background-color: #198754;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 0.375rem 0 0 0.375rem;
        }

        .btn-remove-item {
            font-size: 0.8rem;
            padding: 0.4rem 0.75rem;
        }

        #btnAddItem {
            font-size: 0.85rem;
            padding: 0.45rem 0.85rem;
            border-radius: 0.5rem;
        }

        textarea[name*="[keterangan]"] {
            resize: vertical;
            min-height: 60px;
        }

        .payment-section {
            display: flex;
            align-items: center;
            gap: 20px;
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
                <div class="step active" id="step1-indicator"><i class="bi bi-person-circle"></i> Data Customer</div>
                <div class="step" id="step2-indicator"><i class="bi bi-cart4"></i> Transaksi MMT</div>
                <div class="step" id="step3-indicator"><i class="bi bi-cash-stack"></i> Pembayaran</div>
            </div>
        </div>

        <!-- Step 1: Form Data Customer -->
        <div class="card shadow-sm border-primary" id="nasabahForm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Data Customer</h5>
            </div>
            <div class="card-body">
                <form id="formNasabah">@csrf
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
                    <div class="card border-primary bg-opacity-10 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
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
                                            <small>Nama Lengkap</small><br>
                                            <strong id="summary-nama">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                                        <div>
                                            <small>Nomor Telepon</small><br>
                                            <strong id="summary-telepon">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <div>
                                            <small>Email</small><br>
                                            <strong id="summary-email">-</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-tag-fill text-primary me-2"></i>
                                        <div>
                                            <small>Jenis Pelanggan</small><br>
                                            <strong id="summary-jenis">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <div>
                                            <small>Alamat</small><br>
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
                            <span class="badge">Produk 1</span>
                            <div class="col-md-3">
                                <label>Tipe Produk</label>
                                <select class="form-select tipe-produk" name="items[0][tipe]" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach ($showProdak as $produk)
                                        <option value="{{ $produk->id }}"
                                            data-harga="{{ optional($produk->hargas->first())->harga ?? 0 }}"
                                            data-diskon="{{ optional($produk->hargas->first())->diskon ?? 0 }}"
                                            data-tipe-produk="{{ $produk->tipe_produk }}">
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 panjang-lebar" style="display:none;">
                                <label>Panjang (m)</label>
                                <input type="number" step="0.1" class="form-control" name="items[0][panjang]">
                            </div>
                            <div class="col-md-2 panjang-lebar" style="display:none;">
                                <label>Lebar (m)</label>
                                <input type="number" step="0.1" class="form-control" name="items[0][lebar]">
                            </div>
                            <div class="col-md-2 qty" style="display:none;">
                                <label>Jumlah (Qty)</label>
                                <input type="number" class="form-control" name="items[0][qty]">
                            </div>
                            <div class="col-md-2 sisi-laminasi" style="display:none;">
                                <label>Sisi</label>
                                <select class="form-select" name="items[0][sisi]">
                                    <option value="1">1 Sisi</option>
                                    <option value="2">2 Sisi</option>
                                </select>
                            </div>
                            <div class="col-md-2 laminasi" style="display:none;">
                                <label>Laminasi</label>
                                <select class="form-select" name="items[0][laminasi]">
                                    <option value="tidak">Tidak</option>
                                    <option value="ya">Ya</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Harga Satuan</label>
                                <input type="text" class="form-control rupiah-input" name="items[0][harga]" readonly>
                            </div>
                            <div class="col-md-2">
                                <label>Diskon Barang</label>
                                <input type="text" class="form-control rupiah-input" name="items[0][diskonbarang]"
                                    disabled>
                            </div>
                            <div class="col-md-3">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="items[0][keterangan]" rows="2" placeholder="Catatan..."></textarea>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-remove-item w-100">
                                    Hapus
                                </button>
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
                                class="form-control rupiah-input" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Diskon (Rp)</label>
                            <input type="text" name="diskon" id="diskon" class="form-control rupiah-input"
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
                <div class="nota-summary mb-4">
                    <div class="card border-primary shadow-sm">
                        <div
                            class="card-header bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0"><i class="bi bi-receipt me-1"></i>Nota Transaksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person-fill text-primary me-2"></i>
                                        <div>
                                            <small>Nama Lengkap</small><br>
                                            <strong id="nota-nama">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone-fill text-primary me-2"></i>
                                        <div>
                                            <small>Nomor Telepon</small><br>
                                            <strong id="nota-telepon">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <div>
                                            <small>Email</small><br>
                                            <strong id="nota-email">-</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-tag-fill text-primary me-2"></i>
                                        <div>
                                            <small>Jenis Pelanggan</small><br>
                                            <strong id="nota-jenis">-</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        <div>
                                            <small>Alamat</small><br>
                                            <strong id="nota-alamat">-</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Detail Produk:</label>
                                    <ul id="nota-produk-list" class="list-unstyled"></ul>
                                    <ul class="list-unstyled mt-3">
                                        <li><strong>Tanggal Selesai/Diambil:</strong> <span
                                                id="nota-tanggal-selesai">-</span></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <div>
                                            <p class="mb-1"><strong>Biaya Desain:</strong> Rp<span
                                                    id="nota-biaya-desain">0</span></p>
                                            <p class="mb-1"><strong>Diskon:</strong> Rp<span id="nota-diskon">0</span>
                                            </p>
                                            <p class="mb-1"><strong>Subtotal:</strong> Rp<span
                                                    id="nota-subtotal">0</span></p>
                                            <p class="mb-1" id="nota-dp-row" style="display: none;"><strong>Down
                                                    Payment (DP):</strong> Rp<span id="nota-dp">0</span></p>
                                            <hr>
                                            <p class="mb-0"><strong>Total Akhir:</strong> Rp<span
                                                    id="nota-total">0</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <label class="form-label">Down Payment (DP) 50%</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" name="dp" id="dp"
                                placeholder="Masukkan DP">
                        </div>
                        <small class="text-muted">DP wajib 50% dari total jika total ≥ Rp 300.000.</small>
                        <div id="dpWarning" style="color: red; display: none;"></div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btnBackToTransaksi">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-success flex-grow-1" id="btnSelesaiTransaksi">
                        <i class="bi bi-check2-circle"></i> Selesai & Simpan
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        window.hargaTieredData = {
            @foreach ($showProdak as $produk)
                @if ($produk->tipe_produk === 'tiered' && $produk->hargas->isNotEmpty())
                    "{{ $produk->id }}": {
                        @foreach ($produk->hargas as $harga)
                            "{{ $harga->min_qty }}-{{ $harga->max_qty }}": {{ $harga->harga }},
                        @endforeach
                    },
                @endif
            @endforeach
        };

        // Custom pricing data
        window.customHargaData = {
            @foreach ($showProdak as $produk)
                @if ($produk->tipe_produk === 'custom' && $produk->hargas->isNotEmpty())
                    "{{ $produk->id }}": {
                        base: {{ $produk->hargas->first()->harga }},
                        laminasi: {{ $produk->hargas->first()->laminasi ?? 0 }}
                    },
                @endif
            @endforeach
        };

        console.log("Harga Tiered Saat Load:", window.hargaTieredData);
    </script>

    <script>
        // Format Rupiah Dinamis
        function formatRupiah(angka, prefix = 'Rp ') {
            let number_string = angka.replace(/[^0-9]/g, '').toString();
            let sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return prefix + rupiah;
        }

        // Global variables
        // window.customHargaData = {};
        let index = 0;

        // Get tiered pricing based on quantity
        function getHargaTiered(produkId, qty) {
            const tiers = window.hargaTieredData[String(produkId)] || {};
            const keys = Object.keys(tiers).sort((a, b) => {
                const [minA] = a.split('-').map(Number);
                const [minB] = b.split('-').map(Number);
                return minA - minB;
            });

            for (let range of keys) {
                const [min, max] = range.split('-').map(Number);
                if (qty >= min && qty <= max) return parseFloat(tiers[range]) || 0;
            }

            return 0;
        }

        // Get custom pricing
        function getCustomHarga(produkId, sisi, laminasi) {
            const data = window.customHargaData?.[produkId] || {};
            let harga = data.base || 0;
            if (sisi == 2) harga *= 1.2;
            if (laminasi === "ya") harga += data.laminasi || 0;
            return Math.round(harga);
        }

        // Attach event listeners to dynamically created inputs
        function attachInputEventListeners(row) {
            // Bind to all input types
            row.find('input, select').on('input change', function() {
                console.log('Input changed:', this.name, 'Value:', this.value);

                // Special handling for tiered products
                if (this.name.includes('[qty]')) {
                    const currentRow = $(this).closest('.mmt-item');
                    const tipeProduk = currentRow.find('.tipe-produk option:selected').data('tipe-produk');

                    if (tipeProduk === 'tiered') {
                        const produkId = currentRow.find('.tipe-produk').val();
                        const qty = parseInt(this.value) || 0;

                        if (qty > 0) {
                            const hargaSatuan = getHargaTiered(produkId, qty);
                            currentRow.find('input[name$="[harga]"]').val(formatRupiah(hargaSatuan.toString()));
                        }
                    }
                }

                calculateTotal();
            });
        }

        // Handle dropdown changes for product selection
        function attachDropdownChangeHandler() {
            $('.tipe-produk').off('change').on('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const row = $(this).closest('.mmt-item');
                const produkId = selectedOption.value;
                const tipeProduk = selectedOption.getAttribute('data-tipe-produk');
                const currentIndex = $(this).attr('name').match(/\[(\d+)\]/)[1];


                console.log('🔄 Pilih Produk:', produkId, 'Tipe:', tipeProduk, 'Index:', currentIndex);

                // Hapus input sebelumnya
                row.find('.dynamic-inputs').remove();

                // HTML dinamis sesuai tipe produk
                let dynamicHTML = '';
                if (tipeProduk === 'per_meter') {
                    dynamicHTML = `
                <div class="col-md-2 dynamic-inputs panjang-lebar">
                    <label class="form-label">Panjang (m)</label>
                    <input type="number" step="0.1" class="form-control panjang-input"
                           name="items[${currentIndex}][panjang]" placeholder="0.0">
                </div>
                <div class="col-md-2 dynamic-inputs panjang-lebar">
                    <label class="form-label">Lebar (m)</label>
                    <input type="number" step="0.1" class="form-control lebar-input"
                           name="items[${currentIndex}][lebar]" placeholder="0.0">
                </div>
            `;
                } else if (tipeProduk === 'tiered' || tipeProduk === 'flat') {
                    dynamicHTML = `
                <div class="col-md-2 dynamic-inputs qty">
                    <label class="form-label">Jumlah (Qty)</label>
                    <input type="number" class="form-control qty-input"
                           name="items[${currentIndex}][qty]" placeholder="0" min="1">
                </div>
            `;
                } else if (tipeProduk === 'custom') {
                    dynamicHTML = `
                <div class="col-md-2 dynamic-inputs qty">
                    <label class="form-label">Jumlah (Qty)</label>
                    <input type="number" class="form-control qty-input"
                           name="items[${currentIndex}][qty]" placeholder="0" min="1">
                </div>
                <div class="col-md-2 dynamic-inputs custom-inputs">
                    <label class="form-label">Sisi</label>
                    <select class="form-select" name="items[${currentIndex}][sisi]">
                        <option value="1">1 Sisi</option>
                        <option value="2">2 Sisi</option>
                    </select>
                </div>
                <div class="col-md-2 dynamic-inputs laminasi">
                    <label class="form-label">Laminasi</label>
                    <select class="form-select" name="items[${currentIndex}][laminasi]">
                        <option value="tidak">Tidak</option>
                        <option value="ya">Ya</option>
                    </select>
                </div>
            `;
                }

                // Tambahkan ke DOM
                if (dynamicHTML) row.append(dynamicHTML);

                // Set prices
                const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                const diskon = parseFloat(selectedOption.getAttribute('data-diskon')) || 0;
                row.find('input[name$="[harga]"]').val(formatRupiah(harga.toString()));
                row.find('input[name$="[diskonbarang]"]').val(formatRupiah(diskon.toString()));
                // ❗ Penting: Tunggu 100ms agar elemen ter-render sebelum pasang event & hitung total
                setTimeout(() => {
                    attachInputEventListeners(row);
                    calculateTotal();
                }, 100);
            });
        }


        // Calculate total with improved debugging
        function calculateTotal() {
            let subtotal = 0;
            console.log('\n=== CALCULATE TOTAL START ===');

            $('.mmt-item').each(function(i) {
                const row = $(this);
                const tipeSelect = row.find('.tipe-produk');
                const selected = tipeSelect.find('option:selected');
                const produkId = tipeSelect.val();
                const tipeProduk = selected.data('tipe-produk');
                let itemTotal = 0;

                console.log(`--- Row ${i+1} ---`);
                console.log('Produk ID:', produkId);
                console.log('Tipe Produk:', tipeProduk);

                if (!produkId || !tipeProduk) {
                    console.warn('⚠️ Row skipped: No product selected');
                    return;
                }

                // FIX 1: Proper handling for per_meter products
                if (tipeProduk === 'per_meter') {
                    const panjang = parseFloat(row.find('input[name$="[panjang]"]').val()) || 0;
                    const lebar = parseFloat(row.find('input[name$="[lebar]"]').val()) || 0;
                    const harga = parseFloat(row.find('input[name$="[harga]"]').val().replace(/[^0-9]/g, '')) || 0;

                    console.log('PER_METER - Panjang:', panjang, 'Lebar:', lebar, 'Harga:', harga);

                    if (panjang > 0 && lebar > 0) {
                        itemTotal = panjang * lebar * harga;
                        console.log('✅ Luas:', panjang * lebar, 'm² | Total:', itemTotal);
                    } else {
                        console.warn('⚠️ Invalid dimensions for per_meter');
                    }
                }
                // Other product types remain unchanged
                else if (tipeProduk === 'tiered') {
                    const harga = parseFloat(row.find('input[name$="[harga]"]').val().replace(/[^0-9]/g, '')) || 0;
                    itemTotal = harga;
                    console.log('✅ Tiered calculation:', harga);
                } else if (tipeProduk === 'flat') {
                    const qty = parseInt(row.find('input[name$="[qty]"]').val()) || 0;
                    const harga = parseFloat(row.find('input[name$="[harga]"]').val().replace(/[^0-9]/g, '')) || 0;
                    itemTotal = qty * harga;
                    console.log('✅ Flat calculation:', qty, 'x', harga, '=', itemTotal);
                } else if (tipeProduk === 'custom') {
                    const qty = parseInt(row.find('input[name$="[qty]"]').val()) || 0;
                    const sisi = row.find('select[name$="[sisi]"]').val();
                    const laminasi = row.find('select[name$="[laminasi]"]').val();
                    const harga = getCustomHarga(produkId, sisi, laminasi);
                    itemTotal = qty * harga;
                    console.log('✅ Custom calculation:', qty, 'x', harga, '=', itemTotal);
                }

                subtotal += itemTotal;
                console.log('Item total:', itemTotal);
                console.log('Running subtotal:', subtotal);
            });

            // Final calculations
            const desain = parseInt($('#biaya_desain').val().replace(/[^0-9]/g, '')) || 0;
            const diskon = parseInt($('#diskon').val().replace(/[^0-9]/g, '')) || 0;
            const dpValue = parseInt($('#dp').val().replace(/[^0-9]/g, '')) || 0;

            const totalAkhir = subtotal + desain - diskon;
            const sisaBayar = totalAkhir - dpValue;

            // Update UI
            $('#total_raw').val(totalAkhir);
            $('#total').val(formatRupiah(totalAkhir.toString()));
            updateDpField();

            // Update nota ringkasan
            if ($('#nota-total').length > 0) {
                $('#nota-subtotal').text(subtotal.toLocaleString('id-ID'));
                $('#nota-biaya-desain').text(desain.toLocaleString('id-ID'));
                $('#nota-diskon').text(diskon.toLocaleString('id-ID'));
                $('#nota-dp').text(dpValue.toLocaleString('id-ID'));
                $('#nota-total').text(sisaBayar.toLocaleString('id-ID'));
            }
        }


        // Update DP field requirements
        function updateDpField() {
            let rawTotal = $('#total').val().replace(/[^0-9]/g, '');
            let totalAkhir = parseInt(rawTotal) || 0;
            const dpField = $('#dpContainer');
            const dpInput = $('#dp');
            const dpWarning = $('#dpWarning');

            if (totalAkhir >= 300000) {
                dpField.fadeIn(200);
                let dpMin = Math.round(totalAkhir * 0.5);
                dpInput.val(dpMin.toLocaleString());
                dpInput.data('min-dp', dpMin);
                dpInput.off('input').on('input', function() {
                    let newDp = parseInt($(this).val().replace(/[^0-9]/g, '')) || 0;
                    let minDp = $(this).data('min-dp');
                    if (newDp < minDp && newDp > 0) {
                        dpWarning.text(`DP minimal Rp${minDp.toLocaleString()}`).fadeIn(200);
                    } else {
                        dpWarning.hide();
                    }
                });
            } else {
                dpField.fadeOut(200);
                dpInput.val('').removeAttr('required');
                dpWarning.hide();
            }
        }

        // Add new item
        $('#btnAddItem').click(function() {
            const html = `
            <div class="mmt-item row g-3" style="display: none;">
                <span class="badge bg-primary">Produk ${index + 1}</span>
                <div class="col-md-3">
                    <label class="form-label">Tipe Produk</label>
                    <select class="form-select tipe-produk" name="items[${index}][tipe]" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($showProdak as $produk)
                            <option value="{{ $produk->id }}"
                                    data-harga="{{ optional($produk->hargas->first())->harga ?? 0 }}"
                                    data-diskon="{{ optional($produk->hargas->first())->diskon ?? 0 }}"
                                    data-tipe-produk="{{ $produk->tipe_produk }}">
                                {{ $produk->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Harga Satuan</label>
                    <input type="text" class="form-control rupiah-input" name="items[${index}][harga]" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Diskon Barang</label>
                    <input type="text" class="form-control rupiah-input" name="items[${index}][diskonbarang]" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="items[${index}][keterangan]" rows="2" placeholder="Catatan..."></textarea>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-item w-100">
                        Hapus
                    </button>
                </div>
            </div>`;

            $('#mmtItemsContainer').append(html);
            $('#mmtItemsContainer .mmt-item').last().fadeIn(300);
            index++;
            attachDropdownChangeHandler();
        });

        // Remove item
        $(document).on('click', '.btn-remove-item', function() {
            $(this).closest('.mmt-item').fadeOut(200, function() {
                $(this).remove();
                calculateTotal();
            });
        });

        // Global event delegation for all input changes
        $(document).on('input keyup change',
            'input[name$="[panjang]"], input[name$="[lebar]"], input[name$="[qty]"], #biaya_desain, #diskon, #dp',
            function() {
                console.log('Global input event:', $(this).attr('name') || $(this).attr('id'), 'Value:', $(this).val());
                calculateTotal();
            }
        );

        // Handle tiered price updates when qty changes
        $(document).on('input', 'input[name$="[qty]"]', function() {
            const row = $(this).closest('.mmt-item');
            const produkId = row.find('.tipe-produk').val();
            const qty = parseInt($(this).val()) || 0;
            const tipeProduk = row.find('.tipe-produk option:selected').data('tipe-produk');

            if (tipeProduk === 'tiered' && qty > 0) {
                const hargaSatuan = getHargaTiered(produkId, qty);
                row.find('input[name$="[harga]"]').val(formatRupiah(hargaSatuan.toString()));
                calculateTotal();
            }
        });

        // Form Submissions and Navigation

        // Submit Form Nasabah
        $('#formNasabah').submit(function(e) {
            e.preventDefault();
            const nama = $('#nama').val();
            const telepon = $('#telepon').val();
            const email = $('#email').val() || '-';
            const jenisPelanggan = $('#jenis_pelanggan').val();
            const alamat = $('#alamat').val();

            if (!nama || !telepon || !alamat) {
                alert('Harap isi semua Data Customer yang diperlukan');
                return;
            }

            $('#summary-nama').text(nama);
            $('#summary-telepon').text(telepon);
            $('#summary-email').text(email);
            $('#summary-jenis').text($('#jenis_pelanggan option:selected').text());
            $('#summary-alamat').text(alamat);

            $('#hidden_nama').val(nama);
            $('#hidden_telepon').val(telepon);
            $('#hidden_email').val(email);
            $('#hidden_jenis_pelanggan').val(jenisPelanggan);
            $('#hidden_alamat').val(alamat);

            $('#nasabahForm').fadeOut(300, function() {
                $('#transaksiForm').fadeIn(300);
                $('#step1-indicator').removeClass('active');
                $('#step2-indicator').addClass('active');
                $('#progressBar').css('width', '66.66%');
            });
        });

        // Back to Nasabah
        $('#btnBackToNasabah, #btnEditNasabah').on('click', function() {
            $('#transaksiForm').fadeOut(300, function() {
                $('#nasabahForm').fadeIn(300);
                $('#step2-indicator').removeClass('active');
                $('#step1-indicator').addClass('active');
                $('#progressBar').css('width', '33.33%');
            });
        });

        // Next to Pembayaran
        $('#btnNextToPembayaran').click(function() {
            let isValid = true;

            $('.mmt-item').each(function() {
                const tipeProduk = $(this).find('.tipe-produk option:selected').data('tipe-produk');
                if (tipeProduk === 'per_meter') {
                    const panjang = $(this).find('input[name$="[panjang]"]').val();
                    const lebar = $(this).find('input[name$="[lebar]"]').val();
                    if (!panjang || !lebar) isValid = false;
                } else if (tipeProduk === 'tiered' || tipeProduk === 'flat' || tipeProduk === 'custom') {
                    const qty = $(this).find('input[name$="[qty]"]').val();
                    if (!qty) isValid = false;
                }
            });

            if (!isValid) {
                alert('Harap lengkapi semua data produk!');
                return;
            }

            // Update nota summary
            $('#nota-nama').text($('#summary-nama').text());
            $('#nota-telepon').text($('#summary-telepon').text());
            $('#nota-email').text($('#summary-email').text());
            $('#nota-jenis').text($('#summary-jenis').text());
            $('#nota-alamat').text($('#summary-alamat').text());

            const tanggalSelesai = $('#tanggal_ambil').val();
            $('#nota-tanggal-selesai').text(tanggalSelesai || '-');

            const produkList = [];
            $('.mmt-item').each(function() {
                const tipe = $(this).find('select[name$="[tipe]"] option:selected').text();
                const panjang = $(this).find('input[name$="[panjang]"]').val() || 0;
                const lebar = $(this).find('input[name$="[lebar]"]').val() || 0;
                const qty = $(this).find('input[name$="[qty]"]').val() || 0;
                const keterangan = $(this).find('textarea[name$="[keterangan]"]').val() || '-';

                let detail = '';
                if (panjang > 0 && lebar > 0) {
                    detail = `${tipe} (${panjang}m x ${lebar}m)`;
                } else if (qty > 0) {
                    detail = `${tipe} (Qty: ${qty})`;
                } else {
                    detail = tipe;
                }

                produkList.push(`<li>${detail} <small>(Ket: ${keterangan})</small></li>`);
            });

            $('#nota-produk-list').html(produkList.join(''));

            // Update final calculations
            calculateTotal();

            $('#transaksiForm').fadeOut(300, function() {
                $('#pembayaranForm').fadeIn(300);
                $('#step2-indicator').removeClass('active');
                $('#step3-indicator').addClass('active');
                $('#progressBar').css('width', '100%');
            });
        });

        // Back to Transaksi
        $('#btnBackToTransaksi').click(function() {
            $('#pembayaranForm').fadeOut(300, function() {
                $('#transaksiForm').fadeIn(300);
                $('#step3-indicator').removeClass('active');
                $('#step2-indicator').addClass('active');
                $('#progressBar').css('width', '66.66%');
            });
        });

        // Submit Final Transaction
        $('#btnSelesaiTransaksi').click(function() {
            const payload = {
                customer: {
                    nama: $('#hidden_nama').val(),
                    telepon: $('#hidden_telepon').val(),
                    email: $('#hidden_email').val(),
                    alamat: $('#hidden_alamat').val(),
                    jenis_pelanggan: $('#hidden_jenis_pelanggan').val()
                },
                items: [],
                summary: {
                    biaya_desain: $('#biaya_desain').val(),
                    diskon: $('#diskon').val(),
                    tanggal_ambil: $('#tanggal_ambil').val(),
                    metode_pembayaran: $('#metode_pembayaran').val(),
                    status_pembayaran: $('#status_pembayaran').val(),
                    dp: $('#dp').val(),
                    total: $('#total_raw').val()
                }
            };

            $('.mmt-item').each(function() {
                payload.items.push({
                    tipe: $(this).find('select[name$="[tipe]"]').val(),
                    panjang: $(this).find('input[name$="[panjang]"]').val() || null,
                    lebar: $(this).find('input[name$="[lebar]"]').val() || null,
                    qty: $(this).find('input[name$="[qty]"]').val() || null,
                    sisi: $(this).find('select[name$="[sisi]"]').val() || null,
                    laminasi: $(this).find('select[name$="[laminasi]"]').val() || null,
                    harga: $(this).find('input[name$="[harga]"]').val(),
                    diskonbarang: $(this).find('input[name$="[diskonbarang]"]').val(),
                    keterangan: $(this).find('textarea[name$="[keterangan]"]').val()
                });
            });

            console.log('Payload to submit:', payload);

            $.ajax({
                url: "{{ route('transaksi.store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(payload),
                success: function(res) {
                    alert('Transaksi berhasil disimpan!');
                    console.log('Response:', res);
                    // Optional: redirect or reset form
                    // window.location.href = '/transaksi';
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Gagal menyimpan transaksi. Silakan coba lagi.');
                }
            });
        });

        // Initialize everything when document is ready
        $(document).ready(function() {
            // Set default date
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_ambil').val(today);

            // Add first item
            $('#btnAddItem').trigger('click');

            // Format rupiah inputs
            $(document).on('keyup', '.rupiah-input', function() {
                this.value = formatRupiah(this.value);
            });

            // Global fallback event listener
            $(document).on('input change', 'input[name$="[panjang]"], input[name$="[lebar]"]', function() {
                console.log('Global dimension event:', this.name, this.value);
                calculateTotal();
            });

            // Initial calculation
            setTimeout(calculateTotal, 300);
        });
    </script>
@endsection
