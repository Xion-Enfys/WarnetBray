<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../models/User.php';

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'register') {
            $nama = $_POST['nama'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Validasi
            if (empty($nama) || empty($username) || empty($password)) {
                $_SESSION['error'] = 'Semua field harus diisi';
                header('Location: ../register.php');
                return;
            }

            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Password tidak cocok';
                header('Location: ../register.php');
                return;
            }

            if ($this->user->checkUsername($username)) {
                $_SESSION['error'] = 'Username sudah digunakan';
                header('Location: ../register.php');
                return;
            }

            if ($this->user->register($nama, $username, $password)) {
                $_SESSION['success'] = 'Registrasi berhasil, silahkan login';
                header('Location: ../login.php');
            } else {
                $_SESSION['error'] = 'Registrasi gagal';
                header('Location: ../register.php');
            }
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'login') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Username dan password harus diisi';
                header('Location: ../login.php');
                return;
            }

            $user = $this->user->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama'] = $user['nama'];

                if ($user['role'] == 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: ../customer/dashboard.php');
                }
            } else {
                $_SESSION['error'] = 'Username atau password salah';
                header('Location: ../login.php');
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: ../login.php');
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }

    public function isCustomer()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'customer';
    }

    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ../login.php');
            exit();
        }
    }

    public function requireAdmin()
    {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: ../customer/dashboard.php');
            exit();
        }
    }
}

// Instantiate and handle request actions
$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'login') {
        $authController->login();
    } elseif ($action == 'register') {
        $authController->register();
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action == 'logout') {
        $authController->logout();
    }
}
?>