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
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label d-block">Sumber Data Customer</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode_customer" id="modeBaru"
                                    value="baru" checked>
                                <label class="form-check-label" for="modeBaru">Pelanggan Baru</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode_customer" id="modeTerdaftar"
                                    value="terdaftar">
                                <label class="form-check-label" for="modeTerdaftar">Pelanggan Terdaftar</label>
                            </div>
                        </div>
                        <div class="col-md-12" id="customerTerdaftarContainer" style="display:none;">
                            <label class="form-label">Pilih Customer</label>
                            <select class="form-select" id="select_customer">
                                <option value="">-- Pilih Customer --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" data-nama="{{ $c->nama }}"
                                        data-telepon="{{ $c->telepon }}" data-email="{{ $c->email }}"
                                        data-jenis="{{ $c->jenis_pelanggan }}" data-alamat="{{ $c->alamat }}">
                                        {{ $c->nama }} - {{ $c->telepon }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Jika tidak ada di daftar, pilih Pelanggan Baru.</small>
                        </div>
                    </div>
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
                    <input type="hidden" name="selected_customer_id" id="selected_customer_id">
                    <input type="hidden" name="nama_nasabah" id="hidden_nama">
                    <input type="hidden" name="telepon_nasabah" id="hidden_telepon">
                    <input type="hidden" name="email_nasabah" id="hidden_email">
                    <input type="hidden" name="jenis_pelanggan" id="hidden_jenis_pelanggan">
                    <input type="hidden" name="alamat_nasabah" id="hidden_alamat">

                    <div id="mmtItemsContainer">
                        {{-- <div class="mmt-item row g-3">
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
                        </div> --}}
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
                                class="form-control rupiah-input" value="0" placeholder="0">
                        </div>
                        <div class="col-md-4 d-none">
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
                    <div class="col-md-4" id="dpOverrideContainer" style="display: none;">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="dp_override">
                            <label class="form-check-label" for="dp_override">Prioritas (Bebas DP)</label>
                        </div>
                        <small class="text-muted">Jika aktif, batas minimal 50% dinonaktifkan.</small>
                    </div>
                    <div class="col-md-4" id="dpContainer" style="display: none;">
                        <label class="form-label">Down Payment (DP)</label>
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
                    <button type="button" class="btn btn-outline-primary" id="btnShowNota" style="display:none;">
                        <i class="bi bi-printer"></i> Tampilkan Nota
                    </button>
                    <button type="button" class="btn btn-outline-dark" id="btnPrintThermal" style="display:none;">
                        <i class="bi bi-printer"></i> Cetak Thermal
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnTestThermal">
                        <i class="bi bi-activity"></i> Tes Koneksi Printer
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

        // Sanitize desimal: pakai titik, bukan koma
        function sanitizeDecimalString(str) {
            if (str == null) return '';
            let s = String(str).replace(/,/g, '.'); // ganti koma -> titik
            // sisakan digit dan titik
            s = s.replace(/[^0-9.]/g, '');
            // hanya satu titik diperbolehkan
            const parts = s.split('.');
            if (parts.length > 2) {
                s = parts[0] + '.' + parts.slice(1).join('');
            }
            return s;
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

            // Khusus panjang/lebar: paksa titik sebagai desimal
            row.on('input', '.panjang-input, .lebar-input', function() {
                const cleaned = sanitizeDecimalString(this.value);
                if (this.value !== cleaned) this.value = cleaned;
            });
        }


        function attachDropdownChangeHandler() {
            $('.tipe-produk').off('change').on('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const row = $(this).closest('.mmt-item');
                const produkId = selectedOption.value;
                const tipeProduk = selectedOption.getAttribute('data-tipe-produk');
                const currentIndex = $(this).attr('name').match(/\[(\d+)\]/)[1];

                row.find('.dynamic-inputs').remove();

                let dynamicHTML = '';
                if (tipeProduk === 'per_meter') {
                    dynamicHTML = `
                    <div class="col-md-2 dynamic-inputs panjang-lebar">
                        <label class="form-label">Panjang (m)</label>
                        <input type="number" step="0.1" inputmode="decimal" lang="en" class="form-control panjang-input" name="items[${currentIndex}][panjang]" placeholder="0.0">
                    </div>
                    <div class="col-md-2 dynamic-inputs panjang-lebar">
                        <label class="form-label">Lebar (m)</label>
                        <input type="number" step="0.1" inputmode="decimal" lang="en" class="form-control lebar-input" name="items[${currentIndex}][lebar]" placeholder="0.0">
                    </div>`;
                } else if (tipeProduk === 'tiered' || tipeProduk === 'flat') {
                    dynamicHTML = `
                    <div class="col-md-2 dynamic-inputs qty">
                        <label class="form-label">Jumlah (Qty)</label>
                        <input type="number" class="form-control qty-input" name="items[${currentIndex}][qty]" placeholder="0" min="1">
                    </div>`;
                } else if (tipeProduk === 'custom') {
                    dynamicHTML = `
                    <div class="col-md-2 dynamic-inputs qty">
                        <label class="form-label">Jumlah (Qty)</label>
                        <input type="number" class="form-control qty-input" name="items[${currentIndex}][qty]" placeholder="0" min="1">
                    </div>
                    <div class="col-md-2 dynamic-inputs custom-inputs">
                        <label class="form-label">Sisi</label>
                        <select class="form-select" name="items[${currentIndex}][sisi]">
                            <option value="1">1 Sisi</option>
                            <option value="2">2 Sisi</option>
                        </select>
                    </div>`;
                }

                if (dynamicHTML) row.append(dynamicHTML);

                const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                const diskon = parseFloat(selectedOption.getAttribute('data-diskon')) || 0;
                row.find('input[name$="[harga]"]').val(formatRupiah(harga.toString()));
                row.find('input[name$="[diskonbarang]"]').val(formatRupiah(diskon.toString()));

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

                console.log(`--- Row ${i + 1} ---`);
                console.log('Produk ID:', produkId);
                console.log('Tipe Produk:', tipeProduk);

                if (!produkId || !tipeProduk) {
                    console.warn('⚠️ Row skipped: No product selected');
                    return;
                }

                if (tipeProduk === 'per_meter') {
                    const panjang = parseFloat(row.find('.panjang-input').val()) || 0;
                    const lebar = parseFloat(row.find('.lebar-input').val()) || 0;
                    const harga = parseFloat(row.find('input[name$="[harga]"]').val().replace(/[^0-9]/g, '')) || 0;
                    const diskonbarang = parseFloat(row.find('input[name$="[diskonbarang]"]').val().replace(
                        /[^0-9]/g, '')) || 0;

                    console.log('PER_METER - Panjang:', panjang, 'Lebar:', lebar, 'Harga:', harga, 'diskonbarang:',
                        diskonbarang);

                    if (panjang > 0 && lebar > 0) {
                        // Diskon dianggap per meter -> turunkan harga satuan terlebih dulu
                        const hargaNet = Math.max(harga - diskonbarang, 0);
                        itemTotal = panjang * lebar * hargaNet;
                    } else {
                        console.warn('⚠️ Invalid dimensions for per_meter');
                    }
                } else if (tipeProduk === 'flat') {
                    const qty = parseInt(row.find('input[name$="[qty]"]').val()) || 0;
                    const harga = parseFloat(row.find('input[name$="[harga]"]').val().replace(/[^0-9]/g, '')) || 0;

                    itemTotal = qty * harga;
                    console.log('FLAT - Qty:', qty, 'Harga:', harga, 'Total:', itemTotal);
                } else if (tipeProduk === 'tiered') {
                    const harga = parseFloat(row.find('input[name$="[harga]"]').val().replace(/[^0-9]/g, '')) || 0;
                    const qty = parseInt(row.find('input[name$="[qty]"]').val()) || 0;

                    itemTotal = qty * harga;
                    console.log('TIERED - Harga (otomatis sesuai qty):', harga);
                } else if (tipeProduk === 'custom') {
                    const qty = parseInt(row.find('input[name$="[qty]"]').val()) || 0;
                    const sisi = row.find('select[name$="[sisi]"]').val();
                    const laminasi = row.find('select[name$="[laminasi]"]').val() || 'tidak';

                    const harga = getCustomHarga(produkId, sisi, laminasi);
                    itemTotal = qty * harga;

                    console.log('CUSTOM - Qty:', qty, 'Harga:', harga, 'Total:', itemTotal);
                }

                subtotal += itemTotal;
                console.log('Item total:', itemTotal);
                console.log('Running subtotal:', subtotal);
            });

            const desain = parseInt($('#biaya_desain').val().replace(/[^0-9]/g, '')) || 0;
            const diskon = parseInt($('#diskon').val().replace(/[^0-9]/g, '')) || 0;
            const dpValue = parseInt($('#dp').val().replace(/[^0-9]/g, '')) || 0;

            const totalAkhir = subtotal + desain - diskon;
            const sisaBayar = totalAkhir - dpValue;

            $('#total_raw').val(totalAkhir);
            $('#total').val(formatRupiah(totalAkhir.toString()));

            // Update UI
            // $('#total_raw').val(totalAkhir);
            // $('#total').val(formatRupiah(totalAkhir.toString()));
            updateDpField();

            // Update nota ringkasan
            if ($('#nota-total').length > 0) {
                $('#nota-subtotal').text(subtotal.toLocaleString('id-ID'));
                $('#nota-biaya-desain').text(desain.toLocaleString('id-ID'));
                $('#nota-diskon').text(diskon.toLocaleString('id-ID'));
                $('#nota-dp').text(dpValue.toLocaleString('id-ID'));
                $('#nota-total').text(sisaBayar.toLocaleString('id-ID'));
            }
            window.subtotalTerakhir = subtotal;
        }



        function updateDpField() {
            let rawTotal = $('#total').val().replace(/[^0-9]/g, '');
            let totalAkhir = parseInt(rawTotal) || 0;
            const dpField = $('#dpContainer');
            const dpInput = $('#dp');
            const dpWarning = $('#dpWarning');
            const statusPembayaran = $('#status_pembayaran').val()?.toLowerCase();

            // Jika status pembayaran LUNAS, DP disembunyikan
            if (statusPembayaran === 'lunas') {
                dpField.fadeOut(200);
                dpInput.val('');
                dpInput.removeAttr('required');
                dpWarning.hide();
            } else {
                // Status bukan lunas → lanjut cek apakah total >= 300rb
                if (totalAkhir >= 300000) {
                    dpField.fadeIn(200);
                    let dpMin = Math.round(totalAkhir * 0.5);

                    dpInput.attr('required', true);
                    dpInput.data('min-dp', dpMin);

                    // Hanya tampilkan default jika DP masih kosong
                    if (dpInput.val() === null || dpInput.val().trim() === '') {
                        dpInput.val(dpMin.toLocaleString());
                    }

                    // Validasi saat user input DP
                    dpInput.off('input').on('input', function() {
                        let rawVal = $(this).val().replace(/[^0-9]/g, '');
                        let newDp = parseInt(rawVal) || 0;
                        let minDp = $(this).data('min-dp');

                        if (!rawVal || newDp < minDp) {
                            dpWarning.text(`DP minimal Rp${minDp.toLocaleString()}`).fadeIn(200);
                        } else {
                            dpWarning.hide();
                        }

                        $(this).val(formatRupiah(rawVal));
                    });

                } else {
                    // Total < 300rb → DP disembunyikan
                    dpField.fadeOut(200);
                    dpInput.val('');
                    dpInput.removeAttr('required');
                    dpWarning.hide();
                }
            }
        }


        // Add new item
        $('#btnAddItem').click(function() {
            const html = `
            <div class="mmt-item row g-3" style="display: none;">
               <div class="col-12">
                    <span class="badge bg-primary rounded-pill px-3 py-2">Produk ${index + 1}</span>
                </div>
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

        // Quick-access nota button after save
        $(document).on('click', '#btnShowNota', function() {
            if (!savedNotaFile) return;

            // DEBUG: Log the original file path
            console.log('DEBUG: Original savedNotaFile:', savedNotaFile);

            // Check if the file path is in old format (starts with "nota/")
            let url;
            if (savedNotaFile.startsWith('nota/')) {
                // Old format: use legacy-pdf endpoint
                const filename = savedNotaFile.replace('nota/', '');
                url = "{{ url('/legacy-pdf') }}" + '/' + encodeURIComponent(filename);
                console.log('DEBUG: Using legacy PDF endpoint for old format file');
            } else {
                // New format: use pdf-storage endpoint
                url = "{{ url('/pdf-storage') }}" + '/' + encodeURIComponent(savedNotaFile);
                console.log('DEBUG: Using pdf-storage endpoint for new format file');
            }

            // DEBUG: Log the final URL
            console.log('DEBUG: Final PDF URL:', url);
            console.log('DEBUG: Saved nota file:', savedNotaFile);

            // AJAX approach to maintain authentication context
            console.log('DEBUG: Making AJAX request for PDF viewing...');
            $.ajax({
                url: url,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                beforeSend: function(xhr) {
                    console.log('DEBUG: Sending AJAX request with authentication context');
                },
                success: function(data, status, xhr) {
                    console.log('DEBUG: AJAX Success - Status:', status);
                    console.log('DEBUG: Response headers:', xhr.getAllResponseHeaders());
                    console.log('DEBUG: Content type:', xhr.getResponseHeader('Content-Type'));
                    console.log('DEBUG: Blob size:', data.size, 'bytes');
                    
                    // Create object URL from blob and open in new window
                    const blobUrl = URL.createObjectURL(data);
                    console.log('DEBUG: Created blob URL:', blobUrl);
                    
                    // Open in new window
                    const newWindow = window.open(blobUrl, '_blank');
                    if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                        console.error('DEBUG: Popup blocked or failed to open');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Popup Diblokir',
                            text: 'Browser memblokir popup. Silakan izinkan popup untuk situs ini atau coba lagi.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        console.log('DEBUG: PDF opened successfully in new window');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('DEBUG: AJAX Error - Status:', status);
                    console.log('DEBUG: Error:', error);
                    console.log('DEBUG: Response text:', xhr.responseText);
                    console.log('DEBUG: Response status:', xhr.status);
                    console.log('DEBUG: Response headers:', xhr.getAllResponseHeaders());
                    
                    // Show user-friendly error message
                    let errorMessage = 'Terjadi kesalahan saat membuka file PDF. Silakan coba lagi.';
                    if (xhr.status === 403) {
                        errorMessage = 'Akses ditolak. Sesi Anda mungkin telah kadaluarsa. Silakan refresh halaman dan coba lagi.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'File PDF tidak ditemukan. File mungkin telah dihapus atau dipindahkan.';
                    } else if (xhr.status >= 500) {
                        errorMessage = 'Terjadi kesalahan pada server. Silakan hubungi administrator.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membuka PDF',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Cetak langsung ke thermal printer (ESC/POS via server)
        $(document).on('click', '#btnPrintThermal', function() {
            if (!savedTransactionId) return;
            $.ajax({
                url: "{{ url('/transaksi/print-thermal') }}/" + savedTransactionId,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terkirim ke Printer',
                        timer: 1200,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    let message = 'Gagal mengirim ke printer thermal.';
                    try {
                        const json = JSON.parse(xhr.responseText);
                        message = json.message || message;
                    } catch (e) {}
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Cetak',
                        text: message
                    });
                }
            });
        });

        // Tes koneksi printer thermal
        $(document).on('click', '#btnTestThermal', function() {
            $.ajax({
                url: "{{ route('transaksi.printThermal.test') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Printer Terdeteksi',
                        text: res.printer || 'OK',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    let message = 'Tidak dapat terhubung ke printer thermal.';
                    try {
                        const json = JSON.parse(xhr.responseText);
                        message = json.message || message;
                    } catch (e) {}
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: message
                    });
                }
            });
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

        // Toggle customer mode
        $(document).on('change', 'input[name="mode_customer"]', function() {
            const mode = $(this).val();
            if (mode === 'terdaftar') {
                $('#customerTerdaftarContainer').slideDown(150);
                // disable manual fields
                $('#nama, #telepon, #email, #alamat').prop('disabled', true);
            } else {
                $('#customerTerdaftarContainer').slideUp(150);
                $('#select_customer').val('');
                $('#selected_customer_id').val('');
                // enable manual fields
                $('#nama, #telepon, #email, #alamat').prop('disabled', false);
            }
        });

        // Prefill manual fields when choosing existing customer (for preview/editing if needed)
        $(document).on('change', '#select_customer', function() {
            const opt = $(this).find('option:selected');
            const cid = opt.val();
            if (!cid) {
                return;
            }
            const nama = opt.data('nama') || '';
            const telepon = opt.data('telepon') || '';
            const email = opt.data('email') || '-';
            const jenis = opt.data('jenis') || '';
            const alamat = opt.data('alamat') || '';

            $('#selected_customer_id').val(cid);
            // Keep manual fields disabled in terdaftar mode; we only mirror to summary later
            $('#summary-nama').text(nama);
            $('#summary-telepon').text(telepon);
            $('#summary-email').text(email);
            $('#summary-jenis').text(jenis);
            $('#summary-alamat').text(alamat);
        });

        // Submit Form Nasabah
        $('#formNasabah').submit(function(e) {
            e.preventDefault();
            const mode = $('input[name="mode_customer"]:checked').val();
            let nama, telepon, email, jenisPelanggan, jenisLabel, alamat, selectedId = '';

            if (mode === 'terdaftar') {
                const opt = $('#select_customer option:selected');
                selectedId = opt.val();
                if (!selectedId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Customer',
                        text: 'Silakan pilih customer terdaftar.'
                    });
                    return;
                }
                nama = opt.data('nama') || '';
                telepon = opt.data('telepon') || '';
                email = opt.data('email') || '-';
                jenisPelanggan = opt.data('jenis') || '';
                jenisLabel = jenisPelanggan ? jenisPelanggan : 'Pelanggan Terdaftar';
                alamat = opt.data('alamat') || '';
                $('#selected_customer_id').val(selectedId);
            } else {
                nama = $('#nama').val();
                telepon = $('#telepon').val();
                email = $('#email').val() || '-';
                jenisPelanggan = '';
                jenisLabel = 'Pelanggan Baru';
                alamat = $('#alamat').val();
                $('#selected_customer_id').val('');
            }

            if (!nama || !telepon || !alamat) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Harap isi semua Data Customer yang diperlukan',
                    confirmButtonText: 'OK'
                });
                return;
            }


            $('#summary-nama').text(nama);
            $('#summary-telepon').text(telepon);
            $('#summary-email').text(email);
            $('#summary-jenis').text(jenisLabel || '-');
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Produk Tidak Lengkap',
                    text: 'Harap lengkapi semua data produk sebelum melanjutkan!',
                    confirmButtonText: 'OK'
                });
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
                if (transactionSaved) {
                    setReadOnlyMode(true);
                }
            });
        });

        function isDpValid() {
            const total = parseInt($('#total_raw').val().replace(/[^0-9]/g, '')) || 0;
            const dp = parseInt($('#dp').val().replace(/[^0-9]/g, '')) || 0;
            const status = $('#status_pembayaran').val()?.toLowerCase();
            const dpMin = Math.round(total * 0.5);

            // Jika bukan "lunas" dan DP di bawah 50%
            if (status !== 'lunas' && dp < dpMin) {
                return {
                    valid: false,
                    dpMin: dpMin,
                    dpNow: dp
                };
            }

            return {
                valid: true
            };
        }

        // Submit Final Transaction
        let isSubmitting = false;
        let transactionSaved = false;
        let savedNotaFile = null;
        let savedTransactionId = null;

        function setReadOnlyMode(flag) {
            const disabled = !!flag;
            $('#nasabahForm :input, #transaksiForm :input, #pembayaranForm :input').prop('disabled', disabled);
            // Keep nav/nota buttons usable
            $('#btnBackToNasabah, #btnEditNasabah, #btnBackToTransaksi, #btnShowNota').prop('disabled', false);
            if (disabled) {
                $('#btnAddItem').hide();
                $('.btn-remove-item').hide();
            }
        }
        $('#btnSelesaiTransaksi').click(function() {
            if (isSubmitting) return;
            isSubmitting = true;
            const $btnSave = $('#btnSelesaiTransaksi');
            const originalHtml = $btnSave.html();
            $btnSave.data('original-html', originalHtml);
            $btnSave.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...');
            calculateTotal();

            const payload = {
                customer: {
                    nama: $('#hidden_nama').val(),
                    telepon: $('#hidden_telepon').val(),
                    email: $('#hidden_email').val(),
                    alamat: $('#hidden_alamat').val(),
                    jenis_pelanggan: $('#hidden_jenis_pelanggan').val()
                },
                customer_id: $('#selected_customer_id').val() || null,
                items: [],
                summary: {
                    biaya_desain: $('#biaya_desain').val(),
                    diskon: $('#diskon').val(),
                    tanggal_ambil: $('#tanggal_ambil').val(),
                    metode_pembayaran: $('#metode_pembayaran').val(),
                    status_pembayaran: $('#status_pembayaran').val(),
                    dp_override: $('#dp_override').is(':checked') ? 1 : 0,
                    dp: $('#dp').val() || 0,
                    subtotal: window.subtotalTerakhir || 0,
                    total: $('#total_raw').val(),
                    bukti_pembayaran: $('#bukti_pembayaran').val() || null
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
                    diskonbarang: $(this).find('input[name$="[diskonbarang]"]').val().replace(
                        /[^0-9]/g, '') || '0',
                    keterangan: $(this).find('textarea[name$="[keterangan]"]').val()
                });
            });


            console.log('Payload to submit:', payload);
            const cek = isDpValid();
            if (!cek.valid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'DP Tidak Cukup',
                    text: `DP minimal adalah 50% dari total (Rp${cek.dpMin.toLocaleString()}). Saat ini hanya Rp${cek.dpNow.toLocaleString()}).`,
                    confirmButtonText: 'Perbaiki'
                });
                $('#dp').focus();
                return;
            }

            $.ajax({
                url: "{{ route('transaksi.store') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify(payload),
                success: function(res) {
                    // Hide save button to avoid double action after successful save
                    $('#btnSelesaiTransaksi').hide();
                    transactionSaved = true;
                    savedNotaFile = res.nota_file || null;
                    savedTransactionId = res.transaction_id || null;
                    $('#btnShowNota').show();
                    isSubmitting = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        text: 'Apakah Anda ingin melihat nota sekarang?',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, tampilkan nota',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // DEBUG: Log the file path from response
                            console.log('DEBUG: nota_file from response:', res.nota_file);
                            
                            // Check if the file path is in old format (starts with "nota/")
                            let url;
                            if (res.nota_file.startsWith('nota/')) {
                                // Old format: use legacy-pdf endpoint
                                const filename = res.nota_file.replace('nota/', '');
                                url = "{{ url('/legacy-pdf') }}" + '/' + encodeURIComponent(filename);
                                console.log('DEBUG: Using legacy PDF endpoint for old format file after save');
                            } else {
                                // New format: use pdf-storage endpoint
                                url = "{{ url('/pdf-storage') }}" + '/' + encodeURIComponent(res.nota_file);
                                console.log('DEBUG: Using pdf-storage endpoint for new format file after save');
                            }

                            // DEBUG: Log the final URL
                            console.log('DEBUG: Final PDF URL after save:', url);
                            console.log('DEBUG: Nota file from response:', res.nota_file);

                            console.log('DEBUG: Encoded file path:', encodeURIComponent(res.nota_file));

                            // Test URL with AJAX first to see response
                            console.log('DEBUG: Making AJAX request for PDF after save...');
                            $.ajax({
                                url: url,
                                method: 'GET',
                                xhrFields: {
                                    responseType: 'blob'
                                },
                                beforeSend: function(xhr) {
                                    console.log('DEBUG: Sending AJAX request with authentication context after save');
                                },
                                success: function(data, status, xhr) {
                                    console.log('DEBUG: AJAX Success after save - Status:', status);
                                    console.log('DEBUG: Response headers:', xhr.getAllResponseHeaders());
                                    console.log('DEBUG: Content type:', xhr.getResponseHeader('Content-Type'));
                                    console.log('DEBUG: Blob size:', data.size, 'bytes');
                                    
                                    // Create object URL from blob and open in new window
                                    const blobUrl = URL.createObjectURL(data);
                                    console.log('DEBUG: Created blob URL after save:', blobUrl);
                                    
                                    // Open in new window
                                    const newWindow = window.open(blobUrl, '_blank');
                                    if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                                        console.error('DEBUG: Popup blocked or failed to open after save');
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Popup Diblokir',
                                            text: 'Browser memblokir popup. Silakan izinkan popup untuk situs ini atau coba lagi.',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        console.log('DEBUG: PDF opened successfully in new window after save');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log('DEBUG: AJAX Error after save - Status:', status);
                                    console.log('DEBUG: Error:', error);
                                    console.log('DEBUG: Response text:', xhr.responseText);
                                    console.log('DEBUG: Response status:', xhr.status);
                                    console.log('DEBUG: Response headers:', xhr.getAllResponseHeaders());
                                    
                                    // Show user-friendly error message
                                    let errorMessage = 'Terjadi kesalahan saat membuka file PDF. Silakan coba lagi.';
                                    if (xhr.status === 403) {
                                        errorMessage = 'Akses ditolak. Sesi Anda mungkin telah kadaluarsa. Silakan refresh halaman dan coba lagi.';
                                    } else if (xhr.status === 404) {
                                        errorMessage = 'File PDF tidak ditemukan. File mungkin telah dihapus atau dipindahkan.';
                                    } else if (xhr.status >= 500) {
                                        errorMessage = 'Terjadi kesalahan pada server. Silakan hubungi administrator.';
                                    }
                                    
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Membuka PDF',
                                        text: errorMessage,
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Transaksi disimpan',
                                text: 'Nota tidak ditampilkan.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });

                    console.log('Response:', res);
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);

                    let message = 'Gagal menyimpan transaksi. Silakan coba lagi.';
                    try {
                        const json = JSON.parse(xhr.responseText);
                        message = json.message || message;
                    } catch (e) {}

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: message,
                        confirmButtonText: 'Tutup'
                    });
                    // Restore button so user can retry
                    const $btnSave = $('#btnSelesaiTransaksi');
                    $btnSave.prop('disabled', false).html($btnSave.data('original-html') ||
                        '<i class="bi bi-check2-circle"></i> Selesai & Simpan');
                    isSubmitting = false;
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

            // Panjang/Lebar: paksa titik sebagai desimal dan bersihkan karakter selain angka/titik
            $(document).on('input', 'input[name$="[panjang]"], input[name$="[lebar]"]', function() {
                const cleaned = sanitizeDecimalString(this.value);
                if (this.value !== cleaned) this.value = cleaned;
            });
            $('#status_pembayaran').on('change', function() {
                updateDpField();
                calculateTotal();
            });
            $(document).on('change', '#dp_override', function() {
                updateDpField();
                calculateTotal();
            });

            // Global fallback event listener
            $(document).on('input change', 'input[name$="[panjang]"], input[name$="[lebar]"]', function() {
                console.log('Global dimension event:', this.name, this.value);
                calculateTotal();
            });

            // Initial calculation
            setTimeout(calculateTotal, 300);
        });

        // Override: DP input + optional per-transaksi prioritas (bebas DP)
        function updateDpField() {
            let totalAkhir = parseInt($('#total').val().replace(/[^0-9]/g, '')) || 0;
            const dpField = $('#dpContainer');
            const dpInput = $('#dp');
            const dpWarning = $('#dpWarning');
            const statusPembayaran = ($('#status_pembayaran').val() || '').toLowerCase();
            const dpOverrideBox = $('#dpOverrideContainer');
            const isOverride = $('#dp_override').is(':checked');

            if (statusPembayaran === 'lunas') {
                dpField.hide();
                dpOverrideBox.hide();
                dpInput.val('');
                dpInput.removeAttr('required');
                dpWarning.hide();
                return;
            }

            // Status DP
            dpOverrideBox.show();
            dpField.show();
            dpInput.attr('required', true);
            const dpMin = (!isOverride && totalAkhir >= 300000) ? Math.round(totalAkhir * 0.5) : 0;
            dpInput.data('min-dp', dpMin);
            if (dpMin > 0 && !dpInput.val()) {
                dpInput.val((dpMin).toLocaleString());
            }
            dpWarning.hide();

            dpInput.off('input').on('input', function() {
                const rawVal = $(this).val().replace(/[^0-9]/g, '');
                const newDp = parseInt(rawVal) || 0;
                const minDp = $(this).data('min-dp') || 0;

                if (!rawVal) {
                    dpWarning.text('Isi nominal DP.').show();
                } else if (minDp > 0 && newDp < minDp) {
                    dpWarning.text(`DP minimal Rp${minDp.toLocaleString()}`).show();
                } else if (newDp > totalAkhir) {
                    dpWarning.text('DP tidak boleh melebihi total.').show();
                } else {
                    dpWarning.hide();
                }
                $(this).val(formatRupiah(rawVal));
            });
        }

        // Override: DP validation logic (respect prioritas override)
        function isDpValid() {
            const total = parseInt($('#total_raw').val().replace(/[^0-9]/g, '')) || 0;
            const dp = parseInt($('#dp').val().replace(/[^0-9]/g, '')) || 0;
            const status = ($('#status_pembayaran').val() || '').toLowerCase();
            const dpMin = Math.round(total * 0.5);

            if (status === 'lunas') return {
                valid: true
            };

            const isOverride = $('#dp_override').is(':checked');
            if (!isOverride && total >= 300000 && dp < dpMin) {
                return {
                    valid: false,
                    message: `DP minimal adalah 50% dari total (Rp${dpMin.toLocaleString()}).`
                };
            }
            if (dp <= 0) return {
                valid: false,
                message: 'Nominal DP harus lebih dari 0.'
            };
            if (dp > total) return {
                valid: false,
                message: 'DP tidak boleh melebihi total.'
            };
            return {
                valid: true
            };
        }
    </script>
@endsection
