<!-- Blade: v_produk.blade.php -->
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
        <div class="modal-dialog modal-dialog-scrollable">
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
                    <div class="mb-3">
                        <label for="editNamaBahan" class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" id="editNamaBahan">
                    </div>
                    <div class="mb-3">
                        <label for="editHargaBahan" class="form-label">Harga per Meter</label>
                        <input type="number" class="form-control" id="editHargaBahan">
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

        function tambahBarisBahan(id = '', nama = '', harga = '') {
            const row = `
                <div class="row mb-2" data-index="${bahanIndex}">
                    <input type="hidden" name="bahan[${bahanIndex}][id]" value="${id}">
                    <div class="col-5">
                        <input type="text" class="form-control" name="bahan[${bahanIndex}][nama_bahan]" value="${nama}" placeholder="Nama Bahan" required>
                    </div>
                    <div class="col-3">
                        <input type="number" class="form-control" name="bahan[${bahanIndex}][harga_per_meter]" value="${harga}" placeholder="Harga per Meter" required>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger btn-remove-bahan">-</button>
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary btn-edit-bahan"
                            data-index="${bahanIndex}" data-nama="${nama}" data-harga="${harga}">
                            Edit
                        </button>
                    </div>
                </div>`;
            $('#bahanContainer').append(row);
            bahanIndex++;
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

        // Modal edit bahan
        let editTargetIndex = null;

        $(document).on('click', '.btn-edit-bahan', function() {
            const index = $(this).data('index');
            const nama = $(this).closest('.row').find('input[name$="[nama_bahan]"]').val();
            const harga = $(this).closest('.row').find('input[name$="[harga_per_meter]"]').val();

            $('#editNamaBahan').val(nama);
            $('#editHargaBahan').val(harga);
            editTargetIndex = index;
            $('#modalEditBahan').modal('show');
        });

        $(document).on('click', '#btnUpdateBahan', function() {
            const namaBaru = $('#editNamaBahan').val();
            const hargaBaru = $('#editHargaBahan').val();
            const row = $(`[data-index='${editTargetIndex}']`);

            row.find('input[name$="[nama_bahan]"]').val(namaBaru);
            row.find('input[name$="[harga_per_meter]"]').val(hargaBaru);
            row.find('.btn-edit-bahan').data('nama', namaBaru).data('harga', hargaBaru);
            $('#modalEditBahan').modal('hide');
        });

        $('#formProduk').submit(function(e) {
            e.preventDefault();

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
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#modalProduk').modal('hide');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        Swal.fire('Gagal', res.message, 'warning');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan produk.', 'error');
                });
        });

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
                                Swal.fire('Berhasil', res.message, 'success');
                                setTimeout(() => location.reload(), 1000);
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
                                Swal.fire('Berhasil', res.message, 'success');
                                setTimeout(() => location.reload(), 1000);
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
    </script>
@endsection
