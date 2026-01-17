<?php
// File: api/public/tambah_laporan_ortu.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Pastikan path ke database benar
include_once '../../config/database.php';

// Cek file enkripsi, jika tidak ada pakai fungsi dummy biar tidak error
if (file_exists('../../includes/encryption.php')) {
    include_once '../../includes/encryption.php';
} else {
    function encryptData($d) { return base64_encode($d); }
}

$data = json_decode(file_get_contents("php://input"));

// LOG DATA (Opsional: Untuk cek jika ada error nanti)
// file_put_contents("debug_ortu.txt", print_r($data, true));

if(
    !empty($data->nama_ortu) && 
    !empty($data->laporan)
){
    // Format Isi Laporan: Gabungkan data pelapor ke dalam isi
    $isi_lengkap = "Pelapor: " . $data->nama_ortu . "\n" .
                   "Siswa Terlapor: " . ($data->nama_siswa ?? '-') . " (" . ($data->kelas ?? '-') . ")\n\n" .
                   "Isi Laporan:\n" . $data->laporan;

    // Enkripsi
    $judul_enc = encryptData("Laporan Orang Tua");
    $isi_enc   = encryptData($isi_lengkap);
    
    // Default Values
    $kategori = "Pribadi";
    $status = "Pending";
    $tgl = date('Y-m-d H:i:s');
    
    // --- KUNCI PERBAIKAN ---
    $id_guru = 1;      // Langsung tembak ke Admin BK (ID 1)
    $id_siswa = NULL;  // Biarkan NULL (Database sudah diizinkan di Langkah 1)

    try {
        // Query menyertakan id_guru
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