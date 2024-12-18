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
    <title>Monitor Tidur - WellBe</title>
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
                        <h2 class="mb-0">Monitor Tidur</h2>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSleepModal">
                        <i class="bi bi-plus-lg me-1"></i> Catat Tidur
                    </button>
                </div>

                <!-- Ringkasan Tidur -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center border-end">
                                <div id="sleepAnimation" style="width: 100%; height: 180px;"></div>
                                <h5 class="mb-1">Ringkasan Minggu Ini</h5>
                                <p class="text-muted small mb-0"><?php echo date('d M', strtotime('monday this week')); ?> - <?php echo date('d M Y', strtotime('sunday this week')); ?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <!-- Rata-rata Durasi -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="sleep-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-clock"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Rata-rata Durasi</h6>
                                                    <div class="stat-value">
                                                        <?php echo isset($sleepSummary['averageDuration']) ? number_format($sleepSummary['averageDuration'], 1) : 0; ?>
                                                        <small>jam</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-primary" role="progressbar" 
                                                             style="width: <?php echo isset($sleepSummary['averageDuration']) ? min(($sleepSummary['averageDuration'] / $sleepSummary['targetDuration']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($sleepSummary['targetDuration']); ?> jam/hari
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kualitas Tidur -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="sleep-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-stars"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Kualitas Tidur</h6>
                                                    <div class="stat-value">
                                                        <?php echo isset($sleepSummary['averageQuality']) ? number_format($sleepSummary['averageQuality'], 1) : 0; ?>
                                                        <small>poin</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             style="width: <?php echo isset($sleepSummary['averageQuality']) ? min(($sleepSummary['averageQuality'] / $sleepSummary['targetSleepScore']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($sleepSummary['targetSleepScore']); ?> poin
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tidur Tepat Waktu -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="sleep-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-moon-stars"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Tidur Tepat Waktu</h6>
                                                    <div class="stat-value">
                                                        <?php echo isset($sleepSummary['onTimeSleep']) ? $sleepSummary['onTimeSleep'] : 0; ?>
                                                        <small>hari</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-info" role="progressbar" 
                                                             style="width: <?php echo $sleepSummary['daysTracked'] > 0 ? min(($sleepSummary['onTimeSleep'] / $sleepSummary['daysTracked']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo $sleepSummary['targetBedtime']; ?> WIB
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bangun Tepat Waktu -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="sleep-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-sunrise"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Bangun Tepat Waktu</h6>
                                                    <div class="stat-value">
                                                        <?php echo isset($sleepSummary['onTimeWake']) ? $sleepSummary['onTimeWake'] : 0; ?>
                                                        <small>hari</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-warning" role="progressbar" 
                                                             style="width: <?php echo $sleepSummary['daysTracked'] > 0 ? min(($sleepSummary['onTimeWake'] / $sleepSummary['daysTracked']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo $sleepSummary['targetWakeTime']; ?> WIB
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
                    .sleep-stat-card {
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

                    .sleep-stat-card::before {
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

                    .sleep-stat-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                    }

                    .sleep-stat-card:hover::before {
                        opacity: 1;
                    }

                    .sleep-stat-card:hover .stat-icon-wrapper i {
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

                    .sleep-stat-card:nth-child(1) .stat-icon-wrapper {
                        background: rgba(13, 110, 253, 0.1);
                        color: #0d6efd;
                    }

                    .sleep-stat-card:nth-child(2) .stat-icon-wrapper {
                        background: rgba(25, 135, 84, 0.1);
                        color: #198754;
                    }

                    .sleep-stat-card:nth-child(3) .stat-icon-wrapper {
                        background: rgba(13, 202, 240, 0.1);
                        color: #0dcaf0;
                    }

                    .sleep-stat-card:nth-child(4) .stat-icon-wrapper {
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

                    @media (max-width: 768px) {
                        .sleep-stat-card {
                            margin-bottom: 1rem;
                        }
                    }
                </style>

                <!-- Tambahkan Lottie dan script di bagian bawah sebelum </body> -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
                <script>
                    // Inisialisasi animasi Lottie
                    const sleepAnimation = lottie.loadAnimation({
                        container: document.getElementById('sleepAnimation'),
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        path: '/assets/Animation - 1734544480013.json'
                    });
                </script>

                <!-- Tantangan Mingguan -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4>Tantangan Mingguan</h4>
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
                                                <?php if ($key === 'sleep_duration'): ?>
                                                    <?php echo $challenge['target'][0]; ?>-<?php echo $challenge['target'][1]; ?> jam/hari
                                                <?php elseif ($key === 'sleep_quality'): ?>
                                                    Kualitas <?php echo implode(' atau ', $challenge['target']); ?>
                                                <?php else: ?>
                                                    Perbedaan waktu tidur ≤ <?php echo $challenge['target']; ?> jam
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

                <!-- Riwayat Tidur -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4>Riwayat Tidur</h4>
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu Tidur</th>
                                        <th>Waktu Bangun</th>
                                        <th>Durasi</th>
                                        <th>Kualitas</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($sleepRecords)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data tidur</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($sleepRecords as $record): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                                <td><?php echo htmlspecialchars($record['sleep_start']); ?></td>
                                                <td><?php echo htmlspecialchars($record['sleep_end']); ?></td>
                                                <td><?php echo htmlspecialchars($record['duration']); ?> jam</td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             style="width: <?php echo htmlspecialchars($record['quality']); ?>%;" 
                                                             aria-valuenow="<?php echo htmlspecialchars($record['quality']); ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?php echo htmlspecialchars($record['quality']); ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($record['notes']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSleepModal<?php echo $record['id']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="/sleep/delete" method="POST" class="d-inline">
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

    <!-- Modal Catat Tidur -->
    <div class="modal fade" id="addSleepModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catat Waktu Tidur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/sleep/store" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Tidur</label>
                            <input type="time" class="form-control" name="sleep_start" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Bangun</label>
                            <input type="time" class="form-control" name="sleep_end" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kualitas Tidur</label>
                            <select class="form-select" name="quality" required>
                                <option value="100">Sangat Baik</option>
                                <option value="80">Baik</option>
                                <option value="60">Cukup</option>
                                <option value="40">Kurang</option>
                                <option value="20">Sangat Kurang</option>
                            </select>
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

    <!-- Modal Edit Tidur -->
    <?php if (!empty($sleepRecords)): ?>
        <?php foreach ($sleepRecords as $record): ?>
            <div class="modal fade" id="editSleepModal<?php echo $record['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Tidur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="/sleep/update" method="POST">
                                <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="date" value="<?php echo $record['date']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Waktu Tidur</label>
                                    <input type="time" class="form-control" name="sleep_start" value="<?php echo $record['sleep_start']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Waktu Bangun</label>
                                    <input type="time" class="form-control" name="sleep_end" value="<?php echo $record['sleep_end']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kualitas Tidur</label>
                                    <select class="form-select" name="quality" required>
                                        <option value="100" <?php echo $record['quality'] == 100 ? 'selected' : ''; ?>>Sangat Baik</option>
                                        <option value="80" <?php echo $record['quality'] == 80 ? 'selected' : ''; ?>>Baik</option>
                                        <option value="60" <?php echo $record['quality'] == 60 ? 'selected' : ''; ?>>Cukup</option>
                                        <option value="40" <?php echo $record['quality'] == 40 ? 'selected' : ''; ?>>Kurang</option>
                                        <option value="20" <?php echo $record['quality'] == 20 ? 'selected' : ''; ?>>Sangat Kurang</option>
                                    </select>
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
    <script>
        // Fungsi untuk memperbarui tampilan data
        function updateSleepDisplay(data) {
            // Update ringkasan mingguan
            if (data.sleepSummary) {
                document.querySelector('[data-total-duration]').textContent = data.sleepSummary.totalDuration;
                document.querySelector('[data-average-quality]').textContent = data.sleepSummary.averageQuality;
                document.querySelector('[data-average-bedtime]').textContent = data.sleepSummary.averageBedtime;
                
                // Update progress bars
                const durationProgress = Math.min((data.sleepSummary.totalDuration / data.sleepSummary.targetDuration) * 100, 100);
                const qualityProgress = Math.min((data.sleepSummary.qualityScore / data.sleepSummary.targetQuality) * 100, 100);
                const scheduleProgress = Math.min((data.sleepSummary.scheduleScore / data.sleepSummary.targetSchedule) * 100, 100);
                
                document.querySelector('[data-duration-progress]').style.width = durationProgress + '%';
                document.querySelector('[data-quality-progress]').style.width = qualityProgress + '%';
                document.querySelector('[data-schedule-progress]').style.width = scheduleProgress + '%';
            }

            // Update tantangan mingguan
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
                            progressBar.setAttribute('aria-valuenow', challenge.percentage);
                        }

                        // Update badge persentase
                        const badge = container.querySelector('.badge');
                        if (badge) {
                            badge.textContent = `${challenge.percentage}%`;
                        }
                    }
                });
            }
        }

        // Fungsi untuk memuat data secara realtime
        function refreshData() {
            fetch('/sleep/data', {
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
                updateSleepDisplay(data);
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
    </script>
</body>
</html> 