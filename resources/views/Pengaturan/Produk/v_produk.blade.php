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

        function formatRupiah(angka, prefix = 'Rp ') {
            // If the input is not a string, convert it to string
            if (typeof angka !== 'string') {
                angka = angka.toString();
            }

            // Remove all non-numeric characters except comma (,)
            let number_string = angka.replace(/[^,\d]/g, '').toString();

            // If empty, return prefix + 0
            if (number_string === '' || number_string === '0') {
                return prefix + '0';
            }

            // Remove leading zeros while maintaining at least one digit
            number_string = number_string.replace(/^0+(?=\d)/, '');

            // Format with thousand separator
            const split = number_string.split(',');
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // Add thousands separators
            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            // Add comma for decimal part if exists
            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix + rupiah;
        }

        // Function to extract numeric value from formatted Rupiah string
        function extractNumericValue(rupiahString) {
            if (!rupiahString) return 0;

            // Jika bukan string, ubah dulu ke string
            if (typeof rupiahString !== 'string') {
                rupiahString = rupiahString.toString();
            }

            return rupiahString.replace(/[^\d]/g, '');
        }


        // Function to add a new material row
        function tambahBarisBahan(id = '', nama = '', harga = '') {
            // Ensure harga is a string for proper handling
            harga = harga.toString();

            const row = `
                <div class="row mb-2" data-index="${bahanIndex}">
                    <input type="hidden" name="bahan[${bahanIndex}][id]" value="${id}">
                    <div class="col-5">
                        <input type="text" class="form-control" name="bahan[${bahanIndex}][nama_bahan]" value="${nama}" placeholder="Nama Bahan" required>
                    </div>
                    <div class="col-3">
                        <input type="text" class="form-control harga-input" name="bahan[${bahanIndex}][harga_per_meter]" value="${formatRupiah(harga)}" placeholder="Rp 0" required>
                        <input type="hidden" name="bahan[${bahanIndex}][harga_raw]" value="${extractNumericValue(harga)}">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-remove-bahan">-</button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-edit-row-bahan" data-index="${bahanIndex}" data-nama="${nama}" data-harga="${harga}" data-id="${id}">
                            Edit
                        </button>
                    </div>
                </div>`;
            $('#bahanContainer').append(row);
            bahanIndex++;

            // Setup event handlers for the newly added row
            setupRupiahInputHandlers();
        }

        // Setup input handlers for Rupiah formatting
        function setupRupiahInputHandlers() {
            // Remove any existing handlers first to prevent duplication
            $('.harga-input').off('keyup change');

            // Add new handlers
            $('.harga-input').on('keyup change', function() {
                // Get the input value
                let value = $(this).val();

                // Extract raw numeric value
                const numericValue = extractNumericValue(value);

                // Format as Rupiah and update input value
                $(this).val(formatRupiah(numericValue));

                // Update the hidden field with raw value
                $(this).closest('.row').find('input[name$="[harga_raw]"]').val(numericValue);
            });
        }

        // Handle form submission - prepare data before submit
        function prepareFormData() {
            $('.harga-input').each(function() {
                // Get the numeric value from the hidden field
                const rawValue = $(this).closest('.row').find('input[name$="[harga_raw]"]').val();

                // Set the raw value to the actual input field for submission
                const inputName = $(this).attr('name');
                $(`input[name="${inputName}"]`).val(rawValue);
            });
        }

        $(document).on('click', '.btn-add-bahan', function() {
            tambahBarisBahan();
        });

        $(document).on('click', '.btn-remove-bahan', function() {
            $(this).closest('.row').remove();
        });

        $(document).on('click', '.btn-show-produk-modal', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            let bahanData;
            try {
                bahanData = JSON.parse($(this).attr('data-bahan'));
            } catch (e) {
                bahanData = [];
            }

            $('#formProduk')[0].reset();
            $('#produkId').val(id ?? '');
            $('#formProduk').attr('action', id ? `/produk/${id}/update` : `{{ route('produk.store') }}`);
            $('.modal-produk-title').text(id ? 'Edit Produk' : 'Tambah Produk');
            $('#nama_produk').val(nama ?? '');
            $('#bahanContainer').html('');
            bahanIndex = 0;

            if (bahanData.length) {
                bahanData.forEach((item) => {
                    tambahBarisBahan(item.id ?? '', item.nama_bahan, item.harga_per_meter);
                });
            } else {
                tambahBarisBahan();
            }
        });

        // Event listener for editing a material row
        $(document).on('click', '.btn-edit-row-bahan', function() {
            const index = $(this).data('index');
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const harga = $(this).data('harga');

            if (typeof index !== 'undefined') {
                const row = $(`[data-index='${index}']`);
                const namaFromInput = row.find('input[name$="[nama_bahan]"]').val();
                const hargaFromInput = row.find('input[name$="[harga_raw]"]').val();

                $('#editNamaBahan').val(namaFromInput);
                $('#editHargaBahan').val(formatRupiah(hargaFromInput));
                $('#editHargaBahanRaw').val(hargaFromInput);
                $('#editBahanId').val(id);
                editTargetIndex = index;
            } else {
                $('#editNamaBahan').val(nama);
                $('#editHargaBahan').val(formatRupiah(harga));
                $('#editHargaBahanRaw').val(extractNumericValue(harga));
                $('#editBahanId').val(id);
                editTargetIndex = null;
            }

            $('#modalEditBahan').modal('show');
        });

        // Event listener for the existing edit buttons in the table
        $(document).on('click', '.btn-edit-bahan', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const harga = $(this).data('harga');

            $('#editNamaBahan').val(nama);
            $('#editHargaBahan').val(formatRupiah(harga));
            $('#editHargaBahanRaw').val(extractNumericValue(harga));
            $('#editBahanId').val(id);
            editTargetIndex = null;

            $('#modalEditBahan').modal('show');
        });

        // Add event handlers for formatting the edit modal's price input
        $('#editHargaBahan').on('keyup change', function() {
            const value = $(this).val();
            const numericValue = extractNumericValue(value);

            $(this).val(formatRupiah(numericValue));
            $('#editHargaBahanRaw').val(numericValue);
        });

        // Event listener for updating the material
        $(document).on('click', '#btnUpdateBahan', function() {
            const namaBaru = $('#editNamaBahan').val();
            const hargaFormatted = $('#editHargaBahan').val(); // ambil string 'Rp 12.000'
            const hargaRaw = extractNumericValue(hargaFormatted); // hasil: 12000
            const bahanId = $('#editBahanId').val();

            console.log('DEBUG | hargaRaw:', hargaRaw, 'namaBaru:', namaBaru); // optional debug

            if (bahanId) {
                $.ajax({
                    url: `/bahan/${bahanId}/update`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama_bahan: namaBaru,
                        harga_per_meter: hargaRaw // Kirim angka murni
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: res.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#modalEditBahan').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message, 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan bahan.', 'error');
                    }
                });
            } else {
                Swal.fire('Error', 'Bahan tidak valid untuk diedit.', 'error');
            }
        });


        // Form submission for product
        $('#formProduk').submit(function(e) {
            e.preventDefault();

            // Prepare the form data before submission
            prepareFormData();

            const form = $(this);
            const url = form.attr('action');
            const formData = form.serialize();

            if ($('[name^="bahan"]').length === 0) {
                Swal.fire('Gagal', 'Setidaknya satu bahan harus ditambahkan.', 'warning');
                return;
            }

            $.post(url, formData)
                .done(function(res) {
                    if (res.status) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: res.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
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

        // Event listener for deleting product
        $(document).on('click', '.btn-delete-produk', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Produk akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/produk/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: res.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    setTimeout(() => location.reload(), 1000);
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'warning');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus produk.',
                                'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-delete-bahan', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Bahan "${nama}" akan dihapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/bahan/${id}`,
                        type: 'DELETE',
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: res.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    setTimeout(() => location.reload(), 1000);
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'warning');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus bahan.',
                                'error');
                        }
                    });
                }
            });
        });

        // Initialize the Rupiah input handlers when the document is ready
        $(document).ready(function() {
            setupRupiahInputHandlers();
        });
    </script>
@endsection
