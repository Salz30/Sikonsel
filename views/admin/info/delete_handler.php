<?php
session_start();
require_once '../../../config/database.php';

// Cek Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru_bk') {
    die("Akses ditolak");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM info_sekolah WHERE id_info = ?");
        $stmt->execute([$id]);
        header("Location: list_info.php");
    } catch (PDOException $e) {
        die("Gagal menghapus: " . $e->getMessage());
    }
}
?>