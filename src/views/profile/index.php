<?php
require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Models/User.php';

use WellBe\Models\User;

// Cek status session sebelum memulai session baru
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

try {
    // Ambil data user dari database
    $user = new User();
    $userData = $user->getUserData($_SESSION['user_id']);
} catch (\Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    $userData = [];
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
    <title>Profil - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }
        
        body {
            background-color: #f8f9fc;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 120px 0 100px;
            color: white;
            margin-bottom: -50px;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff20" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,165.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
            opacity: 0.1;
        }

        .avatar-wrapper {
            position: relative;
            width: 180px;
            height: 180px;
            margin: -90px auto 20px;
            z-index: 2;
        }

        .avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 6px solid white;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
        }

        .avatar-change {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--primary-color);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 4px solid white;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .avatar-change:hover {
            background: var(--secondary-color);
            transform: scale(1.1) rotate(15deg);
        }

        .profile-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            margin-bottom: 25px;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .profile-card .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 25px;
        }

        .profile-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--primary-color);
        }

        .profile-stats {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            border-right: 1px solid rgba(0,0,0,0.05);
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-item i {
            font-size: 28px;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-item h5 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .achievement-badge {
            text-align: center;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .achievement-badge:hover {
            transform: translateY(-5px);
        }

        .badge-icon {
            width: 70px;
            height: 70px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
            color: var(--primary-color);
            transition: all 0.3s ease;
            position: relative;
        }

        .badge-icon::after {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            opacity: 0.1;
            z-index: -1;
            transition: all 0.3s ease;
        }

        .achievement-badge:hover .badge-icon::after {
            transform: scale(1.2);
            opacity: 0.2;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.15);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .btn-save {
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78,115,223,0.3);
        }

        .btn-save::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn-save:hover::after {
            left: 100%;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .avatar-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .avatar-option {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            cursor: pointer;
            border: 4px solid transparent;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .avatar-option:hover {
            transform: scale(1.1);
            border-color: var(--primary-color);
        }

        .avatar-option.selected {
            border-color: var(--primary-color);
            box-shadow: 0 0 20px rgba(78,115,223,0.3);
        }

        .progress-ring {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.05);
            margin: 0 auto 20px;
            text-align: center;
        }

        .progress-ring-content h3 {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .progress-ring-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .progress {
            height: 10px;
            border-radius: 10px;
            background-color: rgba(0,0,0,0.1);
        }

        .progress-bar {
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .stat-item {
                border-right: none;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            
            .stat-item:last-child {
                border-bottom: none;
            }
            
            .avatar-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include '../src/views/layouts/navbar.php'; ?>

    <div class="profile-header">
        <div class="container">
            <h1 class="text-center mb-0" data-aos="fade-down">Profil Saya</h1>
            <p class="text-center text-white mt-2">Member sejak <?php echo date('F Y', strtotime($userData['created_at'] ?? 'now')); ?></p>
        </div>
    </div>

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo htmlspecialchars($error);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo htmlspecialchars($success);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card profile-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body text-center">
                        <div class="avatar-wrapper">
                            <img src="<?php echo htmlspecialchars($userData['avatar'] ?? 'https://assets.onecompiler.app/42zydsevf/43388wgwe/11.jpg'); ?>" 
                                 alt="Avatar" class="avatar" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <div class="avatar-change" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                <i class="bi bi-camera"></i>
                            </div>
                        </div>
                        <h4 class="mb-1"><?php echo htmlspecialchars($userData['name'] ?? ''); ?></h4>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
                        
                        <div class="progress-ring mb-4">
                            <div class="progress-ring-content">
                                <h3 class="mb-0">Kelengkapan Profil</h3>
                                <h2 class="mb-0 text-primary"><?php 
                                    $completedFields = 0;
                                    $totalFields = 7; // name, email, gender, birth_date, height, weight, health_target
                                    
                                    if (!empty($userData['name'])) $completedFields++;
                                    if (!empty($userData['email'])) $completedFields++;
                                    if (!empty($userData['gender'])) $completedFields++;
                                    if (!empty($userData['birth_date'])) $completedFields++;
                                    if (!empty($userData['height'])) $completedFields++;
                                    if (!empty($userData['weight'])) $completedFields++;
                                    if (!empty($userData['health_target'])) $completedFields++;
                                    
                                    $completionPercentage = round(($completedFields / $totalFields) * 100);
                                    echo $completionPercentage . '%';
                                ?></h2>
                                <div class="progress mt-3" style="height: 10px;">
                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: <?php echo $completionPercentage; ?>%"></div>
                                </div>
                                <p class="text-muted small mt-2">
                                    <?php 
                                    $remainingFields = $totalFields - $completedFields;
                                    if ($remainingFields > 0) {
                                        echo "Lengkapi " . $remainingFields . " data lagi untuk profil lengkap";
                                    } else {
                                        echo "Profil Anda sudah lengkap!";
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="profile-stats row g-0">
                            <div class="col-4 stat-item">
                                <i class="bi bi-trophy"></i>
                                <h5>15</h5>
                                <small class="text-muted">Tantangan</small>
                            </div>
                            <div class="col-4 stat-item">
                                <i class="bi bi-graph-up"></i>
                                <h5><?php 
                                    $avgProgress = isset($dashboardData['weeklyProgress']) ? 
                                        round(($dashboardData['weeklyProgress']['nutrition']['percentage'] +
                                        $dashboardData['weeklyProgress']['exercise']['percentage'] +
                                        $dashboardData['weeklyProgress']['sleep']['percentage']) / 3) : 0;
                                    echo $avgProgress;
                                ?>%</h5>
                                <small class="text-muted">Target</small>
                            </div>
                            <div class="col-4 stat-item">
                                <i class="bi bi-star"></i>
                                <h5>120</h5>
                                <small class="text-muted">Poin</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card profile-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pencapaian</h5>
                        <span class="badge bg-primary">3/8</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-4 achievement-badge">
                                <div class="badge-icon">
                                    <i class="bi bi-award"></i>
                                </div>
                                <small>Pemula</small>
                            </div>
                            <div class="col-4 achievement-badge">
                                <div class="badge-icon">
                                    <i class="bi bi-lightning"></i>
                                </div>
                                <small>Konsisten</small>
                            </div>
                            <div class="col-4 achievement-badge">
                                <div class="badge-icon">
                                    <i class="bi bi-heart"></i>
                                </div>
                                <small>Sehat</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card profile-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-header">
                        <h5 class="mb-0">Aktivitas Terkini</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-circle-fill text-success me-2" style="font-size: 8px;"></i>
                                    <div>
                                        <p class="mb-0">Menyelesaikan tantangan hari ini</p>
                                        <small class="text-muted">2 jam yang lalu</small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-circle-fill text-primary me-2" style="font-size: 8px;"></i>
                                    <div>
                                        <p class="mb-0">Mencapai target kalori</p>
                                        <small class="text-muted">5 jam yang lalu</small>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-circle-fill text-warning me-2" style="font-size: 8px;"></i>
                                    <div>
                                        <p class="mb-0">Memulai tantangan baru</p>
                                        <small class="text-muted">1 hari yang lalu</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card profile-card mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Pribadi</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="profileForm">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id'] ?? ''); ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-person me-2"></i>Nama Lengkap
                                    </label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-envelope me-2"></i>Email
                                    </label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-calendar me-2"></i>Tanggal Lahir
                                    </label>
                                    <input type="date" class="form-control" name="birth_date" 
                                           value="<?php echo htmlspecialchars($userData['birth_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-gender-ambiguous me-2"></i>Jenis Kelamin
                                    </label>
                                    <select class="form-select" name="gender">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" <?php echo ($userData['gender'] ?? '') === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="P" <?php echo ($userData['gender'] ?? '') === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-arrows-vertical me-2"></i>Tinggi Badan (cm)
                                    </label>
                                    <input type="number" class="form-control" name="height" 
                                           value="<?php echo htmlspecialchars($userData['height'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-speedometer2 me-2"></i>Berat Badan (kg)
                                    </label>
                                    <input type="number" class="form-control" name="weight" 
                                           value="<?php echo htmlspecialchars($userData['weight'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-bullseye me-2"></i>Target Kesehatan
                                </label>
                                <textarea class="form-control" name="health_target" rows="3" 
                                          placeholder="Tuliskan target kesehatan Anda"><?php echo htmlspecialchars($userData['health_target'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="bi bi-check2-circle me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card profile-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="card-header">
                        <h5 class="mb-0">Preferensi Notifikasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                                <label class="form-check-label" for="emailNotif">
                                    <i class="bi bi-envelope me-2"></i>Notifikasi Email
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Terima pembaruan dan pengingat melalui email</small>
                        </div>
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="reminderNotif" checked>
                                <label class="form-check-label" for="reminderNotif">
                                    <i class="bi bi-bell me-2"></i>Pengingat Harian
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Terima pengingat untuk aktivitas harian</small>
                        </div>
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="achievementNotif">
                                <label class="form-check-label" for="achievementNotif">
                                    <i class="bi bi-trophy me-2"></i>Notifikasi Pencapaian
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Dapatkan pemberitahuan saat mencapai target</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Avatar -->
    <div class="modal fade" id="avatarModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Pilih Avatar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="avatarTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="default-tab" data-bs-toggle="tab" data-bs-target="#default-avatars" type="button" role="tab">
                                Avatar Default
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-avatar" type="button" role="tab">
                                Upload Avatar
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="avatarTabsContent">
                        <!-- Default Avatars -->
                        <div class="tab-pane fade show active" id="default-avatars" role="tabpanel">
                            <div class="avatar-grid">
                                <?php
                                $avatars = [
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/11.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/12.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/13.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/14.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/15.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/16.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/17.jpg',
                                    'https://assets.onecompiler.app/42zydsevf/43388wgwe/18.jpg'
                                ];
                                foreach ($avatars as $index => $avatar): ?>
                                    <img src="<?php echo $avatar; ?>" 
                                         alt="Avatar <?php echo $index + 1; ?>" 
                                         class="avatar-option <?php echo $index === 0 ? 'selected' : ''; ?>"
                                         onclick="selectAvatar(this, '<?php echo $avatar; ?>', 'default')">
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Upload Avatar -->
                        <div class="tab-pane fade" id="upload-avatar" role="tabpanel">
                            <div class="text-center p-4">
                                <div class="upload-preview mb-3">
                                    <img id="avatarPreview" src="/assets/images/placeholder.png" alt="Preview" 
                                         style="max-width: 200px; max-height: 200px; border-radius: 50%;">
                                </div>
                                <div class="upload-controls">
                                    <input type="file" class="form-control" id="avatarUpload" accept="image/*" style="display: none;">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('avatarUpload').click()">
                                        <i class="bi bi-upload me-2"></i>Pilih Gambar
                                    </button>
                                    <div class="text-muted mt-2">
                                        <small>Format yang didukung: JPG, PNG, GIF (Max. 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveAvatar()">
                        <i class="bi bi-check2 me-2"></i>Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        let selectedAvatar = '<?php echo htmlspecialchars($userData['avatar'] ?? 'https://assets.onecompiler.app/42zydsevf/43388wgwe/11.jpg'); ?>';
        let avatarType = 'default';
        let uploadedFile = null;

        function selectAvatar(element, avatarUrl, type) {
            document.querySelectorAll('.avatar-option').forEach(avatar => {
                avatar.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedAvatar = avatarUrl;
            avatarType = type;
        }

        // Preview uploaded image
        document.getElementById('avatarUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Hanya file gambar yang diperbolehkan.');
                    this.value = '';
                    return;
                }

                uploadedFile = file;
                avatarType = 'upload';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                    selectedAvatar = e.target.result;
                    
                    // Remove selection from default avatars
                    document.querySelectorAll('.avatar-option').forEach(avatar => {
                        avatar.classList.remove('selected');
                    });
                }
                reader.readAsDataURL(file);
            }
        });

        async function saveAvatar() {
            const saveButton = document.querySelector('#avatarModal .btn-primary');
            saveButton.disabled = true;
            showAlert('info', 'Sedang memproses...');
            
            try {
                let formData = new FormData();
                formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
                
                if (avatarType === 'upload' && uploadedFile) {
                    formData.append('avatar_file', uploadedFile);
                    formData.append('avatar_type', 'upload');
                } else {
                    formData.append('avatar_url', selectedAvatar);
                    formData.append('avatar_type', 'default');
                }
                
                const response = await fetch('/profile/update-avatar', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    // Update avatar di halaman
                    const avatarImg = document.querySelector('.avatar');
                    if (avatarImg) {
                        avatarImg.src = result.data?.avatar_url || selectedAvatar;
                    }
                    
                    // Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    showAlert('success', result.message || 'Avatar berhasil diperbarui');
                    
                    // Reset upload form
                    document.getElementById('avatarUpload').value = '';
                    uploadedFile = null;
                } else {
                    throw new Error(result.message || 'Gagal memperbarui avatar');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', `Gagal memperbarui avatar: ${error.message}`);
            } finally {
                saveButton.disabled = false;
            }
        }

        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            showAlert('info', 'Sedang memproses...');
            
            try {
                const formData = new FormData(this);
                
                // Debug log
                console.log('Form data being sent:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                
                const response = await fetch('/profile/update', {
                    method: 'POST',
                    body: formData
                });
                
                // Debug log
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (error) {
                    console.error('Failed to parse response:', error);
                    console.error('Raw response:', responseText);
                    throw new Error('Server response is not valid JSON');
                }
                
                console.log('Parsed response:', result);
                
                if (result.success) {
                    showAlert('success', result.message);
                    
                    // Update displayed data if available
                    if (result.data) {
                        document.querySelector('h4.mb-1').textContent = result.data.name;
                        document.querySelector('p.text-muted.mb-3').textContent = result.data.email;
                    }
                    
                    // Refresh halaman setelah 1 detik
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(result.message || 'Gagal memperbarui profil');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', error.message || 'Terjadi kesalahan saat memperbarui profil');
            } finally {
                submitButton.disabled = false;
            }
        });

        function showAlert(type, message) {
            // Hapus alert yang sudah ada
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            const container = document.querySelector('.container');
            const firstRow = container.querySelector('.row');
            container.insertBefore(alert, firstRow);
            
            if (type !== 'info') {
                // Auto-hide alert after 5 seconds
                setTimeout(() => {
                    const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                    if (alertInstance) {
                        alertInstance.close();
                    }
                }, 5000);
            }
        }

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                if (alertInstance) {
                    alertInstance.close();
                }
            });
        }, 5000);
    </script>
</body>
</html> 
</html> 