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
    <title>Tips Kesehatan - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <style>
        .tips-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .tips-search {
            max-width: 500px;
            margin: 1.5rem auto 0;
        }

        .tips-search .input-group {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .tips-search input {
            border: none;
            padding: 0.8rem 1.2rem;
            font-size: 1rem;
        }

        .tips-search input:focus {
            box-shadow: none;
        }

        .tips-search .btn {
            background: var(--primary-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
        }

        .tips-search .btn:hover {
            background: var(--secondary-color);
        }

        .tips-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #fff;
            position: relative;
        }

        .tips-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .lottie-container {
            height: 250px;
            background: #f8f9fa;
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            position: relative;
        }

        .lottie-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 100%);
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        .tips-card:hover .lottie-container::after {
            opacity: 1;
        }

        .lottie-container img {
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .tips-card:hover .lottie-container img {
            transform: scale(1.1);
        }

        .tips-category {
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 0.5rem;
            margin-bottom: 0.8rem;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tips-category.nutrition {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .tips-category.exercise {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .tips-category.sleep {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .tips-category.mental {
            background-color: rgba(111, 66, 193, 0.1);
            color: #6f42c1;
        }

        .card-body {
            padding: 1.8rem;
            position: relative;
        }

        .card-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2d3436;
            line-height: 1.4;
        }

        .tips-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #636e72;
        }

        .tips-meta i {
            margin-right: 0.4rem;
            font-size: 1rem;
        }

        .tips-meta span {
            margin-right: 1.2rem;
            display: flex;
            align-items: center;
        }

        .card-text {
            color: #636e72;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .read-more {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0;
            position: relative;
        }

        .read-more::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .read-more:hover::after {
            width: 100%;
        }

        .read-more i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .read-more:hover i {
            transform: translateX(8px);
        }

        .tips-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 1;
        }

        .tips-filter {
            margin-bottom: 3rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .tips-filter .btn {
            border-radius: 50px;
            padding: 0.8rem 1.8rem;
            margin: 0.4rem;
            border: none;
            background: white;
            color: #636e72;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .tips-filter .btn:hover,
        .tips-filter .btn.active {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .daily-tip-card, .weekly-tip-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .daily-tip-card:hover, .weekly-tip-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .day-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            min-width: 50px;
            text-align: center;
        }

        .schedule-item {
            padding: 0.5rem;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .schedule-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .menu-item h6 {
            color: var(--primary-color);
            font-weight: 600;
        }

        .menu-item p {
            color: #636e72;
        }

        .weekly-menu .menu-item {
            padding: 0.8rem;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .weekly-menu .menu-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../src/views/layouts/navbar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5 pt-4">
        <!-- Header Section -->
        <div class="tips-header text-center" data-aos="fade-up">
            <h1 class="h2 mb-2">Tips Kesehatan</h1>
            <div class="tips-search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari tips...">
                    <button class="btn" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="tips-filter text-center" data-aos="fade-up">
            <button class="btn active" data-filter="all">Semua</button>
            <button class="btn" data-filter="nutrition">Nutrisi</button>
            <button class="btn" data-filter="exercise">Olahraga</button>
            <button class="btn" data-filter="sleep">Tidur</button>
            <button class="btn" data-filter="mental">Kesehatan Mental</button>
        </div>

        <!-- Tips Grid -->
        <div class="row g-4">
            <!-- Tip 1 - Nutrisi -->
            <div class="col-md-4" data-aos="fade-up">
                <div class="tips-card card">
                    <span class="tips-badge">
                        <i class="bi bi-star-fill me-1"></i>
                        Populer
                    </span>
                    <div class="lottie-container">
                        <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T1.jpg" alt="Nutrisi Seimbang" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body">
                        <span class="tips-category nutrition">
                            <i class="bi bi-apple me-1"></i>
                            Nutrisi
                        </span>
                        <h5 class="card-title">Panduan Nutrisi Seimbang</h5>
                        <div class="tips-meta">
                            <span><i class="bi bi-calendar3"></i>20 Nov 2023</span>
                            <span><i class="bi bi-eye"></i>1.2k Views</span>
                            <span><i class="bi bi-clock"></i>5 min read</span>
                        </div>
                        <p class="card-text">Pelajari cara menyusun menu makanan sehat dan seimbang untuk kebutuhan nutrisi harianmu.</p>
                        <a href="#" class="read-more">
                            Baca Selengkapnya 
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tip 2 - Olahraga -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="tips-card card">
                    <span class="tips-badge">
                        <i class="bi bi-lightning-fill me-1"></i>
                        Terbaru
                    </span>
                    <div class="lottie-container">
                        <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T2.jpg" alt="Olahraga di Rumah" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body">
                        <span class="tips-category exercise">
                            <i class="bi bi-activity me-1"></i>
                            Olahraga
                        </span>
                        <h5 class="card-title">Tips Olahraga di Rumah</h5>
                        <div class="tips-meta">
                            <span><i class="bi bi-calendar3"></i>19 Nov 2023</span>
                            <span><i class="bi bi-eye"></i>980 Views</span>
                            <span><i class="bi bi-clock"></i>7 min read</span>
                        </div>
                        <p class="card-text">Temukan berbagai gerakan olahraga yang bisa dilakukan di rumah tanpa alat khusus.</p>
                        <a href="#" class="read-more">
                            Baca Selengkapnya 
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tip 3 - Tidur -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="tips-card card">
                    <div class="lottie-container">
                        <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T3.jpg" alt="Tidur Berkualitas" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body">
                        <span class="tips-category sleep">
                            <i class="bi bi-moon-stars me-1"></i>
                            Tidur
                        </span>
                        <h5 class="card-title">Rahasia Tidur Berkualitas</h5>
                        <div class="tips-meta">
                            <span><i class="bi bi-calendar3"></i>18 Nov 2023</span>
                            <span><i class="bi bi-eye"></i>850 Views</span>
                            <span><i class="bi bi-clock"></i>4 min read</span>
                        </div>
                        <p class="card-text">Ketahui cara-cara untuk mendapatkan kualitas tidur yang lebih baik setiap malam.</p>
                        <a href="#" class="read-more">
                            Baca Selengkapnya 
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tip 4 - Mental Health -->
            <div class="col-md-4" data-aos="fade-up">
                <div class="tips-card card">
                    <span class="tips-badge">
                        <i class="bi bi-heart-fill me-1"></i>
                        Rekomendasi
                    </span>
                    <div class="lottie-container">
                        <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T4.jpg" alt="Teknik Meditasi" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body">
                        <span class="tips-category mental">
                            <i class="bi bi-brain me-1"></i>
                            Kesehatan Mental
                        </span>
                        <h5 class="card-title">Teknik Meditasi untuk Pemula</h5>
                        <div class="tips-meta">
                            <span><i class="bi bi-calendar3"></i>17 Nov 2023</span>
                            <span><i class="bi bi-eye"></i>720 Views</span>
                            <span><i class="bi bi-clock"></i>6 min read</span>
                        </div>
                        <p class="card-text">Pelajari dasar-dasar meditasi untuk menjaga kesehatan mental dan mengurangi stres.</p>
                        <a href="#" class="read-more">
                            Baca Selengkapnya 
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tip 5 - Breakfast -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="tips-card card">
                    <div class="lottie-container">
                        <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T5.jpg" alt="Menu Sarapan Sehat" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body">
                        <span class="tips-category nutrition">
                            <i class="bi bi-egg-fried me-1"></i>
                            Nutrisi
                        </span>
                        <h5 class="card-title">Menu Sarapan Sehat dan Praktis</h5>
                        <div class="tips-meta">
                            <span><i class="bi bi-calendar3"></i>16 Nov 2023</span>
                            <span><i class="bi bi-eye"></i>650 Views</span>
                            <span><i class="bi bi-clock"></i>3 min read</span>
                        </div>
                        <p class="card-text">Ide menu sarapan yang sehat, praktis, dan bisa disiapkan dalam waktu singkat.</p>
                        <a href="#" class="read-more">
                            Baca Selengkapnya 
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tip 6 - Stretching -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="tips-card card">
                    <div class="lottie-container">
                        <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T6.jpg" alt="Gerakan Peregangan" class="img-fluid w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body">
                        <span class="tips-category exercise">
                            <i class="bi bi-person-walking me-1"></i>
                            Olahraga
                        </span>
                        <h5 class="card-title">Gerakan Peregangan untuk Pekerja</h5>
                        <div class="tips-meta">
                            <span><i class="bi bi-calendar3"></i>15 Nov 2023</span>
                            <span><i class="bi bi-eye"></i>580 Views</span>
                            <span><i class="bi bi-clock"></i>5 min read</span>
                        </div>
                        <p class="card-text">Rangkaian gerakan peregangan yang bisa dilakukan di meja kerja untuk mencegah pegal.</p>
                        <a href="#" class="read-more">
                            Baca Selengkapnya 
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips Harian & Mingguan Section -->
        <div class="tips-special mt-5 pt-3">
            <!-- Tips Harian -->
            <div class="daily-tips mb-5" data-aos="fade-up">
                <h2 class="mb-4">Tips Harian</h2>
                <div class="card daily-tip-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="https://assets.onecompiler.app/42zydsevf/433baurbg/T1.jpg" alt="Tips Hari Ini" class="img-fluid h-100 object-fit-cover rounded-start">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="tips-category nutrition">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Tips Hari Ini
                                    </span>
                                    <span class="badge bg-primary rounded-pill"></span>
                                </div>
                                <h3 class="card-title h4">5 Kebiasaan Pagi untuk Tubuh Lebih Sehat</h3>
                                <ul class="list-unstyled mt-3 mb-4">
                                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Minum air putih hangat saat bangun tidur</li>
                                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Lakukan peregangan ringan 5-10 menit</li>
                                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Sarapan dengan menu seimbang</li>
                                    <li class="mb-2"><i class="bi bi-check2-circle text-success me-2"></i>Meditasi atau berdoa 5 menit</li>
                                    <li><i class="bi bi-check2-circle text-success me-2"></i>Berjalan kaki atau bersepeda ke tempat kerja</li>
                                </ul>
                                <a href="#" class="read-more">
                                    Pelajari Selengkapnya
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Mingguan -->
            <div class="weekly-tips mb-5" data-aos="fade-up">
                <h2 class="mb-4">Tips Mingguan</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card weekly-tip-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="tips-category exercise">
                                        <i class="bi bi-calendar-week me-1"></i>
                                        Minggu Ini
                                    </span>
                                    <span class="badge bg-primary rounded-pill">Minggu 3</span>
                                </div>
                                <h3 class="card-title h4 mb-4">Program Olahraga Mingguan</h3>
                                <div class="weekly-schedule">
                                    <div class="schedule-item mb-3 d-flex align-items-center">
                                        <span class="day-badge">Sen</span>
                                        <span class="ms-3">Cardio - 30 menit jogging</span>
                                    </div>
                                    <div class="schedule-item mb-3 d-flex align-items-center">
                                        <span class="day-badge">Sel</span>
                                        <span class="ms-3">Latihan kekuatan tubuh bagian atas</span>
                                    </div>
                                    <div class="schedule-item mb-3 d-flex align-items-center">
                                        <span class="day-badge">Rab</span>
                                        <span class="ms-3">Yoga atau pilates</span>
                                    </div>
                                    <div class="schedule-item mb-3 d-flex align-items-center">
                                        <span class="day-badge">Kam</span>
                                        <span class="ms-3">Latihan kekuatan tubuh bagian bawah</span>
                                    </div>
                                    <div class="schedule-item mb-3 d-flex align-items-center">
                                        <span class="day-badge">Jum</span>
                                        <span class="ms-3">HIIT - 20 menit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card weekly-tip-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="tips-category nutrition">
                                        <i class="bi bi-calendar-week me-1"></i>
                                        Minggu Ini
                                    </span>
                                    <span class="badge bg-primary rounded-pill">Minggu 3</span>
                                </div>
                                <h3 class="card-title h4 mb-4">Menu Makanan Sehat Mingguan</h3>
                                <div class="weekly-menu">
                                    <div class="menu-item mb-3">
                                        <h6 class="mb-2">Sarapan</h6>
                                        <p class="mb-0">Oatmeal dengan buah dan kacang-kacangan</p>
                                    </div>
                                    <div class="menu-item mb-3">
                                        <h6 class="mb-2">Makan Siang</h6>
                                        <p class="mb-0">Salad dengan protein (ayam/ikan/tahu) dan quinoa</p>
                                    </div>
                                    <div class="menu-item mb-3">
                                        <h6 class="mb-2">Makan Malam</h6>
                                        <p class="mb-0">Sup sayuran dengan daging tanpa lemak</p>
                                    </div>
                                    <div class="menu-item">
                                        <h6 class="mb-2">Camilan Sehat</h6>
                                        <p class="mb-0">Buah-buahan segar atau kacang-kacangan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Tips Section -->
        <div class="mt-5 pt-3">
            <h2 class="mb-4" data-aos="fade-up">Video Tips Kesehatan</h2>
            <div class="row g-4">
                <!-- Video 1 -->
                <div class="col-md-6" data-aos="fade-up">
                    <div class="card tips-card">
                        <div class="ratio ratio-16x9 rounded-top overflow-hidden">
                            <iframe 
                                src="https://www.youtube.com/embed/NCAUHZ2g8mo" 
                                title="Tips Menjaga Kesehatan Mental" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="card-body">
                            <span class="tips-category mental">Kesehatan Tubuh</span>
                            <h5 class="card-title">Tips Menjaga Kesehatan Tubuh</h5>
                            <div class="tips-meta">
                                <span><i class="bi bi-person-circle"></i>dr. Zaidul Akbar</span>
                                <span><i class="bi bi-youtube"></i>Helmi Yahya Bicara</span>
                            </div>
                            <p class="card-text">Pelajari tips-tips penting untuk menjaga kesehatan Tubuh ala Rasulullah Anda bersama dr. Zaidul Akbar.</p>
                        </div>
                    </div>
                </div>

                <!-- Video 2 -->
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card tips-card">
                        <div class="ratio ratio-16x9 rounded-top overflow-hidden">
                            <iframe 
                                src="https://www.youtube.com/embed/foN4IsP6-uc" 
                                title="Tips Menjaga Kesehatan Tubuh" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="card-body">
                            <span class="tips-category exercise">Kesehatan Umum</span>
                            <h5 class="card-title">Tips Menjaga Kesehatan Tubuh</h5>
                            <div class="tips-meta">
                                <span><i class="bi bi-person-circle"></i>dr. Tirta</span>
                                <span><i class="bi bi-youtube"></i>Tirta PengPengPeng</span>
                            </div>
                            <p class="card-text">Temukan cara-cara efektif untuk menaikkan berat badan secara sehat, bersama dr.Tirta</p>
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

        // Fungsi untuk mencari tips
        function searchTips(query) {
            query = query.toLowerCase();
            const tipCards = document.querySelectorAll('.tips-card');
            
            tipCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const category = card.querySelector('.tips-category').textContent.toLowerCase();
                const content = card.querySelector('.card-text').textContent.toLowerCase();
                
                if (title.includes(query) || category.includes(query) || content.includes(query)) {
                    card.closest('.col-md-4').style.display = 'block';
                } else {
                    card.closest('.col-md-4').style.display = 'none';
                }
            });
        }

        // Event listener untuk pencarian real-time
        document.querySelector('.tips-search input').addEventListener('input', function(e) {
            searchTips(e.target.value);
        });

        // Event listener untuk tombol cari
        document.querySelector('.tips-search button').addEventListener('click', function() {
            const searchQuery = document.querySelector('.tips-search input').value;
            searchTips(searchQuery);
        });

        // Event listener untuk tombol Enter pada input pencarian
        document.querySelector('.tips-search input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchQuery = this.value;
                searchTips(searchQuery);
            }
        });

        // Filter tips berdasarkan kategori
        document.querySelectorAll('.tips-filter .btn').forEach(button => {
            button.addEventListener('click', function() {
                // Hapus kelas active dari semua tombol
                document.querySelectorAll('.tips-filter .btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Tambah kelas active ke tombol yang diklik
                this.classList.add('active');
                
                // Ambil nilai filter
                const filter = this.dataset.filter;
                
                // Filter kartu tips
                const tipCards = document.querySelectorAll('.tips-card');
                tipCards.forEach(card => {
                    const category = card.querySelector('.tips-category').className;
                    if (filter === 'all') {
                        card.closest('.col-md-4').style.display = 'block';
                    } else if (category.includes(filter)) {
                        card.closest('.col-md-4').style.display = 'block';
                    } else {
                        card.closest('.col-md-4').style.display = 'none';
                    }
                });
            });
        });

        // Tampilkan tips populer saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua tips yang memiliki badge populer
            const popularTips = document.querySelectorAll('.tips-card .tips-badge');
            popularTips.forEach(badge => {
                if (badge.textContent.trim() === 'Populer') {
                    badge.closest('.tips-card').classList.add('popular');
                }
            });
        });

        // Fungsi untuk mengurutkan tips berdasarkan views
        function sortByViews() {
            const tipsContainer = document.querySelector('.row.g-4');
            const tipCards = Array.from(document.querySelectorAll('.col-md-4'));
            
            tipCards.sort((a, b) => {
                const viewsA = parseInt(a.querySelector('.bi-eye').parentElement.textContent.replace(/[^0-9]/g, ''));
                const viewsB = parseInt(b.querySelector('.bi-eye').parentElement.textContent.replace(/[^0-9]/g, ''));
                return viewsB - viewsA;
            });
            
            tipCards.forEach(card => tipsContainer.appendChild(card));
        }

        // Panggil fungsi sort saat tombol Populer diklik
        document.querySelector('[data-filter="all"]').addEventListener('click', sortByViews);
    </script>
</body>
</html>