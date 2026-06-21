<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAdmin();

require_once '../models/Booking.php';
$booking = new Booking();
$bookings = $booking->getAllBookings();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Booking - WarnetBray</title>
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

        .table-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 20px;
        }

        .table {
            color: #fff !important;
            background: transparent !important;
        }

        .table thead,
        .table tbody,
        .table tr,
        .table th,
        .table td {
            background: transparent !important;
            color: #fff !important;
        }

        .table thead th {
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            color: #8892b0;
        }

        .table tbody td {
            border-color: rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
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

        .status-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-waiting {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa94d;
        }

        .status-paid {
            background: rgba(0, 123, 255, 0.2);
            color: #4dabf7;
        }

        .status-playing {
            background: rgba(255, 193, 7, 0.2);
            color: #ffd43b;
        }

        .status-finished {
            background: rgba(0, 255, 0, 0.2);
            color: #51cf66;
        }

        .status-cancelled {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <div class="sidebar-brand">
                    <i class="fas fa-gamepad"></i> WarnetBray
                </div>
                <a href="dashboard.php" class="sidebar-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="computers.php" class="sidebar-link">
                    <i class="fas fa-desktop"></i> Komputer
                </a>
                <a href="users.php" class="sidebar-link">
                    <i class="fas fa-users"></i> User
                </a>
                <a href="booking.php" class="sidebar-link active">
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

            <div class="col-md-10 main-content">
                <div>
                    <h2 class="text-white"><i class="fas fa-calendar-alt"></i> Kelola Booking</h2>
                    <p class="text-muted">Daftar semua booking yang dilakukan</p>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> <?= $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'];
                        unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>PC</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Durasi</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $book): ?>
                                    <tr>
                                        <td><?= $book['id'] ?></td>
                                        <td><?= htmlspecialchars($book['user_name']) ?></td>
                                        <td><?= htmlspecialchars($book['nama_pc']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($book['tanggal'])) ?></td>
                                        <td><?= date('H:i', strtotime($book['jam_mulai'])) ?></td>
                                        <td><?= $book['durasi'] ?> Jam</td>
                                        <td>Rp <?= number_format($book['total_harga'], 0, ',', '.') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($book['status']) {
                                                case 'WAITING_PAYMENT':
                                                    $statusClass = 'status-waiting';
                                                    break;
                                                case 'PAID':
                                                    $statusClass = 'status-paid';
                                                    break;
                                                case 'PLAYING':
                                                    $statusClass = 'status-playing';
                                                    break;
                                                case 'FINISHED':
                                                    $statusClass = 'status-finished';
                                                    break;
                                                case 'CANCELLED':
                                                    $statusClass = 'status-cancelled';
                                                    break;
                                            }
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <?= str_replace('_', ' ', $book['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>