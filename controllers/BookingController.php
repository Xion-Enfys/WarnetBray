<?php
session_start();
require_once '../models/Booking.php';
require_once '../models/Computer.php';
require_once '../models/Payment.php';
require_once 'AuthController.php';

$auth = new AuthController();
$auth->requireLogin();

$booking = new Booking();
$computer = new Computer();
$payment = new Payment();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // CREATE: Membuat booking baru
    if ($action == 'create') {
        $user_id = $_SESSION['user_id'];
        $computer_id = intval($_POST['computer_id'] ?? 0);
        $tanggal = $_POST['tanggal'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $durasi = intval($_POST['durasi'] ?? 0);
        $total_harga = intval($_POST['total_harga'] ?? 0);

        // Validasi
        if ($computer_id <= 0) {
            $_SESSION['error'] = 'Pilih komputer terlebih dahulu';
            header('Location: ../customer/computers.php');
            exit();
        }

        if (empty($tanggal)) {
            $_SESSION['error'] = 'Tanggal harus diisi';
            header('Location: ../customer/booking.php?computer_id=' . $computer_id);
            exit();
        }

        if (empty($jam_mulai)) {
            $_SESSION['error'] = 'Jam mulai harus diisi';
            header('Location: ../customer/booking.php?computer_id=' . $computer_id);
            exit();
        }

        if ($durasi <= 0) {
            $_SESSION['error'] = 'Durasi harus dipilih';
            header('Location: ../customer/booking.php?computer_id=' . $computer_id);
            exit();
        }

        // Cek ketersediaan komputer
        $pc = $computer->getComputerById($computer_id);
        if (!$pc || $pc['status'] != 'AVAILABLE') {
            $_SESSION['error'] = 'Komputer tidak tersedia';
            header('Location: ../customer/computers.php');
            exit();
        }

        // Hitung ulang total harga (untuk keamanan)
        $calculated_total = $pc['harga_perjam'] * $durasi;
        if ($total_harga != $calculated_total) {
            $_SESSION['error'] = 'Total harga tidak valid';
            header('Location: ../customer/booking.php?computer_id=' . $computer_id);
            exit();
        }

        // Update status komputer
        $computer->updateStatus($computer_id, 'WAITING_PAYMENT');

        // Create booking
        $booking_id = $booking->createBooking(
            $user_id, 
            $computer_id, 
            $tanggal, 
            $jam_mulai, 
            $durasi, 
            $total_harga
        );

        if ($booking_id) {
            $_SESSION['success'] = 'Booking berhasil dibuat, silahkan lakukan pembayaran';
            header('Location: ../customer/payment.php?booking_id=' . $booking_id);
        } else {
            // Rollback status komputer
            $computer->updateStatus($computer_id, 'AVAILABLE');
            $_SESSION['error'] = 'Booking gagal dibuat';
            header('Location: ../customer/computers.php');
        }
        exit();
    }

    // VERIFY PAYMENT: Verifikasi pembayaran oleh admin
    if ($action == 'verify_payment') {
        $auth->requireAdmin();
        
        $booking_id = intval($_POST['booking_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($booking_id <= 0) {
            $_SESSION['error'] = 'ID booking tidak valid';
            header('Location: ../admin/payment.php');
            exit();
        }

        $book = $booking->getBookingById($booking_id);
        if (!$book) {
            $_SESSION['error'] = 'Booking tidak ditemukan';
            header('Location: ../admin/payment.php');
            exit();
        }

        if ($status == 'PAID') {
            // Update booking status
            if ($booking->updateStatus($booking_id, 'PAID')) {
                // Update computer status
                $computer->updateStatus($book['computer_id'], 'PAID');
                
                // Update payment status
                $pay = $payment->getPaymentByBooking($booking_id);
                if ($pay) {
                    $payment->updateStatus($pay['id'], 'SUCCESS');
                }
                
                $_SESSION['success'] = 'Pembayaran berhasil diverifikasi, PC siap digunakan';
            } else {
                $_SESSION['error'] = 'Gagal verifikasi pembayaran';
            }
        } else if ($status == 'CANCELLED') {
            // Update booking status
            if ($booking->updateStatus($booking_id, 'CANCELLED')) {
                // Update computer status back to available
                $computer->updateStatus($book['computer_id'], 'AVAILABLE');
                
                // Update payment status
                $pay = $payment->getPaymentByBooking($booking_id);
                if ($pay) {
                    $payment->updateStatus($pay['id'], 'FAILED');
                }
                
                $_SESSION['error'] = 'Pembayaran ditolak';
            } else {
                $_SESSION['error'] = 'Gagal menolak pembayaran';
            }
        } else {
            $_SESSION['error'] = 'Status tidak valid';
        }
        
        header('Location: ../admin/payment.php');
        exit();
    }

    // START PLAYING: Aktifkan PC untuk mulai bermain
    if ($action == 'start_playing') {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        
        if ($booking_id <= 0) {
            $_SESSION['error'] = 'ID booking tidak valid';
            header('Location: ../customer/dashboard.php');
            exit();
        }

        $book = $booking->getBookingById($booking_id);
        if (!$book || $book['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Booking tidak ditemukan';
            header('Location: ../customer/dashboard.php');
            exit();
        }

        if ($book['status'] != 'PAID') {
            $_SESSION['error'] = 'Pembayaran belum diverifikasi';
            header('Location: ../customer/history.php');
            exit();
        }

        // Update status
        if ($booking->updateStatus($booking_id, 'PLAYING')) {
            $computer->updateStatus($book['computer_id'], 'PLAYING');
            $_SESSION['success'] = 'PC aktif, timer berjalan';
            header('Location: ../customer/timer.php?booking_id=' . $booking_id);
        } else {
            $_SESSION['error'] = 'Gagal mengaktifkan PC';
            header('Location: ../customer/history.php');
        }
        exit();
    }

    // FINISH: Selesaikan sesi bermain
    if ($action == 'finish') {
        $booking_id = intval($_POST['booking_id'] ?? 0);
        
        if ($booking_id <= 0) {
            $_SESSION['error'] = 'ID booking tidak valid';
            header('Location: ../customer/dashboard.php');
            exit();
        }

        $book = $booking->getBookingById($booking_id);
        if (!$book || $book['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Booking tidak ditemukan';
            header('Location: ../customer/dashboard.php');
            exit();
        }

        if ($book['status'] != 'PLAYING') {
            $_SESSION['error'] = 'Sesi tidak aktif';
            header('Location: ../customer/dashboard.php');
            exit();
        }

        // Update status
        if ($booking->updateStatus($booking_id, 'FINISHED')) {
            $computer->updateStatus($book['computer_id'], 'AVAILABLE');
            $_SESSION['success'] = 'Sesi selesai, terima kasih sudah bermain!';
            header('Location: ../customer/history.php');
        } else {
            $_SESSION['error'] = 'Gagal menyelesaikan sesi';
            header('Location: ../customer/timer.php?booking_id=' . $booking_id);
        }
        exit();
    }
}

// Redirect jika akses langsung
header('Location: ../index.php');
exit();
?>