<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Ambil Semua Data Siswa
 * Perbaikan: Mengambil nama_lengkap dari tabel users (JOIN)
 */
function getAllSiswa($conn) {
    try {
        $sql = "SELECT siswa.*, users.username, users.nama_lengkap 
                FROM siswa 
                JOIN users ON siswa.user_id = users.id_user 
                ORDER BY users.nama_lengkap ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Ambil Satu Data Siswa
 */
function getSiswaById($conn, $id) {
    // Perbaikan: Ambil nama dari tabel users juga
    $sql = "SELECT siswa.*, users.nama_lengkap 
            FROM siswa 
            JOIN users ON siswa.user_id = users.id_user 
            WHERE siswa.id_siswa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Tambah Siswa Baru
 * Perbaikan: Menghapus insert 'nama_lengkap' ke tabel siswa (karena kolomnya tidak ada)
 * Perbaikan: Return pesan error spesifik jika gagal
 */
function tambahSiswa($conn, $data) {
    try {
        $conn->beginTransaction();

        // 1. Buat Akun User (Simpan Nama di sini)
        // Password default adalah NISN
        $passwordHash = password_hash($data['nisn'], PASSWORD_BCRYPT);
        
        $stmtUser = $conn->prepare("INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, 'siswa', ?)");
        $stmtUser->execute([$data['nisn'], $passwordHash, $data['nama']]);
        
        $userId = $conn->lastInsertId();

        // 2. Buat Profil Siswa (JANGAN masukkan nama_lengkap ke sini)
        $stmtSiswa = $conn->prepare("INSERT INTO siswa (user_id, nisn, kelas, alamat) VALUES (?, ?, ?, ?)");
        $stmtSiswa->execute([$userId, $data['nisn'], $data['kelas'], $data['alamat']]);

        $conn->commit();
        return ["success" => true];

    } catch (Exception $e) {
        $conn->rollBack();
        // Kembalikan pesan error asli dari database untuk debugging
        return ["success" => false, "message" => $e->getMessage()];
    }
}

/**
 * Update Siswa
 */
function updateSiswa($conn, $id, $data) {
    try {
        $conn->beginTransaction();

        // 1. Update tabel siswa
        $stmt = $conn->prepare("UPDATE siswa SET nisn = ?, kelas = ?, alamat = ? WHERE id_siswa = ?");
        $stmt->execute([$data['nisn'], $data['kelas'], $data['alamat'], $id]);
        
        // 2. Update tabel users (Nama & Username/NISN)
        $getIds = $conn->prepare("SELECT user_id FROM siswa WHERE id_siswa = ?");
        $getIds->execute([$id]);
        $row = $getIds->fetch();
        
        if ($row) {
             $upUser = $conn->prepare("UPDATE users SET nama_lengkap = ?, username = ? WHERE id_user = ?");
             $upUser->execute([$data['nama'], $data['nisn'], $row['user_id']]);
        }

        $conn->commit();
        return ["success" => true];

    } catch (Exception $e) {
        $conn->rollBack();
        return ["success" => false, "message" => $e->getMessage()];
    }
}

/**
 * Hapus Satu Siswa (Beserta User-nya)
 */
function deleteSiswa($conn, $id_siswa) {
    try {
        $conn->beginTransaction();

        // 1. Ambil user_id sebelum data siswa dihapus
        $stmtGet = $conn->prepare("SELECT user_id FROM siswa WHERE id_siswa = ?");
        $stmtGet->execute([$id_siswa]);
        $row = $stmtGet->fetch();

        if ($row) {
            // 2. Hapus data di tabel SISWA dulu (karena Foreign Key)
            $stmtSiswa = $conn->prepare("DELETE FROM siswa WHERE id_siswa = ?");
            $stmtSiswa->execute([$id_siswa]);

            // 3. Hapus data di tabel USERS (Login)
            $stmtUser = $conn->prepare("DELETE FROM users WHERE id_user = ?");
            $stmtUser->execute([$row['user_id']]);
        }

        $conn->commit();
        return true;
    } catch (PDOException $e) {
        $conn->rollBack();
        return false;
    }
}

/**
 * Hapus Banyak Siswa Sekaligus (Bulk Delete)
 */
function deleteBulkSiswa($conn, $ids_array) {
    try {
        // Looping deleteSiswa agar logic user_id nya tetap jalan
        foreach ($ids_array as $id) {
            deleteSiswa($conn, $id);
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>