<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAdmin();

require_once '../models/User.php';
$user = new User();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    if ($id != $_SESSION['user_id']) {
        if ($user->deleteUser($id)) {
            $_SESSION['success'] = 'User berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus user';
        }
    } else {
        $_SESSION['error'] = 'Tidak dapat menghapus akun sendiri';
    }
    header('Location: users.php');
    exit();
}
$users = $user->getAllUsers(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - WarnetBray</title>
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

        .role-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-admin {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6b6b;
        }

        .role-customer {
            background: rgba(0, 123, 255, 0.2);
            color: #4dabf7;
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
                <a href="users.php" class="sidebar-link active">
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

            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white"><i class="fas fa-users"></i> Kelola User</h2>
                        <p class="text-muted">Daftar semua user yang terdaftar</p>
                    </div>
                    <div>
                        <span class="badge bg-info">Total: <?= count($users) ?> User</span>
                    </div>
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
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><?= $u['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($u['nama']) ?></strong></td>
                                        <td><?= htmlspecialchars($u['username']) ?></td>
                                        <td>
                                            <span
                                                class="role-badge <?= $u['role'] == 'admin' ? 'role-admin' : 'role-customer' ?>">
                                                <?= $u['role'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                                        <td>
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                                <button class="btn btn-danger btn-sm" onclick="deleteUser(<?= $u['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
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

    
    <script>
        function deleteUser(id) {
            if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'users.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>