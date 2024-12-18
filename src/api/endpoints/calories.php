<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use WellBe\Api\CalorieCalculator;

header('Content-Type: application/json');

// Pastikan request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Baca data JSON dari request body
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$calculator = new CalorieCalculator();

// Route berdasarkan action yang diminta
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'daily':
            // Hitung kebutuhan kalori harian
            if (!isset($data['weight'], $data['height'], $data['age'], $data['gender'], $data['activity_level'])) {
                throw new Exception('Missing required parameters');
            }

            $calories = $calculator->calculateDailyCalories(
                $data['weight'],
                $data['height'],
                $data['age'],
                $data['gender'],
                $data['activity_level']
            );

            $macros = $calculator->calculateMacronutrients($calories);
            $bmi = $calculator->calculateBMI($data['weight'], $data['height']);

            echo json_encode([
                'daily_calories' => $calories,
                'macronutrients' => $macros,
                'bmi' => $bmi
            ]);
            break;

        case 'exercise':
            // Hitung kalori yang terbakar saat olahraga
            if (!isset($data['weight'], $data['duration'], $data['exercise_type'])) {
                throw new Exception('Missing required parameters');
            }

            $calories = $calculator->calculateExerciseCalories(
                $data['weight'],
                $data['duration'],
                $data['exercise_type']
            );

            echo json_encode([
                'calories_burned' => $calories
            ]);
            break;

        case 'target':
            // Hitung target kalori berdasarkan tujuan
            if (!isset($data['daily_calories'], $data['weight_goal'])) {
                throw new Exception('Missing required parameters');
            }

            $targetCalories = $calculator->calculateTargetCalories(
                $data['daily_calories'],
                $data['weight_goal']
            );

            $macros = $calculator->calculateMacronutrients($targetCalories);

            echo json_encode([
                'target_calories' => $targetCalories,
                'macronutrients' => $macros
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} 