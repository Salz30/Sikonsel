<?php
// File: Sikonsel/api/siswa/update_profile.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id_siswa)) {
    try {
        // Query update khusus untuk Data Ortu
        $query = "UPDATE siswa SET 
                  nama_ortu = :nama_ortu, 
                  no_hp_ortu = :no_hp 
                  WHERE id_siswa = :id";
        
        $stmt = $conn->prepare($query);
        
        // Bersihkan input
        $nama_ortu = htmlspecialchars(strip_tags($data->nama_ortu ?? ''));
        $no_hp     = htmlspecialchars(strip_tags($data->no_whatsapp_ortu ?? ''));
        
        $params = [
            ':nama_ortu' => $nama_ortu,
            ':no_hp'     => $no_hp,
            ':id'        => $data->id_siswa
        ];

        if($stmt->execute($params)){
            echo json_encode(["status" => "success", "message" => "Profil berhasil diperbarui"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal update database"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
}
?>