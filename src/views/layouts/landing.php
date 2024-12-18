<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WellBe - Wellness & Balance dalam Genggaman Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
        }
        
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 120px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
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

        .feature-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(var(--bs-primary-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transition: transform 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            background: var(--primary-color);
            color: white;
        }

        .stats-box {
            padding: 2.5rem;
            text-align: center;
            border-radius: 1rem;
            background: white;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stats-box:hover {
            transform: translateY(-10px);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff10" fill-opacity="1" d="M0,160L48,144C96,128,192,96,288,106.7C384,117,480,171,576,181.3C672,192,768,160,864,144C960,128,1056,128,1152,138.7C1248,149,1344,171,1392,181.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
        }

        .btn-glow {
            position: relative;
            animation: glow 1.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 0 5px #fff, 0 0 10px #fff, 0 0 15px var(--primary-color);
            }
            to {
                box-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px var(--primary-color);
            }
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .text-gradient {
            background: linear-gradient(to right, #fff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 3.5rem;
            line-height: 1.2;
        }

        .highlight-text {
            color: #fff;
            position: relative;
            display: inline-block;
        }

        .highlight-text::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        .feature-icon-small {
            width: 40px;
            height: 40px;
            min-width: 40px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .features-list p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .ratings-icons {
            font-size: 0.9rem;
        }

        .verified-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }

        @media (max-width: 768px) {
            .text-gradient {
                font-size: 2.5rem;
            }
            
            .features-list p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-heart-pulse text-primary"></i> WellBe
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-4" href="/register">Daftar Sekarang</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="badge bg-light text-primary mb-3 px-3 py-2 rounded-pill" data-aos="fade-down" data-aos-delay="100">
                        <i class="bi bi-stars"></i> Platform Kesehatan #1 Menurut Saya
                    </span>
                    <h1 class="display-4 fw-bold mb-4 text-gradient">
                        Wujudkan Gaya Hidup<br>
                        <span class="highlight-text">Sehat & Seimbang</span>
                    </h1>
                    <div class="features-list mb-4">
                        <div class="d-flex align-items-center mb-3" data-aos="fade-up" data-aos-delay="200">
                            <div class="feature-icon-small bg-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-shield-check text-primary"></i>
                            </div>
                            <p class="mb-0 text-white">Pantau nutrisi harian dengan mudah</p>
                        </div>
                        <div class="d-flex align-items-center mb-3" data-aos="fade-up" data-aos-delay="300">
                            <div class="feature-icon-small bg-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-graph-up-arrow text-primary"></i>
                            </div>
                            <p class="mb-0 text-white">Tracking aktivitas fisik real-time</p>
                        </div>
                        <div class="d-flex align-items-center mb-4" data-aos="fade-up" data-aos-delay="400">
                            <div class="feature-icon-small bg-white rounded-circle me-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-moon-stars text-primary"></i>
                            </div>
                            <p class="mb-0 text-white">Analisis kualitas tidur yang akurat</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/register" class="btn btn-light btn-lg btn-glow" data-aos="fade-up" data-aos-delay="500">
                            <i class="bi bi-rocket me-2"></i>Mulai Sekarang
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg" data-aos="fade-up" data-aos-delay="600">
                            <i class="bi bi-arrow-right-circle me-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                    <div class="mt-4 d-flex align-items-center" data-aos="fade-up" data-aos-delay="700">
                        <div class="d-flex align-items-center me-4">
                            <div class="ratings-icons text-warning me-2">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <span class="text-white">4.8/5 Rating</span>
                        </div>
                        <div class="verified-badge">
                            <i class="bi bi-patch-check-fill text-info me-1"></i>
                            <span class="text-white">Terverifikasi Olehku</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div id="lottieAnimation" class="floating"></div>
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stats-box">
                        <i class="bi bi-people fs-1 text-primary mb-3"></i>
                        <h3 class="display-6 fw-bold">10,000+</h3>
                        <p class="mb-0">Pengguna Aktif</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stats-box">
                        <i class="bi bi-star fs-1 text-warning mb-3"></i>
                        <h3 class="display-6 fw-bold">4.8/5</h3>
                        <p class="mb-0">Rating Pengguna</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="stats-box">
                        <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                        <h3 class="display-6 fw-bold">85%</h3>
                        <p class="mb-0">Target Tercapai</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Fitur Unggulan</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-egg-fried fs-1"></i>
                            </div>
                            <h3 class="h4 mb-3">Pelacakan Nutrisi</h3>
                            <p class="mb-0">Catat asupan makanan dan minuman harian dengan perhitungan kalori otomatis dan rekomendasi nutrisi personal.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-activity fs-1"></i>
                            </div>
                            <h3 class="h4 mb-3">Aktivitas Fisik</h3>
                            <p class="mb-0">Rekam jenis olahraga, durasi, dan intensitas. Dapatkan tantangan mingguan untuk meningkatkan motivasi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-moon fs-1"></i>
                            </div>
                            <h3 class="h4 mb-3">Monitor Tidur</h3>
                            <p class="mb-0">Pantau durasi dan kualitas tidur Anda. Dapatkan rekomendasi waktu tidur optimal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Pertanyaan Umum</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="100">
                            <h3 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Apakah WellBe gratis?
                                </button>
                            </h3>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, WellBe dapat digunakan secara gratis dengan fitur dasar. Kami juga menyediakan versi premium dengan fitur tambahan.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm mb-3" data-aos="fade-up" data-aos-delay="200">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Bagaimana cara memulai?
                                </button>
                            </h3>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Cukup daftar akun dan isi profil kesehatan Anda. Setelah itu, Anda bisa langsung mulai menggunakan semua fitur WellBe.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 shadow-sm" data-aos="fade-up" data-aos-delay="300">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Apakah data saya aman?
                                </button>
                            </h3>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, kami mengutamakan keamanan data pengguna. Semua data disimpan dengan enkripsi dan tidak akan dibagikan ke pihak ketiga.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h2 class="display-5 fw-bold mb-4">Mulai Perjalanan Sehat Anda</h2>
                    <p class="lead mb-4">Bergabunglah dengan ribuan pengguna lainnya yang telah merasakan manfaat WellBe</p>
                    <a href="/register" class="btn btn-light btn-lg px-5 btn-glow">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3">WellBe</h5>
                    <p class="mb-0">Wellness & Balance dalam Genggaman Anda</p>
                </div>
                <div class="col-lg-2">
                    <h5 class="mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-light text-decoration-none">Fitur</a></li>
                        <li><a href="#faq" class="text-light text-decoration-none">FAQ</a></li>
                        <li><a href="/about" class="text-light text-decoration-none">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h5 class="mb-3">Akun</h5>
                    <ul class="list-unstyled">
                        <li><a href="/login" class="text-light text-decoration-none">Masuk</a></li>
                        <li><a href="/register" class="text-light text-decoration-none">Daftar</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="mb-3">Hubungi Kami</h5>
                    <p class="mb-0">Email: info@wellbe.com</p>
                    <p class="mb-0">Telepon: (021) 1234-5678</p>
                    <div class="mt-3">
                        <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <small>&copy; 2023 WellBe. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>
                        <a href="#" class="text-light text-decoration-none me-3">Kebijakan Privasi</a>
                        <a href="#" class="text-light text-decoration-none">Syarat & Ketentuan</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        // Lottie Animation
        const animation = {
            container: document.getElementById('lottieAnimation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://assets2.lottiefiles.com/packages/lf20_twijbubv.json' // Animasi kesehatan sederhana
        };
        
        lottie.loadAnimation(animation);
    </script>
</body>
</html> 