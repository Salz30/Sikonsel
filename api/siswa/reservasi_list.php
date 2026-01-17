<?php
// File: api/siswa/reservasi_list.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include '../../config/database.php'; 

$id_siswa = $_POST['id_siswa'] ?? '';

// Jika kosong, coba ambil dari JSON
if (empty($id_siswa)) {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_siswa = $input['id_siswa'] ?? '';
}

if (empty($id_siswa)) {
    echo json_encode(['success' => false, 'message' => 'ID Siswa tidak ditemukan']);
    exit();
}

try {
    // Sesuaikan nama tabel dengan screenshot Anda: `reservasi`
    $query = "SELECT * FROM reservasi WHERE id_siswa = :id_siswa ORDER BY tgl_temu DESC, jam_temu DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_siswa', $id_siswa);
    $stmt->execute();
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>