<?php
// File: Sikonsel/index.php
require_once 'includes/auth.php';

// Cek Login (arahkan ke views/auth/login.php jika belum)
$user = checkLogin('views/auth/login.php');

// Redirect berdasarkan Role
if ($user['role'] == 'guru_bk') {
    header("Location: views/admin/dashboard_admin.php");
} else {
    header("Location: views/siswa/dashboard_siswa.php");
}
exit;
?>