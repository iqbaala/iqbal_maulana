<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use WellBe\Controllers\AuthController;
use WellBe\Controllers\ProfileController;
use WellBe\Controllers\NutritionController;
use WellBe\Controllers\ExerciseController;
use WellBe\Controllers\SleepController;
use WellBe\Controllers\DashboardController;
use WellBe\Controllers\StatisticsController;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$basePath = '/public';
$request = str_replace($basePath, '', $request);

// Simple router
switch ($request) {
    case '/':
    case '':
        // Redirect ke home
        header('Location: /home');
        exit;
        break;

    case '/home':
        // Tampilkan halaman home
        require __DIR__ . '/../src/views/layouts/landing.php';
        break;
    
    case '/register':
        // Jika user sudah login, redirect ke dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        require __DIR__ . '/../src/views/auth/register.php';
        break;
    
    case '/login':
        // Jika user sudah login, redirect ke dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        require __DIR__ . '/../src/views/auth/login.php';
        break;
    
    case '/auth/login':
        $auth = new AuthController();
        $auth->login();
        break;
    
    case '/auth/register':
        $auth = new AuthController();
        $auth->register();
        break;
    
    case '/auth/logout':
        $auth = new AuthController();
        $auth->logout();
        break;
    
    case '/dashboard':
        // Cek apakah user sudah login
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        $dashboard = new DashboardController();
        $dashboard->index();
        break;

    case '/nutrition':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        $nutrition = new NutritionController();
        $nutrition->index();
        break;

    case '/nutrition/add':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        $nutrition = new NutritionController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nutrition->store($_POST);
        } else {
            $nutrition->create();
        }
        break;

    case '/nutrition/update':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /nutrition');
            exit;
        }

        $nutrition = new NutritionController();
        $nutrition->update();
        break;

    case '/nutrition/data':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => true, 'message' => 'Unauthorized']);
            exit;
        }
        
        $nutrition = new NutritionController();
        $nutrition->getData();
        break;

    case '/nutrition/delete':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /nutrition');
            exit;
        }

        $nutrition = new NutritionController();
        $nutrition->delete();
        break;

    case '/exercise':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        $exercise = new ExerciseController();
        $exercise->index();
        break;

    case '/exercise/store':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /exercise');
            exit;
        }

        $exercise = new ExerciseController();
        $exercise->store();
        break;

    case '/exercise/update':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /exercise');
            exit;
        }

        $exercise = new ExerciseController();
        $exercise->update();
        break;

    case '/exercise/delete':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /exercise');
            exit;
        }

        $exercise = new ExerciseController();
        $exercise->delete();
        break;

    case '/sleep':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        $sleep = new SleepController();
        $sleep->index();
        break;

    case '/sleep/store':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /sleep');
            exit;
        }

        $sleep = new SleepController();
        $sleep->store();
        break;

    case '/sleep/update':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /sleep');
            exit;
        }

        $sleep = new SleepController();
        $sleep->update();
        break;

    case '/sleep/delete':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode tidak diizinkan';
            header('Location: /sleep');
            exit;
        }

        $sleep = new SleepController();
        $sleep->delete();
        break;

    case '/statistics':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../src/views/statistics/index.php';
        break;

    case '/challenges':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../src/views/challenges/index.php';
        break;

    case '/tips':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../src/views/tips/index.php';
        break;

    case '/profile':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        require __DIR__ . '/../src/views/profile/index.php';
        break;

    case '/profile/update':
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
        $profile = new \WellBe\Controllers\ProfileController();
        $profile->update();
        break;

    case '/profile/update-avatar':
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
            exit;
        }

        $profile = new ProfileController();
        $profile->updateAvatar();
        break;

    case '/about':
        require __DIR__ . '/../src/views/about/index.php';
        break;

    case '/statistics/data':
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $statistics = new StatisticsController();
        $statistics->getData();
        break;

    case '/exercise/data':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => true, 'message' => 'Unauthorized']);
            exit;
        }
        
        $exercise = new ExerciseController();
        $exercise->getData();
        break;

    case '/sleep/data':
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => true, 'message' => 'Unauthorized']);
            exit;
        }
        
        $sleep = new SleepController();
        $sleep->getData();
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../src/views/layouts/404.php';
        break;
} 