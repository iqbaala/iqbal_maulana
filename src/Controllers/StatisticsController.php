<?php

namespace App\Controllers;

use MongoDB\Client;
use App\Database;

class StatisticsController
{
    private $mongoClient;

    public function __construct() {
        // Memastikan session sudah dimulai
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Cek apakah user sudah login
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        try {
            // Ubah ini karena kita menggunakan MongoDB
            $db = Database::getInstance();
            $this->mongoClient = new Client('mongodb://localhost:27017');
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
            exit;
        }
    }

    // Tambahkan method baru untuk API
    public function getNutritionTrends() {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'];
            $stats = $this->getNutritionStats($userId);
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}