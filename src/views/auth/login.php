<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- Background Animation -->
<div class="fixed inset-0 -z-10">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-600/20"></div>
    <div class="absolute inset-0" id="particles-js"></div>
</div>

<div class="min-h-screen flex items-center justify-center p-4 relative">
    <!-- Animated Shapes -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
        <div class="bubble bubble-4"></div>
    </div>

    <div class="bg-white/90 backdrop-blur-lg w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform hover:scale-[1.02] transition-all duration-300">
        <div class="relative">
            <div class="h-48 flex justify-center items-center bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-t-2xl">
                <div id="lottie-container" class="w-48 h-48 transform hover:scale-110 transition-transform duration-300"></div>
            </div>
            
            <div class="px-8 pt-6 pb-8">
                <h2 class="text-3xl font-bold text-center mb-2 text-gray-800 transform hover:scale-105 transition-transform">
                    Selamat Datang Kembali!
                </h2>
                <p class="text-center text-gray-600 mb-8">Senang bertemu dengan Anda lagi</p>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-shake" role="alert">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/auth/login" class="space-y-6">
                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" required
                                   class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 outline-none text-gray-600 text-lg group-hover:border-blue-300"
                                   placeholder="Masukkan email Anda">
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" required
                                   class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 outline-none text-gray-600 text-lg group-hover:border-blue-300"
                                   placeholder="Masukkan password">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center text-gray-600 hover:text-gray-700 cursor-pointer group">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition-colors group-hover:border-blue-400">
                            <span class="ml-2">Ingat saya</span>
                        </label>
                        <a href="/forgot-password" class="text-blue-600 hover:text-blue-700 hover:underline transition-colors">
                            Lupa password?
                        </a>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-lg font-semibold hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transform hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
                        <span class="absolute w-0 h-0 transition-all duration-300 ease-out bg-white rounded-full group-hover:w-full group-hover:h-full opacity-10"></span>
                        <span class="relative flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk
                        </span>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Belum punya akun? 
                        <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline transition-colors">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Particles.js -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<!-- Lottie Animation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

<script>
    // Particles.js Config
    particlesJS("particles-js", {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: "#4F46E5" },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: false },
            size: { value: 3, random: true },
            line_linked: {
                enable: true,
                distance: 150,
                color: "#4F46E5",
                opacity: 0.4,
                width: 1
            },
            move: {
                enable: true,
                speed: 2,
                direction: "none",
                random: false,
                straight: false,
                out_mode: "out",
                bounce: false
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: { enable: true, mode: "repulse" },
                onclick: { enable: true, mode: "push" },
                resize: true
            }
        },
        retina_detect: true
    });

    // Animasi Lottie yang lebih menarik
    const animation = lottie.loadAnimation({
        container: document.getElementById('lottie-container'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'https://assets8.lottiefiles.com/packages/lf20_jcikwtux.json' // Animasi login yang lebih modern
    });

    // Toggle Password Visibility
    function togglePassword(button) {
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Animasi untuk form input
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.group').classList.add('scale-[1.02]');
        });
        input.addEventListener('blur', function() {
            this.closest('.group').classList.remove('scale-[1.02]');
        });
    });
</script>

<style>
    @keyframes float {
        0% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }

    .bubble {
        position: absolute;
        background: linear-gradient(45deg, rgba(79, 70, 229, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .bubble-1 {
        width: 100px;
        height: 100px;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }

    .bubble-2 {
        width: 150px;
        height: 150px;
        top: 40%;
        right: 15%;
        animation-delay: -2s;
    }

    .bubble-3 {
        width: 80px;
        height: 80px;
        bottom: 20%;
        left: 20%;
        animation-delay: -4s;
    }

    .bubble-4 {
        width: 120px;
        height: 120px;
        bottom: 30%;
        right: 25%;
        animation-delay: -6s;
    }

    .group {
        transition: all 0.3s ease;
    }

    .animate-shake {
        animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
    }

    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?> 