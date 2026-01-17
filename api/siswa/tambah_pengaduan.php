<?php
// File: api/siswa/tambah_pengaduan.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include '../../config/database.php';

if (file_exists('../../includes/encryption.php')) {
    include_once '../../includes/encryption.php';
} else {
    function encryptData($d) { return base64_encode($d); }
}

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id_siswa) && !empty($data->judul) && !empty($data->isi)){
    
    // 1. Enkripsi Data
    $judul_enc = encryptData($data->judul);
    $isi_enc   = encryptData($data->isi);
    
    // 2. Data Default
    $kategori = !empty($data->kategori) ? $data->kategori : 'Lainnya';
    $status = "Pending";
    $tgl = date('Y-m-d H:i:s');
    
    // 3. AUTO ASSIGN KE ADMIN (User ID 1 di Database Anda)
    // Ini penting agar laporan tidak "nyangkut" tanpa pemilik
    $id_guru = 1; 

    try {
        $query = "INSERT INTO laporan_bk (id_siswa, id_guru, judul_laporan, isi_laporan, kategori, status, tgl_laporan) 
                  VALUES (:id_siswa, :id_guru, :judul, :isi, :kategori, :status, :tgl)";

        $stmt = $conn->prepare($query);
        $params = [
            ":id_siswa" => $data->id_siswa,
            ":id_guru"  => $id_guru,
            ":judul"    => $judul_enc,
            ":isi"      => $isi_enc,
            ":kategori" => $kategori,
            ":status"   => $status,
            ":tgl"      => $tgl
        ];

        if($stmt->execute($params)){
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Curhat Terkirim!"]);
        } else {
            $err = $stmt->errorInfo();
            echo json_encode(["success" => false, "message" => "Gagal: " . $err[2]]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "DB Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Data Kurang"]);
}
?>