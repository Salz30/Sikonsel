<?php
// File: api/public/tambah_laporan_ortu.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Pastikan path ke database benar
include_once '../../config/database.php';

// Cek file enkripsi
if (file_exists('../../includes/encryption.php')) {
    include_once '../../includes/encryption.php';
} else {
    function encryptData($d) { return base64_encode($d); }
}

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->nama_ortu) && 
    !empty($data->laporan)
){
    // Format Isi Laporan
    $isi_lengkap = "Pelapor: " . $data->nama_ortu . "\n" .
                   "Siswa Terlapor: " . ($data->nama_siswa ?? '-') . " (" . ($data->kelas ?? '-') . ")\n\n" .
                   "Isi Laporan:\n" . $data->laporan;

    // Enkripsi
    $judul_enc = encryptData("Laporan Orang Tua: " . substr($data->laporan, 0, 20) . "...");
    $isi_enc   = encryptData($isi_lengkap);
    
    // Default Values
    $kategori = "Pribadi";
    $status = "Pending";
    $tgl = date('Y-m-d H:i:s');
    
    // --- PERBAIKAN: CARI ID SISWA BERDASARKAN NAMA & KELAS ---
    $id_guru = 1;      
    $id_siswa = NULL;

    if (!empty($data->nama_siswa) && !empty($data->kelas)) {
        try {
            // Mencocokkan nama dari tabel users dan kelas dari tabel siswa
            $stmtSearch = $conn->prepare("SELECT s.id_siswa 
                                         FROM siswa s 
                                         JOIN users u ON s.user_id = u.id_user 
                                         WHERE u.nama_lengkap = ? AND s.kelas = ?");
            $stmtSearch->execute([$data->nama_siswa, $data->kelas]);
            $rowSiswa = $stmtSearch->fetch();
            
            if ($rowSiswa) {
                $id_siswa = $rowSiswa['id_siswa'];
            }
        } catch (Exception $e) {
            // Jika pencarian gagal, biarkan id_siswa tetap NULL
        }
    }

    try {
        $query = "INSERT INTO laporan_bk (id_siswa, id_guru, judul_laporan, isi_laporan, kategori, status, tgl_laporan) 
                  VALUES (:id_siswa, :id_guru, :judul, :isi, :kategori, :status, :tgl)";
        
        $stmt = $conn->prepare($query);
        $params = [
            ":id_siswa" => $id_siswa,
            ":id_guru"  => $id_guru,
            ":judul"    => $judul_enc,
            ":isi"      => $isi_enc,
            ":kategori" => $kategori,
            ":status"   => $status,
            ":tgl"      => $tgl
        ];

        if($stmt->execute($params)){
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Laporan Terkirim"]);
        } else {
            $err = $stmt->errorInfo();
            echo json_encode(["success" => false, "message" => "Gagal Query: " . $err[2]]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error DB: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Data form tidak lengkap"]);
}
?>
