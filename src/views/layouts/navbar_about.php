<nav class="navbar navbar-about navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
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
                    <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/about' ? 'active' : ''; ?>" href="/about">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/login' ? 'active' : ''; ?>" href="/login">Masuk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white px-4 <?php echo $_SERVER['REQUEST_URI'] === '/register' ? 'active' : ''; ?>" href="/register">Daftar</a>
                </li>
            </ul>
        </div>
    </div>
</nav> 