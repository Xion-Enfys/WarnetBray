<?php
session_start();
require_once '../controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAdmin();

require_once '../models/Computer.php';
$computer = new Computer();

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $nama_pc = $_POST['nama_pc'];
        $spesifikasi = $_POST['spesifikasi'];
        $harga_perjam = $_POST['harga_perjam'];

        if ($computer->createComputer($nama_pc, $spesifikasi, $harga_perjam)) {
            $_SESSION['success'] = 'Komputer berhasil ditambahkan';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan komputer';
        }
        header('Location: computers.php');
        exit();
    }

    if ($action == 'edit') {
        $id = $_POST['id'];
        $nama_pc = $_POST['nama_pc'];
        $spesifikasi = $_POST['spesifikasi'];
        $harga_perjam = $_POST['harga_perjam'];
        $status = $_POST['status'];

        if ($computer->updateComputer($id, $nama_pc, $spesifikasi, $harga_perjam, $status)) {
            $_SESSION['success'] = 'Komputer berhasil diupdate';
        } else {
            $_SESSION['error'] = 'Gagal mengupdate komputer';
        }
        header('Location: computers.php');
        exit();
    }

    if ($action == 'delete') {
        $id = $_POST['id'];
        if ($computer->deleteComputer($id)) {
            $_SESSION['success'] = 'Komputer berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus komputer';
        }
        header('Location: computers.php');
        exit();
    }
}

$computers = $computer->getAllComputers();
$edit_id = $_GET['edit'] ?? 0;
$edit_data = null;
if ($edit_id) {
    $edit_data = $computer->getComputerById($edit_id);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Komputer - WarnetBray</title>
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

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            padding: 10px 15px;
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

        .modal-content {
            background: #0a0e1a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-close {
            filter: invert(1);
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

        select.form-control option {
            background: #0a0e1a;
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
                <a href="computers.php" class="sidebar-link active">
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

            <div class="col-md-10 main-content">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white"><i class="fas fa-desktop"></i> Kelola Komputer</h2>
                        <p class="text-muted">Tambah, edit, atau hapus data komputer</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Tambah PC
                    </button>
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
                                    <th>Nama PC</th>
                                    <th>Spesifikasi</th>
                                    <th>Harga/Jam</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($computers as $pc): ?>
                                    <tr>
                                        <td><?= $pc['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($pc['nama_pc']) ?></strong></td>
                                        <td><?= htmlspecialchars($pc['spesifikasi']) ?></td>
                                        <td>Rp <?= number_format($pc['harga_perjam'], 0, ',', '.') ?></td>
                                        <td>
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
                                        </td>
                                        <td>
                                            <a href="?edit=<?= $pc['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteComputer(<?= $pc['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Komputer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama PC</label>
                            <input type="text" name="nama_pc" class="form-control" placeholder="Contoh: PC 07" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Spesifikasi</label>
                            <textarea name="spesifikasi" class="form-control"
                                placeholder="Contoh: Intel i7, RAM 32GB, RTX 4070" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga per Jam</label>
                            <input type="number" name="harga_perjam" class="form-control" placeholder="5000" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <?php if ($edit_data): ?>
        <div class="modal fade show" id="editModal" tabindex="-1" style="display: block;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Komputer</h5>
                        <a href="computers.php" class="btn-close"></a>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama PC</label>
                                <input type="text" name="nama_pc" class="form-control"
                                    value="<?= htmlspecialchars($edit_data['nama_pc']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Spesifikasi</label>
                                <textarea name="spesifikasi" class="form-control"
                                    required><?= htmlspecialchars($edit_data['spesifikasi']) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Harga per Jam</label>
                                <input type="number" name="harga_perjam" class="form-control"
                                    value="<?= $edit_data['harga_perjam'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="AVAILABLE" <?= $edit_data['status'] == 'AVAILABLE' ? 'selected' : '' ?>>
                                        Available</option>
                                    <option value="MAINTENANCE" <?= $edit_data['status'] == 'MAINTENANCE' ? 'selected' : '' ?>>
                                        Maintenance</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="computers.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="../assets/js/script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>