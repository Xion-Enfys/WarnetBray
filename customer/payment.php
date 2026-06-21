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
require_once '../models/Payment.php';

$booking_id = $_GET['booking_id'] ?? 0;
$booking = new Booking();
$payment = new Payment();

$book = $booking->getBookingById($booking_id);

if (!$book || $book['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Booking tidak ditemukan';
    header('Location: computers.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - WarnetBray</title>
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
        .payment-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .payment-header h2 {
            color: #00d4ff;
            font-weight: 700;
        }
        .booking-details {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 12px 15px;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
            color: #fff;
        }
        .form-control::placeholder {
            color: #8892b0;
        }
        .form-label {
            color: #ccd6f6;
            font-weight: 500;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #7b2ffc);
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #8892b0;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
        }
        .total-amount {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .total-amount h3 {
            color: #ffd43b;
            font-weight: 700;
        }
        select.form-control option {
            background: #0a0e1a;
        }
        .upload-area {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-area:hover {
            border-color: #00d4ff;
            background: rgba(0, 212, 255, 0.05);
        }
        .upload-area i {
            font-size: 3rem;
            color: #8892b0;
        }
        .upload-area p {
            color: #8892b0;
            margin: 0;
        }
        .file-name {
            color: #00d4ff;
            margin-top: 10px;
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

    <div class="container">
        <div class="payment-container">
            <div class="payment-header">
                <h2><i class="fas fa-credit-card"></i> Pembayaran</h2>
                <p class="text-muted">Lakukan pembayaran untuk mengaktifkan PC</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="booking-details">
                <div class="row">
                    <div class="col-6">
                        <strong>Komputer:</strong> <?= htmlspecialchars($book['nama_pc']) ?>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Durasi:</strong> <?= $book['durasi'] ?> Jam
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($book['tanggal'])) ?>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Jam:</strong> <?= date('H:i', strtotime($book['jam_mulai'])) ?>
                    </div>
                </div>
            </div>

            <div class="total-amount">
                <p>Total Pembayaran</p>
                <h3>Rp <?= number_format($book['total_harga'], 0, ',', '.') ?></h3>
            </div>

            <form action="../controllers/PaymentController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="booking_id" value="<?= $book['id'] ?>">
                <input type="hidden" name="jumlah" value="<?= $book['total_harga'] ?>">

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-money-bill"></i> Metode Pembayaran</label>
                    <select name="metode" class="form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="Transfer">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-upload"></i> Upload Bukti Pembayaran</label>
                    <div class="upload-area" onclick="document.getElementById('bukti').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik untuk upload bukti pembayaran</p>
                        <p class="text-muted small">Format: JPG, PNG (Max 2MB)</p>
                        <div class="file-name" id="fileName"></div>
                    </div>
                    <input type="file" name="bukti_bayar" id="bukti" class="d-none" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                </button>
                <a href="computers.php" class="btn btn-secondary mt-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </form>

            <div class="mt-3 text-center text-muted small">
                <p><i class="fas fa-info-circle"></i> Pembayaran akan diverifikasi oleh admin</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('bukti').addEventListener('change', function(e) {
            const fileName = this.files[0]?.name || 'Belum ada file';
            document.getElementById('fileName').textContent = fileName;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>