@extends('layouts.app')

@section('title', 'Profile Settings')
@section('breadcrumb')
    <span class="text-muted fw-light">Page /</span> Profile Settings
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <div class="card-body">
                        <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input class="form-control" type="text" id="name" name="name"
                                        value="{{ auth()->user()->name }}" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control" type="email" id="email" name="email"
                                        value="{{ auth()->user()->email }}" required />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" id="btnUpdateProfile" class="btn btn-primary me-2">Save
                                    changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <h5 class="card-header">Change Password</h5>
                    <div class="card-body">
                        <form id="formChangePassword" method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input class="form-control" type="password" name="new_password" id="new_password"
                                        placeholder="············" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input class="form-control" type="password" name="new_password_confirmation"
                                        id="new_password_confirmation" placeholder="············" required />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" id="btnUpdatePassword" class="btn btn-warning">Update
                                    Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Input',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                });
            @endif
        });
    </script>
@endsection
