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

// Ambil pesan sukses/error jika ada
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Fisik - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <?php include '../src/views/layouts/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-0">Aktivitas Fisik</h2>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExerciseModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Aktivitas
                    </button>
                </div>

                <!-- Ringkasan Aktivitas -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center border-end">
                                <div id="exerciseAnimation" style="width: 100%; height: 180px;"></div>
                                <h5 class="mb-1">Ringkasan Minggu Ini</h5>
                                <p class="text-muted small mb-0"><?php echo date('d M', strtotime('monday this week')); ?> - <?php echo date('d M Y', strtotime('sunday this week')); ?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <!-- Total Durasi -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="exercise-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-stopwatch"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Total Durasi</h6>
                                                    <div class="stat-value">
                                                        <span data-total-duration><?php echo isset($exerciseSummary['totalDuration']) ? number_format($exerciseSummary['totalDuration']) : 0; ?></span>
                                                        <small>menit</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-primary" role="progressbar" 
                                                             data-duration-progress
                                                             style="width: <?php echo isset($exerciseSummary['totalDuration']) ? min(($exerciseSummary['totalDuration'] / $exerciseSummary['targetDuration']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($exerciseSummary['targetDuration']); ?> menit
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kalori Terbakar -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="exercise-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-activity"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Kalori Terbakar</h6>
                                                    <div class="stat-value">
                                                        <span data-total-calories-burned><?php echo isset($exerciseSummary['totalCaloriesBurned']) ? number_format($exerciseSummary['totalCaloriesBurned']) : 0; ?></span>
                                                        <small>kkal</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             data-calories-burned-progress
                                                             style="width: <?php echo isset($exerciseSummary['totalCaloriesBurned']) ? min(($exerciseSummary['totalCaloriesBurned'] / $exerciseSummary['targetCalories']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($exerciseSummary['targetCalories']); ?> kkal
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sesi Latihan -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="exercise-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-calendar-check"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Sesi Latihan</h6>
                                                    <div class="stat-value">
                                                        <span data-total-sessions><?php echo isset($exerciseSummary['totalSessions']) ? $exerciseSummary['totalSessions'] : 0; ?></span>
                                                        <small>sesi</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-info" role="progressbar" 
                                                             data-sessions-progress
                                                             style="width: <?php echo isset($exerciseSummary['totalSessions']) ? min(($exerciseSummary['totalSessions'] / $exerciseSummary['targetSessions']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo $exerciseSummary['targetSessions']; ?> sesi
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Aktivitas Terbanyak -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="exercise-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-trophy"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Aktivitas Terbanyak</h6>
                                                    <div class="most-activities">
                                                        <?php if (!empty($exerciseSummary['mostFrequentActivities'])): ?>
                                                            <?php foreach ($exerciseSummary['mostFrequentActivities'] as $activity => $count): ?>
                                                                <div class="activity-item">
                                                                    <span class="activity-name"><?php echo htmlspecialchars($activity); ?></span>
                                                                    <span class="activity-count"><?php echo $count; ?>x</span>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <div class="text-muted">Belum ada data</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .exercise-stat-card {
                        background: #fff;
                        border-radius: 15px;
                        padding: 1.5rem;
                        transition: all 0.3s ease;
                        position: relative;
                        overflow: hidden;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                        cursor: pointer;
                        height: 100%;
                    }

                    .exercise-stat-card::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.2) 100%);
                        opacity: 0;
                        transition: opacity 0.3s ease;
                    }

                    .exercise-stat-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                    }

                    .exercise-stat-card:hover::before {
                        opacity: 1;
                    }

                    .exercise-stat-card:hover .stat-icon-wrapper i {
                        transform: scale(1.1) rotate(5deg);
                    }

                    .stat-content {
                        position: relative;
                        z-index: 1;
                    }

                    .stat-icon-wrapper {
                        width: 50px;
                        height: 50px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-bottom: 1rem;
                        font-size: 1.5rem;
                        transition: all 0.3s ease;
                    }

                    .exercise-stat-card:nth-child(1) .stat-icon-wrapper {
                        background: rgba(13, 110, 253, 0.1);
                        color: #0d6efd;
                    }

                    .exercise-stat-card:nth-child(2) .stat-icon-wrapper {
                        background: rgba(25, 135, 84, 0.1);
                        color: #198754;
                    }

                    .exercise-stat-card:nth-child(3) .stat-icon-wrapper {
                        background: rgba(13, 202, 240, 0.1);
                        color: #0dcaf0;
                    }

                    .exercise-stat-card:nth-child(4) .stat-icon-wrapper {
                        background: rgba(255, 193, 7, 0.1);
                        color: #ffc107;
                    }

                    .stat-icon-wrapper i {
                        transition: transform 0.3s ease;
                    }

                    .stat-info h6 {
                        font-size: 0.9rem;
                        color: #6c757d;
                        margin-bottom: 0.5rem;
                    }

                    .stat-value {
                        font-size: 1.8rem;
                        font-weight: 700;
                        line-height: 1.2;
                        margin-bottom: 1rem;
                    }

                    .stat-value small {
                        font-size: 0.9rem;
                        font-weight: 400;
                        opacity: 0.8;
                        margin-left: 0.25rem;
                    }

                    .progress {
                        height: 6px;
                        background-color: rgba(0,0,0,0.05);
                        border-radius: 10px;
                        margin-bottom: 0.75rem;
                    }

                    .progress-bar {
                        border-radius: 10px;
                        transition: width 0.5s ease;
                    }

                    .stat-target {
                        font-size: 0.85rem;
                        color: #6c757d;
                    }

                    .most-activities {
                        margin-top: 0.5rem;
                    }

                    .activity-item {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 0.25rem 0;
                        border-bottom: 1px dashed rgba(0,0,0,0.1);
                    }

                    .activity-item:last-child {
                        border-bottom: none;
                    }

                    .activity-name {
                        font-size: 0.9rem;
                        color: #495057;
                    }

                    .activity-count {
                        font-size: 0.9rem;
                        font-weight: 600;
                        color: #198754;
                    }

                    @media (max-width: 768px) {
                        .exercise-stat-card {
                            margin-bottom: 1rem;
                        }
                    }
                </style>

                <!-- Tambahkan Lottie dan script di bagian bawah sebelum </body> -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        try {
                            // Inisialisasi animasi Lottie
                            const exerciseAnimation = lottie.loadAnimation({
                                container: document.getElementById('exerciseAnimation'),
                                renderer: 'svg',
                                loop: true,
                                autoplay: true,
                                path: '/assets/Animation - 1734544232194.json'
                            });

                            exerciseAnimation.addEventListener('data_failed', function() {
                                console.log('Gagal memuat animasi Lottie');
                                // Tampilkan ikon statis sebagai fallback
                                const container = document.getElementById('exerciseAnimation');
                                container.innerHTML = `
                                    <div style="text-align: center;">
                                        <div style="margin-bottom: 15px;">
                                            <i class="bi bi-person-arms-up" style="font-size: 64px; color: #0d6efd;"></i>
                                        </div>
                                        <div style="display: flex; justify-content: center; gap: 20px;">
                                            <div class="text-center">
                                                <i class="bi bi-person-walking" style="font-size: 36px; color: #198754;"></i>
                                                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Jalan</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="bi bi-bicycle" style="font-size: 36px; color: #0dcaf0;"></i>
                                                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Sepeda</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="bi bi-stopwatch" style="font-size: 36px; color: #dc3545;"></i>
                                                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Durasi</div>
                                            </div>
                                            <div class="text-center">
                                                <i class="bi bi-lightning-charge" style="font-size: 36px; color: #ffc107;"></i>
                                                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Kalori</div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                container.style.display = 'flex';
                                container.style.alignItems = 'center';
                                container.style.justifyContent = 'center';
                                container.style.height = '180px';
                            });
                        } catch (error) {
                            console.error('Error saat memuat animasi:', error);
                            // Tampilkan ikon statis jika terjadi error
                            const container = document.getElementById('exerciseAnimation');
                            container.innerHTML = `
                                <div style="text-align: center;">
                                    <div style="margin-bottom: 15px;">
                                        <i class="bi bi-person-arms-up" style="font-size: 64px; color: #0d6efd;"></i>
                                    </div>
                                    <div style="display: flex; justify-content: center; gap: 20px;">
                                        <div class="text-center">
                                            <i class="bi bi-person-walking" style="font-size: 36px; color: #198754;"></i>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Jalan</div>
                                        </div>
                                        <div class="text-center">
                                            <i class="bi bi-bicycle" style="font-size: 36px; color: #0dcaf0;"></i>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Sepeda</div>
                                        </div>
                                        <div class="text-center">
                                            <i class="bi bi-stopwatch" style="font-size: 36px; color: #dc3545;"></i>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Durasi</div>
                                        </div>
                                        <div class="text-center">
                                            <i class="bi bi-lightning-charge" style="font-size: 36px; color: #ffc107;"></i>
                                            <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">Kalori</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.style.display = 'flex';
                            container.style.alignItems = 'center';
                            container.style.justifyContent = 'center';
                            container.style.height = '180px';
                        }

                        // Fungsi untuk memperbarui tampilan data
                        function updateExerciseDisplay(data) {
                            // Update ringkasan mingguan
                            if (data.exerciseSummary) {
                                document.querySelector('[data-total-duration]').textContent = data.exerciseSummary.totalDuration;
                                document.querySelector('[data-total-calories-burned]').textContent = data.exerciseSummary.totalCaloriesBurned;
                                document.querySelector('[data-total-sessions]').textContent = data.exerciseSummary.totalSessions;
                                
                                // Update progress bars
                                const durationProgress = Math.min((data.exerciseSummary.totalDuration / data.exerciseSummary.targetDuration) * 100, 100);
                                const caloriesProgress = Math.min((data.exerciseSummary.totalCaloriesBurned / data.exerciseSummary.targetCalories) * 100, 100);
                                const sessionsProgress = Math.min((data.exerciseSummary.totalSessions / data.exerciseSummary.targetSessions) * 100, 100);
                                
                                document.querySelector('[data-duration-progress]').style.width = durationProgress + '%';
                                document.querySelector('[data-calories-burned-progress]').style.width = caloriesProgress + '%';
                                document.querySelector('[data-sessions-progress]').style.width = sessionsProgress + '%';
                            }

                            // Update tantangan aktif
                            if (data.activeChallenges) {
                                Object.entries(data.activeChallenges).forEach(([key, challenge]) => {
                                    const container = document.querySelector(`[data-challenge="${key}"]`);
                                    if (container) {
                                        // Update teks progress
                                        const progressText = container.querySelector(`#challenge-${key}-text`);
                                        if (progressText) {
                                            progressText.textContent = `${challenge.achieved_days} dari ${challenge.total_days} hari tercapai`;
                                        }

                                        // Update progress bar
                                        const progressBar = container.querySelector(`#challenge-${key}-progress`);
                                        if (progressBar) {
                                            progressBar.style.width = `${challenge.percentage}%`;
                                            progressBar.textContent = `${challenge.percentage}%`;
                                            progressBar.setAttribute('aria-valuenow', challenge.percentage);
                                        }
                                    }
                                });
                            }
                        }

                        // Fungsi untuk memuat data secara realtime
                        function refreshData() {
                            fetch('/exercise/data', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Content-Type': 'application/json'
                                },
                                credentials: 'same-origin'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.error) {
                                    throw new Error(data.message || 'Terjadi kesalahan');
                                }
                                updateExerciseDisplay(data);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                        }

                        // Perbarui data setiap 3 detik
                        setInterval(refreshData, 3000);

                        // Perbarui data saat halaman dimuat
                        document.addEventListener('DOMContentLoaded', function() {
                            refreshData();
                        });
                    });
                </script>

                <!-- Tantangan Aktif -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4>Tantangan Aktif</h4>
                        <div class="row g-3">
                            <?php foreach ($activeChallenges as $key => $challenge): ?>
                                <div class="col-md-4">
                                    <div class="alert alert-info py-2 px-3 mb-0" data-challenge="<?php echo $key; ?>">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0"><i class="bi bi-trophy"></i> <?php echo htmlspecialchars($challenge['name']); ?></h6>
                                            <span class="badge bg-primary"><?php echo $challenge['percentage']; ?>%</span>
                                        </div>
                                        <p class="small mb-1" id="challenge-<?php echo $key; ?>-text">
                                            <?php echo $challenge['achieved_days']; ?> dari <?php echo $challenge['total_days']; ?> hari tercapai
                                        </p>
                                        <div class="small text-muted">
                                            <span class="target-text">
                                                Target: 
                                                <?php if ($key === 'daily_exercise'): ?>
                                                    <?php echo $challenge['target']; ?> menit/hari
                                                <?php elseif ($key === 'high_intensity'): ?>
                                                    Latihan intensitas <?php echo strtolower(implode(' atau ', $challenge['target'])); ?>
                                                <?php else: ?>
                                                    <?php echo $challenge['target']; ?> kalori/hari
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar" id="challenge-<?php echo $key; ?>-progress" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $challenge['percentage']; ?>%;" 
                                                 aria-valuenow="<?php echo $challenge['percentage']; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Aktivitas -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4>Riwayat Aktivitas</h4>
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Aktivitas</th>
                                        <th>Durasi</th>
                                        <th>Intensitas</th>
                                        <th>Kalori</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($exerciseRecords)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data aktivitas</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($exerciseRecords as $record): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                                <td><?php echo htmlspecialchars($record['time']); ?></td>
                                                <td><?php echo htmlspecialchars($record['exercise_name']); ?></td>
                                                <td><?php echo htmlspecialchars($record['duration']); ?> menit</td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($record['intensity']) {
                                                            'Rendah' => 'success',
                                                            'Sedang' => 'warning',
                                                            'Tinggi' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo htmlspecialchars($record['intensity']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($record['calories_burned']); ?> kkal</td>
                                                <td><?php echo htmlspecialchars($record['notes']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editExerciseModal<?php echo $record['id']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="/exercise/delete" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Aktivitas -->
    <div class="modal fade" id="addExerciseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/exercise/store" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Jenis Aktivitas</label>
                            <input type="text" class="form-control" name="exercise_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="time" class="form-control" name="time" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Durasi (menit)</label>
                            <input type="number" class="form-control" name="duration" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Intensitas</label>
                            <select class="form-select" name="intensity" required>
                                <option value="Rendah">Rendah</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Tinggi">Tinggi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kalori Terbakar</label>
                            <input type="number" class="form-control" name="calories_burned" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Aktivitas -->
    <?php if (!empty($exerciseRecords)): ?>
        <?php foreach ($exerciseRecords as $record): ?>
            <div class="modal fade" id="editExerciseModal<?php echo $record['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Aktivitas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="/exercise/update" method="POST">
                                <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Jenis Aktivitas</label>
                                    <input type="text" class="form-control" name="exercise_name" value="<?php echo htmlspecialchars($record['exercise_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="date" value="<?php echo $record['date']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Waktu</label>
                                    <input type="time" class="form-control" name="time" value="<?php echo $record['time']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Durasi (menit)</label>
                                    <input type="number" class="form-control" name="duration" value="<?php echo $record['duration']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Intensitas</label>
                                    <select class="form-select" name="intensity" required>
                                        <option value="Rendah" <?php echo $record['intensity'] === 'Rendah' ? 'selected' : ''; ?>>Rendah</option>
                                        <option value="Sedang" <?php echo $record['intensity'] === 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
                                        <option value="Tinggi" <?php echo $record['intensity'] === 'Tinggi' ? 'selected' : ''; ?>>Tinggi</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kalori Terbakar</label>
                                    <input type="number" class="form-control" name="calories_burned" value="<?php echo $record['calories_burned']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" name="notes" rows="3"><?php echo htmlspecialchars($record['notes']); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 