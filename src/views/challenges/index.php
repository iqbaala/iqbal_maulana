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

// Ambil data tantangan aktif dari database
require_once __DIR__ . '/../../Controllers/ChallengeController.php';
$challengeController = new ChallengeController();
$activeChallenges = $challengeController->getActiveChallenges($_SESSION['user_id']);

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
    <title>Tantangan - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <style>
        .challenge-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .challenge-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .challenge-card .card-body {
            padding: 1.5rem;
        }

        .challenge-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .challenge-card .card-text {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .challenge-card .progress {
            height: 10px;
            border-radius: 5px;
            background-color: rgba(0,0,0,0.05);
            margin-bottom: 0.5rem;
        }

        .challenge-card .progress-bar {
            border-radius: 5px;
        }

        .challenge-card small {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .challenge-card .badge {
            padding: 0.5em 1em;
            font-weight: 500;
        }

        .challenge-card .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .challenge-card .icon-circle i {
            font-size: 1.2rem;
        }

        .challenge-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .challenge-stats {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }

        .challenge-stats h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .challenge-stats p {
            margin-bottom: 0;
            opacity: 0.8;
        }

        .btn-join {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-join:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .achievement-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 30px;
            height: 30px;
            background: gold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .achievement-badge i {
            color: white;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../src/views/layouts/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <!-- Header Section -->
        <div class="challenge-header" data-aos="fade-up">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-3">Tantangan Kesehatan</h2>
                    <p class="mb-0">Tingkatkan gaya hidup sehatmu dengan mengikuti tantangan-tantangan menarik!</p>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-6">
                            <div class="challenge-stats">
                                <h3>12</h3>
                                <p>Tantangan Selesai</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="challenge-stats">
                                <h3>3</h3>
                                <p>Tantangan Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tantangan Aktif -->
        <div class="card shadow-sm mb-4" data-aos="fade-up">
            <div class="card-body">
                <h4 class="mb-4">Tantangan Aktif</h4>
                <div class="row">
                    <?php foreach ($activeChallenges as $challenge): ?>
                    <div class="col-md-4 mb-3">
                        <div class="challenge-card card border-<?php echo $challenge['type']; ?>">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-<?php echo $challenge['type']; ?> bg-opacity-10">
                                        <i class="bi bi-<?php echo $challenge['icon']; ?> text-<?php echo $challenge['type']; ?>"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($challenge['name']); ?></h5>
                                        <span class="badge bg-<?php echo $challenge['type']; ?>">Aktif</span>
                                    </div>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars($challenge['description']); ?></p>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-<?php echo $challenge['type']; ?> progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: <?php echo $challenge['percentage']; ?>%;" 
                                         aria-valuenow="<?php echo $challenge['percentage']; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"><?php echo $challenge['percentage']; ?>%</div>
                                </div>
                                <small><?php echo $challenge['achieved_days']; ?> dari <?php echo $challenge['total_days']; ?> hari tercapai</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Tantangan Tersedia -->
        <div class="card shadow-sm mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-body">
                <h4 class="mb-4">Tantangan Tersedia</h4>
                <div class="row">
                    <!-- Tantangan 1 -->
                    <div class="col-md-4 mb-3">
                        <div class="challenge-card card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-primary bg-opacity-10">
                                        <i class="bi bi-trophy text-primary"></i>
                                    </div>
                                    <h5 class="card-title mb-0">10.000 Langkah</h5>
                                </div>
                                <p class="card-text">Capai 10.000 langkah setiap hari selama seminggu</p>
                                <button class="btn btn-join w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Mulai Tantangan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tantangan 2 -->
                    <div class="col-md-4 mb-3">
                        <div class="challenge-card card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-success bg-opacity-10">
                                        <i class="bi bi-trophy text-success"></i>
                                    </div>
                                    <h5 class="card-title mb-0">Diet Seimbang</h5>
                                </div>
                                <p class="card-text">Konsumsi makanan dari 5 kelompok makanan</p>
                                <button class="btn btn-join w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Mulai Tantangan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tantangan 3 -->
                    <div class="col-md-4 mb-3">
                        <div class="challenge-card card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-warning bg-opacity-10">
                                        <i class="bi bi-trophy text-warning"></i>
                                    </div>
                                    <h5 class="card-title mb-0">Meditasi Harian</h5>
                                </div>
                                <p class="card-text">Meditasi 10 menit setiap hari selama 2 minggu</p>
                                <button class="btn btn-join w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Mulai Tantangan
                                </button>
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

        // Fungsi untuk memulai tantangan
        function startChallenge(challengeId) {
            // Implementasi logika untuk memulai tantangan
            console.log('Memulai tantangan:', challengeId);
        }

        // Event listener untuk tombol mulai tantangan
        document.querySelectorAll('.btn-join').forEach(button => {
            button.addEventListener('click', function() {
                const challengeCard = this.closest('.challenge-card');
                const challengeName = challengeCard.querySelector('.card-title').textContent;
                startChallenge(challengeName);
            });
        });

        const nutritionAnimation = lottie.loadAnimation({
            container: document.getElementById('nutritionAnimation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://lottie.host/7c1db6c1-0e0e-4f3f-b7e7-e46d0f4f49c1/6DIBEXZgYm.json'
        });
    </script>
</body>
</html> 