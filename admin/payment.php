<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAdmin();

require_once '../models/Payment.php';
require_once '../models/Booking.php';
require_once '../models/Computer.php';

$payment = new Payment();
$booking = new Booking();
$computer = new Computer();

// Handle verification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'verify') {
        $payment_id = $_POST['payment_id'];
        $booking_id = $_POST['booking_id'];
        $status = $_POST['status'];
        
        if ($status == 'SUCCESS') {
            // Update payment status
            $payment->updateStatus($payment_id, 'SUCCESS');
            // Update booking status
            $booking->updateStatus($booking_id, 'PAID');
            // Update computer status
            $book = $booking->getBookingById($booking_id);
            if ($book) {
                $computer->updateStatus($book['computer_id'], 'PAID');
            }
            $_SESSION['success'] = 'Pembayaran berhasil diverifikasi';
        } else {
            $payment->updateStatus($payment_id, 'FAILED');
            $booking->updateStatus($booking_id, 'CANCELLED');
            $book = $booking->getBookingById($booking_id);
            if ($book) {
                $computer->updateStatus($book['computer_id'], 'AVAILABLE');
            }
            $_SESSION['error'] = 'Pembayaran ditolak';
        }
        header('Location: payment.php');
        exit();
    }
}

$payments = $payment->getPendingPayments();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran - WarnetBray</title>
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
        .payment-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .payment-card:hover {
            transform: translateY(-3px);
        }
        .payment-card .amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffd43b;
        }
        .payment-card .details {
            color: #8892b0;
        }
        .btn-success {
            background: linear-gradient(135deg, #51cf66, #2b8a3e);
            border: none;
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #c92a2a);
            border: none;
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
            padding: 5px 15px;
            font-size: 0.8rem;
        }
        .status-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-pending {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa94d;
        }
        .status-success {
            background: rgba(0, 255, 0, 0.2);
            color: #51cf66;
        }
        .status-failed {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
        }
        .proof-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .proof-image:hover {
            transform: scale(1.1);
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
                <a href="payment.php" class="sidebar-link active">
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
                    <h2 class="text-white"><i class="fas fa-credit-card"></i> Verifikasi Pembayaran</h2>
                    <p class="text-muted">Verifikasi pembayaran yang menunggu konfirmasi</p>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (count($payments) > 0): ?>
                    <?php foreach ($payments as $p): ?>
                        <div class="payment-card">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <strong><?= htmlspecialchars($p['user_name']) ?></strong>
                                    <div class="details"><?= htmlspecialchars($p['nama_pc']) ?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="details">
                                        <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($p['tanggal_bayar'])) ?>
                                    </div>
                                    <div class="details">
                                        <i class="fas fa-money-bill"></i> <?= $p['metode'] ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="amount">Rp <?= number_format($p['jumlah'], 0, ',', '.') ?></div>
                                </div>
                                <div class="col-md-2">
                                    <?php if ($p['bukti_bayar']): ?>
                                        <img src="../uploads/payment/<?= $p['bukti_bayar'] ?>" 
                                             class="proof-image" 
                                             alt="Bukti Pembayaran"
                                             onclick="window.open(this.src, '_blank')">
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada bukti</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-3 text-end">
                                    <span class="status-badge status-pending">PENDING</span>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="verify">
                                        <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="booking_id" value="<?= $p['booking_id'] ?>">
                                        <button type="submit" name="status" value="SUCCESS" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Terima
                                        </button>
                                        <button type="submit" name="status" value="FAILED" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h4>Tidak Ada Pembayaran Pending</h4>
                        <p class="text-muted">Semua pembayaran sudah diverifikasi</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>