<?php
namespace WellBe\Controllers;

use WellBe\Models\User;

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function showLogin() {
        // Cek jika user sudah login
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function showRegister() {
        // Cek jika user sudah login
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function register() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /register');
                exit;
            }

            // Validasi input
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $birth_date = filter_input(INPUT_POST, 'birth_date', FILTER_SANITIZE_STRING);
            $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);

            // Validasi data
            if (!$name || !$email || !$password || !$password_confirm || !$birth_date || !$gender) {
                throw new \Exception('Semua field harus diisi');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Format email tidak valid');
            }

            if (strlen($password) < 6) {
                throw new \Exception('Password minimal 6 karakter');
            }

            if ($password !== $password_confirm) {
                throw new \Exception('Password tidak cocok');
            }

            // Buat user baru
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'birth_date' => $birth_date,
                'gender' => $gender,
                'role' => 'user',
                'status' => 'active'
            ];

            $result = $this->user->create($userData);
            
            if ($result) {
                $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
                header('Location: /login');
                exit;
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    public function login() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /login');
                exit;
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || !$password) {
                throw new \Exception('Email dan password harus diisi');
            }

            $user = $this->user->authenticate($email, $password);
            
            if ($user) {
                // Set session
                $_SESSION['user_id'] = (string) $user['_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                header('Location: /dashboard');
                exit;
            }

            throw new \Exception('Email atau password salah');

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /login');
            exit;
        }
    }

    public function logout() {
        try {
            // Hapus semua data session
            session_unset();
            session_destroy();
            
            // Redirect ke login
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
    }

    public function resetPassword() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Method not allowed');
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            
            if (!$email) {
                throw new \Exception('Email harus diisi');
            }

            $user = $this->user->findByEmail($email);
            
            if (!$user) {
                throw new \Exception('Email tidak ditemukan');
            }

            // TODO: Implement password reset logic
            // 1. Generate reset token
            // 2. Save token to database
            // 3. Send reset email
            
            $_SESSION['success'] = 'Instruksi reset password telah dikirim ke email Anda';
            header('Location: /login');
            exit;

        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /forgot-password');
            exit;
        }
    }
}
