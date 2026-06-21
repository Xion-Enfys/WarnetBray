<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $conn;
    private $table = 'payments';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createPayment($booking_id, $metode, $jumlah, $bukti_bayar = null) {
        $query = "INSERT INTO " . $this->table . " (booking_id, metode, jumlah, bukti_bayar) 
                  VALUES (:booking_id, :metode, :jumlah, :bukti_bayar)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->bindParam(':metode', $metode);
        $stmt->bindParam(':jumlah', $jumlah);
        $stmt->bindParam(':bukti_bayar', $bukti_bayar);
        return $stmt->execute();
    }

    public function getPaymentByBooking($booking_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE booking_id = :booking_id ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllPayments() {
        $query = "SELECT p.*, b.user_id, u.nama as user_name, c.nama_pc 
                  FROM " . $this->table . " p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.user_id = u.id
                  JOIN computers c ON b.computer_id = c.id
                  ORDER BY p.tanggal_bayar DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function getPendingPayments() {
        $query = "SELECT p.*, b.user_id, u.nama as user_name, c.nama_pc, b.total_harga
                  FROM " . $this->table . " p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN users u ON b.user_id = u.id
                  JOIN computers c ON b.computer_id = c.id
                  WHERE p.status = 'PENDING'
                  ORDER BY p.tanggal_bayar ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>