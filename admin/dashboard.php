<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAdmin();

require_once '../models/Computer.php';
require_once '../models/User.php';
require_once '../models/Booking.php';

$computer = new Computer();
$user = new User();
$booking = new Booking();

$total_pc = $computer->countComputers();
$available = $computer->countByStatus('AVAILABLE');
$playing = $computer->countByStatus('PLAYING');
$total_users = count($user->getAllUsers());
$today_bookings = $booking->countTodayBookings();
$revenue = $booking->getTotalRevenue();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - WarnetBray</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #0a0e1a;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            min-height: 100vh;
            padding: 20px 0;
        }
        .sidebar-brand {
            color: #00d4ff;
            font-weight: 700;
            font-size: 1.5rem;
            padding: 0 20px 30px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 20px;
        }
        .sidebar-brand:hover {
            color: #00d4ff;
        }
        .sidebar-link {
            color: #8892b0;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border-left-color: #00d4ff;
        }
        .sidebar-link.active {
            color: #00d4ff;
            background: rgba(0, 212, 255, 0.05);
            border-left-color: #00d4ff;
        }
        .sidebar-link i {
            width: 25px;
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .stats-card .icon {
            font-size: 2.5rem;
        }
        .stats-card .number {
            font-size: 2rem;
            font-weight: 700;
        }
        .stats-card .label {
            color: #8892b0;
            font-size: 0.9rem;
        }
        .section-title {
            color: #fff;
            font-weight: 700;
            margin: 30px 0 20px 0;
        }
        .section-title i {
            color: #00d4ff;
            margin-right: 10px;
        }
        .table {
            color: #fff;
        }
        .table thead th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            color: #8892b0;
        }
        .table tbody td {
            border-color: rgba(255, 255, 255, 0.05);
        }
        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }
        .status-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
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
        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #7b2ffc);
            border: none;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }
        .btn-sm {
            padding: 5px 12px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="sidebar-brand">
                    <i class="fas fa-gamepad"></i> WarnetBray
                </div>
                <a href="dashboard.php" class="sidebar-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="computers.php" class="sidebar-link">
                    <i class="fas fa-desktop"></i> Komputer
                </a>
                <a href="users.php" class="sidebar-link">
                    <i class="fas fa-users"></i> User
                </a>
                <a href="booking.php" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i> Booking
                </a>
                <a href="payment.php" class="sidebar-link">
                    <i class="fas fa-credit-card"></i> Pembayaran
                </a>
                <a href="report.php" class="sidebar-link">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                <a href="../logout.php" class="sidebar-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="text-white">Dashboard Admin</h2>
                        <p class="text-muted">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></p>
                    </div>
                    <div>
                        <span class="badge bg-info">Admin</span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="number text-white"><?= $total_pc ?></div>
                                    <div class="label">Total Komputer</div>
                                </div>
                                <div class="icon text-info">
                                    <i class="fas fa-desktop"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="number text-success"><?= $available ?></div>
                                    <div class="label">PC Tersedia</div>
                                </div>
                                <div class="icon text-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="number text-warning"><?= $playing ?></div>
                                    <div class="label">PC Aktif</div>
                                </div>
                                <div class="icon text-warning">
                                    <i class="fas fa-gamepad"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="number text-warning"><?= $today_bookings ?></div>
                                    <div class="label">Booking Hari Ini</div>
                                </div>
                                <div class="icon text-warning">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="number text-primary"><?= $total_users ?></div>
                                    <div class="label">Total User</div>
                                </div>
                                <div class="icon text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="number text-success">Rp <?= number_format($revenue, 0, ',', '.') ?></div>
                                    <div class="label">Total Pendapatan</div>
                                </div>
                                <div class="icon text-success">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="section-title">
                    <i class="fas fa-bolt"></i> Aksi Cepat
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="computers.php?action=add" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Tambah PC
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="payment.php" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle"></i> Verifikasi Pembayaran
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="booking.php" class="btn btn-primary w-100">
                            <i class="fas fa-calendar-alt"></i> Kelola Booking
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="report.php" class="btn btn-primary w-100">
                            <i class="fas fa-chart-bar"></i> Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>