<?php
// Cek status session sebelum memulai session baru
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .progress {
            height: 10px;
            border-radius: 10px;
            background-color: rgba(0,0,0,0.1);
        }

        .progress-bar {
            border-radius: 10px;
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card.bg-light {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .filter-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        .achievement-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            position: relative;
            overflow: hidden;
        }

        .achievement-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        /* Gaya tambahan untuk kartu target */
        .target-card {
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .target-card .progress {
            height: 20px !important;
            margin: 15px 0;
            background-color: rgba(0,0,0,0.05);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        }

        .target-card .progress-bar {
            font-weight: 600;
            font-size: 14px;
            line-height: 20px;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.2);
            transition: width 0.6s ease;
        }

        .target-card h5 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .target-card .target-text {
            font-size: 14px;
            color: #6c757d;
        }

        .target-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .target-card .text-success {
            color: #28a745 !important;
        }

        .target-card .text-info {
            color: #17a2b8 !important;
        }

        .target-card .text-warning {
            color: #ffc107 !important;
        }

        .target-card .bg-success {
            background-color: #28a745 !important;
        }

        .target-card .bg-info {
            background-color: #17a2b8 !important;
        }

        .target-card .bg-warning {
            background-color: #ffc107 !important;
        }

        /* Gaya untuk input tanggal */
        input[type="date"] {
            position: relative;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            background-color: #fff;
            font-family: inherit;
        }

        input[type="date"]:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* Gaya untuk select periode */
        .form-select {
            padding: 0.375rem 2.25rem 0.375rem 0.75rem;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .filter-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: none;
            border-radius: 15px;
        }

        .filter-card .form-label {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../src/views/layouts/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4" data-aos="fade-right">Statistik & Analisis</h2>

                <!-- Filter Periode -->
                <div class="card filter-card shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Periode</label>
                                <select class="form-select" id="period">
                                    <option value="week">Minggu Ini</option>
                                    <option value="month">Bulan Ini</option>
                                    <option value="3months">3 Bulan Terakhir</option>
                                    <option value="year">Tahun Ini</option>
                                    <option value="custom">Kustom</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Dari Tanggal</label>
                                <input type="date" class="form-control" id="start_date" 
                                       pattern="\d{2}/\d{2}/\d{4}" 
                                       placeholder="dd/mm/yyyy"
                                       disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="end_date" 
                                       pattern="\d{2}/\d{2}/\d{4}" 
                                       placeholder="dd/mm/yyyy"
                                       disabled>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    // Tambahkan script ini setelah deklarasi fungsi updateCharts
                    document.getElementById('period').addEventListener('change', function() {
                        const startDateInput = document.getElementById('start_date');
                        const endDateInput = document.getElementById('end_date');
                        const isCustomPeriod = this.value === 'custom';
                        
                        startDateInput.disabled = !isCustomPeriod;
                        endDateInput.disabled = !isCustomPeriod;
                        
                        if (!isCustomPeriod) {
                            // Set tanggal berdasarkan periode yang dipilih
                            const today = new Date();
                            let startDate = new Date();
                            let endDate = new Date();
                            
                            switch(this.value) {
                                case 'week':
                                    startDate = new Date(today.setDate(today.getDate() - today.getDay() + 1));
                                    endDate = new Date(today.setDate(today.getDate() - today.getDay() + 7));
                                    break;
                                case 'month':
                                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                                    break;
                                case '3months':
                                    startDate = new Date(today.setMonth(today.getMonth() - 2));
                                    startDate.setDate(1);
                                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                                    break;
                                case 'year':
                                    startDate = new Date(today.getFullYear(), 0, 1);
                                    endDate = new Date(today.getFullYear(), 11, 31);
                                    break;
                            }
                            
                            startDateInput.value = startDate.toISOString().split('T')[0];
                            endDateInput.value = endDate.toISOString().split('T')[0];
                        }
                        
                        updateCharts();
                    });

                    // Format tanggal ke format Indonesia
                    function formatDateID(date) {
                        const options = { 
                            day: '2-digit', 
                            month: '2-digit', 
                            year: 'numeric'
                        };
                        return new Date(date).toLocaleDateString('id-ID', options);
                    }

                    // Event listener untuk memformat tampilan tanggal
                    document.getElementById('start_date').addEventListener('change', function() {
                        this.setAttribute('data-date', formatDateID(this.value));
                    });

                    document.getElementById('end_date').addEventListener('change', function() {
                        this.setAttribute('data-date', formatDateID(this.value));
                    });
                </script>

                <!-- Grafik Nutrisi -->
                <div class="card shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-pie-chart-fill stats-icon me-3"></i>
                            <h4 class="mb-0">Tren Nutrisi</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="nutritionChart" height="300"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Ringkasan Nutrisi</h5>
                                        <ul class="list-unstyled">
                                            <li class="mb-3" data-nutrition="calories">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Rata-rata Kalori</span>
                                                    <span class="fw-bold">1800 kkal/hari</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                                                </div>
                                            </li>
                                            <li class="mb-3" data-nutrition="protein">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Protein</span>
                                                    <span class="fw-bold">60g/hari</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 75%"></div>
                                                </div>
                                            </li>
                                            <li class="mb-3" data-nutrition="carbs">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Karbohidrat</span>
                                                    <span class="fw-bold">250g/hari</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 80%"></div>
                                                </div>
                                            </li>
                                            <li data-nutrition="fat">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Lemak</span>
                                                    <span class="fw-bold">60g/hari</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 70%"></div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Aktivitas -->
                <div class="card shadow-sm mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-activity stats-icon me-3"></i>
                            <h4 class="mb-0">Tren Aktivitas Fisik</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="exerciseChart" height="300"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Ringkasan Aktivitas</h5>
                                        <ul class="list-unstyled">
                                            <li class="mb-3" data-exercise="duration">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Durasi Total</span>
                                                    <span class="fw-bold">0 menit</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </li>
                                            <li class="mb-3" data-exercise="calories">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Kalori Terbakar</span>
                                                    <span class="fw-bold">0 kkal</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </li>
                                            <li data-exercise="sessions">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>Sesi Latihan</span>
                                                    <span class="fw-bold">0 kali</span>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Tidur -->
                <div class="card shadow-sm mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-moon-stars stats-icon me-3"></i>
                            <h4 class="mb-0">Pola Tidur</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="sleepChart" height="300"></canvas>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="fw-bold mb-3">Ringkasan Minggu Ini</h5>
                                        <ul class="list-unstyled">
                                            <li class="mb-4" data-sleep="duration">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold">Rata-rata Durasi</span>
                                                    <span class="text-primary">0</span>
                                                </div>
                                                <div class="text-muted small mb-2">dari 8 jam/hari</div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" 
                                                         role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </li>
                                            <li class="mb-4" data-sleep="quality">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold">Kualitas Tidur</span>
                                                    <span class="text-success">0</span>
                                                </div>
                                                <div class="text-muted small mb-2">dari 85 poin</div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                                         role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </li>
                                            <li class="mb-4" data-sleep="bedtime">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold">Tidur Tepat Waktu</span>
                                                    <span class="text-info">0</span>
                                                </div>
                                                <div class="text-muted small mb-2">dari 7 hari</div>
                                                <div class="text-muted small">Target: <span class="fw-bold">22:00 WIB</span></div>
                                            </li>
                                            <li data-sleep="waketime">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold">Bangun Tepat Waktu</span>
                                                    <span class="text-warning">0</span>
                                                </div>
                                                <div class="text-muted small mb-2">dari 7 hari</div>
                                                <div class="text-muted small">Target: <span class="fw-bold">06:00 WIB</span></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pencapaian -->
                <div class="card achievement-card shadow-sm" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-trophy stats-icon me-3"></i>
                            <h4 class="mb-0">Pencapaian Target</h4>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card target-card" id="nutrition-target">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <i class="bi bi-pie-chart-fill fs-1 text-success"></i>
                                        </div>
                                        <h5 class="text-center">Target Nutrisi</h5>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: 0%;" 
                                                 aria-valuenow="0" aria-valuemin="0" 
                                                 aria-valuemax="100">0%</div>
                                        </div>
                                        <p class="text-center mb-0"><small class="target-text">0/0 kkal tercapai</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card target-card" id="exercise-target">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <i class="bi bi-activity fs-1 text-info"></i>
                                        </div>
                                        <h5 class="text-center">Target Aktivitas</h5>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-info progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: 0%;" 
                                                 aria-valuenow="0" aria-valuemin="0" 
                                                 aria-valuemax="100">0%</div>
                                        </div>
                                        <p class="text-center mb-0"><small class="target-text">0/0 menit tercapai</small></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card target-card" id="sleep-target">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <i class="bi bi-moon-stars fs-1 text-warning"></i>
                                        </div>
                                        <h5 class="text-center">Target Tidur</h5>
                                        <div class="progress mb-2" style="height: 20px;">
                                            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: 0%;" 
                                                 aria-valuenow="0" aria-valuemin="0" 
                                                 aria-valuemax="100">0%</div>
                                        </div>
                                        <p class="text-center mb-0"><small class="target-text">0/0 jam tercapai</small></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        // Inisialisasi AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Fungsi untuk memformat angka dengan pemisah ribuan
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi untuk memformat angka dengan 1 desimal
        function formatDecimal(number) {
            return Number(number).toFixed(1);
        }

        // Grafik Nutrisi
        const nutritionChart = new Chart(document.getElementById('nutritionChart'), {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Kalori',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#198754'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `${formatNumber(context.raw)} kkal`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value) + ' kkal';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Grafik Aktivitas
        const exerciseChart = new Chart(document.getElementById('exerciseChart'), {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Durasi (menit)',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(13, 110, 253, 0.8)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `${formatNumber(context.raw)} menit`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value) + ' menit';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Grafik Tidur
        const sleepChart = new Chart(document.getElementById('sleepChart'), {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Durasi Tidur (jam)',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#6f42c1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `${formatNumber(context.raw)} jam`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value) + ' jam';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Fungsi untuk mengambil data nutrisi real-time
        async function fetchNutritionData() {
            try {
                const response = await fetch('/nutrition/data', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                // Update ringkasan nutrisi
                if (data.nutritionSummary) {
                    // Update data grafik nutrisi
                    if (data.nutritionSummary.weeklyData && data.nutritionSummary.weeklyData.calories) {
                        nutritionChart.data.datasets[0].data = data.nutritionSummary.weeklyData.calories;
                        nutritionChart.update('none'); // Gunakan 'none' untuk animasi lebih halus
                    }

                    // Hitung rata-rata harian
                    const avgCalories = Math.round(data.nutritionSummary.totalCalories / 7);
                    const avgProtein = Math.round(data.nutritionSummary.totalProtein / 7);
                    const avgCarbs = Math.round(data.nutritionSummary.totalCarbs / 7);
                    const avgFat = Math.round(data.nutritionSummary.totalFat / 7);

                    // Update nilai kalori
                    document.querySelector('[data-nutrition="calories"] .fw-bold').textContent = 
                        `${formatNumber(avgCalories)} kkal/hari`;
                    document.querySelector('[data-nutrition="calories"] .progress-bar').style.width = 
                        `${Math.min((avgCalories / (data.nutritionSummary.targetCalories / 7)) * 100, 100)}%`;

                    // Update nilai protein
                    document.querySelector('[data-nutrition="protein"] .fw-bold').textContent = 
                        `${formatNumber(avgProtein)}g/hari`;
                    document.querySelector('[data-nutrition="protein"] .progress-bar').style.width = 
                        `${Math.min((avgProtein / (data.nutritionSummary.targetProtein / 7)) * 100, 100)}%`;

                    // Update nilai karbohidrat
                    document.querySelector('[data-nutrition="carbs"] .fw-bold').textContent = 
                        `${formatNumber(avgCarbs)}g/hari`;
                    document.querySelector('[data-nutrition="carbs"] .progress-bar').style.width = 
                        `${Math.min((avgCarbs / (data.nutritionSummary.targetCarbs / 7)) * 100, 100)}%`;

                    // Update nilai lemak
                    document.querySelector('[data-nutrition="fat"] .fw-bold').textContent = 
                        `${formatNumber(avgFat)}g/hari`;
                    document.querySelector('[data-nutrition="fat"] .progress-bar').style.width = 
                        `${Math.min((avgFat / (data.nutritionSummary.targetFat / 7)) * 100, 100)}%`;
                }
            } catch (error) {
                console.error('Error fetching nutrition data:', error);
            }
        }

        // Fungsi untuk mengambil data exercise real-time
        async function fetchExerciseData() {
            try {
                const response = await fetch('/exercise/data', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                // Update ringkasan exercise
                if (data.exerciseSummary) {
                    // Update data grafik aktivitas jika ada data mingguan
                    if (data.exerciseSummary.weeklyData && data.exerciseSummary.weeklyData.duration) {
                        exerciseChart.data.datasets[0].data = data.exerciseSummary.weeklyData.duration;
                        exerciseChart.update('none'); // Gunakan 'none' untuk animasi lebih halus
                    }

                    // Update durasi total
                    document.querySelector('[data-exercise="duration"] .fw-bold').textContent = 
                        `${formatNumber(data.exerciseSummary.totalDuration)} menit`;
                    document.querySelector('[data-exercise="duration"] .progress-bar').style.width = 
                        `${Math.min((data.exerciseSummary.totalDuration / data.exerciseSummary.targetDuration) * 100, 100)}%`;

                    // Update kalori terbakar
                    document.querySelector('[data-exercise="calories"] .fw-bold').textContent = 
                        `${formatNumber(data.exerciseSummary.totalCaloriesBurned)} kkal`;
                    document.querySelector('[data-exercise="calories"] .progress-bar').style.width = 
                        `${Math.min((data.exerciseSummary.totalCaloriesBurned / data.exerciseSummary.targetCalories) * 100, 100)}%`;

                    // Update sesi latihan
                    document.querySelector('[data-exercise="sessions"] .fw-bold').textContent = 
                        `${formatNumber(data.exerciseSummary.totalSessions)} kali`;
                    document.querySelector('[data-exercise="sessions"] .progress-bar').style.width = 
                        `${Math.min((data.exerciseSummary.totalSessions / data.exerciseSummary.targetSessions) * 100, 100)}%`;
                }
            } catch (error) {
                console.error('Error fetching exercise data:', error);
            }
        }

        // Fungsi untuk mengambil data tidur real-time
        async function fetchSleepData() {
            try {
                const response = await fetch('/sleep/data', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();
                
                // Update ringkasan tidur
                if (data.sleepSummary) {
                    // Update data grafik tidur jika ada data mingguan
                    if (data.sleepSummary.weeklyData) {
                        sleepChart.data.datasets[0].data = data.sleepSummary.weeklyData;
                        sleepChart.update('none'); // Gunakan 'none' untuk animasi lebih halus
                    }

                    // Update rata-rata durasi
                    const durationElement = document.querySelector('[data-sleep="duration"]');
                    const durationValue = data.sleepSummary.averageDuration || 0;
                    const durationTarget = data.sleepSummary.targetDuration || 8;
                    const durationPercentage = Math.min((durationValue / durationTarget) * 100, 100);
                    
                    durationElement.querySelector('.text-primary').textContent = formatDecimal(durationValue);
                    durationElement.querySelector('.progress-bar').style.width = `${durationPercentage}%`;
                    durationElement.querySelector('.progress-bar').setAttribute('aria-valuenow', Math.round(durationPercentage));

                    // Update kualitas tidur
                    const qualityElement = document.querySelector('[data-sleep="quality"]');
                    const qualityValue = data.sleepSummary.averageQuality || 0;
                    const qualityTarget = data.sleepSummary.targetSleepScore || 85;
                    const qualityPercentage = Math.min((qualityValue / qualityTarget) * 100, 100);
                    
                    qualityElement.querySelector('.text-success').textContent = Math.round(qualityValue);
                    qualityElement.querySelector('.progress-bar').style.width = `${qualityPercentage}%`;
                    qualityElement.querySelector('.progress-bar').setAttribute('aria-valuenow', Math.round(qualityPercentage));

                    // Update tidur tepat waktu
                    const bedtimeElement = document.querySelector('[data-sleep="bedtime"]');
                    const onTimeSleep = data.sleepSummary.onTimeSleep || 0;
                    bedtimeElement.querySelector('.text-info').textContent = onTimeSleep;
                    bedtimeElement.querySelector('.fw-bold').textContent = data.sleepSummary.targetBedtime + ' WIB';

                    // Update bangun tepat waktu
                    const waketimeElement = document.querySelector('[data-sleep="waketime"]');
                    const onTimeWake = data.sleepSummary.onTimeWake || 0;
                    waketimeElement.querySelector('.text-warning').textContent = onTimeWake;
                    waketimeElement.querySelector('.fw-bold').textContent = data.sleepSummary.targetWakeTime + ' WIB';
                }
            } catch (error) {
                console.error('Error fetching sleep data:', error);
            }
        }

        // Fungsi untuk mengambil dan memperbarui data target secara real-time
        async function fetchTargetData() {
            try {
                const [nutritionResponse, exerciseResponse, sleepResponse] = await Promise.all([
                    fetch('/nutrition/data', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                        }
                    }),
                    fetch('/exercise/data', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                        }
                    }),
                    fetch('/sleep/data', {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': '<?php echo $_SESSION["csrf_token"]; ?>'
                        }
                    })
                ]);

                const nutritionData = await nutritionResponse.json();
                const exerciseData = await exerciseResponse.json();
                const sleepData = await sleepResponse.json();

                // Update Target Nutrisi
                if (nutritionData.nutritionSummary) {
                    const nutritionCard = document.getElementById('nutrition-target');
                    const nutritionProgress = nutritionCard.querySelector('.progress-bar');
                    const nutritionText = nutritionCard.querySelector('.target-text');
                    
                    const caloriesAchieved = nutritionData.nutritionSummary.totalCalories || 0;
                    const caloriesTarget = nutritionData.nutritionSummary.targetCalories || 2000;
                    const nutritionPercentage = Math.min((caloriesAchieved / caloriesTarget) * 100, 100);
                    
                    nutritionProgress.style.width = `${nutritionPercentage}%`;
                    nutritionProgress.textContent = `${Math.round(nutritionPercentage)}%`;
                    nutritionProgress.setAttribute('aria-valuenow', Math.round(nutritionPercentage));
                    nutritionText.textContent = `${formatNumber(caloriesAchieved)}/${formatNumber(caloriesTarget)} kkal tercapai`;
                }

                // Update Target Aktivitas
                if (exerciseData.exerciseSummary) {
                    const exerciseCard = document.getElementById('exercise-target');
                    const exerciseProgress = exerciseCard.querySelector('.progress-bar');
                    const exerciseText = exerciseCard.querySelector('.target-text');
                    
                    const durationAchieved = exerciseData.exerciseSummary.totalDuration || 0;
                    const durationTarget = exerciseData.exerciseSummary.targetDuration || 300;
                    const exercisePercentage = Math.min((durationAchieved / durationTarget) * 100, 100);
                    
                    exerciseProgress.style.width = `${exercisePercentage}%`;
                    exerciseProgress.textContent = `${Math.round(exercisePercentage)}%`;
                    exerciseProgress.setAttribute('aria-valuenow', Math.round(exercisePercentage));
                    exerciseText.textContent = `${formatNumber(durationAchieved)}/${formatNumber(durationTarget)} menit tercapai`;
                }

                // Update Target Tidur
                if (sleepData.sleepSummary) {
                    const sleepCard = document.getElementById('sleep-target');
                    const sleepProgress = sleepCard.querySelector('.progress-bar');
                    const sleepText = sleepCard.querySelector('.target-text');
                    
                    const sleepAchieved = sleepData.sleepSummary.averageDuration || 0;
                    const sleepTarget = sleepData.sleepSummary.targetDuration || 8;
                    const sleepPercentage = Math.min((sleepAchieved / sleepTarget) * 100, 100);
                    
                    sleepProgress.style.width = `${sleepPercentage}%`;
                    sleepProgress.textContent = `${Math.round(sleepPercentage)}%`;
                    sleepProgress.setAttribute('aria-valuenow', Math.round(sleepPercentage));
                    sleepText.textContent = `${formatDecimal(sleepAchieved)}/${sleepTarget} jam tercapai`;
                }

                // Tambahkan animasi saat data diperbarui
                document.querySelectorAll('.target-card').forEach(card => {
                    card.style.transition = 'transform 0.3s ease';
                    card.style.transform = 'scale(1.02)';
                    setTimeout(() => {
                        card.style.transform = 'scale(1)';
                    }, 300);
                });

            } catch (error) {
                console.error('Error fetching target data:', error);
            }
        }

        // Panggil fungsi saat halaman dimuat
        fetchNutritionData();
        fetchExerciseData();
        fetchSleepData();
        fetchTargetData();

        // Update data setiap 30 detik
        setInterval(fetchNutritionData, 30000);
        setInterval(fetchExerciseData, 30000);
        setInterval(fetchSleepData, 30000);
        setInterval(fetchTargetData, 30000);

        // Event listener untuk filter periode
        document.getElementById('period').addEventListener('change', function() {
            updateCharts();
        });

        // Event listener untuk filter tanggal
        document.getElementById('start_date').addEventListener('change', updateCharts);
        document.getElementById('end_date').addEventListener('change', updateCharts);

        function updateCharts() {
            const period = document.getElementById('period').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Perbarui data berdasarkan filter
            fetchNutritionData();
            fetchExerciseData();
            fetchSleepData();
        }

        // Konfigurasi Chart.js
        Chart.defaults.font.family = "'Inter', 'system-ui', '-apple-system', 'sans-serif'";
        Chart.defaults.font.size = 13;
        Chart.defaults.plugins.legend.display = false;
    </script>
</body>
</html> 