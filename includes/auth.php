<?php
/**
 * Auth System - HYBRID MODE
 * Otomatis menyesuaikan keamanan berdasarkan lingkungan (Localhost vs Hosting)
 */
require_once __DIR__ . '/../config/database.php';

define('SECRET_KEY', 'sikonsel_rancaekek_secure_key_2024');

// Deteksi apakah sedang di Localhost
function isLocalhost() {
    return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || $_SERVER['SERVER_NAME'] === 'localhost';
}

function generateToken($data) {
    // SECURITY LEVEL:
    // Jika di Hosting: Simpan User Agent (Sidik Jari Browser)
    // Jika di Localhost: Abaikan (Supaya tidak gampang logout sendiri saat develop)
    if (!isLocalhost()) {
        $data['ua'] = $_SERVER['HTTP_USER_AGENT'];
    }

    $payload = json_encode($data);
    $signature = hash_hmac('sha256', $payload, SECRET_KEY);
    return base64_encode($payload . '.' . $signature);
}

function verifyToken($token) {
    $decoded = base64_decode($token);
    if (!$decoded) return false;

    $parts = explode('.', $decoded);
    if (count($parts) != 2) return false;

    $payload = $parts[0];
    $signature = $parts[1];

    $expected_signature = hash_hmac('sha256', $payload, SECRET_KEY);
    if (!hash_equals($expected_signature, $signature)) {
        return false;
    }

    $data = json_decode($payload, true);
    if (!isset($data['exp']) || $data['exp'] < time()) {
        return false;
    }

    // Validasi User Agent hanya dilakukan jika BUKAN localhost
    if (!isLocalhost()) {
        if (!isset($data['ua']) || $data['ua'] !== $_SERVER['HTTP_USER_AGENT']) {
            return false; // Anti-Session Hijacking Aktif di Hosting
        }
    }

    return $data;
}

function login($username, $password, $conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $data = [
                'user_id' => $user['id_user'],
                'username' => $user['username'],
                'role' => $user['role'],
                'nama' => $user['nama_lengkap'],
                'exp' => time() + (86400 * 1)
            ];
            
            $token = generateToken($data);
            
            // --- PENGATURAN COOKIE CERDAS ---
            if (isLocalhost()) {
                // MODE LOCALHOST: Santai, yang penting bisa login
                setcookie('sikonsel_session', $token, time() + 86400, '/');
            } else {
                // MODE HOSTING (Production): Keamanan Penuh!
                // Syarat: Hosting wajib sudah HTTPS (Gembok Hijau)
                setcookie('sikonsel_session', $token, [
                    'expires' => time() + 86400,
                    'path' => '/',
                    'domain' => $_SERVER['HTTP_HOST'], // Mengunci cookie ke domain sekolah
                    'secure' => true,     // Cookie hanya dikirim lewat HTTPS
                    'httponly' => true,   // Anti-XSS (JavaScript tidak bisa baca cookie)
                    'samesite' => 'Strict' // Anti-CSRF tingkat tinggi
                ]);
            }

            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

function checkLogin($loginPath = '../views/login.php') {
    if (!isset($_COOKIE['sikonsel_session'])) {
        header("Location: " . $loginPath);
        exit;
    }

    $userData = verifyToken($_COOKIE['sikonsel_session']);
    
    if (!$userData) {
        setcookie('sikonsel_session', '', time() - 3600, '/');
        $separator = (strpos($loginPath, '?') !== false) ? '&' : '?';
        header("Location: " . $loginPath . $separator . "msg=session_expired");
        exit;
    }
    
    return $userData;
}

function logout() {
    setcookie('sikonsel_session', '', time() - 3600, '/');
    header("Location: login.php");
    exit;
}
?>