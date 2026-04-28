@extends('layouts.app')

@section('title', 'Log Sensor')

@section('breadcrumb')
    <span class="text-muted fw-light">Page /</span> Log Sensor
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5>Table Sensor Data</h5>

                <form action="{{ route('export_sensor') }}" method="GET" class="d-flex align-items-end gap-2">
                    <div>
                        <label class="form-label small mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" required>
                    </div>
                    <div>
                        <label class="form-label small mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bx bx-file me-1"></i> Export Excel
                    </button>
                </form>
            </div>

            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Time Created</th>
                            <th>Humidity</th>
                            <th>Temperature</th>
                            <th>Soil Moisture</th>
                            <th>Rain Status</th>
                        </tr>
                    </thead>
                    <tbody id="sensor-table">
                        <tr>
                            <td colspan="5" class="text-center py-4">Loading data...</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3 d-flex justify-content-center" id="pagination-links">
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;

        /**
         * Load data sensor dari server
         */
        function loadSensorData(page = 1) {
            currentPage = page;
            const tableBody = document.getElementById('sensor-table');
            const paginationContainer = document.getElementById('pagination-links');

            fetch(`/data-sensor?page=${page}`)
                .then(res => res.json())
                .then(data => {
                    let rows = '';

                    if (!data.data || data.data.length === 0) {
                        rows = `<tr><td colspan="5" class="text-center py-4">No data available</td></tr>`;
                    } else {
                        data.data.forEach((sensor, index) => {
                            // Format Tanggal
                            const date = new Date(sensor.created_at);
                            const formattedDate = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            // Logika Warna Temperature
                            const tempClass = sensor.temperature > 35 ? 'text-danger fw-bold' : 'text-dark';

                            // Logika Badge Soil Moisture
                            const soilBadge = sensor.soil_moisture < 30 ? 'bg-label-warning' :
                                'bg-label-success';

                            rows += `
                            <tr>
                                <td><span class="text-muted">${(data.from ?? 0) + index}</span></td>
                                <td><small class="text-nowrap">${formattedDate}</small></td>
                                <td>${sensor.humidity}%</td>
                                <td><span class="${tempClass}">${sensor.temperature}°C</span></td>
                                <td>
                                    <span class="badge ${soilBadge}">
                                        ${sensor.soil_moisture}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge ${sensor.rain_status === 'rain' ? 'bg-label-danger' : 'bg-label-success'}">
                                        ${sensor.rain_status === 'rain' ? 'Rain' : 'No Rain'}
                                    </span>
                                </td>
                            </tr>
                        `;
                        });
                    }

                    tableBody.innerHTML = rows;
                    renderPagination(data.links);
                })
                .catch(err => {
                    console.error("Error loading data:", err);
                    tableBody.innerHTML =
                        `<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>`;
                });
        }

        /**
         * Render links pagination
         */
        function renderPagination(links) {
            if (!links || links.length <= 3) {
                document.getElementById('pagination-links').innerHTML = '';
                return;
            }

            let paginationHtml = `<ul class="pagination pagination-sm">`;

            links.forEach(link => {
                let label = link.label.replace(/&laquo;/g, '«').replace(/&raquo;/g, '»');

                if (link.url === null) {
                    paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">${label}</span>
                    </li>`;
                } else {
                    const pageNumber = new URL(link.url).searchParams.get("page");
                    paginationHtml += `
                    <li class="page-item ${link.active ? 'active' : ''}">
                        <button class="page-link" onclick="loadSensorData(${pageNumber})">
                            ${label}
                        </button>
                    </li>`;
                }
            });

            paginationHtml += `</ul>`;
            document.getElementById('pagination-links').innerHTML = paginationHtml;
        }

        // Initial Load
        document.addEventListener('DOMContentLoaded', () => {
            loadSensorData(currentPage);
        });
    </script>
@endsection
