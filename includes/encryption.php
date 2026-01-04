<?php
/**
 * Modul Keamanan Informasi (InfoSec)
 * Algoritma: AES-256-CBC
 */

// Kunci Rahasia (Harusnya disimpan di Environment Variable, tapi untuk tugas kita taruh sini)
// Kunci harus 32 karakter untuk AES-256
define('ENCRYPTION_KEY', 'kunci_rahasia_sikonsel_smpn4_rck'); 

/**
 * Fungsi Enkripsi Data
 */
function encryptData($data) {
    $method = "aes-256-cbc";
    $key = ENCRYPTION_KEY;
    
    // Generate Initialization Vector (IV) secara acak - Penting agar hasil enkripsi selalu beda
    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);
    
    // Proses Enkripsi
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    
    // Gabungkan IV dan Ciphertext lalu encode ke Base64 agar bisa disimpan di DB
    return base64_encode($iv . $encrypted);
}

/**
 * Fungsi Dekripsi Data
 */
function decryptData($data) {
    $method = "aes-256-cbc";
    $key = ENCRYPTION_KEY;
    
    // Decode dari Base64
    $data = base64_decode($data);
    $ivLength = openssl_cipher_iv_length($method);
    
    // Pisahkan IV dan Ciphertext
    $iv = substr($data, 0, $ivLength);
    $ciphertext = substr($data, $ivLength);
    
    // Proses Dekripsi
    return openssl_decrypt($ciphertext, $method, $key, 0, $iv);
}
?>