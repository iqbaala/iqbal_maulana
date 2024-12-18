<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Syarat dan Ketentuan</h1>
            <p class="text-lg text-gray-600">Terakhir diperbarui: <?php echo date('d F Y'); ?></p>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 space-y-8">
            <!-- Introduction -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Pendahuluan</h2>
                <p class="text-gray-600 leading-relaxed">
                    Selamat datang di WellBe. Dengan menggunakan layanan kami, Anda menyetujui syarat dan ketentuan ini. 
                    Harap baca dengan seksama sebelum menggunakan aplikasi kami.
                </p>
            </section>

            <!-- Account Terms -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Ketentuan Akun</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <i class="fas fa-user text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Pendaftaran Akun</h3>
                            <p class="text-gray-600">Anda harus berusia minimal 17 tahun dan memberikan informasi yang akurat saat mendaftar.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-lock text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Keamanan Akun</h3>
                            <p class="text-gray-600">Anda bertanggung jawab untuk menjaga kerahasiaan akun dan password Anda.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Service Usage -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Penggunaan Layanan</h2>
                <div class="bg-blue-50 rounded-xl p-6 space-y-4">
                    <p class="text-gray-600">Anda setuju untuk:</p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Tidak menyalahgunakan layanan untuk tujuan ilegal</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Tidak mengganggu atau merusak layanan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Tidak membagikan konten yang melanggar hukum</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-500 mt-1 mr-3"></i>
                            <span>Menghormati hak privasi pengguna lain</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Content Guidelines -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Pedoman Konten</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-green-50 rounded-xl p-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Konten yang Diizinkan</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Informasi kesehatan umum</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Tips gaya hidup sehat</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span>Pengalaman pribadi</span>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-red-50 rounded-xl p-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-3">Konten yang Dilarang</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-2"></i>
                                <span>Konten ilegal</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-2"></i>
                                <span>Informasi menyesatkan</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-times text-red-500 mr-2"></i>
                                <span>Konten berbahaya</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Limitation of Liability -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Batasan Tanggung Jawab</h2>
                <div class="bg-yellow-50 rounded-xl p-6">
                    <p class="text-gray-600 leading-relaxed">
                        WellBe tidak bertanggung jawab atas:
                    </p>
                    <ul class="mt-4 space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                            <span>Kerugian yang timbul dari penggunaan layanan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                            <span>Keakuratan informasi dari pengguna lain</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                            <span>Gangguan teknis atau keamanan</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Changes to Terms -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Perubahan Ketentuan</h2>
                <div class="bg-purple-50 rounded-xl p-6">
                    <p class="text-gray-600 leading-relaxed">
                        WellBe berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan diumumkan melalui:
                    </p>
                    <ul class="mt-4 space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-bell text-purple-500 mr-3"></i>
                            <span>Notifikasi dalam aplikasi</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-purple-500 mr-3"></i>
                            <span>Email ke pengguna terdaftar</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-globe text-purple-500 mr-3"></i>
                            <span>Pembaruan di website kami</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Contact -->
            <section>
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Hubungi Kami</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Jika Anda memiliki pertanyaan tentang syarat dan ketentuan ini, silakan hubungi kami melalui:
                </p>
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-envelope text-gray-500"></i>
                        <span class="text-gray-600">terms@wellbe.com</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?> 