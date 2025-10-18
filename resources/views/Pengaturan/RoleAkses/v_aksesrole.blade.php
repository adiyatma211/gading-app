@extends('layouts.base')

@section('content')
    <style>
        .ui-autocomplete {
            z-index: 99999 !important;
            background: white;
            max-height: 200px;
            overflow-y: auto;
        }

        .ui-menu-item {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
    </style>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pengguna dan Hak Akses Role Pengguna</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Akses Role User</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" id="addUserBtn" data-bs-toggle="modal"
                            data-bs-target="#userModal">
                            <i class="bi bi-person-plus"></i> Tambah User
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gerUser as $index => $a)
                                    <tr class="table-row" data-id="{{ $a->id }}" data-name="{{ $a->name }}"
                                        data-username="{{ $a->username }}" data-roleid="{{ $a->role_id }}"
                                        data-deletests="{{ $a->deleteSts }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $a->name }}</td>
                                        <td>{{ $a->username }}</td>
                                        <td>{{ $a->role->rolesName ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $a->deleteSts == 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $a->deleteSts == 0 ? 'Active' : 'Non Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#roleModal">Edit</button>
                                            <button class="btn btn-sm btn-danger btn-delete-user"
                                                data-id="{{ $a->id }}"
                                                data-name="{{ $a->name }}">Hapus</button>
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

    <!-- Modal Edit Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form id="roleForm" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="userId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalTitle">Edit Role untuk Pengguna</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required disabled>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Pilih Role</label>
                            <select name="role_id" id="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach ($getRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->rolesName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <form id="userForm" method="POST" action="/aksesRole/store-user">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Tambah User Baru</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="new_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="new_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_role" class="form-label">Pilih Role</label>
                            <select name="role_id" id="new_role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach ($getRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->rolesName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.btn-warning').click(function() {
                const row = $(this).closest('tr');
                const userId = row.data('id');
                const name = row.data('name');
                const username = row.data('username');
                const roleId = row.data('roleid');

                $('#roleModalTitle').text('Edit Role untuk Pengguna');
                $('#roleForm').attr('action', '/aksesRole/update-role/' + userId);
                $('#userId').val(userId);
                $('#name').val(name).prop('disabled', false);
                $('#username').val(username).prop('disabled', true);
                $('#role').val(roleId);
            });

            $('#roleForm').submit(function(e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function(response) {
                        if (response.status === true || response.status === 'success') {
                            Swal.fire('Berhasil', response.message, 'success');
                            $('#roleModal').modal('hide');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire('Gagal', response.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        Swal.fire('Error', res?.message ?? 'Gagal menyimpan data.', 'error');
                    }
                });
            });

            $('#userForm').submit(function(e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function(response) {
                        if (response.status === true || response.status === 'success') {
                            Swal.fire('Berhasil', response.message, 'success');
                            $('#userModal').modal('hide');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire('Gagal', response.message, 'warning');
                        }
                    },
                    error: function(xhr) {
                        const res = xhr.responseJSON;
                        Swal.fire('Error', res?.message ?? 'Gagal menyimpan user.', 'error');
                    }
                });
            });

            $('.btn-delete-user').click(function() {
                const userId = $(this).data('id');
                const userName = $(this).data('name');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Data user "${userName}" akan dihapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/aksesRole/delete-user/${userId}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire('Berhasil', response.message, 'success');
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    Swal.fire('Gagal', response.message, 'warning');
                                }
                            },
                            error: function() {
                                Swal.fire('Error',
                                    'Terjadi kesalahan saat menghapus user.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
