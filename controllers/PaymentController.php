<?php
session_start();
require_once '../models/Payment.php';
require_once '../models/Booking.php';
require_once '../models/Computer.php';
require_once 'AuthController.php';

$auth = new AuthController();
$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $booking_id = $_POST['booking_id'];
        $metode = $_POST['metode'];
        $jumlah = $_POST['jumlah'];
        
        // Handle file upload
        $bukti_bayar = null;
        if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0) {
            $target_dir = "../uploads/payment/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION);
            $bukti_bayar = 'payment_' . time() . '.' . $file_extension;
            $target_file = $target_dir . $bukti_bayar;
            
            // Validate image
            $check = getimagesize($_FILES['bukti_bayar']['tmp_name']);
            if ($check === false) {
                $_SESSION['error'] = 'File bukan gambar yang valid';
                header('Location: ../customer/payment.php?booking_id=' . $booking_id);
                exit();
            }
            
            // Check file size (max 2MB)
            if ($_FILES['bukti_bayar']['size'] > 2000000) {
                $_SESSION['error'] = 'Ukuran file terlalu besar (maks 2MB)';
                header('Location: ../customer/payment.php?booking_id=' . $booking_id);
                exit();
            }
            
            if (!move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $target_file)) {
                $_SESSION['error'] = 'Gagal upload bukti pembayaran';
                header('Location: ../customer/payment.php?booking_id=' . $booking_id);
                exit();
            }
        }
        
        $payment = new Payment();
        if ($payment->createPayment($booking_id, $metode, $jumlah, $bukti_bayar)) {
            // Update booking status tetap WAITING_PAYMENT
            $_SESSION['success'] = 'Pembayaran berhasil dikirim, menunggu verifikasi admin';
        } else {
            $_SESSION['error'] = 'Gagal mengirim pembayaran';
        }
        header('Location: ../customer/history.php');
        exit();
    }
}
?>