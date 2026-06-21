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

$computer_id = $_GET['computer_id'] ?? 0;
$pc = $computer->getComputerById($computer_id);

if (!$pc || $pc['status'] != 'AVAILABLE') {
    $_SESSION['error'] = 'Komputer tidak tersedia';
    header('Location: computers.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Komputer - WarnetBray</title>
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

        .booking-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }

        .booking-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .booking-header h2 {
            color: #00d4ff;
            font-weight: 700;
        }

        .pc-info {
            background: rgba(0, 212, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 212, 255, 0.2);
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

        .total-price {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .total-price h3 {
            color: #ffd43b;
            font-weight: 700;
        }

        .total-price p {
            color: #8892b0;
            margin: 0;
        }

        select.form-control option {
            background: #0a0e1a;
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

    <div class="container">
        <div class="booking-container">
            <div class="booking-header">
                <h2><i class="fas fa-calendar-plus"></i> Booking Komputer</h2>
                <p class="text-muted">Isi form untuk melakukan booking</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="pc-info">
                <div class="row">
                    <div class="col-6">
                        <strong>Komputer:</strong> <?= htmlspecialchars($pc['nama_pc']) ?>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Harga:</strong> Rp <?= number_format($pc['harga_perjam'], 0, ',', '.') ?>/jam
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted"><i class="fas fa-microchip"></i>
                        <?= htmlspecialchars($pc['spesifikasi']) ?></small>
                </div>
            </div>

            <form action="../controllers/BookingController.php" method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="computer_id" value="<?= $pc['id'] ?>">

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-calendar-day"></i> Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" min="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-clock"></i> Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-hourglass"></i> Durasi (Jam)</label>
                    <select name="durasi" class="form-control" id="durasi" required>
                        <option value="1">1 Jam</option>
                        <option value="2">2 Jam</option>
                        <option value="3">3 Jam</option>
                        <option value="4">4 Jam</option>
                        <option value="5">5 Jam</option>
                        <option value="6">6 Jam</option>
                        <option value="8">8 Jam</option>
                        <option value="10">10 Jam</option>
                        <option value="12">12 Jam</option>
                    </select>
                </div>

                <div class="total-price">
                    <p>Total Pembayaran</p>
                    <h3>Rp <span id="totalPrice">0</span></h3>
                    <small class="text-muted">Harga per jam: Rp
                        <?= number_format($pc['harga_perjam'], 0, ',', '.') ?></small>
                    <input type="hidden" name="total_harga" id="totalHarga" value="0">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                </button>
                <a href="computers.php" class="btn btn-secondary mt-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('durasi').addEventListener('change', function () {
            const hargaPerJam = <?= $pc['harga_perjam'] ?>;
            const durasi = parseInt(this.value);
            const total = hargaPerJam * durasi;
            document.getElementById('totalPrice').textContent = total.toLocaleString('id-ID');
            document.getElementById('totalHarga').value = total;
        });

        // Trigger change on load
        document.getElementById('durasi').dispatchEvent(new Event('change'));
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>