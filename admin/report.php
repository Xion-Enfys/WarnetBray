<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAdmin();

require_once '../models/Booking.php';
$booking = new Booking();

// Default: today
$start = $_GET['start'] ?? date('Y-m-d');
$end = $_GET['end'] ?? date('Y-m-d');
$revenue = $booking->getRevenueByPeriod($start, $end);
$bookings = $booking->getAllBookings();

// Filter by date
$filtered_bookings = array_filter($bookings, function ($b) use ($start, $end) {
    $date = date('Y-m-d', strtotime($b['tanggal']));
    return $date >= $start && $date <= $end;
});
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - WarnetBray</title>
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
            text-align: center;
        }

        .stats-card .number {
            font-size: 2rem;
            font-weight: 700;
        }

        .stats-card .label {
            color: #8892b0;
            font-size: 0.9rem;
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

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
            color: #fff;
        }

        .form-label {
            color: #ccd6f6;
        }

        .btn-outline-primary {
            color: #00d4ff;
            border-color: #00d4ff;
        }

        .btn-outline-primary:hover {
            background: #00d4ff;
            color: #fff;
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 20px;
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
                <a href="booking.php" class="sidebar-link">
                    <i class="fas fa-calendar-alt"></i> Booking
                </a>
                <a href="payment.php" class="sidebar-link">
                    <i class="fas fa-credit-card"></i> Pembayaran
                </a>
                <a href="report.php" class="sidebar-link active">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                <a href="../logout.php" class="sidebar-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <div class="col-md-10 main-content">
                <div>
                    <h2 class="text-white"><i class="fas fa-chart-bar"></i> Laporan Pendapatan</h2>
                    <p class="text-muted">Lihat laporan pendapatan berdasarkan periode</p>
                </div>

                <!-- Filter -->
                <div class="filter-section">
                    <form method="GET" class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start" class="form-control" value="<?= $start ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end" class="form-control" value="<?= $end ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Stats -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="stats-card">
                            <div class="number text-success">Rp <?= number_format($revenue, 0, ',', '.') ?></div>
                            <div class="label">Total Pendapatan</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card">
                            <div class="number text-warning"><?= count($filtered_bookings) ?></div>
                            <div class="label">Total Transaksi</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="stats-card">
                            <div class="number text-info">
                                <?= count(array_unique(array_column($filtered_bookings, 'user_id'))) ?></div>
                            <div class="label">Jumlah Pelanggan</div>
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <div class="table-container">
                    <h5 class="text-white mb-3"><i class="fas fa-list"></i> Detail Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>PC</th>
                                    <th>Tanggal</th>
                                    <th>Durasi</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($filtered_bookings) > 0): ?>
                                    <?php foreach ($filtered_bookings as $book): ?>
                                        <tr>
                                            <td><?= $book['id'] ?></td>
                                            <td><?= htmlspecialchars($book['user_name']) ?></td>
                                            <td><?= htmlspecialchars($book['nama_pc']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($book['tanggal'])) ?></td>
                                            <td><?= $book['durasi'] ?> Jam</td>
                                            <td>Rp <?= number_format($book['total_harga'], 0, ',', '.') ?></td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($book['status']) {
                                                    case 'FINISHED':
                                                        $statusClass = 'text-success';
                                                        break;
                                                    case 'CANCELLED':
                                                        $statusClass = 'text-danger';
                                                        break;
                                                    default:
                                                        $statusClass = 'text-warning';
                                                }
                                                ?>
                                                <span class="<?= $statusClass ?>">
                                                    <?= str_replace('_', ' ', $book['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada transaksi</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if (count($filtered_bookings) > 0): ?>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">Total:</td>
                                        <td>Rp <?= number_format($revenue, 0, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>