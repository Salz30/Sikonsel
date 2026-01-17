<?php
/**
 * File: Sikonsel/views/auth/logout.php
 * Fungsi: Menghapus sesi dan mengembalikan user ke halaman login.
 */

// 1. Panggil fungsi logout dari auth.php
require_once '../../includes/auth.php';

// 2. Jalankan pembersihan sesi & cookie
logout();

// 3. REDIRECT MANUAL (Penting! Agar tidak blank putih)
// Karena file ini satu folder dengan login.php, kita bisa langsung panggil namanya.
header("Location: login.php");
exit;
?>