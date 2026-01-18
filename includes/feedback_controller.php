<?php
/**
 * File: Sikonsel/includes/feedback_controller.php
 */
require_once __DIR__ . '/../config/database.php';

// Fungsi untuk mengecek apakah siswa sudah memberi rating untuk reservasi tertentu
function checkFeedbackExists($conn, $id_reservasi) {
    $stmt = $conn->prepare("SELECT id_feedback FROM feedback_konseling WHERE id_reservasi = ?");
    $stmt->execute([$id_reservasi]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Mengembalikan data jika ada, false jika tidak
}

// Fungsi untuk menyimpan feedback baru
function insertFeedback($conn, $id_reservasi, $rating, $komentar) {
    try {
        $sql = "INSERT INTO feedback_konseling (id_reservasi, rating, komentar) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$id_reservasi, $rating, $komentar]);
    } catch (PDOException $e) {
        return false;
    }
}
?>