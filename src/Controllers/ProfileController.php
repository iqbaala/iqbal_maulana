<?php

namespace WellBe\Controllers;

use WellBe\Models\User;

class ProfileController {
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new User();
    }

    public function update() {
        try {
            // Set header untuk JSON response
            header('Content-Type: application/json; charset=utf-8');

            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
                $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new \Exception('Token keamanan tidak valid');
            }

            // Validasi input dasar
            if (empty($_POST['name']) || empty($_POST['email'])) {
                throw new \Exception('Nama dan email harus diisi');
            }

            // Validasi email
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Format email tidak valid');
            }

            // Buat array data yang akan diupdate
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email'])
            ];

            // Tambahkan field opsional dengan validasi
            if (isset($_POST['birth_date']) && !empty($_POST['birth_date'])) {
                // Validasi format tanggal
                $date = \DateTime::createFromFormat('Y-m-d', $_POST['birth_date']);
                if ($date && $date->format('Y-m-d') === $_POST['birth_date']) {
                    $data['birth_date'] = $_POST['birth_date'];
                }
            }

            if (isset($_POST['gender']) && in_array($_POST['gender'], ['L', 'P'])) {
                $data['gender'] = $_POST['gender'];
            }

            if (isset($_POST['height']) && is_numeric($_POST['height'])) {
                $height = (float) $_POST['height'];
                if ($height > 0 && $height < 300) { // Validasi tinggi badan masuk akal
                    $data['height'] = $height;
                }
            }

            if (isset($_POST['weight']) && is_numeric($_POST['weight'])) {
                $weight = (float) $_POST['weight'];
                if ($weight > 0 && $weight < 500) { // Validasi berat badan masuk akal
                    $data['weight'] = $weight;
                }
            }

            if (isset($_POST['health_target']) && !empty($_POST['health_target'])) {
                $data['health_target'] = trim($_POST['health_target']);
            }

            // Debug log
            error_log('Mencoba update profil untuk user ID: ' . $_SESSION['user_id']);
            error_log('Data yang akan diupdate: ' . print_r($data, true));

            // Update data user
            if ($this->userModel->update($_SESSION['user_id'], $data)) {
                $_SESSION['user_name'] = $data['name']; // Update nama di session
                
                // Get updated user data
                $userData = $this->userModel->getUserData($_SESSION['user_id']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui',
                    'data' => $userData
                ]);
            } else {
                throw new \Exception('Gagal memperbarui profil');
            }
        } catch (\Exception $e) {
            error_log('Error saat update profil: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateAvatar() {
        try {
            // Pastikan request adalah POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Metode tidak diizinkan');
            }

            // Pastikan user sudah login
            if (!isset($_SESSION['user_id'])) {
                throw new \Exception('Silakan login terlebih dahulu');
            }

            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
                $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                throw new \Exception('Token keamanan tidak valid');
            }

            $avatarType = $_POST['avatar_type'] ?? 'default';
            $avatarUrl = '';

            if ($avatarType === 'upload' && isset($_FILES['avatar_file'])) {
                $file = $_FILES['avatar_file'];
                
                // Validasi file
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('Error saat upload file');
                }

                // Validasi ukuran file (max 2MB)
                if ($file['size'] > 2 * 1024 * 1024) {
                    throw new \Exception('Ukuran file terlalu besar (maksimal 2MB)');
                }

                // Validasi tipe file
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedTypes)) {
                    throw new \Exception('Tipe file tidak didukung');
                }

                // Generate nama file unik
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('avatar_') . '.' . $extension;
                
                // Buat direktori jika belum ada
                $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Pindahkan file
                $uploadPath = $uploadDir . $filename;
                if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    throw new \Exception('Gagal menyimpan file');
                }

                // Set URL avatar
                $avatarUrl = '/uploads/avatars/' . $filename;

                // Hapus avatar lama jika ada
                if (isset($userData['avatar']) && strpos($userData['avatar'], '/uploads/avatars/') === 0) {
                    $oldAvatarPath = __DIR__ . '/../../public' . $userData['avatar'];
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }
            } else {
                // Gunakan URL avatar default
                $avatarUrl = $_POST['avatar_url'] ?? '';
                
                // Validasi URL avatar default
                if (!preg_match('/^https:\/\/assets\.onecompiler\.app\//', $avatarUrl)) {
                    throw new \Exception('URL avatar tidak valid');
                }
            }

            // Update avatar di database
            $result = $this->userModel->update($_SESSION['user_id'], ['avatar' => $avatarUrl]);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Avatar berhasil diperbarui',
                    'data' => ['avatar_url' => $avatarUrl]
                ]);
            } else {
                throw new \Exception('Gagal memperbarui avatar di database');
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
} 