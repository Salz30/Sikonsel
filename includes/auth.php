<?php
/**
 * File: Sikonsel/includes/auth.php
 * Solusi: Murni menggunakan Session dengan fitur Auto-Logout 10 Menit.
 * Keamanan: Menghapus fitur cookie permanen untuk mencegah akses tidak sah.
 */

require_once __DIR__ . '/../config/database.php';

// 1. Mulai Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Fungsi checkLogin
 * Logika: Cek Timeout -> Cek Session -> Jika gagal, Tendang.
 */
function checkLogin($redirectPath = '../auth/login.php') {
    // --- LOGIKA TIMEOUT 5 MENIT (300 DETIK) ---
    $timeout_duration = 300; 
    
    if (isset($_SESSION['user_id'])) {
        if (isset($_SESSION['last_activity'])) {
            $elapsed_time = time() - $_SESSION['last_activity'];
            if ($elapsed_time > $timeout_duration) {
                // Sesi kedaluwarsa karena tidak ada aktivitas
                logout();
                header("Location: " . $redirectPath . "?msg=sesi_berakhir");
                exit();
            }
        }
        // Perbarui waktu aktivitas terakhir setiap kali halaman diakses
        $_SESSION['last_activity'] = time();
        return $_SESSION;
    }
    // -------------------------------------------

    // Jika tidak ada session user_id, redirect ke login
    header("Location: " . $redirectPath . "?msg=belum_login");
    exit();
}

/**
 * Fungsi setAppLogin
 * Hanya membuat Session dan mencatat waktu awal aktivitas.
 */
function setAppLogin($user) {
    // Set data identitas ke dalam Session
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['nama']    = $user['nama_lengkap'];
    
    // Set waktu awal aktivitas untuk logika timeout
    $_SESSION['last_activity'] = time();
}

/**
 * Fungsi logout
 * Menghapus semua data sesi sampai bersih.
 */
function logout() {
    // Hapus semua variabel session
    $_SESSION = array();

    // Hapus cookie session (PHPSESSID) dari browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Hancurkan session di server
    session_destroy();
}
?>