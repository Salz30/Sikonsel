<?php
/**
 * File: Sikonsel/includes/auth.php
 * Solusi: Menggabungkan Session (Utama) dan Cookie (Cadangan/Syarat Ujian)
 */

require_once __DIR__ . '/../config/database.php';

// 1. Mulai Session (Wajib ada di setiap halaman)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kunci Rahasia untuk Enkripsi Cookie (Syarat Keamanan Sederhana)
define('AUTH_KEY', 'kunci_rahasia_sikonsel_2026');

/**
 * Fungsi checkLogin
 * Logika: Cek Session dulu -> Kalau kosong, baru Cek Cookie -> Kalau kosong semua, Tendang.
 */
function checkLogin($redirectPath = '../auth/login.php') {
    global $conn;

    // TAHAP 1: Cek Session (Cara paling cepat & stabil)
    if (isset($_SESSION['user_id'])) {
        // Session valid, user boleh lewat.
        // Opsional: Bisa ambil data terbaru dari DB jika perlu
        return $_SESSION;
    }

    // TAHAP 2: Cek Cookie (Fitur "Remember Me" / Syarat Ujian)
    // Dijalankan HANYA jika Session kosong (misal browser baru dibuka)
    if (isset($_COOKIE['sikonsel_ingat_saya'])) {
        $cookieValue = $_COOKIE['sikonsel_ingat_saya'];
        
        // Dekripsi Cookie sederhana: ID:HASH
        $decoded = base64_decode($cookieValue);
        $parts = explode(':', $decoded);
        
        if (count($parts) === 2) {
            $id_user = $parts[0];
            $hash_validasi = $parts[1];
            
            // Verifikasi keaslian cookie (agar tidak bisa dipalsukan)
            $hash_seharusnya = md5($id_user . AUTH_KEY);
            
            if ($hash_validasi === $hash_seharusnya) {
                // COOKIE VALID! Restore Session (Auto Login)
                // Kita perlu ambil data role dari DB untuk set session
                try {
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
                    $stmt->execute([$id_user]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user) {
                        // Kembalikan nyawa session
                        $_SESSION['user_id'] = $user['id_user'];
                        $_SESSION['role']    = $user['role'];
                        $_SESSION['nama']    = $user['nama_lengkap'];
                        return $user; // Login berhasil via Cookie
                    }
                } catch (Exception $e) {
                    // Ignore error, lanjut redirect
                }
            }
        }
    }

    // TAHAP 3: Gagal Semua (Belum Login)
    // Redirect ke halaman login dengan pesan error
    header("Location: " . $redirectPath . "?msg=belum_login");
    exit();
}

/**
 * Fungsi setAppLogin (PENTING!)
 * Panggil fungsi ini di login.php saat username & password benar.
 * Fungsi ini yang akan membuat Session DAN Cookie sekaligus.
 */
function setAppLogin($user) {
    // 1. Set Session (Wajib)
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['nama']    = $user['nama_lengkap'];
    
    // 2. Set Cookie (Syarat Ujian)
    // Format: ID_USER : MD5(ID + KUNCI) -> di-Base64 biar rapi
    $cookieIsi = base64_encode($user['id_user'] . ':' . md5($user['id_user'] . AUTH_KEY));
    
    // Simpan Cookie selama 30 Hari
    setcookie('sikonsel_ingat_saya', $cookieIsi, time() + (86400 * 30), "/");
}

/**
 * Fungsi logout
 * Hapus Session dan Cookie sampai bersih.
 */
function logout() {
    // Hapus Session
    session_unset();
    session_destroy();
    
    // Hapus Cookie
    setcookie('sikonsel_ingat_saya', '', time() - 3600, "/");
}
?>