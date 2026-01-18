<?php
/**
 * Konfigurasi Database Sikonsel
 * Menggunakan PDO untuk keamanan (mencegah SQL Injection)
 */

$host = "localhost";
$db_name = "db_sikonsel_rancaekek";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set mode error ke Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode ke associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
