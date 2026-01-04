<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Ambil Reservasi milik Siswa tertentu
 */
function getReservasiBySiswa($conn, $id_siswa) {
    $sql = "SELECT * FROM reservasi WHERE id_siswa = ? ORDER BY tgl_temu DESC, jam_temu ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_siswa]);
    return $stmt->fetchAll();
}

/**
 * Ajukan Reservasi Baru
 */
function tambahReservasi($conn, $data) {
    try {
        $sql = "INSERT INTO reservasi (id_siswa, tgl_temu, jam_temu, keperluan, status) 
                VALUES (?, ?, ?, ?, 'Menunggu')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['id_siswa'],
            $data['tanggal'],
            $data['jam'],
            $data['keperluan']
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Ambil Semua Reservasi (Untuk Guru BK)
 */
function getAllReservasi($conn) {
    // Join dengan tabel siswa & users untuk dapat nama siswa
    $sql = "SELECT reservasi.*, users.nama_lengkap as nama_siswa, siswa.kelas 
            FROM reservasi 
            JOIN siswa ON reservasi.id_siswa = siswa.id_siswa
            JOIN users ON siswa.user_id = users.id_user
            ORDER BY 
                FIELD(reservasi.status, 'Menunggu', 'Disetujui', 'Ditolak', 'Selesai'),
                reservasi.tgl_temu ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Update Status Reservasi (Terima/Tolak)
 */
function updateStatusReservasi($conn, $id, $status, $catatan) {
    try {
        $sql = "UPDATE reservasi SET status = ?, catatan_guru = ? WHERE id_reservasi = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$status, $catatan, $id]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

?>