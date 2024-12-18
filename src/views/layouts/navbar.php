<?php
$current_page = $_SERVER['REQUEST_URI'];
$navbar_class = 'navbar';

if (strpos($current_page, '/dashboard') !== false) {
    $navbar_class .= ' navbar-dashboard';
} elseif (strpos($current_page, '/nutrition') !== false) {
    $navbar_class .= ' navbar-nutrition';
} elseif (strpos($current_page, '/exercise') !== false) {
    $navbar_class .= ' navbar-exercise';
} elseif (strpos($current_page, '/sleep') !== false) {
    $navbar_class .= ' navbar-sleep';
} elseif (strpos($current_page, '/tips') !== false) {
    $navbar_class .= ' navbar-tips';
} else {
    $navbar_class .= ' navbar-home';
}
?>
<nav class="<?php echo $navbar_class; ?> navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/home">
            <i class="bi bi-heart-pulse text-primary"></i> WellBe
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : ''; ?>" 
                       href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/nutrition') !== false ? 'active' : ''; ?>" 
                       href="/nutrition"><i class="bi bi-egg-fried"></i> Nutrisi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/exercise') !== false ? 'active' : ''; ?>" 
                       href="/exercise"><i class="bi bi-activity"></i> Aktivitas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/sleep') !== false ? 'active' : ''; ?>" 
                       href="/sleep"><i class="bi bi-moon"></i> Tidur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/statistics') !== false ? 'active' : ''; ?>" 
                       href="/statistics"><i class="bi bi-graph-up"></i> Statistik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/challenges') !== false ? 'active' : ''; ?>" 
                       href="/challenges"><i class="bi bi-trophy"></i> Tantangan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/tips') !== false ? 'active' : ''; ?>" 
                       href="/tips"><i class="bi bi-lightbulb"></i> Tips</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/profile"><i class="bi bi-gear"></i> Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/auth/logout"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav> 