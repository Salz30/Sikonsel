<?php
// File: Sikonsel/includes/reservasi_controller.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/fcm_sender.php'; // WAJIB: Panggil file pengirim

// Fungsi Ambil Semua Reservasi (Untuk Admin)
function getAllReservasi($conn) {
    $sql = "SELECT r.*, s.nisn, s.kelas, u.nama_lengkap 
            FROM reservasi r
            JOIN siswa s ON r.id_siswa = s.id_siswa
            JOIN users u ON s.user_id = u.id_user
            ORDER BY r.tgl_temu DESC, r.jam_temu DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi Update Status & Kirim Notif
function updateStatusReservasi($conn, $id_reservasi, $status, $catatan) {
    try {
        // 1. Update Database
        $sql = "UPDATE reservasi SET status = ?, catatan_guru = ? WHERE id_reservasi = ?";
        $stmt = $conn->prepare($sql);
        $berhasil = $stmt->execute([$status, $catatan, $id_reservasi]);

        // 2. Jika Update Sukses, Kirim Notifikasi ke Siswa
        if ($berhasil) {
            // Ambil Token HP Siswa
            $sqlToken = "SELECT u.fcm_token 
                         FROM reservasi r
                         JOIN siswa s ON r.id_siswa = s.id_siswa
                         JOIN users u ON s.user_id = u.id_user
                         WHERE r.id_reservasi = ?";
            $stmtToken = $conn->prepare($sqlToken);
            $stmtToken->execute([$id_reservasi]);
            $user = $stmtToken->fetch(PDO::FETCH_ASSOC);

            if ($user && !empty($user['fcm_token'])) {
                $judul = "Update Jadwal Konseling";
                $pesan = "Status jadwal Anda sekarang: " . strtoupper($status) . ". Cek aplikasi untuk detailnya.";
                
                // Kirim Notifikasi (Fungsi dari fcm_sender.php)
                sendPushNotification($user['fcm_token'], $judul, $pesan);
            }
        }
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Fungsi Ambil Reservasi per Siswa (Untuk Profil Detail)
function getReservasiBySiswa($conn, $id_siswa) {
    $sql = "SELECT * FROM reservasi WHERE id_siswa = ? ORDER BY tgl_temu DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_siswa]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi Insert Reservasi Baru (Untuk Siswa Buat Janji)
function insertReservasi($conn, $id_siswa, $tanggal, $jam, $keperluan) {
    try {
        $status = "Menunggu";
        $created_at = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO reservasi (id_siswa, tgl_temu, jam_temu, keperluan, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_siswa, $tanggal, $jam, $keperluan, $status, $created_at]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>