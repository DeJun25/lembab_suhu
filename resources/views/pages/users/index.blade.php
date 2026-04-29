@extends('layouts.app')

@section('title', 'Manage Users')
@section('breadcrumb')
    <span class="text-muted fw-light">Page /</span> Manage Users
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Table Users</h5>
                @if (auth()->user()->role === 'super_admin')
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="pb-1 tf-icons bx bx-plus"></i>Add User</button>
                @endif
            </div>
            <div class="table-responsive text-nowrap mx-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            @if (auth()->user()->role === 'super_admin')
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="user-table">
                    </tbody>
                </table>

                <div class="m-2" id="pagination-links">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">User Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="userForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" id="user_id">

                        <div class="mb-3">
                            <label for="name" class="col-md-2 col-form-label">Name</label>
                            <input class="form-control" type="text" placeholder="Admin" name="name" id="name"
                                class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="col-md-2 col-form-label">Email</label>
                            <input class="form-control" type="email" placeholder="test@example.com" name="email"
                                id="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="col-md-2 col-form-label">Phone</label>
                            <input class="form-control" type="text" placeholder="0833851812" name="phone"
                                id="phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')" inputmode="numeric"
                                pattern="[0-9]*" maxlength="15" />
                        </div>

                        <div class="mb-3">
                            <label for="role" class="col-md-2 col-form-label">Role</label>
                            <select id="role" name="role" class="form-select">
                                <option value="" selected hidden>Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="userForm" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Name</th>
                            <td id="detail_name"></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td id="detail_email"></td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td id="detail_phone"></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><span class="badge bg-label-primary" id="detail_role"></span></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td id="detail_created"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;

        const isSuperAdmin = "{{ auth()->user()->role === 'super_admin' ? 1 : 0 }}";

        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let form = this;
            let formData = new FormData(form);
            let id = document.getElementById('user_id').value;
            let url = id ? `/users/${id}` : `/users`;

            if (id) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(async res => {
                    if (!res.ok) {
                        let errorData = await res.json().catch(() => ({
                            message: 'Terjadi kesalahan server'
                        }));
                        throw new Error(errorData.message || 'Gagal menyimpan data');
                    }
                    return res.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Data berhasil disimpan.',
                    });

                    let modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    modal.hide();
                    form.reset();
                    document.getElementById('user_id').value = '';
                    loadUsers(currentPage);
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: err.message,
                    });
                });
        });

        function openAddModal() {
            document.getElementById('userForm').reset();
            document.getElementById('user_id').value = '';

            document.getElementById('modalTitle').innerText = 'Add New User';

            let modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }

        function editUser(id) {
            fetch(`/users/${id}`)
                .then(res => res.json())
                .then(user => {
                    document.getElementById('user_id').value = user.id;
                    document.getElementById('name').value = user.name;
                    document.getElementById('email').value = user.email;
                    document.getElementById('phone').value = user.phone ?? '';
                    document.getElementById('role').value = user.role;

                    document.getElementById('modalTitle').innerText = 'Edit User: ' + user.name;

                    let modal = new bootstrap.Modal(document.getElementById('userModal'));
                    modal.show();
                })
                .concatch(err => console.error(err));
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/users/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(async res => {
                            let data = await res.json().catch(() => ({}));

                            if (!res.ok) {
                                throw new Error(data.message || 'Terjadi kesalahan server');
                            }

                            Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                            loadUsers(currentPage);
                        })
                        .catch(err => {
                            Swal.fire('Gagal!', err.message || 'Gagal menghapus', 'error');
                        });
                }
            });
        }

        function showUser(id) {
            fetch(`/users/${id}`)
                .then(res => res.json())
                .then(user => {
                    document.getElementById('detail_name').innerText = user.name;
                    document.getElementById('detail_email').innerText = user.email;
                    document.getElementById('detail_phone').innerText = user.phone ?? '-';
                    document.getElementById('detail_role').innerText = user.role.toUpperCase();

                    // Format tanggal jika ada (opsional)
                    const date = new Date(user.created_at);
                    document.getElementById('detail_created').innerText = date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });

                    let modal = new bootstrap.Modal(document.getElementById('userDetailModal'));
                    modal.show();
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Gagal mengambil data detail', 'error');
                });
        }

        function loadUsers(page = currentPage) {
            currentPage = page;
            fetch(`/getallusers?page=${page}`)
                .then(res => res.json())
                .then(data => {
                    let rows = '';

                    if (data.data.length === 0) {
                        rows = `<tr><td colspan="5" class="text-center">No data</td></tr>`;
                    } else {
                        data.data.forEach((user, index) => {
                            let actionButtons = `
                                <button class="btn btn-icon btn-sm btn-outline-info" onclick="showUser(${user.id})" title="View Detail">
                                    <i class="bx bx-show"></i>
                                </button>
                            `;

                            if (isSuperAdmin === "1") {
                                actionButtons += `
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-icon btn-sm btn-outline-warning" onclick="editUser(${user.id})">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm btn-outline-danger" onclick="deleteUser(${user.id})">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }

                            rows += `
                                <tr>
                                    <td>${(data.from ?? 0) + index}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>${user.role}</td>
                                    <td><div class="d-flex gap-1">${actionButtons}</div></td>
                                </tr>
                            `;
                        });
                    }

                    document.getElementById('user-table').innerHTML = rows;

                    let pagination = `<ul class="pagination">`;

                    data.links.forEach(link => {
                        let label = link.label
                            .replace(/&laquo;/g, '«')
                            .replace(/&raquo;/g, '»');

                        if (link.url === null) {
                            pagination += `
                        <li class="page-item disabled">
                            <span class="page-link">${label}</span>
                        </li>
                    `;
                        } else {
                            let page = new URL(link.url).searchParams.get("page");

                            pagination += `
                        <li class="page-item ${link.active ? 'active' : ''}">
                            <button class="page-link" onclick="loadUsers(${page})">
                                ${label}
                            </button>
                        </li>
                    `;
                        }
                    });

                    pagination += `</ul>`;

                    document.getElementById('pagination-links').innerHTML = pagination;
                })
                .catch(err => {
                    console.error(err);
                });
        }

        loadUsers(currentPage);
    </script>
@endsection
