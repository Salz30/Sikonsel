<?php
// File: Sikonsel/includes/siswa_controller.php
require_once __DIR__ . '/../config/database.php';

function getSiswaById($conn, $id) {
    $sql = "SELECT siswa.*, users.nama_lengkap, users.username 
            FROM siswa 
            JOIN users ON siswa.user_id = users.id_user 
            WHERE siswa.id_siswa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function updateSiswa($conn, $id, $data) {
    try {
        $conn->beginTransaction();
        if (!is_numeric($data['nisn'])) {
            return ["success" => false, "message" => "NISN harus berupa angka!"];
        }

        $stmtSiswa = $conn->prepare("UPDATE siswa SET nisn = ?, kelas = ?, alamat = ?, nama_ortu = ?, no_hp_ortu = ? WHERE id_siswa = ?");
        $stmtSiswa->execute([
            $data['nisn'], 
            $data['kelas'], 
            $data['alamat'], 
            $data['nama_ortu'],   
            $data['no_hp_ortu'],  
            $id
        ]);
        
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

function getAllSiswa($conn) {
    try {
        $sql = "SELECT s.*, u.username, u.nama_lengkap 
                FROM siswa s 
                JOIN users u ON s.user_id = u.id_user 
                ORDER BY u.nama_lengkap ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function deleteSiswa($conn, $id_siswa) {
    try {
        $stmt = $conn->prepare("SELECT user_id FROM siswa WHERE id_siswa = ?");
        $stmt->execute([$id_siswa]);
        $row = $stmt->fetch();

        if ($row) {
            $del = $conn->prepare("DELETE FROM users WHERE id_user = ?");
            return $del->execute([$row['user_id']]);
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

function deleteBulkSiswa($conn, $ids) {
    try {
        $conn->beginTransaction(); 
        foreach ($ids as $id) {
            deleteSiswa($conn, $id);
        }
        $conn->commit(); 
        return true;
    } catch (Exception $e) {
        $conn->rollBack(); 
        return false;
    }
}

// --- MODIFIKASI UTAMA DI SINI ---
function tambahSiswa($conn, $data) {
    try {
        $conn->beginTransaction();

        // 1. VALIDASI BACKEND: Pastikan NISN hanya angka
        if (!is_numeric($data['nisn'])) {
            return ["success" => false, "message" => "NISN harus berupa angka!"];
        }

        // 2. Cek Username (NISN) Kembar
        $cek = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
        $cek->execute([$data['nisn']]);
        if ($cek->fetch()) {
            return ["success" => false, "message" => "NISN sudah terdaftar!"];
        }

        // 3. KONSEP BARU: Password Default '123456'
        $defaultPassword = '123456'; 
        $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        $role = 'siswa';
        
        // Insert ke tabel USERS
        $sqlUser = "INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->execute([
            $data['nisn'],
            $passwordHash, // Password hash dari '123456'
            $role,
            $data['nama']
        ]);
        
        $newUserId = $conn->lastInsertId();

        // Insert ke tabel SISWA
        $sqlSiswa = "INSERT INTO siswa (user_id, nisn, kelas, alamat, nama_ortu, no_hp_ortu) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtSiswa = $conn->prepare($sqlSiswa);
        $stmtSiswa->execute([
            $newUserId,
            $data['nisn'],
            $data['kelas'],
            $data['alamat'],
            $data['nama_ortu'] ?? null, 
            $data['no_hp_ortu'] ?? null
        ]);

        $conn->commit();
        return ["success" => true];

    } catch (Exception $e) {
        $conn->rollBack();
        return ["success" => false, "message" => "Error Sistem: " . $e->getMessage()];
    }
}