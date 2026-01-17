<?php
// File: api/siswa/tambah_reservasi.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include '../../config/database.php'; 

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->id_siswa) && 
    !empty($data->tgl_temu) && 
    !empty($data->jam_temu) && 
    !empty($data->keperluan)
){
    $status = "Menunggu"; // Default status saat baru ajukan
    $created_at = date('Y-m-d H:i:s');

    try {
        // Kolom sesuai screenshot: id_siswa, tgl_temu, jam_temu, keperluan, status, created_at
        $query = "INSERT INTO reservasi (id_siswa, tgl_temu, jam_temu, keperluan, status, created_at) 
                  VALUES (:id_siswa, :tgl, :jam, :keperluan, :status, :created_at)";

        $stmt = $conn->prepare($query);

        $params = [
            ":id_siswa"   => $data->id_siswa,
            ":tgl"        => $data->tgl_temu,
            ":jam"        => $data->jam_temu,
            ":keperluan"  => $data->keperluan,
            ":status"     => $status,
            ":created_at" => $created_at
        ];

        if($stmt->execute($params)){
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Janji temu berhasil diajukan!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menyimpan data."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap."]);
}
?>