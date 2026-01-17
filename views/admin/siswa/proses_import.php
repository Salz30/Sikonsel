<?php
// File: Sikonsel/views/admin/siswa/proses_import.php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';
checkLogin();

// Fungsi Pembantu: Membersihkan Angka
function bersihkanAngka($string) {
    // Hanya ambil karakter angka 0-9. Buang huruf, spasi, tanda petik ('), strip (-), dll.
    return preg_replace('/[^0-9]/', '', $string);
}

if (isset($_POST['import']) && isset($_FILES['file_csv'])) {
    
    $file = $_FILES['file_csv']['tmp_name'];
    $ext  = pathinfo($_FILES['file_csv']['name'], PATHINFO_EXTENSION);

    if (strtolower($ext) !== 'csv') {
        echo "<script>alert('Format file harus .csv!'); window.location='list_siswa.php';</script>";
        exit;
    }

    // 1. Deteksi Pemisah (Koma atau Titik Koma)
    $handle = fopen($file, "r");
    $firstLine = fgets($handle);
    fclose($handle);

    $delimiter = ','; 
    if (strpos($firstLine, ';') !== false) {
        $delimiter = ';'; 
    }

    // 2. Mulai Proses Data
    $handle = fopen($file, "r");
    
    $sukses = 0;
    $gagal  = 0;
    $baris  = 0;

    while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        $baris++;
        
        // Skip Header
        if ($baris == 1) continue;
        
        // Bersihkan karakter BOM jika ada di awal file
        $rawNisn = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim($row[0]));

        // --- PEMBERSIHAN DATA (SANITASI) ---
        // Bersihkan NISN & No HP dari tanda petik, spasi, atau huruf
        $cleanNISN = bersihkanAngka($rawNisn);
        $cleanHP   = bersihkanAngka($row[5] ?? '');

        // Mapping Data
        $data = [
            'nisn'       => $cleanNISN,
            'nama'       => trim($row[1] ?? ''), // Nama biarkan ada spasi/huruf
            'kelas'      => trim($row[2] ?? ''),
            'alamat'     => trim($row[3] ?? ''),
            'nama_ortu'  => trim($row[4] ?? ''),
            'no_hp_ortu' => $cleanHP
        ];

        // Validasi: NISN & Nama Wajib Ada
        if (empty($data['nisn']) || empty($data['nama'])) {
            continue; 
        }

        // Panggil Controller (tambahSiswa sudah punya validasi angka & pass default)
        $result = tambahSiswa($conn, $data);

        if ($result['success']) {
            $sukses++;
        } else {
            $gagal++;
        }
    }

    fclose($handle);

    echo "<script>
        alert('Import Selesai!\\nSukses: $sukses Data\\nGagal: $gagal Data (Kemungkinan NISN duplikat).');
        window.location='list_siswa.php';
    </script>";
}
?>