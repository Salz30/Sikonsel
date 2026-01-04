<?php
/**
 * Script untuk mereset/membuat user admin
 * Jalankan file ini SEKALI saja lewat browser
 */
require_once 'config/database.php';

$username = 'admin_bk';
$password_plain = 'admin123';
$role = 'guru_bk';
$nama = 'Ibu Guru BK';

// Hash password baru
$password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

try {
    // Cek apakah user sudah ada
    $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        // Update user yang ada
        $stmt = $conn->prepare("UPDATE users SET password = ?, role = ?, nama_lengkap = ? WHERE username = ?");
        $stmt->execute([$password_hash, $role, $nama, $username]);
        echo "<h1 style='color:green'>Sukses! User '$username' berhasil di-update.</h1>";
    } else {
        // Buat user baru
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, nama_lengkap) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password_hash, $role, $nama]);
        echo "<h1 style='color:green'>Sukses! User '$username' berhasil dibuat.</h1>";
    }
    
    echo "<p>Password saat ini: <strong>$password_plain</strong></p>";
    echo "<p><a href='views/login.php'>Klik di sini untuk Login</a></p>";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}