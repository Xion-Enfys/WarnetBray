<?php
require_once __DIR__ . '/../config/database.php';

class Booking {
    private $conn;
    private $table = 'bookings';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createBooking($user_id, $computer_id, $tanggal, $jam_mulai, $durasi, $total_harga) {
        $query = "INSERT INTO " . $this->table . " (user_id, computer_id, tanggal, jam_mulai, durasi, total_harga) 
                  VALUES (:user_id, :computer_id, :tanggal, :jam_mulai, :durasi, :total_harga)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':computer_id', $computer_id);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->bindParam(':jam_mulai', $jam_mulai);
        $stmt->bindParam(':durasi', $durasi);
        $stmt->bindParam(':total_harga', $total_harga);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getBookingsByUser($user_id) {
        $query = "SELECT b.*, c.nama_pc, u.nama as user_name FROM " . $this->table . " b
                  JOIN computers c ON b.computer_id = c.id
                  JOIN users u ON b.user_id = u.id
                  WHERE b.user_id = :user_id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBookings() {
        $query = "SELECT b.*, c.nama_pc, u.nama as user_name FROM " . $this->table . " b
                  JOIN computers c ON b.computer_id = c.id
                  JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookingById($id) {
        $query = "SELECT b.*, c.nama_pc, u.nama as user_name FROM " . $this->table . " b
                  JOIN computers c ON b.computer_id = c.id
                  JOIN users u ON b.user_id = u.id
                  WHERE b.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function countTodayBookings() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalRevenue() {
        $query = "SELECT SUM(total_harga) as total FROM " . $this->table . " WHERE status = 'PAID' OR status = 'PLAYING' OR status = 'FINISHED'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function getRevenueByPeriod($start, $end) {
        $query = "SELECT SUM(total_harga) as total FROM " . $this->table . " 
                  WHERE (status = 'PAID' OR status = 'PLAYING' OR status = 'FINISHED')
                  AND DATE(created_at) BETWEEN :start AND :end";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start', $start);
        $stmt->bindParam(':end', $end);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }
}
?>