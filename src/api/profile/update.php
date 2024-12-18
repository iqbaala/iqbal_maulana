<?php
// Start output buffering
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../Config/Database.php';
require_once __DIR__ . '/../../Models/User.php';

use WellBe\Models\User;

session_start();

function sendJsonResponse($success, $message, $data = null) {
    // Clear any previous output
    if (ob_get_length()) ob_clean();
    
    // Set header untuk JSON response
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Debug: Log request data
error_log('POST data received: ' . print_r($_POST, true));
error_log('Session data: ' . print_r($_SESSION, true));

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(false, 'Unauthorized');
}

try {
    if (empty($_POST)) {
        throw new \Exception('Data form tidak diterima');
    }

    if (empty($_POST['name']) || empty($_POST['email'])) {
        throw new \Exception('Nama dan email harus diisi');
    }

    $user = new User();
    
    // Data yang akan diupdate
    $updateData = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email'])
    ];

    // Optional fields
    if (!empty($_POST['birth_date'])) {
        $updateData['birth_date'] = new \MongoDB\BSON\UTCDateTime(strtotime($_POST['birth_date']) * 1000);
    }
    if (!empty($_POST['gender'])) {
        $updateData['gender'] = $_POST['gender'];
    }
    if (!empty($_POST['height'])) {
        $updateData['height'] = (int)$_POST['height'];
    }
    if (!empty($_POST['weight'])) {
        $updateData['weight'] = (int)$_POST['weight'];
    }
    if (!empty($_POST['health_target'])) {
        $updateData['health_target'] = trim($_POST['health_target']);
    }

    // Debug: Log data yang akan diupdate
    error_log('Data to update: ' . print_r($updateData, true));

    // Update user data
    $success = $user->update($_SESSION['user_id'], $updateData);

    if ($success) {
        // Update session data
        $_SESSION['user_name'] = $updateData['name'];
        $_SESSION['user_email'] = $updateData['email'];
        
        // Get updated user data
        $userData = $user->getUserData($_SESSION['user_id']);
        
        sendJsonResponse(true, 'Profil berhasil diperbarui', $userData);
    } else {
        throw new \Exception('Gagal memperbarui profil di database');
    }

} catch (\Exception $e) {
    error_log('Error in update.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    sendJsonResponse(false, $e->getMessage());
} 