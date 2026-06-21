<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireLogin();

if ($auth->isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

require_once '../models/Booking.php';
$booking = new Booking();
$bookings = $booking->getBookingsByUser($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - WarnetBray</title>
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
            font-weight: 600;
            padding: 15px 10px;
        }

        .table tbody td {
            padding: 15px 10px;
            vertical-align: middle;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
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

        .empty-state {
            text-align: center;
            padding: 50px 0;
        }

        .empty-state i {
            font-size: 4rem;
            color: #8892b0;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #fff;
        }

        .empty-state p {
            color: #8892b0;
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
                        <a class="nav-link" href="computers.php">
                            <i class="fas fa-desktop"></i> Komputer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="history.php">
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
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white"><i class="fas fa-history"></i> Riwayat Transaksi</h2>
                <p class="text-muted">Daftar semua transaksi pemesanan Anda</p>
            </div>
            <a href="computers.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Booking Baru
            </a>
        </div>

        <div class="table-container">
            <?php if (count($bookings) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PC</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $index => $book): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($book['nama_pc']) ?></strong></td>
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
                                    <td>
                                        <?php if ($book['status'] == 'WAITING_PAYMENT'): ?>
                                            <a href="payment.php?booking_id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-credit-card"></i> Bayar
                                            </a>
                                        <?php elseif ($book['status'] == 'PLAYING'): ?>
                                            <a href="timer.php?booking_id=<?= $book['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-clock"></i> Timer
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>Belum Ada Transaksi</h4>
                    <p>Anda belum melakukan booking komputer apapun</p>
                    <a href="computers.php" class="btn btn-primary mt-3">
                        <i class="fas fa-desktop"></i> Lihat Komputer
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>