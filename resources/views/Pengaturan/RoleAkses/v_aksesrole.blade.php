@extends('layouts.base')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hak Akses Role User</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        Akses Role User
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <!-- Tombol untuk membuka modal tambah role -->
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                            data-bs-target="#addRoleModal">
                            Tambah Role
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
                                    <tr class="table-row" data-id="{{ $a->id }}" data-name="{{ $a->name }} "
                                        data-username="{{ $a->username }}" data-roleid="{{ $a->role_id }} "
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
                                                data-bs-target="#editRoleModal">
                                                Edit
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

    {{-- Modal untuk Tambah Role --}}
    <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <form action="" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="user_id" id="addUserId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Role untuk Pengguna</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Input dengan fitur autocomplete untuk memilih pengguna -->
                        <div class="mb-3">
                            <label for="addName" class="form-label">Nama Pengguna</label>
                            <input type="text" class="form-control" id="addName" name="name" autocomplete="off"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="addUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="addUsername" name="username" readonly>
                        </div>
                        <!-- Pilih Role Baru -->
                        <div class="mb-3">
                            <label for="addRole" class="form-label">Pilih Role Baru</label>
                            <select name="role_id" id="addRole" class="form-select" required>
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

    {{-- Script untuk Autocomplete Nama Pengguna --}}
    <script>
        $(document).ready(function() {
            // Menambahkan Autocomplete pada kolom input Nama Pengguna
            $('#addName').autocomplete({
                source: function(request, response) {
                    console.log("🔍 Mengirim request dengan term:", request
                        .term); // log sebelum request dikirim

                    $.ajax({
                        url: "{{ route('user.search.name') }}",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            console.log("✅ Respons dari server:",
                                data); // log data dari server
                            response(data);
                        },
                        error: function(xhr, status, error) {
                            console.error("❌ Terjadi error:", error);
                            console.error("Detail:", xhr.responseText);
                        }
                    });
                },
                minLength: 3, // Minimal 3 huruf untuk memulai pencarian
                select: function(event, ui) {
                    console.log("👤 Pengguna dipilih:", ui.item); // log saat item dipilih

                    $('#addName').val(ui.item.label);
                    $('#addUsername').val(ui.item.username);
                    $('#addUserId').val(ui.item.id);
                    return false;
                }
            });
        });
    </script>
@endsection
