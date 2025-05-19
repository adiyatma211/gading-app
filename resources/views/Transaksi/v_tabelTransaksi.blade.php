@extends('layouts.base')
@section('content')
    <div class="page-heading">
        <h3><i class="bi bi-table me-2"></i>Daftar Transaksi</h3>
    </div>
    <section class="section">
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white mb-3">
                <h5 class="card-title mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Transaksi MMT</h5>
            </div>
            <div class="card-body">
                <!-- Tabel Hasil Transaksi -->
                <div class="table-responsive">
                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Nasabah</th>
                                <th>Nomor Telepon</th>
                                <th>Total Harga</th>
                                <th>Tanggal Transaksi</th>
                                <th>Tanggal Diambil</th>
                                <th>Nota</th>
                                <th>Tanggal Selesai</th>
                                <th>Setatus Pemesanan</th>
                                <th>Status Transaksi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction as $a)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $a->customer->nama }}</td>
                                    <td>{{ $a->customer->telepon }}</td>
                                    <td>Rp{{ number_format($a->total, 0, ',', '.') }}</td>
                                    <td>{{ $a->tanggal_transaksi }}</td>
                                    <td>{{ $a->tanggal_ambil }}</td>
                                    <td>
                                        @if ($a->nota_file)
                                            <a href="{{ asset('nota/' . $a->nota_file) }}" target="_blank"
                                                rel="noopener noreferrer"
                                                style="display: inline-block; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; color: #007bff;"
                                                class="nota-link">
                                                {{ $a->nota_file }}
                                            </a>
                                        @else
                                            <span class="text-muted"
                                                style="display: inline-block; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; color: #007bff;">Nota
                                                Belum dibuat</span>
                                        @endif
                                    </td>
                                    <td>{{ $a->tanggal_selesai }}</td>

                                    <td>
                                        @if (empty($a->tanggal_selesai))
                                            <span class="badge bg-warning text-white">Proses</span>
                                        @else
                                            <span class="badge bg-success text-white">Selesai</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($a->status_pembayaran == 'lunas')
                                            <span class="badge bg-success text-white">Lunas</span>
                                        @elseif ($a->status_pembayaran == 'dp')
                                            <span class="badge bg-danger text-white">Done Payment</span>
                                        @else
                                            <span
                                                class="badge bg-secondary text-white">{{ ucfirst($a->status_pembayaran) }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#" class="btn btn-sm btn-info btn-detail-transaksi"
                                                data-bs-toggle="modal" data-bs-target="#detailTransaksiModal"
                                                data-id-transaksi="{{ $a->id }}">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <button class="btn btn-sm btn-primary btn-update-status" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal"
                                                data-nama-customer="{{ $a->customer->nama }}"
                                                data-id-transaksi="{{ $a->id }}"
                                                data-status-pembayaran="{{ $a->status_pembayaran }}"
                                                data-dp="{{ $a->dp }}" data-total="{{ $a->total }}">
                                                Update Status
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="updateStatusModalLabel"><i
                            class="bi bi-pencil-square me-2"></i>Update
                        Status Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUpdateStatus">
                        <input type="hidden" id="id_transaksi" name="id_transaksi">
                        <!-- Tambahkan di dalam form -->
                        <input type="hidden" id="dp" name="dp">
                        <input type="hidden" id="total" name="total">
                        <input type="hidden" id="status_pembayaran" name="status_pembayaran">
                        <!-- Field Nama Customer -->
                        <div class="mb-3">
                            <label for="nama_customer" class="form-label">Nama Customer</label>
                            <input type="text" class="form-control" id="nama_customer" name="nama_customer"
                                placeholder="Masukkan nama customer" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="status_transaksi" class="form-label">Status Transaksi</label>
                            <select class="form-select" id="status_transaksi" name="status_transaksi" required>
                                <option value="proses">Proses</option>
                                <option value="diambil">Diambil</option>
                            </select>
                        </div>
                        <!-- Field tambahan jika status = "Diambil" -->
                        <div id="form-diambil" style="display: none;">
                            <div class="mb-3">
                                <label for="tanggal_diambil" class="form-label">Tanggal Diambil</label>
                                <input type="date" class="form-control" id="tanggal_diambil" name="tanggal_diambil">
                            </div>
                            <div class="mb-3">
                                <label for="diambil_oleh" class="form-label">Diambil Oleh</label>
                                <input type="text" class="form-control" id="diambil_oleh" name="diambil_oleh"
                                    placeholder="Nama orang yang mengambil">
                            </div>
                            <div class="mb-3" id="form-kekurangan" style="display: none;">
                                <label for="kekurangan" class="form-label">Kekurangan</label>
                                <input type="text" class="form-control" id="kekurangan" name="kekurangan" readonly>
                            </div>

                            {{-- <div class="mb-3">
                                <label for="bukti_pengambilan" class="form-label">Bukti Pengambilan</label>
                                <input type="file" class="form-control" id="bukti_pengambilan"
                                    name="bukti_pengambilan" accept="image/*">
                                <!-- Elemen untuk menampilkan pratinjau gambar -->
                                <div id="preview-container" style="margin-top: 10px; display: none;">
                                    <img id="image-preview" src="#" alt="Preview"
                                        style="max-width: 100%; max-height: 200px; border: 1px solid #ccc; padding: 5px;">
                                </div>
                            </div> --}}
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Detail Transaksi -->
    <div class="modal fade" id="detailTransaksiModal" tabindex="-1" aria-labelledby="detailTransaksiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="detailTransaksiModalLabel">
                        <i class="bi bi-eye me-2"></i>Detail Transaksi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nama Kasir:</th>
                            <td>:</td>
                            <td id="detail_nama_kasir">-</td>
                        </tr>
                        <tr>
                            <th>Nama Customer</th>
                            <td>:</td>
                            <td id="detail_nama_customer">-</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td>:</td>
                            <td id="detail_total">-</td>
                        </tr>
                        <tr>
                            <th>DP</th>
                            <td>:</td>
                            <td id="detail_dp">-</td>
                        </tr>
                        <tr>
                            <th>Kekurangan</th>
                            <td>:</td>
                            <td id="detail_kekurangan">-</td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran</th>
                            <td>:</td>
                            <td id="detail_status_pembayaran">-</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diambil</th>
                            <td>:</td>
                            <td id="detail_tanggal_diambil">-</td>
                        </tr>
                        <tr>
                            <th>Diambil Oleh</th>
                            <td>:</td>
                            <td id="detail_diambil_oleh">-</td>
                        </tr>
                        {{-- <tr>
                            <th>Bukti Pengambilan</th>
                            <td>:</td>
                            <td id="detail_bukti_pengambilan">
                                <span class="text-muted">Tidak ada bukti</span>
                            </td>
                        </tr> --}}
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menampilkan detail transaksi saat tombol "Detail" diklik (AJAX)
        $(document).on('click', '.btn-detail-transaksi', function() {
            const idTransaksi = $(this).data('id-transaksi');

            $('#detail-transaksi-body #loading').show();
            $('#detail-transaksi-body #detail-table').hide();

            // Reset semua field sebelum ambil data baru
            $('#detail_nama_kasir').text('-');
            $('#detail_nama_customer').text('-');
            $('#detail_total').text('-');
            $('#detail_dp').text('-');
            $('#detail_kekurangan').text('-');
            $('#detail_status_pembayaran').html('-');
            $('#detail_tanggal_diambil').text('-');
            $('#detail_diambil_oleh').text('-');
            $('#detail_bukti_pengambilan').html('<span class="text-muted">Tidak ada bukti</span>');

            $('#detailTransaksiModal').modal('show');

            // Ambil data dari server
            $.ajax({
                url: "{{ url('/transaksi/detail') }}/" + idTransaksi,
                method: "GET",
                success: function(response) {
                    if (response.success && response.data) {
                        const t = response.data;
                        const customerKasir = t.createdBy ? t.createdBy : '-';
                        const customer = t.customer ? t.customer.nama : '-';
                        const total = parseInt(t.total) || 0;
                        const dp = parseInt(t.dp) || 0;
                        const kekurangan = total - dp;

                        $('#detail_nama_kasir').text(customerKasir);
                        $('#detail_nama_customer').text(customer);
                        $('#detail_total').text('Rp' + total.toLocaleString());
                        $('#detail_dp').text('Rp' + dp.toLocaleString());
                        $('#detail_kekurangan').text('Rp' + kekurangan.toLocaleString());

                        // Status Pembayaran
                        let badgeStatus = '';
                        if (t.status_pembayaran === 'lunas') {
                            badgeStatus = '<span class="badge bg-success text-white">Lunas</span>';
                        } else if (t.status_pembayaran === 'dp') {
                            badgeStatus =
                                '<span class="badge bg-danger text-white">Done Payment</span>';
                        } else {
                            badgeStatus = '<span class="badge bg-secondary text-white">' + t
                                .status_pembayaran + '</span>';
                        }
                        $('#detail_status_pembayaran').html(badgeStatus);

                        $('#detail_tanggal_diambil').text(t.tanggal_ambil || '-');
                        $('#detail_diambil_oleh').text(t.diambil_oleh || '-');

                        // // Tampilkan Bukti Pengambilan
                        // const previewContainer = $('#detail_bukti_pengambilan');
                        // if (t.bukti_pengambilan) {
                        //     const imagePath = '{{ asset('bukti_pengambilan') }}/' + t
                        //         .bukti_pengambilan;
                        //     previewContainer.html('<img src="' + imagePath +
                        //         '" alt="Bukti Pengambilan" style="max-width: 100%; border: 1px solid #ccc;">'
                        //     );
                        // } else {
                        //     previewContainer.html(
                        //         '<span class="text-muted">Tidak ada bukti pengambilan.</span>');
                        // }

                        $('#detail-transaksi-body #loading').hide();
                        $('#detail-transaksi-body #detail-table').show();
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Gagal memuat detail transaksi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    $('#detail-transaksi-body').html('<div class="alert alert-danger">' + errorMsg +
                        '</div>');
                }
            });
        });
    </script>

    <script>
        // Menampilkan modal saat tombol "Update Status" ditekan
        // Menampilkan modal dan mengisi data
        $(document).on('click', '.btn-update-status', function() {
            const namaCustomer = $(this).data('nama-customer');
            const idTransaksi = $(this).data('id-transaksi');
            const dp = $(this).data('dp');
            const total = $(this).data('total');
            const statusPembayaran = $(this).data('status-pembayaran');

            $('#nama_customer').val(namaCustomer);
            $('#id_transaksi').val(idTransaksi);
            $('#dp').val(dp);
            $('#total').val(total);
            $('#status_pembayaran').val(statusPembayaran);

            // Logika untuk menampilkan/menghitung kekurangan
            if (statusPembayaran === 'dp') {
                const kekurangan = total - dp;
                $('#kekurangan').val('Rp' + kekurangan.toLocaleString('id-ID'));
                $('#form-kekurangan').fadeIn(200);
            } else {
                $('#form-kekurangan').fadeOut(200);
            }

            $('#updateStatusModal').modal('show');
        });

        // Menampilkan/menyembunyikan form tambahan berdasarkan status
        $('#status_transaksi').change(function() {
            const status = $(this).val();
            if (status === 'diambil') {
                $('#form-diambil').fadeIn(200);
            } else {
                $('#form-diambil').fadeOut(200);
            }
        });

        // Handle submit form update status
        $('#formUpdateStatus').submit(function(e) {
            e.preventDefault();
            const statusTransaksi = $('#status_transaksi').val();
            const tanggalDiambil = $('#tanggal_diambil').val();
            const diambilOleh = $('#diambil_oleh').val();
            const idTransaksi = $('#id_transaksi').val();
            const dp = parseFloat($('#dp').val()) || 0;
            const total = parseFloat($('#total').val()) || 0;
            const statusPembayaran = $('#status_pembayaran').val();

            // Ambil file dari input
            const fileInput = document.getElementById('bukti_pengambilan');
            const file = fileInput.files[0];

            // Validasi sederhana
            if (statusTransaksi === 'diambil') {
                if (!tanggalDiambil || !diambilOleh || !file) {
                    Swal.fire('Error', 'Harap lengkapi semua data untuk status "Diambil"', 'error');
                    return;
                }
            }

            // Buat payload dengan FormData
            const payload = new FormData();
            payload.append('id_transaksi', idTransaksi);
            payload.append('diambil', statusTransaksi);
            payload.append('status_pembayaran', statusPembayaran);

            if (statusTransaksi === 'diambil') {
                payload.append('tanggal_ambil', tanggalDiambil);
                payload.append('diambil_oleh', diambilOleh);
                payload.append('bukti_pengambilan', file);
            }

            // Tambahkan kekurangan jika status_pembayaran == 'dp'
            if (statusPembayaran === 'dp') {
                const kekurangan = total - dp;
                payload.append('kekurangan', kekurangan);
            }

            // Tampilkan isi payload di console (untuk debugging)
            console.log('--- Payload FormData ---');
            for (let [key, value] of payload.entries()) {
                console.log(key, ':', value);
            }

            // Kirim ke server dengan AJAX
            $.ajax({
                url: "{{ route('transaksi.updateTransaksi') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: payload,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Status transaksi berhasil diperbarui.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#updateStatusModal').modal('hide');
                        $('#formUpdateStatus')[0].reset();
                        $('#form-diambil').fadeOut(200);
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Gagal memperbarui status transaksi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                        confirmButtonText: 'Tutup'
                    });
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buktiPengambilanInput = document.getElementById('bukti_pengambilan');
            const previewContainer = document.getElementById('preview-container');
            const imagePreview = document.getElementById('image-preview');

            // Tangani event saat file dipilih
            buktiPengambilanInput.addEventListener('change', function(event) {
                const file = event.target.files[0]; // Ambil file yang dipilih

                if (file) {
                    // Tampilkan container pratinjau
                    previewContainer.style.display = 'block';

                    // Baca file menggunakan FileReader
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Set src gambar pratinjau ke hasil pembacaan file
                        imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(file); // Baca file sebagai URL data
                } else {
                    // Sembunyikan container jika tidak ada file yang dipilih
                    previewContainer.style.display = 'none';
                    imagePreview.src = '#';
                }
            });
        });
    </script>
@endsection
