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
                        <form id="formAccountSettings" onsubmit="updateProfile(event)">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input class="form-control" type="text" id="name" name="name" value="{{ auth()->user()->name }}" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input class="form-control" type="email" id="email" name="email" value="{{ auth()->user()->email }}" required />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" id="btnUpdateProfile" class="btn btn-primary me-2">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <h5 class="card-header">Change Password</h5>
                    <div class="card-body">
                        <form id="formChangePassword" onsubmit="updatePassword(event)">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input class="form-control" type="password" name="new_password" id="new_password" placeholder="············" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="············" required />
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" id="btnUpdatePassword" class="btn btn-warning">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi umum untuk menangani fetch
        async function postData(url, data, btnId) {
            const btn = document.getElementById(btnId);
            const originalText = btn.innerText;
            
            // Set loading state
            btn.disabled = true;
            btn.innerText = 'Processing...';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    alert(result.message || 'Success!');
                    return true;
                } else {
                    // Menampilkan error validasi Laravel jika ada
                    let errorMsg = result.message;
                    if (result.errors) {
                        errorMsg = Object.values(result.errors).flat().join('\n');
                    }
                    alert(errorMsg || 'Something went wrong');
                    return false;
                }
            } catch (err) {
                console.error(err);
                alert('Connection error');
                return false;
            } finally {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        }

        // Update Profil
        async function updateProfile(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            await postData('{{ route("profile.update") }}', data, 'btnUpdateProfile');
        }

        // Update Password
        async function updatePassword(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            if (data.new_password !== data.new_password_confirmation) {
                alert('Confirmation password does not match!');
                return;
            }

            const success = await postData('{{ route("profile.password") }}', data, 'btnUpdatePassword');
            if (success) e.target.reset();
        }
    </script>
@endsection
