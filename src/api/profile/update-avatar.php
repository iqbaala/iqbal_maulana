<?php
// Aktifkan output buffering di awal
ob_start();

// Matikan semua error reporting untuk production
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Models/User.php';

use WellBe\Models\User;

session_start();

// Fungsi untuk mengirim response JSON
function sendJsonResponse($success, $message, $data = null, $statusCode = 200) {
    // Bersihkan output buffer
    ob_clean();
    
    // Set header
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    
    // Siapkan response
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    // Kirim response
    echo json_encode($response);
    
    // Flush dan hentikan output buffering
    ob_end_flush();
    exit;
}

// Log untuk debugging
error_log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
error_log('Content-Type: ' . $_SERVER['CONTENT_TYPE']);

try {
    // Cek login
    if (!isset($_SESSION['user_id'])) {
        sendJsonResponse(false, 'Unauthorized', null, 401);
    }

    // Cek method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Method not allowed', null, 405);
    }

    // Baca raw input
    $rawData = file_get_contents('php://input');
    error_log('Raw input: ' . $rawData);

    // Parse JSON
    $data = json_decode($rawData, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(false, 'Invalid JSON: ' . json_last_error_msg(), null, 400);
    }

    // Validasi data
    if (!isset($data['avatar']) || empty($data['avatar'])) {
        sendJsonResponse(false, 'Avatar URL is required', null, 400);
    }

    // Update avatar
    $user = new User();
    $success = $user->updateAvatar($_SESSION['user_id'], $data['avatar']);

    if ($success) {
        sendJsonResponse(true, 'Avatar berhasil diperbarui', [
            'avatar_url' => $data['avatar']
        ]);
    } else {
        sendJsonResponse(false, 'Gagal memperbarui avatar', null, 500);
    }

} catch (\Exception $e) {
    error_log('Error in update-avatar.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    sendJsonResponse(false, 'Server error: ' . $e->getMessage(), null, 500);
}