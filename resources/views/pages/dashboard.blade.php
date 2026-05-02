@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb')
    <span class="text-muted fw-light">Page /</span> Dashboard
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Order Statistics -->
            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Humidity</h5>
                            <i class="fa-solid fa-droplet text-primary"></i>
                            <small class="text-muted">
                                Current: {{ $data->humidity ?? 0 }}%
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <div style="width:300px; height:300px; text-align:center;">
                                <canvas id="humidityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Order Statistics -->

            <!-- Order Statistics -->
            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Temperature</h5>
                            <i class="fa-solid fa-temperature-empty text-danger"></i>
                            <small class="text-muted">
                                Current: {{ $data->temperature ?? 0 }}°C
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <div style="width:300px; height:300px; text-align:center;">
                                <canvas id="temperatureChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Order Statistics -->

            <!-- Order Statistics -->
            <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Soil</h5>
                            <i class="fa-solid fa-leaf text-success"></i>
                            <small class="text-muted">
                                Current: {{ $data->soil_moisture ?? 0 }}%
                            </small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <div style="width:300px; height:300px; text-align:center;">
                                <canvas id="soilChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Order Statistics -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Realtime Sensor Chart</h5>
                        <form action="{{ route('export_sensor') }}" method="GET" class="d-flex align-items-end gap-2">
                            <div>
                                <label class="form-label small">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label small">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bx bx-file me-1"></i> Export Excel
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <canvas id="realtimeChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const temperature = {{ $data->temperature ?? 0 }};
                const humidity = {{ $data->humidity ?? 0 }};
                const soil = {{ $data->soil_moisture ?? 0 }};

                // plugin text tengah
                const centerText = {
                    id: 'centerText',
                    beforeDraw(chart) {
                        const {
                            width
                        } = chart;
                        const {
                            height
                        } = chart;
                        const ctx = chart.ctx;

                        ctx.restore();
                        const fontSize = (height / 100).toFixed(2);
                        ctx.font = fontSize + "em sans-serif";
                        ctx.textBaseline = "middle";

                        const text = chart.config.data.datasets[0].data[0].toFixed(0) + "%";
                        const textX = Math.round((width - ctx.measureText(text).width) / 2);
                        const textY = height / 1.3;

                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                };

                // =========================
                // HUMIDITY
                // =========================
                new Chart(document.getElementById('humidityChart'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [humidity, 100 - humidity],
                            backgroundColor: ['#00cfe8', '#e0e0e0'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        rotation: -90,
                        circumference: 180,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    },
                });

                // =========================
                // TEMPERATURE (PERSEN)
                // =========================
                new Chart(document.getElementById('temperatureChart'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [temperature, 100 - temperature],
                            backgroundColor: ['#ff4d4f', '#e0e0e0'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        rotation: -90,
                        circumference: 180,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    },
                });

                // =========================
                // SOIL
                // =========================
                new Chart(document.getElementById('soilChart'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [soil, 100 - soil],
                            backgroundColor: ['#28c76f', '#e0e0e0'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        rotation: -90,
                        circumference: 180,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    },
                });

            });

            const ctxRealtime = document.getElementById('realtimeChart');

            const realtimeChart = new Chart(ctxRealtime, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'Temperature (°C)',
                            data: [],
                            borderColor: '#ff4d4f',
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'Humidity (%)',
                            data: [],
                            borderColor: '#00cfe8',
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'Soil (%)',
                            data: [],
                            borderColor: '#28c76f',
                            fill: false,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    animation: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });

            function loadRealtimeData() {
                fetch('/api/realtime')
                    .then(res => res.json())
                    .then(data => {

                        const arrayData = Object.values(data); // 🔥 ini penting

                        const labels = [];
                        const tempData = [];
                        const humData = [];
                        const soilData = [];

                        arrayData.forEach(item => {
                            labels.push(new Date(item.created_at).toLocaleTimeString());
                            tempData.push(item.temperature);
                            humData.push(item.humidity);
                            soilData.push(item.soil_moisture);
                        });

                        realtimeChart.data.labels = labels;
                        realtimeChart.data.datasets[0].data = tempData;
                        realtimeChart.data.datasets[1].data = humData;
                        realtimeChart.data.datasets[2].data = soilData;

                        realtimeChart.update();
                    });
            }

            setInterval(loadRealtimeData, 5000); // tiap 5 detik
            loadRealtimeData(); // pertama kali load
        </script>
    @endpush
@endsection
