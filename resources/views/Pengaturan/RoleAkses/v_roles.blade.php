@extends('layouts.base')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Role User</h3>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-3">Role User</h5>
                    <button type="button" class="btn btn-outline-success btn-show-role-modal" data-bs-toggle="modal"
                        data-bs-target="#exampleModalScrollable">
                        Tambah Role
                    </button>
                </div>


                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Role</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($getRoles as $index => $a)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $a->roles }}</td>
                                        <td>{{ $a->keterangan }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning btn-show-role-modal"
                                                data-id="{{ $a->id }}" data-role="{{ $a->roles }}"
                                                data-keterangan="{{ $a->keterangan }}" data-bs-toggle="modal"
                                                data-bs-target="#exampleModalScrollable">
                                                Edit
                                            </button>

                                            <button class="btn btn-sm btn-danger btn-delete-role"
                                                data-id="{{ $a->id }}" data-role="{{ $a->roles }}">
                                                Matikan Role
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal Tambah/Edit Role -->
    <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title modal-role-title">Tambah Role Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="formRole" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="roleId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Nama Role</label>
                            <input type="text" class="form-control" id="roleName" name="role" required>
                        </div>
                        <div class="mb-3">
                            <label for="roleDesc" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="roleDesc" name="keterangan" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit-role">Simpan Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- JS Section --}}
    <script>
        const formRole = document.getElementById('formRole');
        const modalTitle = document.querySelector('.modal-role-title');
        const submitBtn = document.querySelector('.btn-submit-role');

        document.querySelectorAll('.btn-show-role-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const role = this.dataset.role;
                const keterangan = this.dataset.keterangan;

                if (id) {
                    modalTitle.innerText = 'Edit Role';
                    submitBtn.innerText = 'Update Role';
                    formRole.action = `/roles/${id}`;
                    formRole.querySelector('input[name="_method"]')?.remove();
                    formRole.insertAdjacentHTML('afterbegin',
                        `<input type="hidden" name="_method" value="PUT">`);
                    document.getElementById('roleId').value = id;
                    document.getElementById('roleName').value = role;
                    document.getElementById('roleDesc').value = keterangan;
                } else {
                    modalTitle.innerText = 'Tambah Role Baru';
                    submitBtn.innerText = 'Simpan Role';
                    formRole.action = `/roles/store`;
                    formRole.querySelector('input[name="_method"]')?.remove();
                    document.getElementById('roleId').value = '';
                    formRole.reset();
                }
            });
        });

        formRole.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', res.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Oops!', 'Terjadi kesalahan.', 'error');
                });
        });

        document.querySelectorAll('.btn-delete-role').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const role = this.dataset.role;

                Swal.fire({
                    title: 'Yakin?',
                    text: `Matikan role "${role}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, matikan!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`/roles/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: new URLSearchParams({
                                    _method: 'DELETE'
                                })
                            })
                            .then(res => res.json())
                            .then(res => {
                                if (res.status === 'success') {
                                    Swal.fire('Berhasil!', res.message, 'success').then(() =>
                                        location.reload());
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            })
                            .catch(() => {
                                Swal.fire('Oops!', 'Terjadi kesalahan.', 'error');
                            });
                    }
                });
            });
        });
    </script>
@endsection
