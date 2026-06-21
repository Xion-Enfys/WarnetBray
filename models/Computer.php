<?php
require_once __DIR__ . '/../config/database.php';

class Computer
{
    private $conn;
    private $table = 'computers';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllComputers()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableComputers()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'AVAILABLE' ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComputerById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createComputer($nama_pc, $spesifikasi, $harga_perjam)
    {
        $query = "INSERT INTO " . $this->table . " (nama_pc, spesifikasi, harga_perjam) VALUES (:nama_pc, :spesifikasi, :harga_perjam)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_pc', $nama_pc);
        $stmt->bindParam(':spesifikasi', $spesifikasi);
        $stmt->bindParam(':harga_perjam', $harga_perjam);
        return $stmt->execute();
    }

    public function updateComputer($id, $nama_pc, $spesifikasi, $harga_perjam, $status)
    {
        $query = "UPDATE " . $this->table . " SET nama_pc = :nama_pc, spesifikasi = :spesifikasi, harga_perjam = :harga_perjam, status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nama_pc', $nama_pc);
        $stmt->bindParam(':spesifikasi', $spesifikasi);
        $stmt->bindParam(':harga_perjam', $harga_perjam);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function deleteComputer($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function countComputers()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countByStatus($status)
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>