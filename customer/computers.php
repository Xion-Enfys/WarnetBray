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
$computers = $computer->getAllComputers();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Komputer - WarnetBray</title>
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

        .pc-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .pc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .pc-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #00d4ff;
        }

        .pc-specs {
            color: #8892b0;
            margin: 10px 0;
            font-size: 0.9rem;
        }

        .pc-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #ffd43b;
        }

        .pc-price span {
            font-size: 0.9rem;
            color: #8892b0;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
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

        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #7b2ffc);
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #8892b0;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }

        .pc-icon {
            font-size: 3rem;
            color: #00d4ff;
            text-align: center;
            margin-bottom: 10px;
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="computers.php">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white"><i class="fas fa-desktop"></i> Daftar Komputer</h2>
                <p class="text-muted">Pilih komputer yang tersedia untuk bermain</p>
            </div>
            <div>
                <span class="badge bg-success">Tersedia</span>
                <span class="badge bg-warning">Digunakan</span>
                <span class="badge bg-danger">Maintenance</span>
                <span class="badge bg-warning text-dark">Menunggu</span>
                <span class="badge bg-primary">Telah Dibayar</span>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (count($computers) > 0): ?>
                <?php foreach ($computers as $pc): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="pc-card">
                            <div class="pc-icon">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <div class="pc-name"><?= htmlspecialchars($pc['nama_pc']) ?></div>
                            <div class="pc-specs">
                                <i class="fas fa-microchip"></i> <?= htmlspecialchars($pc['spesifikasi']) ?>
                            </div>
                            <div class="pc-price">
                                Rp <?= number_format($pc['harga_perjam'], 0, ',', '.') ?> <span>/jam</span>
                            </div>
                            <div class="mt-2 mb-3">
                                <?php
                                $statusClass = '';
                                switch ($pc['status']) {
                                    case 'AVAILABLE':
                                        $statusClass = 'status-available';
                                        break;
                                    case 'PLAYING':
                                        $statusClass = 'status-playing';
                                        break;
                                    case 'MAINTENANCE':
                                        $statusClass = 'status-maintenance';
                                        break;
                                    case 'WAITING_PAYMENT':
                                        $statusClass = 'status-waiting';
                                        break;
                                    case 'PAID':
                                        $statusClass = 'status-paid';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?= $statusClass ?>">
                                    <?= str_replace('_', ' ', $pc['status']) ?>
                                </span>
                            </div>
                            <?php if ($pc['status'] == 'AVAILABLE'): ?>
                                <a href="booking.php?computer_id=<?= $pc['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus"></i> Booking
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-times-circle"></i> Tidak Tersedia
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-desktop fa-3x mb-3"></i>
                        <p>Belum ada komputer tersedia</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>