@extends('layouts.base')
@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Manajemen Produk</h3>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Produk</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 d-flex gap-2">
                        <button type="button" class="btn btn-outline-success btn-show-produk-modal" data-bs-toggle="modal"
                            data-bs-target="#modalProduk">
                            Tambah Produk
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Bahan</th>
                                    <th>Harga per Meter</th>
                                    <th>Diskon</th>
                                    <th>Total Harga</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produk as $index => $item)
                                    @foreach ($item->bahan as $bahan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_produk }}</td>
                                            <td>{{ $bahan->nama_bahan }}</td>
                                            <td>Rp{{ number_format($bahan->harga_per_meter) }}</td>
                                            <td>Rp{{ number_format($bahan->diskon) }}</td>
                                            <td>Rp{{ number_format($bahan->total_harga) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-bahan"
                                                    data-id="{{ $bahan->id }}" data-nama="{{ $bahan->nama_bahan }}">
                                                    Hapus Bahan
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary btn-edit-bahan"
                                                    data-id="{{ $bahan->id }}" data-nama="{{ $bahan->nama_bahan }}"
                                                    data-harga="{{ $bahan->harga_per_meter }}">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Tambah/Edit Produk -->
    <div class="modal fade" id="modalProduk" tabindex="-1" aria-labelledby="modalProdukTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title modal-produk-title">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formProduk">
                    @csrf
                    <input type="hidden" name="id" id="produkId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_produk" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                        </div>

                        <div id="bahanContainer"></div>

                        <div class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-success btn-add-bahan">+ Tambah
                                Bahan</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit-produk">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Bahan -->
    <div class="modal fade" id="modalEditBahan" tabindex="-1" aria-labelledby="modalEditBahanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editBahanId">
                    <div class="mb-3">
                        <label for="editNamaBahan" class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" id="editNamaBahan">
                    </div>

                    <div class="mb-3">
                        <label for="editHargaBahan" class="form-label">Harga per Meter</label>
                        <input type="text" class="form-control harga-input" id="editHargaBahan" placeholder="Rp 0" />
                        <input type="hidden" id="editHargaBahanRaw">
                    </div>
                    <div class="col-md-3">
                        <label for="diskon_bahan" class="form-label">Diskon</label>
                        <input type="number" class="form-control" name="diskon_bahan[]" value="0" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnUpdateBahan">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        let bahanIndex = 0;
        let editTargetIndex = null;

        // Format Rupiah
        function formatRupiah(angka, prefix = 'Rp ') {
            if (typeof angka !== 'string') {
                angka = angka.toString();
            }
            let number_string = angka.replace(/[^,\d]/g, '');
            if (number_string === '' || number_string === '0') {
                return prefix + '0';
            }
            number_string = number_string.replace(/^0+(?!$)/, ''); // Hilangkan leading zero
            const split = number_string.split(',');
            let rupiah = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return prefix + rupiah + (split[1] ? ',' + split[1].substr(0, 2) : '');
        }

        // Extract Numeric Value
        function extractNumericValue(rupiahString) {
            return rupiahString ? parseInt(rupiahString.replace(/[^\d]/g, '')) : 0;
        }


        // Tambah Baris Bahan
        function tambahBarisBahan(id = '', nama = '', harga = '', diskon = '') {
            const row = `
        <div class="row mb-2 bahan-row" data-index="${bahanIndex}">
            <input type="hidden" name="bahan[${bahanIndex}][id]" value="${id}">
            <div class="col-3">
                <input type="text" class="form-control" name="bahan[${bahanIndex}][nama_bahan]" value="${nama}" placeholder="Nama Bahan" required>
            </div>
            <div class="col-2">
                <input type="text" class="form-control harga-input" name="bahan[${bahanIndex}][harga_per_meter]" value="${formatRupiah(harga)}" placeholder="Rp 0" required>
            </div>
            <div class="col-2">
                <input type="text" class="form-control diskon-input" name="bahan[${bahanIndex}][diskon]" value="${formatRupiah(diskon)}" placeholder="Rp 0">
            </div>
            <div class="col-2">
                <input type="text" class="form-control total-harga-input" name="bahan[${bahanIndex}][total_harga]" value="Rp 0" readonly>
            </div>
            <div class="col-2 d-flex align-items-center gap-1">
                <button type="button" class="btn btn-primary btn-edit-row-bahan" data-index="${bahanIndex}" data-id="${id}">Edit</button>
                <button type="button" class="btn btn-danger btn-remove-bahan">Hapus</button>
            </div>
        </div>`;
            $('#bahanContainer').append(row);
            bahanIndex++;
            setupRupiahInputHandlers();
            hitungTotalSemua();
        }

        // Setup Rupiah Input
        function setupRupiahInputHandlers() {
            $('.harga-input, .diskon-input').off('input').on('input', function() {
                const row = $(this).closest('.bahan-row');
                let value = $(this).val().replace(/[^0-9]/g, ''); // Hanya angka

                // Hindari leading zero (0) saat mengetik
                if (value.startsWith('0') && value.length > 1) {
                    value = value.replace(/^0+/, '');
                }

                $(this).val(formatRupiah(value));
                hitungTotalSemua();
            });
        }

        // Hitung Total Semua Bahan
        function hitungTotalSemua() {
            $('.bahan-row').each(function() {
                const row = $(this);
                const harga = extractNumericValue(row.find('.harga-input').val());
                const diskon = extractNumericValue(row.find('.diskon-input').val());
                const total = Math.max(harga - diskon, 0);
                row.find('.total-harga-input').val(formatRupiah(total));
            });

            let totalSemua = 0;
            $('.bahan-row').each(function() {
                const total = extractNumericValue($(this).find('.total-harga-input').val());
                totalSemua += total;
            });

            $('#totalSemua').text(formatRupiah(totalSemua));
        }


        // Prepare Form Data
        function prepareFormData() {
            $('.harga-input, .diskon-input, .total-harga-input').each(function() {
                const rawValue = extractNumericValue($(this).val());
                $(this).val(rawValue);
            });
        }

        // Tambah Bahan Baru
        $(document).on('click', '.btn-add-bahan', function() {
            tambahBarisBahan();
        });

        // Hapus Bahan
        $(document).on('click', '.btn-remove-bahan', function() {
            $(this).closest('.bahan-row').remove();
            hitungTotalSemua();
        });

        // Show Produk Modal (Tambah/Edit)
        $(document).on('click', '.btn-show-produk-modal', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            $('#formProduk')[0].reset();
            $('#produkId').val(id ?? '');
            $('#formProduk').attr('action', id ? `/produk/${id}/update` : `{{ route('produk.store') }}`);
            $('.modal-produk-title').text(id ? 'Edit Produk' : 'Tambah Produk');
            $('#nama_produk').val(nama ?? '');
            $('#bahanContainer').html('');
            bahanIndex = 0;

            const bahanData = JSON.parse($(this).attr('data-bahan') || '[]');
            if (bahanData.length) {
                bahanData.forEach(item => {
                    tambahBarisBahan(item.id ?? '', item.nama_bahan, item.harga_per_meter, item.diskon ??
                        0);
                });
            } else {
                tambahBarisBahan();
            }
            hitungTotalSemua();
        });

        // Form Submission Produk
        $('#formProduk').submit(function(e) {
            e.preventDefault();
            prepareFormData();

            const url = $(this).attr('action');
            const formData = $(this).serialize();

            if ($('[name^="bahan"]').length === 0) {
                Swal.fire('Gagal', 'Setidaknya satu bahan harus ditambahkan.', 'warning');
                return;
            }

            $.post(url, formData)
                .done(function(res) {
                    if (res.status) {
                        Swal.fire('Berhasil', res.message, 'success').then(() => {
                            $('#modalProduk').modal('hide');
                            setTimeout(() => location.reload(), 1000);
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'warning');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan produk.', 'error');
                });
        });

        // Delete Bahan
        $(document).on('click', '.btn-delete-bahan', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const row = $(this).closest('.bahan-row');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Bahan "${nama}" akan dihapus dari database.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan DELETE ke server
                    $.ajax({
                        url: `/bahan/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Tambahkan CSRF Token
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: res.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload setelah klik OK
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'warning');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus bahan.',
                                'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });



        // Initialize
        $(document).ready(function() {
            setupRupiahInputHandlers();
            hitungTotalSemua();
        });
    </script>
@endsection
