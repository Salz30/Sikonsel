<?php
/**
 * Modul Keamanan Informasi (InfoSec)
 * Algoritma: AES-256-CBC
 * REVISI HYBRID: Mendukung Data Terenkripsi (Web) & Plain Text (Mobile)
 */

define('ENCRYPTION_KEY', 'kunci_rahasia_sikonsel_smpn4_rck'); 

/**
 * Fungsi Enkripsi Data
 */
function encryptData($data) {
    $method = "aes-256-cbc";
    $key = ENCRYPTION_KEY;
    
    // Generate IV
    $ivLength = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivLength);
    
    // Enkripsi
    $encrypted = openssl_encrypt($data, $method, $key, 0, $iv);
    
    // Encode Base64
    return base64_encode($iv . $encrypted);
}

/**
 * Fungsi Dekripsi Data (SMART DECRYPT)
 * Logika: Coba dekripsi dulu, jika gagal berarti itu data dari Mobile (Text Biasa)
 */
function decryptData($data) {
    // 1. Simpan data asli untuk cadangan
    $originalData = $data;

    // 2. Cek apakah data kosong?
    if (empty($data)) return "";

    $method = "aes-256-cbc";
    $key = ENCRYPTION_KEY;
    
    try {
        // 3. Coba Decode Base64
        $decoded = base64_decode($data, true);
        
        // Jika bukan Base64 yang valid, berarti pasti Text Biasa (dari Mobile)
        if ($decoded === false) {
            return $originalData;
        }

        $ivLength = openssl_cipher_iv_length($method);
        
        // Cek panjang data cukup untuk IV tidak?
        if (strlen($decoded) <= $ivLength) {
            return $originalData;
        }

        $iv = substr($decoded, 0, $ivLength);
        $ciphertext = substr($decoded, $ivLength);
        
        // 4. Proses Dekripsi
        $decrypted = openssl_decrypt($ciphertext, $method, $key, 0, $iv);

        // 5. PENENTUAN AKHIR:
        // Jika hasil dekripsi FALSE (Gagal), berarti itu Text Biasa.
        // Jika berhasil, kembalikan hasil dekripsi.
        if ($decrypted === false) {
            return $originalData;
        } else {
            return $decrypted;
        }

    } catch (Exception $e) {
        // Jika terjadi error apapun, kembalikan data asli (aman)
        return $originalData;
    }
}
?>