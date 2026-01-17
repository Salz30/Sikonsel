<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Sesuaikan path ini dengan struktur folder hosting Anda
require_once '../../config/database.php';

try {
    // Ambil data terbaru (Limit 50 agar tidak berat)
    $stmt = $conn->prepare("SELECT * FROM info_sekolah ORDER BY tgl_posting DESC LIMIT 50");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $data]);
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => "Gagal memuat data"]);
}
?>