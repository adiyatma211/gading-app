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
    </style>
    <div class="page-heading">
        <h3><i class="bi bi-receipt-cutoff me-2"></i>Form Transaksi</h3>
    </div>
    <section class="section">
        <!-- Step Indicator -->
        <div class="steps mb-4">
            <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary" role="progressbar" id="progressBar" style="width: 50%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <div class="step active" id="step1-indicator">
                    <i class="bi bi-person-circle"></i> Data Customer
                </div>
                <div class="step" id="step2-indicator">
                    <i class="bi bi-cart4"></i> Transaksi MMT
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
        <!-- Step 2: Form Transaksi MMT (awalnya tersembunyi) -->
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
                                    <option value="Frontlite 280">Frontlite 280</option>
                                    <option value="Frontlite 340">Frontlite 340</option>
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
                                <input type="number" class="form-control" name="items[0][harga]" required>
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
                            <i class="bi bi-plus-circle"></i> Tambah MMT
                        </button>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Biaya Desain (Opsional)</label>
                            <input type="number" name="biaya_desain" id="biaya_desain" class="form-control"
                                value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Diskon (Rp)</label>
                            <input type="number" name="diskon" id="diskon" class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Ambil / Selesai</label>
                            <input type="date" name="tanggal_ambil" id="tanggal_ambil" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-4">
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
                        <button type="submit" class="btn btn-success flex-grow-1">
                            <i class="bi bi-check2-circle"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- SCRIPT -->
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
                const harga = parseFloat($(this).find('input[name$="[harga]"]').val()) || 0;
                subtotal += panjang * lebar * harga;
            });
            const desain = parseFloat($('#biaya_desain').val()) || 0;
            const diskon = parseFloat($('#diskon').val()) || 0;
            const total = subtotal + desain - diskon;
            $('#total').val(total.toLocaleString('id-ID'));
        }

        // Menambah item MMT baru
        $('#btnAddItem').click(function() {
            const html = `
            <div class="mmt-item row g-3" style="display: none;">
                <span class="badge">Prodak ${index + 1}</span>
                <div class="col-md-3">
                    <label class="form-label">Tipe MMT</label>
                    <select class="form-select" name="items[${index}][tipe]">
                        <option value="Frontlite 280">Frontlite 280</option>
                        <option value="Frontlite 340">Frontlite 340</option>
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
                    <input type="number" class="form-control" name="items[${index}][harga]" required>
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
            </div>`;
            $('#mmtItemsContainer').append(html);
            $('#mmtItemsContainer .mmt-item').last().fadeIn(300);
            index++;
        });

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
                $('#progressBar').css('width', '100%');
            });
        });

        // Kembali ke form nasabah
        $('#btnBackToNasabah, #btnEditNasabah').click(function() {
            $('#transaksiForm').fadeOut(300, function() {
                $('#nasabahForm').fadeIn(300);
                $('#step2-indicator').removeClass('active');
                $('#step1-indicator').addClass('active');
                $('#progressBar').css('width', '50%');
            });
        });

        // Submit transaksi
        $('#formTransaksi').submit(function(e) {
            e.preventDefault();

            // Validasi form transaksi
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

            // Menampilkan pesan sukses
            Swal.fire({
                title: 'Transaksi Berhasil!',
                text: 'Data Customer dan transaksi MMT telah disimpan.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Transaksi Baru',
                cancelButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset semua form dan kembali ke langkah pertama
                    resetAllForms();
                }
            });
        });

        // Fungsi untuk mereset semua form
        function resetAllForms() {
            $('#formNasabah')[0].reset();
            $('#formTransaksi')[0].reset();

            // Reset juga item MMT menjadi hanya 1
            $('#mmtItemsContainer').html(`
            <div class="mmt-item row g-3">
                <span class="badge">Prodak 1</span>
                <div class="col-md-3">
                    <label class="form-label">Tipe MMT</label>
                    <select class="form-select" name="items[0][tipe]">
                        <option value="Frontlite 280">Frontlite 280</option>
                        <option value="Frontlite 340">Frontlite 340</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Panjang (m)</label>
                    <input type="number" step="0.1" class="form-control" name="items[0][panjang]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lebar (m)</label>
                    <input type="number" step="0.1" class="form-control" name="items[0][lebar]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Harga/m</label>
                    <input type="number" class="form-control" name="items[0][harga]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea class="form-control" name="items[0][keterangan]" rows="2" placeholder="Tambahkan catatan untuk produk ini..."></textarea>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-remove-item w-100">
                        <i class="bi bi-x-circle"></i> Hapus
                    </button>
                </div>
            </div>`);
            index = 1;
            $('#total').val('0');

            // Kembali ke step 1
            $('#transaksiForm').hide();
            $('#nasabahForm').show();
            $('#step2-indicator').removeClass('active');
            $('#step1-indicator').addClass('active');
            $('#progressBar').css('width', '50%');

            // Set tanggal hari ini
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_ambil').val(today);
        }

        // Set tanggal hari ini saat halaman dimuat
        $(document).ready(function() {
            const today = new Date().toISOString().split('T')[0];
            $('#tanggal_ambil').val(today);
        });
    </script>
@endsection
