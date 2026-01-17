<?php
// File: Sikonsel/views/admin/siswa/download_template.php

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=template_siswa_siap_isi.csv');

$output = fopen('php://output', 'w');

// Tambahkan BOM agar Excel membaca karakter dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// HEADER KOLOM (Pemisah Titik Koma ';')
fputcsv($output, array('NISN', 'Nama Lengkap', 'Kelas', 'Alamat', 'Nama Ortu', 'No HP Ortu'), ';');

// DATA CONTOH 1
// Perhatikan: Angka diberi tanda kutip satu (') di depan agar Excel membacanya sebagai TEXT
// Ini menjaga agar angka 0 di depan (0812...) TIDAK HILANG.
fputcsv($output, array("'1234567890", 'Budi Santoso', 'VII-A', 'Jl. Melati No 1', 'Pak Santoso', "'08123456789"), ';');

// DATA CONTOH 2
fputcsv($output, array("'0987654321", 'Siti Aminah', 'VIII-B', 'Jl. Mawar No 2', 'Bu Aminah', "'08129876543"), ';');

fclose($output);
exit;
?>