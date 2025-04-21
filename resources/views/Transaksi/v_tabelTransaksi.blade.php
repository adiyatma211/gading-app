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
                                <th>Nota</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>081234567890</td>
                                <td>Rp 1.500.000</td>
                                <td>12-10-2023</td>
                                <td>NOTA-GADING-001</td>
                                <td>
                                    <span class="badge bg-warning text-dark">Proses</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn btn-sm btn-info"
                                            onclick="alert('Detail transaksi belum tersedia.')">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning btn-update-status"
                                            data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                            <i class="bi bi-pencil-square"></i> Update Status
                                        </button>
                                        {{-- <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button> --}}
                                    </div>
                                </td>
                            </tr>
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
                            <div class="mb-3">
                                <label for="bukti_pengambilan" class="form-label">Bukti Pengambilan</label>
                                <input type="file" class="form-control" id="bukti_pengambilan" name="bukti_pengambilan">
                            </div>
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

    <script>
        // Menampilkan modal saat tombol "Update Status" ditekan
        $(document).on('click', '.btn-update-status', function() {
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

            // Ambil nilai dari form
            const statusTransaksi = $('#status_transaksi').val();
            const tanggalDiambil = $('#tanggal_diambil').val();
            const diambilOleh = $('#diambil_oleh').val();
            const buktiPengambilan = $('#bukti_pengambilan').val();

            // Validasi
            if (statusTransaksi === 'diambil') {
                if (!tanggalDiambil || !diambilOleh || !buktiPengambilan) {
                    Swal.fire('Error', 'Harap lengkapi semua data untuk status "Diambil"', 'error');
                    return;
                }
            }

            // Simulasikan pengiriman data
            Swal.fire({
                title: 'Berhasil!',
                text: 'Status transaksi berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                $('#updateStatusModal').modal('hide'); // Tutup modal
                $('#formUpdateStatus')[0].reset(); // Reset form
                $('#form-diambil').fadeOut(200); // Sembunyikan form tambahan
            });
        });
    </script>
@endsection
