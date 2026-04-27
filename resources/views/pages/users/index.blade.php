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
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            @if(auth()->user()->role === 'super_admin')
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
                    <h5 class="modal-title">User Form</h5>
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


    <script>
        let currentPage = 1;

        const isSuperAdmin = "{{ auth()->user()->role === 'super_admin' ? true : false}}";

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
                    let data = await res.json();

                    if (!res.ok) {
                        console.error(data);
                        alert("Error: " + (data.message || "Validation gagal"));
                        return;
                    }

                    let modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    modal.hide();

                    form.reset();
                    document.getElementById('user_id').value = '';

                    loadUsers(currentPage);
                })
                .catch(err => console.error(err));
        });

        function openAddModal() {
            document.getElementById('userForm').reset();
            document.getElementById('user_id').value = '';

            let modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        }

        function editUser(id) {
            fetch(`/users/${id}`)
                .then(res => res.json())
                .then(user => {
                    // isi form
                    document.getElementById('user_id').value = user.id;
                    document.getElementById('name').value = user.name;
                    document.getElementById('email').value = user.email;
                    document.getElementById('phone').value = user.phone ?? '';
                    document.getElementById('role').value = user.role;

                    // buka modal
                    let modal = new bootstrap.Modal(document.getElementById('userModal'));
                    modal.show();
                })
                .catch(err => console.error(err));
        }

        function deleteUser(id) {
            if (!confirm('Yakin mau hapus user ini?')) return;

            fetch(`/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    let data = await res.json();

                    if (!res.ok) {
                        alert(data.message || 'Gagal hapus user');
                        return;
                    }

                    loadUsers(currentPage);
                })
                .catch(err => console.error(err));
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
                            let actionButtons = '';

                            if (isSuperAdmin) {
                                actionButtons = `
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-icon btn-sm btn-outline-warning" onclick="editUser(${user.id})">
                                            <i class="bx bx-edit-alt"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm btn-outline-danger" onclick="deleteUser(${user.id})">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>`;
                            }

                            rows += `
                                <tr>
                                    <td>${(data.from ?? 0) + index}</td>
                                    <td>${user.name}</td>
                                    <td>${user.email}</td>
                                    <td>${user.role}</td>
                                    <td>${actionButtons}</td>
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
