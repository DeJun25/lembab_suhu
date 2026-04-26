@extends('layouts.app')

@section('title', 'Log Sensor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Tables /</span> Basic Tables</h4> --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Manage Sensor Data</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Time Created</th>
                            <th>Humidity</th>
                            <th>Temperature</th>
                            <th>Soil Moisture</th>
                        </tr>
                    </thead>
                    <tbody id="sensor-table">
                    </tbody>
                </table>

                <div class="m-2" id="pagination-links">
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;

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
            fetch(`/data-sensor?page=${page}`)
                .then(res => res.json())
                .then(data => {
                    let rows = '';

                    if (data.data.length === 0) {
                        rows = `<tr><td colspan="5" class="text-center">No data</td></tr>`;
                    } else {
                        data.data.forEach((sensor, index) => {
                            let date = new Date(sensor.created_at);
                            let formattedDate = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            let tempClass = sensor.temperature > 35 ? 'text-danger fw-bold' : 'text-dark';
                            rows += `
                                <tr>
                                    <td><span class="text-muted">${(data.from ?? 0) + index}</span></td>
                                    <td><small>${formattedDate}</small></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">${sensor.humidity}%</span>
                                        </div>
                                    </td>
                                    <td><span class="${tempClass}">${sensor.temperature}°C</span></td>
                                    <td>
                                        <span class="badge bg-label-${sensor.soil_moisture < 30 ? 'warning' : 'success'}">
                                            ${sensor.soil_moisture}%
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                    }

                    document.getElementById('sensor-table').innerHTML = rows;

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
