<?php
// File: api/login.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Aktifkan error reporting sementara untuk debugging
ini_set('display_errors', 1);
error_reporting(0);

// Cek file dependensi sebelum diload
if (!file_exists('../config/database.php')) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "File database.php tidak ditemukan di server."]);
    exit;
}

require_once '../config/database.php';
// require_once '../includes/auth.php'; // Opsional, jika error hapus baris ini

$input = file_get_contents("php://input");
$data = json_decode($input);

// Debugging: Cek apakah JSON masuk
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Format JSON tidak valid dari aplikasi."]);
    exit;
}

$username = $data->username ?? '';
$password = $data->password ?? '';
$fcm_token = $data->fcm_token ?? '';

if (!empty($username) && !empty($password)) {
    try {
        // 1. Cek User
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            // 2. Update Token FCM
            if (!empty($fcm_token)) {
                $updateToken = $conn->prepare("UPDATE users SET fcm_token = :token WHERE id_user = :id");
                $updateToken->execute([':token' => $fcm_token, ':id' => $user['id_user']]);
            }

            // 3. Cek Role
            if ($user['role'] !== 'siswa') {
                echo json_encode(["success" => false, "message" => "Akun ini bukan akun Siswa."]);
                exit;
            }

            // 4. Cari ID Siswa
            $stmtSiswa = $conn->prepare("SELECT id_siswa, nama_lengkap FROM siswa WHERE user_id = :uid");
            $stmtSiswa->bindParam(':uid', $user['id_user']);
            $stmtSiswa->execute();
            $dataSiswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);
            
            // Fallback jika data siswa belum lengkap tapi user login
            $id_siswa_fix = $dataSiswa ? $dataSiswa['id_siswa'] : 0;
            $nama_fix = $dataSiswa ? $dataSiswa['nama_lengkap'] : $user['nama_lengkap'];

            // 5. Generate Token Sederhana
            $token = bin2hex(random_bytes(16));

            echo json_encode([
                "success" => true,
                "message" => "Login berhasil!",
                "data"    => [
                    "token" => $token,
                    "nama"  => $nama_fix,
                    "nisn"  => $user['username'],
                    "id_siswa" => $id_siswa_fix
                ]
            ]);

        } else {
            echo json_encode(["success" => false, "message" => "Username atau Password salah."]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap (Username/Password kosong)."]);
}
?>