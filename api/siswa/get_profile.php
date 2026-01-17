<?php
// File: Sikonsel/api/siswa/get_profile.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include '../../config/database.php';

$id_siswa = $_GET['id_siswa'] ?? '';

if(empty($id_siswa)) {
    echo json_encode(["success" => false, "message" => "ID Siswa kosong"]);
    exit;
}

try {
    // Ambil data nama_ortu dan no_hp_ortu
    $stmt = $conn->prepare("SELECT nama_ortu, no_hp_ortu FROM siswa WHERE id_siswa = ?");
    $stmt->execute([$id_siswa]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if($data){
        echo json_encode(["success" => true, "data" => $data]);
    } else {
        echo json_encode(["success" => false, "message" => "Data tidak ditemukan"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>