<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireLogin();

if ($auth->isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

require_once '../models/Computer.php';
$computer = new Computer();
$total_pc = $computer->countComputers();
$available = $computer->countByStatus('AVAILABLE');
$playing = $computer->countByStatus('PLAYING');
$maintenance = $computer->countByStatus('MAINTENANCE');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer - WarnetBray</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #0a0e1a;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: rgba(10, 14, 26, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .navbar-brand {
            color: #00d4ff;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .navbar-brand:hover {
            color: #00d4ff;
        }
        .nav-link {
            color: #8892b0 !important;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #00d4ff !important;
        }
        .nav-link.active {
            color: #00d4ff !important;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .dashboard-card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .dashboard-card .number {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .dashboard-card .label {
            color: #8892b0;
            margin-top: 5px;
        }
        .welcome-text {
            color: #ccd6f6;
            margin-bottom: 30px;
        }
        .welcome-text h2 {
            color: #fff;
            font-weight: 700;
        }
        .welcome-text h2 span {
            color: #00d4ff;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #7b2ffc);
            border: none;
            padding: 10px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }
        .btn-outline-primary {
            color: #00d4ff;
            border-color: #00d4ff;
        }
        .btn-outline-primary:hover {
            background: #00d4ff;
            color: #fff;
        }
        .quick-actions {
            margin-top: 30px;
        }
        .quick-actions .btn {
            margin: 5px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-available {
            background: rgba(0, 255, 0, 0.2);
            color: #51cf66;
        }
        .status-playing {
            background: rgba(255, 193, 7, 0.2);
            color: #ffd43b;
        }
        .status-maintenance {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
        }
        .status-waiting {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa94d;
        }
        .status-paid {
            background: rgba(0, 123, 255, 0.2);
            color: #4dabf7;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-gamepad"></i> WarnetBray
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="computers.php">
                            <i class="fas fa-desktop"></i> Komputer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">
                            <i class="fas fa-history"></i> Riwayat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="welcome-text">
            <h2>Selamat datang, <span><?= htmlspecialchars($_SESSION['nama']) ?></span>! 👋</h2>
            <p class="text-muted">Siap untuk bermain game? Pilih komputer favoritmu sekarang!</p>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="icon text-info">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div class="number text-white"><?= $total_pc ?></div>
                    <div class="label">Total Komputer</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="number text-white"><?= $available ?></div>
                    <div class="label">Tersedia</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="icon text-warning">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <div class="number text-white"><?= $playing ?></div>
                    <div class="label">Sedang Digunakan</div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="dashboard-card">
                    <div class="icon text-danger">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="number text-white"><?= $maintenance ?></div>
                    <div class="label">Maintenance</div>
                </div>
            </div>
        </div>

        <div class="quick-actions text-center">
            <h4 class="text-white">Mulai Bermain</h4>
            <p class="text-muted">Pilih komputer dan mulai sesi gamingmu!</p>
            <a href="computers.php" class="btn btn-primary btn-lg">
                <i class="fas fa-desktop"></i> Lihat Daftar Komputer
            </a>
            <a href="history.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-history"></i> Riwayat Transaksi
            </a>
        </div>

        <hr class="border-secondary">
        <div class="text-center text-muted small">
            <p><i class="fas fa-info-circle"></i> Sistem pembayaran di awal. Pastikan melakukan pembayaran setelah booking.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>