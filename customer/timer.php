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
require_once '../models/Computer.php';

$booking_id = $_GET['booking_id'] ?? 0;
$booking = new Booking();
$computer = new Computer();

$book = $booking->getBookingById($booking_id);

if (!$book || $book['user_id'] != $_SESSION['user_id'] || $book['status'] != 'PLAYING') {
    $_SESSION['error'] = 'Sesi tidak aktif';
    header('Location: dashboard.php');
    exit();
}

$end_time = strtotime($book['tanggal'] . ' ' . $book['jam_mulai']) + ($book['durasi'] * 3600);
$remaining = max(0, $end_time - time());
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Bermain - WarnetBray</title>
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
        .timer-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
        }
        .timer-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .timer-display {
            font-size: 4rem;
            font-weight: 700;
            color: #00d4ff;
            font-family: 'Courier New', monospace;
            text-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
            padding: 20px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            border: 1px solid rgba(0, 212, 255, 0.2);
        }
        .timer-info {
            margin: 30px 0;
        }
        .timer-info h3 {
            color: #fff;
        }
        .timer-info p {
            color: #8892b0;
        }
        .pc-name {
            font-size: 1.5rem;
            color: #ffd43b;
            font-weight: 600;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(255, 193, 7, 0.2);
            color: #ffd43b;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #00d4ff, #7b2ffc);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 212, 255, 0.3);
        }
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 0, 0, 0.3);
        }
        .progress-bar {
            height: 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            margin: 20px 0;
            overflow: hidden;
        }
        .progress-bar .progress {
            height: 100%;
            background: linear-gradient(90deg, #00d4ff, #7b2ffc);
            border-radius: 10px;
            transition: width 1s linear;
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

    <div class="timer-container">
        <div class="timer-card">
            <div class="mb-3">
                <span class="status-badge">
                    <i class="fas fa-gamepad"></i> SEDANG BERMAIN
                </span>
            </div>
            
            <div class="pc-name">
                <i class="fas fa-desktop"></i> <?= htmlspecialchars($book['nama_pc']) ?>
            </div>
            
            <div class="timer-info">
                <p>User: <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong></p>
                <p>Durasi: <?= $book['durasi'] ?> Jam</p>
            </div>

            <div class="timer-display" id="timer">
                <?= gmdate('H:i:s', $remaining) ?>
            </div>

            <div class="progress-bar">
                <div class="progress" id="progressBar"></div>
            </div>

            <div class="mt-4">
                <form action="../controllers/BookingController.php" method="POST">
                    <input type="hidden" name="action" value="finish">
                    <input type="hidden" name="booking_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-stop-circle"></i> Selesai
                    </button>
                </form>
            </div>

            <div class="mt-3 text-muted small">
                <p><i class="fas fa-info-circle"></i> Timer akan berhenti otomatis saat waktu habis</p>
            </div>
        </div>
    </div>

    <script>
        const endTime = <?= $end_time ?>;
        const duration = <?= $book['durasi'] * 3600 ?>;
        
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            let remaining = endTime - now;
            
            if (remaining <= 0) {
                document.getElementById('timer').textContent = '00:00:00';
                document.getElementById('progressBar').style.width = '100%';
                
                // Auto finish
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../controllers/BookingController.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'action';
                input.value = 'finish';
                form.appendChild(input);
                const input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = 'booking_id';
                input2.value = '<?= $book['id'] ?>';
                form.appendChild(input2);
                document.body.appendChild(form);
                form.submit();
                return;
            }
            
            const hours = Math.floor(remaining / 3600);
            const minutes = Math.floor((remaining % 3600) / 60);
            const seconds = remaining % 60;
            
            document.getElementById('timer').textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
            
            const progress = ((duration - remaining) / duration) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>