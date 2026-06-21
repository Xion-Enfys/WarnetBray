<?php
session_start();
require_once '../models/Computer.php';
require_once 'AuthController.php';

$auth = new AuthController();
$auth->requireAdmin();

$computer = new Computer();

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    // ADD: Tambah komputer baru
    if ($action == 'add') {
        $nama_pc = trim($_POST['nama_pc'] ?? '');
        $spesifikasi = trim($_POST['spesifikasi'] ?? '');
        $harga_perjam = intval($_POST['harga_perjam'] ?? 0);
        
        // Validasi
        if (empty($nama_pc)) {
            $_SESSION['error'] = 'Nama PC harus diisi';
        } elseif ($harga_perjam <= 0) {
            $_SESSION['error'] = 'Harga per jam harus lebih dari 0';
        } else {
            if ($computer->createComputer($nama_pc, $spesifikasi, $harga_perjam)) {
                $_SESSION['success'] = 'Komputer berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan komputer';
            }
        }
        header('Location: ../admin/computers.php');
        exit();
    }
    
    // EDIT: Update komputer
    if ($action == 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $nama_pc = trim($_POST['nama_pc'] ?? '');
        $spesifikasi = trim($_POST['spesifikasi'] ?? '');
        $harga_perjam = intval($_POST['harga_perjam'] ?? 0);
        $status = $_POST['status'] ?? 'AVAILABLE';
        
        // Validasi status
        $allowed_status = ['AVAILABLE', 'WAITING_PAYMENT', 'PAID', 'PLAYING', 'MAINTENANCE'];
        if (!in_array($status, $allowed_status)) {
            $status = 'AVAILABLE';
        }
        
        if (empty($nama_pc)) {
            $_SESSION['error'] = 'Nama PC harus diisi';
        } elseif ($harga_perjam <= 0) {
            $_SESSION['error'] = 'Harga per jam harus lebih dari 0';
        } elseif ($id <= 0) {
            $_SESSION['error'] = 'ID komputer tidak valid';
        } else {
            if ($computer->updateComputer($id, $nama_pc, $spesifikasi, $harga_perjam, $status)) {
                $_SESSION['success'] = 'Komputer berhasil diupdate';
            } else {
                $_SESSION['error'] = 'Gagal mengupdate komputer';
            }
        }
        header('Location: ../admin/computers.php');
        exit();
    }
    
    // DELETE: Hapus komputer
    if ($action == 'delete') {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID komputer tidak valid';
        } else {
            // Cek apakah komputer sedang digunakan
            $pc = $computer->getComputerById($id);
            if ($pc && in_array($pc['status'], ['PLAYING', 'PAID', 'WAITING_PAYMENT'])) {
                $_SESSION['error'] = 'Komputer sedang digunakan, tidak dapat dihapus';
            } else {
                if ($computer->deleteComputer($id)) {
                    $_SESSION['success'] = 'Komputer berhasil dihapus';
                } else {
                    $_SESSION['error'] = 'Gagal menghapus komputer';
                }
            }
        }
        header('Location: ../admin/computers.php');
        exit();
    }
    
    // UPDATE STATUS: Ubah status komputer
    if ($action == 'update_status') {
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'AVAILABLE';
        
        $allowed_status = ['AVAILABLE', 'WAITING_PAYMENT', 'PAID', 'PLAYING', 'MAINTENANCE'];
        if (!in_array($status, $allowed_status)) {
            $status = 'AVAILABLE';
        }
        
        if ($id <= 0) {
            $_SESSION['error'] = 'ID komputer tidak valid';
        } else {
            if ($computer->updateStatus($id, $status)) {
                $_SESSION['success'] = 'Status komputer berhasil diupdate';
            } else {
                $_SESSION['error'] = 'Gagal mengupdate status komputer';
            }
        }
        header('Location: ../admin/computers.php');
        exit();
    }
}

// GET: Untuk mendapatkan data komputer (AJAX)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'] ?? '';
    
    // Get computer by ID (untuk edit form via AJAX)
    if ($action == 'get_computer') {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $data = $computer->getComputerById($id);
            header('Content-Type: application/json');
            echo json_encode($data);
            exit();
        }
    }
    
    // Get available computers (untuk booking form)
    if ($action == 'get_available') {
        $data = $computer->getAvailableComputers();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
?>