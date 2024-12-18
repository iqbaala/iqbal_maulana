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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .gradient-custom {
            background: linear-gradient(45deg, #4158d0, #c850c0);
        }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .bg-nutrition { background-color: #e8f5e9; }
        .bg-exercise { background-color: #e3f2fd; }
        .bg-sleep { background-color: #f3e5f5; }
        .text-nutrition { color: #2e7d32; }
        .text-exercise { color: #1565c0; }
        .text-sleep { color: #7b1fa2; }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <?php include '../src/views/layouts/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <!-- Welcome Section -->
        <div class="card gradient-custom text-white mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="display-6 mb-2">Selamat Datang, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Pengguna'; ?>!</h2>
                        <p class="mb-0">
                            <i class="bi bi-calendar-check me-2"></i>
                            <?php 
                            $formatter = new IntlDateFormatter(
                                'id_ID',
                                IntlDateFormatter::FULL,
                                IntlDateFormatter::NONE,
                                'Asia/Jakarta',
                                IntlDateFormatter::GREGORIAN,
                                'EEEE, dd MMMM yyyy'
                            );
                            echo $formatter->format(new DateTime('now'));
                            ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-inline-block text-center p-3 rounded-circle bg-white bg-opacity-25">
                            <h3 class="mb-0">Progress Mingguan</h3>
                            <h2 class="mb-0">
                                <?php
                                $avgProgress = isset($dashboardData['weeklyProgress']) ? 
                                    round(($dashboardData['weeklyProgress']['nutrition']['percentage'] +
                                    $dashboardData['weeklyProgress']['exercise']['percentage'] +
                                    $dashboardData['weeklyProgress']['sleep']['percentage']) / 3) : 0;
                                echo $avgProgress . '%';
                                ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-nutrition h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="activity-icon bg-white text-nutrition">
                                <i class="bi bi-egg-fried"></i>
                            </div>
                            <h5 class="mb-0 text-nutrition">Nutrisi Hari Ini</h5>
                        </div>
                        <h3 class="text-nutrition"><?php echo isset($dashboardData['nutrition']['avgCalories']) ? $dashboardData['nutrition']['avgCalories'] : 0; ?> kkal</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo isset($dashboardData['nutrition']['avgCalories']) ? min(($dashboardData['nutrition']['avgCalories'] / 2000) * 100, 100) : 0; ?>%"></div>
                        </div>
                        <small class="text-muted">Target: 2000 kkal</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-exercise h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="activity-icon bg-white text-exercise">
                                <i class="bi bi-activity"></i>
                            </div>
                            <h5 class="mb-0 text-exercise">Aktivitas Minggu Ini</h5>
                        </div>
                        <h3 class="text-exercise"><?php echo isset($dashboardData['exercise']['totalDuration']) ? $dashboardData['exercise']['totalDuration'] : 0; ?> menit</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo isset($dashboardData['exercise']['totalDuration']) ? min(($dashboardData['exercise']['totalDuration'] / 210) * 100, 100) : 0; ?>%"></div>
                        </div>
                        <small class="text-muted">Target: 210 menit/minggu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-sleep h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="activity-icon bg-white text-sleep">
                                <i class="bi bi-moon-stars"></i>
                            </div>
                            <h5 class="mb-0 text-sleep">Rata-rata Tidur</h5>
                        </div>
                        <h3 class="text-sleep"><?php echo isset($dashboardData['sleep']['avgDuration']) ? $dashboardData['sleep']['avgDuration'] : 0; ?> jam</h3>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-purple" role="progressbar" style="width: <?php echo isset($dashboardData['sleep']['avgDuration']) ? min(($dashboardData['sleep']['avgDuration'] / 8) * 100, 100) : 0; ?>%"></div>
                        </div>
                        <small class="text-muted">Target: 8 jam/hari</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Stats Section -->
        <div class="row">
            <!-- Nutrition Details -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h4 class="mb-0"><i class="bi bi-pie-chart-fill text-success me-2"></i>Detail Nutrisi</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Protein</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['nutrition']['avgProtein']) ? $dashboardData['nutrition']['avgProtein'] : 0; ?>g</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Karbohidrat</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['nutrition']['avgCarbs']) ? $dashboardData['nutrition']['avgCarbs'] : 0; ?>g</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Lemak</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['nutrition']['avgFat']) ? $dashboardData['nutrition']['avgFat'] : 0; ?>g</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Hari Tercatat</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['nutrition']['daysTracked']) ? $dashboardData['nutrition']['daysTracked'] : 0; ?>/7</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exercise Details -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h4 class="mb-0"><i class="bi bi-activity text-primary me-2"></i>Detail Aktivitas</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Total Durasi</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['exercise']['totalDuration']) ? $dashboardData['exercise']['totalDuration'] : 0; ?> min</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Kalori Terbakar</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['exercise']['totalCaloriesBurned']) ? $dashboardData['exercise']['totalCaloriesBurned'] : 0; ?></h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Jenis Aktivitas</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['exercise']['uniqueActivities']) ? $dashboardData['exercise']['uniqueActivities'] : 0; ?></h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-muted mb-1">Hari Aktif</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['exercise']['daysTracked']) ? $dashboardData['exercise']['daysTracked'] : 0; ?>/7</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sleep Details -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h4 class="mb-0"><i class="bi bi-moon-stars text-purple me-2"></i>Detail Tidur</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <h6 class="text-muted mb-1">Rata-rata Durasi</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['sleep']['avgDuration']) ? $dashboardData['sleep']['avgDuration'] : 0; ?> jam</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <h6 class="text-muted mb-1">Kualitas Tidur</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['sleep']['avgQuality']) ? $dashboardData['sleep']['avgQuality'] : 0; ?>%</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <h6 class="text-muted mb-1">Hari Tercatat</h6>
                                    <h4 class="mb-0"><?php echo isset($dashboardData['sleep']['daysTracked']) ? $dashboardData['sleep']['daysTracked'] : 0; ?>/7</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru</h4>
                        <span class="badge bg-primary">10 Terakhir</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($dashboardData['recentActivities'])): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <p class="lead mt-3 text-muted">Belum ada aktivitas tercatat</p>
                                <a href="/nutrition" class="btn btn-primary me-2">Catat Nutrisi</a>
                                <a href="/exercise" class="btn btn-success me-2">Catat Aktivitas</a>
                                <a href="/sleep" class="btn btn-info">Catat Tidur</a>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($dashboardData['recentActivities'] as $activity): ?>
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $iconClass = '';
                                                $bgClass = '';
                                                switch ($activity['type']) {
                                                    case 'nutrition':
                                                        $iconClass = 'bi-egg-fried text-success';
                                                        $bgClass = 'bg-success bg-opacity-10';
                                                        break;
                                                    case 'exercise':
                                                        $iconClass = 'bi-activity text-primary';
                                                        $bgClass = 'bg-primary bg-opacity-10';
                                                        break;
                                                    case 'sleep':
                                                        $iconClass = 'bi-moon-stars text-purple';
                                                        $bgClass = 'bg-purple bg-opacity-10';
                                                        break;
                                                }
                                                ?>
                                                <div class="activity-icon <?php echo $bgClass; ?>">
                                                    <i class="bi <?php echo $iconClass; ?>"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($activity['description']); ?></h6>
                                                    <small class="text-muted"><?php echo $activity['date']->format('d M Y H:i'); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 