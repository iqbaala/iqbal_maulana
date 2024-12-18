<?php
// Cek status session sebelum memulai session baru
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - WellBe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #224abe;
        }

        .hero-about {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 120px 0;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=2070') center/cover;
            opacity: 0.1;
        }

        .hero-about::after {
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
            width: 80px;
            height: 80px;
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

        .value-card {
            padding: 2rem;
            text-align: center;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-10px);
        }

        .value-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(var(--bs-primary-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .stats-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .stat-card {
            padding: 2rem;
            text-align: center;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            height: 100%;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(var(--bs-primary-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .team-section {
            padding: 80px 0;
            background: white;
        }

        .team-member {
            text-align: center;
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-10px);
        }

        .member-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            overflow: hidden;
            border: 5px solid var(--primary-color);
        }

        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            margin: 0 5px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            transform: translateY(-3px);
            background: var(--secondary-color);
        }

        .contact-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .contact-info {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            height: 100%;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
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

        .text-gradient {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../src/views/layouts/navbar_about.php'; ?>

    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <h1 class="display-4 fw-bold mb-4">Tentang WellBe</h1>
                    <p class="lead mb-4">Menjadi pendamping setia dalam perjalanan menuju gaya hidup sehat dan seimbang untuk setiap individu.</p>
                    <div class="mt-5">
                        <div class="row g-4">
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                                <div class="feature-icon">
                                    <i class="bi bi-activity fs-3"></i>
                                </div>
                                <h4>Monitoring Kesehatan</h4>
                                <p class="text-white-50">Pantau aktivitas fisik, nutrisi, dan kualitas tidur Anda</p>
                            </div>
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                                <div class="feature-icon">
                                    <i class="bi bi-trophy fs-3"></i>
                                </div>
                                <h4>Tantangan Sehat</h4>
                                <p class="text-white-50">Ikuti tantangan kesehatan untuk hidup lebih sehat</p>
                            </div>
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                                <div class="feature-icon">
                                    <i class="bi bi-graph-up fs-3"></i>
                                </div>
                                <h4>Analisis Data</h4>
                                <p class="text-white-50">Lihat perkembangan kesehatan Anda secara detail</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Nilai-Nilai Kami</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-heart"></i>
                        </div>
                        <h3 class="h4 mb-3">Kepedulian</h3>
                        <p class="text-muted">Kami menempatkan kesehatan dan kesejahteraan pengguna sebagai prioritas utama dalam setiap keputusan.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="h4 mb-3">Integritas</h3>
                        <p class="text-muted">Kami berkomitmen untuk menjaga kepercayaan pengguna dengan transparansi dan keamanan data.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="bi bi-lightning"></i>
                        </div>
                        <h3 class="h4 mb-3">Inovasi</h3>
                        <p class="text-muted">Kami terus berinovasi untuk memberikan solusi terbaik dalam mencapai gaya hidup sehat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">Tim Pengembang</h2>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <div class="member-photo">
                            <img src="https://assets.onecompiler.app/42zydsevf/433bumvpr/WhatsApp%20Image%202024-12-18%20at%20.jpg" alt="Developer Photo">
                        </div>
                        <h4>Muhammad Iqbal Maulana</h4>
                        <p class="text-muted mb-2">InsyaAllah Full Stack Developer</p>
                        <p class="mb-3"></p>
                        <p class="mb-4">Mahasiswa Teknik Informatika Universitas Islam Nahdlatul Ulama Jepara</p>
                        <div class="social-links">
                            <a href="#" target="_blank"><i class="bi bi-github"></i></a>
                            <a href="#" target="_blank"><i class="bi bi-facebook"></i></a>
                            <a href="#" target="_blank"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-5">
                    <h2 class="mb-4" data-aos="fade-up">Hubungi Kami</h2>
                    <p class="lead" data-aos="fade-up" data-aos-delay="100">Ada pertanyaan atau saran? Jangan ragu untuk menghubungi kami.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-info h-100">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Email</h5>
                                <p class="mb-0">miqbalm511@gmail.com</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Lokasi</h5>
                                <p class="mb-0">Universitas Islam Nahdlatul Ulama<br>Jl. Taman Siswa , Jepara</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Jam Operasional</h5>
                                <p class="mb-0">Senin - Jumat: 08.00 - 16.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-info">
                        <form action="mailto:miqbalm511@gmail.com" method="POST" enctype="text/plain">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subjek</label>
                                <input type="text" name="subject" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pesan</label>
                                <textarea name="message" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-glow">Kirim Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>
</html> 