<?php
// File: api/siswa/riwayat.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// 1. Include Koneksi & Enkripsi
include '../../config/database.php'; 
include '../../includes/encryption.php'; // WAJIB: Agar bisa baca pesan yang di-enkripsi

// 2. Terima ID Siswa
$id_siswa = $_POST['id_siswa'] ?? '';

// Jika kosong, coba ambil dari JSON (untuk jaga-jaga)
if (empty($id_siswa)) {
    $input = json_decode(file_get_contents("php://input"), true);
    $id_siswa = $input['id_siswa'] ?? '';
}

if (empty($id_siswa)) {
    echo json_encode(['success' => false, 'message' => 'ID Siswa tidak ditemukan']);
    exit();
}

try {
    // 3. Query ke Tabel yang BENAR (laporan_bk)
    // Kita ambil semua kolom
    $query = "SELECT * FROM laporan_bk WHERE id_siswa = :id_siswa ORDER BY tgl_laporan DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_siswa', $id_siswa);
    $stmt->execute();
    
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasil_bersih = [];

    // 4. Proses Dekripsi Data (Looping)
    foreach ($raw_data as $row) {
        // Coba dekripsi Judul & Isi
        // Asumsi nama fungsi dekripsi di file encryption.php Anda adalah 'decryptData'
        // Jika nama fungsinya beda, tolong sesuaikan.
        $judul_decrypted = decryptData($row['judul_laporan']);
        $isi_decrypted   = decryptData($row['isi_laporan']);

        $hasil_bersih[] = [
            'id_laporan' => $row['id_laporan'] ?? $row['id'], // Jaga-jaga nama ID
            'judul'      => $judul_decrypted, // Kita ubah key jadi 'judul' biar cocok sama Flutter
            'isi'        => $isi_decrypted,   // Kita ubah key jadi 'isi' biar cocok sama Flutter
            'kategori'   => $row['kategori'],
            'status'     => $row['status'],
            'tanggal'    => $row['tgl_laporan']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $hasil_bersih
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'System Error: ' . $e->getMessage()
    ]);
}
?>