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
                                    <th>Keterangan</th>
                                    <th>Diskon</th>
                                    <th>Harga</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produk as $index => $item)
                                    @foreach ($item->hargas as $harga)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->nama_produk }}</td>

                                            {{-- Keterangan Produk --}}
                                            @if ($item->tipe_produk === 'per_meter')
                                                <td>Harga per Meter</td>
                                                <td>Rp{{ number_format($harga->diskon ?? 0) }}</td> {{-- Tambahkan ini --}}
                                            @elseif ($item->tipe_produk === 'tiered')
                                                <td>Qty {{ $harga->min_qty }} - {{ $harga->max_qty }}</td>
                                                <td>-</td> {{-- Kosongkan jika bukan per_meter --}}
                                            @elseif ($item->tipe_produk === 'flat')
                                                <td>Banner</td>
                                                <td>-</td>
                                            @elseif ($item->tipe_produk === 'custom')
                                                <td>Kartu Nama ({{ $harga->sisi }} sisi) - Laminasi:
                                                    {{ $harga->laminasi ? 'Ya' : 'Tidak' }}</td>
                                                <td>-</td>
                                            @endif

                                            <td>Rp{{ number_format($harga->harga) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger btn-delete-harga"
                                                    data-id="{{ $harga->id }}" data-nama="{{ $item->nama_produk }}">
                                                    Hapus
                                                </button>
                                                <button type="button" class="btn btn-sm btn-primary btn-edit-produk"
                                                    data-id="{{ $item->id }}" data-nama="{{ $item->nama_produk }}"
                                                    data-tipe="{{ $item->tipe_produk }}"
                                                    data-hargas='@json($item->hargas)'>
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
                        <div class="mb-3">
                            <label for="tipe_produk" class="form-label">Tipe Produk</label>
                            <select class="form-select" id="tipe_produk" name="tipe_produk" required>
                                <option value="">Pilih Tipe</option>
                                <option value="per_meter">MMT (Harga per Meter)</option>
                                <option value="tiered">Cetak A3</option>
                                <option value="flat">Banner</option>
                                <option value="custom">Kartu Nama</option>
                            </select>
                        </div>
                        <div id="hargaContainer"></div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-outline-success btn-add-harga" id="btnAddHarga">+
                                Tambah Harga</button>
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

    <!-- Modal Edit Harga -->
    <div class="modal fade" id="modalEditHarga" tabindex="-1" aria-labelledby="modalEditHargaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Harga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editHargaId">
                    <div class="mb-3">
                        <label for="editHargaValue" class="form-label">Harga</label>
                        <input type="text" class="form-control harga-input" id="editHargaValue" placeholder="Rp 0" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnUpdateHarga">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).on('click', '.btn-edit-produk', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');
            const tipe = $(this).data('tipe');
            const hargas = $(this).data('hargas');

            $('#formProduk')[0].reset();
            $('#produkId').val(id);
            $('#formProduk').attr('action', `/produk/${id}/update`);
            $('.modal-produk-title').text('Edit Produk');
            $('#methodField').remove();
            $('#formProduk').append('<input type="hidden" name="_method" value="PUT" id="methodField">');
            $('#nama_produk').val(nama);
            $('#tipe_produk').val(tipe).trigger('change');

            setTimeout(() => {
                $('#hargaContainer').html('');
                hargaIndex = 0;

                if (tipe === 'per_meter') {
                    hargas.forEach((h, idx) => {
                        tambahBarisHargaPerMeter(idx);
                        $(`.harga-input:eq(${idx})`).val(formatRupiah(h.harga?.toString() || '0'));
                        $(`.diskon-input:eq(${idx})`).val(formatRupiah(h.diskon?.toString() ||
                            '0'));
                        calculateMMTTotal($('.harga-row:eq(0)'));
                    });
                    hargaIndex = hargas.length;
                } else if (tipe === 'tiered') {
                    hargas.forEach((h, idx) => {
                        tambahTierPricing(idx);
                        $(`.harga-input:eq(${idx})`).val(formatRupiah(h.harga?.toString() || '0'));
                        $(`[name="harga[${idx}][min_qty]"]`).val(h.min_qty);
                        $(`[name="harga[${idx}][max_qty]"]`).val(h.max_qty);
                    });
                    hargaIndex += hargas.length;
                } else if (tipe === 'flat') {
                    hargas.forEach((h, idx) => {
                        tambahBanner(idx);
                        $(`.harga-input:eq(${idx})`).val(formatRupiah(h.harga?.toString() || '0'));
                    });
                    hargaIndex = hargas.length;
                } else if (tipe === 'custom') {
                    hargas.forEach((h, idx) => {
                        tambahKartuNamaForm(idx);
                        $(`.harga-input:eq(${idx})`).val(formatRupiah(h.harga?.toString() || '0'));
                        $(`[name="harga[${idx}][sisi]"]`).val(h.sisi);
                        if (h.laminasi) {
                            $(`[name="harga[${idx}][laminasi]"]`).prop('checked', true);
                        }
                    });
                    hargaIndex = hargas.length;
                }
            }, 300);
        });
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        let hargaIndex = 0;

        // Format Rupiah
        function formatRupiah(angka, prefix = 'Rp ') {
            if (typeof angka !== 'string') angka = angka.toString();
            let number_string = angka.replace(/[^,\d]/g, '');
            const split = number_string.split(',');
            let rupiah = split[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return prefix + rupiah + (split[1] ? ',' + split[1].substr(0, 2) : '');
        }

        function extractNumericValue(rupiahString) {
            return rupiahString ? parseInt(rupiahString.replace(/[^\d]/g, '')) : 0;
        }

        function setupRupiahInputHandlers() {
            $('.harga-input').off('input').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(formatRupiah(value));
            });
        }

        function setupDiskonInputHandlers() {
            $('.diskon-input').off('input').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(formatRupiah(value));
                const hargaRow = $(this).closest('.harga-row');
                calculateMMTTotal(hargaRow);
            });
        }

        function calculateMMTTotal(hargaRow) {
            const hargaInput = hargaRow.find('.harga-input');
            const diskonInput = hargaRow.find('.diskon-input');
            const totalDisplay = hargaRow.find('.total-harga-display');
            const totalValue = hargaRow.find('.total-harga-value');

            const hargaAsli = extractNumericValue(hargaInput.val());
            const diskon = extractNumericValue(diskonInput.val());

            if (hargaAsli > 0 && diskon >= 0) {
                const totalHarga = hargaAsli - diskon;
                totalDisplay.val(formatRupiah(totalHarga.toString()));
                totalValue.val(totalHarga);
            } else {
                totalDisplay.val('Rp 0');
                totalValue.val(0);
            }
        }

        function tambahBarisHargaPerMeter(index) {
            const row = `
        <div class="card mb-3 harga-row" data-index="${index}">
            <div class="card-header">
                <h6 class="mb-0">MMT - Harga per Meter #${index + 1}</h6>
            </div>
            <div class="card-body">
                <input type="hidden" name="harga[${index}][produk_id]" value="">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Harga per Meter</label>
                        <input type="text" class="form-control harga-input" name="harga[${index}][harga]" placeholder="Rp 0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Diskon (Rp)</label>
                        <input type="text" class="form-control diskon-input" name="harga[${index}][diskon]" placeholder="Rp 0" value="Rp 0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Harga MMT</label>
                        <input type="text" class="form-control total-harga-display" placeholder="Rp 0" readonly>
                        <input type="hidden" name="harga[${index}][total_harga]" class="total-harga-value">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-harga-row">Hapus</button>
                    </div>
                </div>
            </div>
        </div>`;
            $('#hargaContainer').append(row);
            setupRupiahInputHandlers();
            setupDiskonInputHandlers();
        }

        function tambahTierPricing(index) {
            const tiers = [{
                    min: 1,
                    max: 10
                },
                {
                    min: 11,
                    max: 50
                },
                {
                    min: 51,
                    max: 100
                },
                {
                    min: 101,
                    max: 9999
                }
            ];

            tiers.forEach((tier, idx) => {
                const i = index + idx;
                const row = `
            <div class="row mb-2 harga-row" data-index="${i}">
                <input type="hidden" name="harga[${i}][produk_id]" value="">
                <div class="col-2">
                    <label>Qty ${tier.min}-${tier.max}</label>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control harga-input" name="harga[${i}][harga]" placeholder="Rp 0">
                </div>
                <div class="col-2">
                    <input type="number" class="form-control" name="harga[${i}][min_qty]" value="${tier.min}" hidden>
                    <input type="number" class="form-control" name="harga[${i}][max_qty]" value="${tier.max}" hidden>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-harga-row">Hapus</button>
                </div>
            </div>`;
                $('#hargaContainer').append(row);
            });
            setupRupiahInputHandlers();
        }

        function tambahBanner(index) {
            const row = `
        <div class="row mb-2 harga-row" data-index="${index}">
            <input type="hidden" name="harga[${index}][produk_id]" value="">
            <div class="col-3">
                <label>Harga Banner</label>
            </div>
            <div class="col-6">
                <input type="text" class="form-control harga-input" name="harga[${index}][harga]" placeholder="Rp 0">
            </div>
            <div class="col-3">
                <button type="button" class="btn btn-sm btn-outline-danger remove-harga-row">Hapus</button>
            </div>
        </div>`;
            $('#hargaContainer').append(row);
            setupRupiahInputHandlers();
        }

        function tambahKartuNamaForm(index) {
            const row = `
        <div class="row mb-2 harga-row" data-index="${index}">
            <input type="hidden" name="harga[${index}][produk_id]" value="">
            <div class="col-3">
                <label>Sisi</label>
                <select class="form-select" name="harga[${index}][sisi]">
                    <option value="1">1 Sisi</option>
                    <option value="2">2 Sisi</option>
                </select>
            </div>
            <div class="col-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="harga[${index}][laminasi]" id="laminasiCheck_${index}">
                    <label class="form-check-label" for="laminasiCheck_${index}">Laminasi</label>
                </div>
            </div>
            <div class="col-3">
                <label>Harga</label>
                <input type="text" class="form-control harga-input" name="harga[${index}][harga]" placeholder="Rp 0">
            </div>
            <div class="col-3">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-sm btn-outline-danger remove-harga-row d-block w-100">Hapus</button>
            </div>
        </div>`;
            $('#hargaContainer').append(row);
            setupRupiahInputHandlers();
        }

        // Event handler untuk dropdown tipe_produk
        $('#tipe_produk').on('change', function() {
            const tipe = $(this).val();
            $('#hargaContainer').html('');
            hargaIndex = 0;

            if (tipe === 'per_meter') {
                tambahBarisHargaPerMeter(hargaIndex++);
            } else if (tipe === 'tiered') {
                tambahTierPricing(hargaIndex);
                hargaIndex += 4;
            } else if (tipe === 'flat') {
                tambahBanner(hargaIndex++);
            } else if (tipe === 'custom') {
                tambahKartuNamaForm(hargaIndex++);
            }
        });

        // Tombol Tambah Harga Dinamis
        $('#btnAddHarga').click(function() {
            const tipe = $('#tipe_produk').val();

            if (!tipe) {
                Swal.fire('Peringatan', 'Pilih tipe produk terlebih dahulu!', 'warning');
                return;
            }

            if (tipe === 'per_meter') {
                tambahBarisHargaPerMeter(hargaIndex++);
            } else if (tipe === 'tiered') {
                tambahTierPricing(hargaIndex);
                hargaIndex += 4;
            } else if (tipe === 'flat') {
                tambahBanner(hargaIndex++);
            } else if (tipe === 'custom') {
                tambahKartuNamaForm(hargaIndex++);
            }
        });

        // Tombol Hapus Harga
        $(document).on('click', '.remove-harga-row', function() {
            $(this).closest('.harga-row').remove();
        });

        // Tombol Edit Produk


        // Submit Form Produk
        $('#formProduk').submit(function(e) {
            e.preventDefault();
            prepareFormData();
            const url = $(this).attr('action');
            const formData = $(this).serialize();

            if ($('.harga-row').length === 0) {
                Swal.fire('Gagal', 'Setidaknya satu harga harus ditambahkan.', 'warning');
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
                .fail(function(xhr) {
                    console.error('Error:', xhr.responseText); // 🔥 Debug log
                    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan produk.', 'error');
                });
        });

        function prepareFormData() {
            $('.harga-input, .diskon-input').each(function() {
                const rawValue = extractNumericValue($(this).val());
                $(this).val(rawValue);
            });
        }

        // Reset modal saat dibuka untuk tambah produk baru
        $('.btn-show-produk-modal').click(function() {
            $('#formProduk')[0].reset();
            $('#produkId').val('');
            $('#methodField').remove();
            $('#formProduk').attr('action', '{{ route('produk.store') }}');
            $('.modal-produk-title').text('Tambah Produk Baru');
            $('#hargaContainer').html('');
            hargaIndex = 0;
        });

        // Reset modal saat ditutup
        $('#modalProduk').on('hidden.bs.modal', function() {
            $('#formProduk')[0].reset();
            $('#hargaContainer').html('');
            $('#formProduk').attr('action', '{{ route('produk.store') }}');
            $('#methodField').remove();
            $('#produkId').val('');
            $('#tipe_produk').val('').trigger('change');
        });

        // Tombol Hapus Harga
        $(document).on('click', '.btn-delete-harga', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `"${nama}" akan dihapus dari database.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/harga-produk/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Berhasil', res.message, 'success').then(() =>
                                    location.reload());
                            } else {
                                Swal.fire('Gagal', res.message, 'warning');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus harga.',
                                'error');
                            console.error('Ajax Error:', xhr.responseText); // 🔥 Debug log
                        }
                    });
                }
            });
        });

        // Tombol Edit Harga
        $(document).on('click', '.btn-edit-harga', function() {
            const id = $(this).data('id');
            $('#editHargaId').val(id);
            $.get(`/harga-produk/${id}`)
                .done(function(data) {
                    $('#editHargaValue').val(formatRupiah(data.harga.toString()));
                    $('#modalEditHarga').modal('show');
                })
                .fail(function() {
                    Swal.fire('Error', 'Gagal mengambil data harga.', 'error');
                    console.error('Gagal memuat data harga melalui AJAX'); // 🔥 Debug log
                });
        });

        // Simpan Perubahan Harga
        $('#btnUpdateHarga').click(function() {
            const id = $('#editHargaId').val();
            const harga = extractNumericValue($('#editHargaValue').val());

            $.ajax({
                url: `/harga-produk/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    harga: harga
                },
                success: function(res) {
                    if (res.status) {
                        Swal.fire('Berhasil', res.message, 'success').then(() => {
                            $('#modalEditHarga').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'warning');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan saat mengupdate harga.', 'error');
                    console.error('AJAX Error:', xhr.responseText); // 🔥 Debug log
                }
            });
        });

        // Inisialisasi awal
        $(document).ready(function() {
            setupRupiahInputHandlers();
            setupDiskonInputHandlers();
        });
    </script>
@endsection
