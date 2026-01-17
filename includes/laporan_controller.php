<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/encryption.php';

/**
 * Ambil Semua Laporan (Untuk Dashboard Guru)
 * PERBAIKAN: Menggunakan LEFT JOIN agar laporan tetap muncul 
 * meskipun data user siswa tidak sempurna.
 */
function getAllLaporan($conn) {
    $sql = "SELECT laporan_bk.*, siswa.nisn, users.nama_lengkap as nama_siswa 
            FROM laporan_bk 
            LEFT JOIN siswa ON laporan_bk.id_siswa = siswa.id_siswa
            LEFT JOIN users ON siswa.user_id = users.id_user
            ORDER BY 
                FIELD(laporan_bk.status, 'Pending', 'Diproses', 'Selesai'), 
                laporan_bk.tgl_laporan DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Ambil Laporan Berdasarkan Siswa
 */
function getLaporanBySiswa($conn, $id_siswa) {
    $sql = "SELECT laporan_bk.*, siswa.nisn, users.nama_lengkap as nama_siswa 
            FROM laporan_bk 
            LEFT JOIN siswa ON laporan_bk.id_siswa = siswa.id_siswa
            LEFT JOIN users ON siswa.user_id = users.id_user
            WHERE laporan_bk.id_siswa = ?
            ORDER BY laporan_bk.tgl_laporan DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_siswa]);
    $result = $stmt->fetchAll();

    // Loop untuk membuka kunci (Decrypt) setiap baris data
    foreach ($result as $key => $row) {
        $result[$key]['judul_laporan'] = decryptData($row['judul_laporan']);
        $result[$key]['isi_laporan_dekripsi'] = decryptData($row['isi_laporan']);
    }

    return $result;
}

/**
 * Ambil Satu Laporan Detail
 * PERBAIKAN: Menambahkan pengambilan kelas (siswa.kelas)
 */
function getLaporanById($conn, $id) {
    $sql = "SELECT laporan_bk.*, 
                   siswa.nisn, 
                   siswa.kelas, 
                   users.nama_lengkap as nama_siswa 
            FROM laporan_bk 
            LEFT JOIN siswa ON laporan_bk.id_siswa = siswa.id_siswa
            LEFT JOIN users ON siswa.user_id = users.id_user
            WHERE laporan_bk.id_laporan = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $laporan = $stmt->fetch();

    if ($laporan) {
        // Dekripsi Judul & Isi agar bisa dibaca manusia
        $laporan['judul_laporan'] = decryptData($laporan['judul_laporan']);
        $laporan['isi_laporan_dekripsi'] = decryptData($laporan['isi_laporan']);
    }

    return $laporan;
}

/**
 * Tambah Laporan Baru
 */
function tambahLaporan($conn, $data) {
    try {
        // Enkripsi Isi & Judul
        $isiTerenkripsi = encryptData($data['isi']);
        $judulTerenkripsi = encryptData($data['judul']);

        $sql = "INSERT INTO laporan_bk (id_siswa, judul_laporan, isi_laporan, kategori, status, tgl_laporan) 
                VALUES (?, ?, ?, ?, 'Pending', NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['id_siswa'], 
            $judulTerenkripsi,
            $isiTerenkripsi,
            $data['kategori']
        ]);

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update Status Laporan
 */
function updateStatusLaporan($conn, $id, $status, $id_guru) {
    try {
        $sql = "UPDATE laporan_bk SET status = ?, id_guru = ? WHERE id_laporan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$status, $id_guru, $id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Hapus Satu Laporan
 */
function deleteLaporan($conn, $id) {
    try {
        $sql = "DELETE FROM laporan_bk WHERE id_laporan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Hapus Banyak Laporan Sekaligus (Bulk Delete)
 */
function deleteBulkLaporan($conn, $ids_array) {
    try {
        $conn->beginTransaction();
        $sql = "DELETE FROM laporan_bk WHERE id_laporan = ?";
        $stmt = $conn->prepare($sql);
        
        foreach ($ids_array as $id) {
            $stmt->execute([$id]);
        }
        
        $conn->commit();
        return true;
    } catch (PDOException $e) {
        $conn->rollBack();
        return false;
    }
}
?>