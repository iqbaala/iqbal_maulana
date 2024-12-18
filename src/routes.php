<?php
require_once __DIR__ . '/config/database.php';

class Router {
    private $routes = [];

    public function addRoute($path, $handler) {
        $this->routes[$path] = $handler;
    }

    public function handleRequest($uri) {
        // Parse query string
        $parsedUrl = parse_url($uri);
        $path = $parsedUrl['path'] ?? '/';
        
        // Remove trailing slash if exists
        $path = rtrim($path, '/');

        // Debug information
        error_log("Requested Path: " . $path);
        error_log("Available Routes: " . print_r($this->routes, true));

        if (array_key_exists($path, $this->routes)) {
            $handler = $this->routes[$path];
            
            // Check if handler contains @ for method specification
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                require_once __DIR__ . "/controllers/{$controller}.php";
                $controllerInstance = new $controller();
                $controllerInstance->$method();
            } else {
                require_once __DIR__ . "/controllers/{$handler}.php";
                $controllerInstance = new $handler();
                $controllerInstance->index();
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found: ' . $path]);
        }
    }
}

$router = new Router();

// Auth routes
$router->addRoute('/', 'AuthController');
$router->addRoute('/login', 'AuthController@showLogin');
$router->addRoute('/register', 'AuthController@showRegister');
$router->addRoute('/auth/login', 'AuthController@login');
$router->addRoute('/auth/register', 'AuthController@register');
$router->addRoute('/logout', 'AuthController@logout');

// Dashboard routes
$router->addRoute('/dashboard', 'DashboardController');

// Profile routes
$router->addRoute('/profile', 'ProfileController');
$router->addRoute('/profile/update', 'ProfileController@update');

// Nutrition routes
$router->addRoute('/nutrition', 'NutritionController');
$router->addRoute('/nutrition/add', 'NutritionController@add');
$router->addRoute('/nutrition/edit', 'NutritionController@edit');
$router->addRoute('/nutrition/delete', 'NutritionController@delete');
$router->addRoute('/nutrition/data', 'NutritionController@getData');

// Exercise routes
$router->addRoute('/exercise', 'ExerciseController');
$router->addRoute('/exercise/add', 'ExerciseController@add');
$router->addRoute('/exercise/edit', 'ExerciseController@edit');
$router->addRoute('/exercise/delete', 'ExerciseController@delete');

// Sleep routes
$router->addRoute('/sleep', 'SleepController');
$router->addRoute('/sleep/add', 'SleepController@add');
$router->addRoute('/sleep/edit', 'SleepController@edit');
$router->addRoute('/sleep/delete', 'SleepController@delete');

// Statistics routes
$router->addRoute('/statistics', 'StatisticsController');
$router->addRoute('/statistics/period', 'StatisticsController@getPeriodStats');

// Handle the request
$router->handleRequest($_SERVER['REQUEST_URI']);
 