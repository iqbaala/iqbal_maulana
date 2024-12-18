<?php
// Cek status session sebelum memulai session baru
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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
    <title>Nutrisi - WellBe</title>
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
                        <h2 class="mb-0">Nutrisi</h2>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNutritionModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Makanan
                    </button>
                </div>

                <!-- Ringkasan Nutrisi -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center border-end">
                                <div id="nutritionAnimation" style="width: 100%; height: 180px; margin-bottom: 1rem;"></div>
                                <h5 class="mb-1">Ringkasan Minggu Ini</h5>
                                <p class="text-muted small mb-0"><?php echo date('d M', strtotime('monday this week')); ?> - <?php echo date('d M Y', strtotime('sunday this week')); ?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="row g-3">
                                    <!-- Total Kalori -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="nutrition-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-lightning-charge"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Total Kalori</h6>
                                                    <div class="stat-value">
                                                        <span data-total-calories><?php echo isset($nutritionSummary['totalCalories']) ? number_format($nutritionSummary['totalCalories']) : 0; ?></span>
                                                        <small>kkal</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-primary" role="progressbar" 
                                                             data-calories-progress
                                                             style="width: <?php echo isset($nutritionSummary['totalCalories']) ? min(($nutritionSummary['totalCalories'] / $nutritionSummary['targetCalories']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($nutritionSummary['targetCalories']); ?> kkal
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Protein -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="nutrition-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-egg-fried"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Protein</h6>
                                                    <div class="stat-value">
                                                        <span data-total-protein><?php echo isset($nutritionSummary['totalProtein']) ? number_format($nutritionSummary['totalProtein'], 1) : 0; ?></span>
                                                        <small>gram</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" role="progressbar" 
                                                             data-protein-progress
                                                             style="width: <?php echo isset($nutritionSummary['totalProtein']) ? min(($nutritionSummary['totalProtein'] / $nutritionSummary['targetProtein']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($nutritionSummary['targetProtein']); ?> gram
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Karbohidrat -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="nutrition-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-grid"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Karbohidrat</h6>
                                                    <div class="stat-value">
                                                        <span data-total-carbs><?php echo isset($nutritionSummary['totalCarbs']) ? number_format($nutritionSummary['totalCarbs'], 1) : 0; ?></span>
                                                        <small>gram</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-info" role="progressbar" 
                                                             data-carbs-progress
                                                             style="width: <?php echo isset($nutritionSummary['totalCarbs']) ? min(($nutritionSummary['totalCarbs'] / $nutritionSummary['targetCarbs']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($nutritionSummary['targetCarbs']); ?> gram
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lemak -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="nutrition-stat-card">
                                            <div class="stat-content">
                                                <div class="stat-icon-wrapper">
                                                    <i class="bi bi-droplet"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h6>Lemak</h6>
                                                    <div class="stat-value">
                                                        <span data-total-fat><?php echo isset($nutritionSummary['totalFat']) ? number_format($nutritionSummary['totalFat'], 1) : 0; ?></span>
                                                        <small>gram</small>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-warning" role="progressbar" 
                                                             data-fat-progress
                                                             style="width: <?php echo isset($nutritionSummary['totalFat']) ? min(($nutritionSummary['totalFat'] / $nutritionSummary['targetFat']) * 100, 100) : 0; ?>%">
                                                        </div>
                                                    </div>
                                                    <div class="stat-target">
                                                        Target: <?php echo number_format($nutritionSummary['targetFat']); ?> gram
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
                    .nutrition-stat-card {
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

                    .nutrition-stat-card::before {
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

                    .nutrition-stat-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                    }

                    .nutrition-stat-card:hover::before {
                        opacity: 1;
                    }

                    .nutrition-stat-card:hover .stat-icon-wrapper i {
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

                    .nutrition-stat-card:nth-child(1) .stat-icon-wrapper {
                        background: rgba(13, 110, 253, 0.1);
                        color: #0d6efd;
                    }

                    .nutrition-stat-card:nth-child(2) .stat-icon-wrapper {
                        background: rgba(25, 135, 84, 0.1);
                        color: #198754;
                    }

                    .nutrition-stat-card:nth-child(3) .stat-icon-wrapper {
                        background: rgba(13, 202, 240, 0.1);
                        color: #0dcaf0;
                    }

                    .nutrition-stat-card:nth-child(4) .stat-icon-wrapper {
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
                        .nutrition-stat-card {
                            margin-bottom: 1rem;
                        }
                    }
                </style>

                <!-- Tantangan Aktif -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="mb-3">Tantangan Aktif</h4>
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
                                        <?php if ($key === 'balanced_meals'): ?>
                                            <div class="small text-muted">
                                                <span class="target-text">Target: <?php echo $challenge['target']['calories'][0]; ?>-<?php echo $challenge['target']['calories'][1]; ?> kkal</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="small text-muted">
                                                <span class="target-text">Target: <?php echo is_array($challenge['target']) ? implode('-', $challenge['target']) : $challenge['target']; ?> <?php echo $key === 'daily_protein' ? 'gram' : 'kkal'; ?></span>
                                            </div>
                                        <?php endif; ?>
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

                <!-- Catatan Makanan -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4>Catatan Makanan</h4>
                        <div class="table-responsive mt-3">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Makanan</th>
                                        <th>Porsi</th>
                                        <th>Kalori</th>
                                        <th>Protein</th>
                                        <th>Karbo</th>
                                        <th>Lemak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($nutritionRecords)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data makanan</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($nutritionRecords as $record): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                                <td><?php echo htmlspecialchars($record['time']); ?></td>
                                                <td><?php echo htmlspecialchars($record['food_name']); ?></td>
                                                <td>1 porsi</td>
                                                <td><?php echo htmlspecialchars($record['calories']); ?> kkal</td>
                                                <td><?php echo htmlspecialchars($record['protein']); ?>g</td>
                                                <td><?php echo htmlspecialchars($record['carbs']); ?>g</td>
                                                <td><?php echo htmlspecialchars($record['fat']); ?>g</td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editNutritionModal<?php echo $record['id']; ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="/nutrition/delete" method="POST" class="d-inline">
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

    <!-- Modal Tambah Makanan -->
    <div class="modal fade" id="addNutritionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Makanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/nutrition/add" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Waktu Makan</label>
                            <input type="time" class="form-control" name="time" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Makanan</label>
                            <input type="text" class="form-control" name="food_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Porsi</label>
                            <input type="text" class="form-control" name="portion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kalori (kkal)</label>
                            <input type="number" class="form-control" name="calories" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Protein (g)</label>
                                    <input type="number" class="form-control" name="protein" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Karbohidrat (g)</label>
                                    <input type="number" class="form-control" name="carbs" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Lemak (g)</label>
                                    <input type="number" class="form-control" name="fat" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Makanan -->
    <?php if (!empty($nutritionRecords)): ?>
        <?php foreach ($nutritionRecords as $record): ?>
            <div class="modal fade" id="editNutritionModal<?php echo $record['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Makanan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="/nutrition/update" method="POST">
                                <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="date" value="<?php echo $record['date']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Waktu Makan</label>
                                    <input type="time" class="form-control" name="time" value="<?php echo $record['time']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Makanan</label>
                                    <input type="text" class="form-control" name="food_name" value="<?php echo htmlspecialchars($record['food_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kalori (kkal)</label>
                                    <input type="number" class="form-control" name="calories" value="<?php echo $record['calories']; ?>" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Protein (g)</label>
                                            <input type="number" class="form-control" name="protein" value="<?php echo $record['protein']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Karbohidrat (g)</label>
                                            <input type="number" class="form-control" name="carbs" value="<?php echo $record['carbs']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Lemak (g)</label>
                                            <input type="number" class="form-control" name="fat" value="<?php echo $record['fat']; ?>" required>
                                        </div>
                                    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Inisialisasi animasi Lottie
                const nutritionAnimation = lottie.loadAnimation({
                    container: document.getElementById('nutritionAnimation'),
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: 'https://assets3.lottiefiles.com/packages/lf20_ysas4vcp.json' // Animasi healthy food dan nutrisi
                });

                nutritionAnimation.addEventListener('data_failed', function() {
                    console.log('Gagal memuat animasi Lottie');
                    // Sembunyikan container animasi jika gagal
                    document.getElementById('nutritionAnimation').style.display = 'none';
                });
            } catch (error) {
                console.error('Error saat memuat animasi:', error);
                // Sembunyikan container animasi jika terjadi error
                document.getElementById('nutritionAnimation').style.display = 'none';
            }

            // Fungsi untuk memformat angka
            function formatNumber(number, decimals = 0) {
                return number.toLocaleString('id-ID', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }

            // Fungsi untuk memperbarui tampilan data
            function updateNutritionDisplay(data) {
                // Update ringkasan mingguan
                document.querySelector('[data-total-calories]').textContent = formatNumber(data.nutritionSummary.totalCalories);
                document.querySelector('[data-total-protein]').textContent = formatNumber(data.nutritionSummary.totalProtein, 1);
                document.querySelector('[data-total-carbs]').textContent = formatNumber(data.nutritionSummary.totalCarbs, 1);
                document.querySelector('[data-total-fat]').textContent = formatNumber(data.nutritionSummary.totalFat, 1);

                // Update progress bars nutrisi
                const caloriesProgress = Math.min((data.nutritionSummary.totalCalories / data.nutritionSummary.targetCalories) * 100, 100);
                const proteinProgress = Math.min((data.nutritionSummary.totalProtein / data.nutritionSummary.targetProtein) * 100, 100);
                const carbsProgress = Math.min((data.nutritionSummary.totalCarbs / data.nutritionSummary.targetCarbs) * 100, 100);
                const fatProgress = Math.min((data.nutritionSummary.totalFat / data.nutritionSummary.targetFat) * 100, 100);

                document.querySelector('[data-calories-progress]').style.width = caloriesProgress + '%';
                document.querySelector('[data-protein-progress]').style.width = proteinProgress + '%';
                document.querySelector('[data-carbs-progress]').style.width = carbsProgress + '%';
                document.querySelector('[data-fat-progress]').style.width = fatProgress + '%';

                // Update tantangan aktif secara real-time
                if (data.activeChallenges) {
                    Object.entries(data.activeChallenges).forEach(([key, challenge]) => {
                        const container = document.querySelector(`[data-challenge="${key}"]`);
                        if (container) {
                            // Update persentase badge
                            const badge = container.querySelector('.badge');
                            if (badge) {
                                badge.textContent = `${challenge.percentage}%`;
                            }

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

                            // Update target text
                            const targetText = container.querySelector('.target-text');
                            if (targetText) {
                                if (key === 'balanced_meals') {
                                    targetText.textContent = `Target: ${challenge.target.calories[0]}-${challenge.target.calories[1]} kkal`;
                                } else {
                                    const targetValue = Array.isArray(challenge.target) ? 
                                        challenge.target.join('-') : challenge.target;
                                    targetText.textContent = `Target: ${targetValue} ${key === 'daily_protein' ? 'gram' : 'kkal'}`;
                                }
                            }
                        }
                    });
                }
            }

            // Fungsi untuk memuat data secara realtime
            function refreshData() {
                fetch('/nutrition/data', {
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
                    updateNutritionDisplay(data);
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
</body>
</html> 