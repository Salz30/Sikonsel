<?php
// File: views/admin/laporan/export/generate_pdf.php

// 1. SETUP PATH YANG BENAR (MENGGUNAKAN ABSOLUTE PATH)
// __DIR__ adalah folder 'export'. 
// Mundur 4 kali: export -> laporan -> admin -> views -> public_html (Root)
$root = realpath(__DIR__ . '/../../../../'); 

if (!$root) {
    die("Error: Gagal mendeteksi root folder. Cek struktur direktori.");
}

// 2. INCLUDE FILE PENTING
require_once $root . '/config/database.php';
require_once $root . '/includes/auth.php';
require_once $root . '/includes/encryption.php';

// Cek Login Admin/Guru BK
$user = checkLogin(); 

// 3. LOGIKA PENGAMBILAN DATA (DATABASE)
$laporanList = [];
$isSingle = false;

// Jika ada parameter 'id' di URL, berarti CETAK SATUAN
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isSingle = true;
    $id = $_GET['id'];
    
    // Query Detail (Join dengan Siswa & User)
    $sql = "SELECT l.*, s.nisn, s.kelas, u.nama_lengkap 
            FROM laporan_bk l
            LEFT JOIN siswa s ON l.id_siswa = s.id_siswa
            LEFT JOIN users u ON s.user_id = u.id_user
            WHERE l.id_laporan = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        $data['judul_laporan'] = decryptData($data['judul_laporan']);
        $data['isi_laporan'] = decryptData($data['isi_laporan']);
        $laporanList[] = $data;
    }
} 
// Jika TIDAK ada 'id', berarti CETAK REKAPAN
else {
    $year = $_GET['year'] ?? date('Y');
    $month = $_GET['month'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT l.*, s.nisn, s.kelas, u.nama_lengkap 
            FROM laporan_bk l
            LEFT JOIN siswa s ON l.id_siswa = s.id_siswa
            LEFT JOIN users u ON s.user_id = u.id_user
            WHERE 1=1";
            
    $params = [];
    
    if ($year) {
        $sql .= " AND YEAR(l.tgl_laporan) = ?";
        $params[] = $year;
    }
    if ($month) {
        $sql .= " AND MONTH(l.tgl_laporan) = ?";
        $params[] = $month;
    }
    if ($status) {
        $sql .= " AND l.status = ?";
        $params[] = $status;
    }
    if ($search) {
        $sql .= " AND (u.nama_lengkap LIKE ? OR s.nisn LIKE ?)";
        $term = "%$search%";
        $params[] = $term;
        $params[] = $term;
    }
    
    $sql .= " ORDER BY l.tgl_laporan DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $rawList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($rawList as $row) {
        $row['judul_laporan'] = decryptData($row['judul_laporan']);
        $laporanList[] = $row;
    }
}

$orientation = $isSingle ? 'portrait' : 'landscape';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan - SIKONSEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* --- CSS MAGIC UNTUK MENGHILANGKAN HEADER/FOOTER BROWSER --- */
        @media print {
            @page {
                size: <?php echo $orientation; ?>; 
                margin: 0; /* PENTING: Margin 0 menghilangkan Header/Footer bawaan browser */
            }
            body {
                padding: 20mm; /* Kita pakai padding body sebagai pengganti margin */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            
            /* Agar tabel rekap tidak terpotong jelek */
            tr { page-break-inside: avoid; }
        }
        
        body { font-family: 'Times New Roman', serif; }
        .font-sans { font-family: Arial, sans-serif; }
    </style>
</head>
<body class="bg-white text-black" onload="window.print()">

    <div class="no-print fixed top-4 right-4 flex gap-2 z-50">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow font-sans text-sm font-bold hover:bg-blue-700">🖨️ Cetak PDF</button>
        <button onclick="window.close()" class="bg-gray-500 text-white px-4 py-2 rounded shadow font-sans text-sm font-bold hover:bg-gray-600">Tutup</button>
    </div>

    <header class="text-center mb-8 border-b-4 border-double border-black pb-4">
        <h2 class="text-2xl font-bold uppercase">SMP NEGERI 4 RANCAEKEK</h2>
        <p class="text-sm">Jl. Rancakendal Dua, Linggar, Kec. Rancaekek, Kabupaten Bandung, Jawa Barat 40394</p>
        <p class="text-sm italic">Laporan Bimbingan & Konseling Siswa</p>
    </header>

    <?php if ($isSingle && !empty($laporanList)): $row = $laporanList[0]; ?>
    
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-xl font-bold underline uppercase">LEMBAR PERMASALAHAN SISWA</h1>
            <p class="text-sm">Nomor: BK/<?php echo date('Y', strtotime($row['tgl_laporan'])); ?>/<?php echo $row['id_laporan']; ?></p>
        </div>

        <table class="w-full text-base mb-6">
            <tr>
                <td class="w-40 py-1 font-bold">Nama Siswa</td>
                <td class="w-4">:</td>
                <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? 'Orang Tua/Wali'); ?></td>
            </tr>
            <tr>
                <td class="py-1 font-bold">Kelas / NISN</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($row['kelas'] ?? '-'); ?> / <?php echo htmlspecialchars($row['nisn'] ?? '-'); ?></td>
            </tr>
            <tr>
                <td class="py-1 font-bold">Hari, Tanggal</td>
                <td>:</td>
                <td><?php echo date('l, d F Y', strtotime($row['tgl_laporan'])); ?></td>
            </tr>
            <tr>
                <td class="py-1 font-bold">Kategori</td>
                <td>:</td>
                <td><?php echo htmlspecialchars($row['kategori']); ?></td>
            </tr>
            <tr>
                <td class="py-1 font-bold">Status</td>
                <td>:</td>
                <td>
                    <span class="border border-black px-2 py-0.5 text-sm font-bold">
                        <?php echo strtoupper($row['status']); ?>
                    </span>
                </td>
            </tr>
        </table>

        <div class="mb-6">
            <h3 class="font-bold border-b border-black inline-block mb-2">Pokok Permasalahan:</h3>
            <div class="border border-gray-500 p-4 bg-gray-50 min-h-[60px]">
                <?php echo htmlspecialchars($row['judul_laporan']); ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-bold border-b border-black inline-block mb-2">Uraian / Detail Laporan:</h3>
            <div class="text-justify leading-relaxed whitespace-pre-wrap">
                <?php echo htmlspecialchars($row['isi_laporan']); ?>
            </div>
        </div>

        <?php if (!empty($row['tanggapan_guru'])): ?>
        <div class="mb-6">
            <h3 class="font-bold border-b border-black inline-block mb-2">Tindak Lanjut / Tanggapan Guru BK:</h3>
            <div class="border border-gray-500 p-4 bg-gray-50 min-h-[60px] whitespace-pre-wrap">
                <?php echo htmlspecialchars($row['tanggapan_guru']); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex justify-end mt-16">
            <div class="text-center w-64">
                <p>Rancaekek, <?php echo date('d F Y'); ?></p>
                <p class="mb-20">Guru Bimbingan Konseling,</p>
                <p class="font-bold underline border-b border-black inline-block min-w-[150px]"></p>
                <p class="text-sm mt-1">NIP. ...........................</p>
            </div>
        </div>
    </div>

    <?php else: ?>

    <div class="w-full">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold uppercase">REKAPITULASI LAPORAN KONSELING</h2>
            <p class="text-sm">Periode Cetak: <?php echo date('d F Y'); ?></p>
        </div>

        <?php if (empty($laporanList)): ?>
            <div class="p-8 text-center border border-gray-300 bg-gray-50 text-gray-500 italic">
                Tidak ada data laporan yang ditemukan untuk periode ini.
            </div>
        <?php else: ?>
            <table class="w-full border-collapse border border-black text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-black px-2 py-2 w-10">No</th>
                        <th class="border border-black px-2 py-2 w-24">Tanggal</th>
                        <th class="border border-black px-2 py-2 w-48">Nama Siswa / Kelas</th>
                        <th class="border border-black px-2 py-2 w-24">Kategori</th>
                        <th class="border border-black px-2 py-2">Pokok Permasalahan</th>
                        <th class="border border-black px-2 py-2 w-24">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($laporanList as $row): ?>
                    <tr>
                        <td class="border border-black px-2 py-2 text-center"><?php echo $no++; ?></td>
                        <td class="border border-black px-2 py-2 text-center">
                            <?php echo date('d/m/Y', strtotime($row['tgl_laporan'])); ?>
                        </td>
                        <td class="border border-black px-2 py-2">
                            <div class="font-bold"><?php echo htmlspecialchars($row['nama_lengkap'] ?? 'Orang Tua'); ?></div>
                            <div class="text-xs text-gray-600"><?php echo htmlspecialchars($row['kelas'] ?? '-'); ?></div>
                        </td>
                        <td class="border border-black px-2 py-2 text-center">
                            <?php echo htmlspecialchars($row['kategori']); ?>
                        </td>
                        <td class="border border-black px-2 py-2 text-justify">
                            <?php echo htmlspecialchars($row['judul_laporan']); ?>
                        </td>
                        <td class="border border-black px-2 py-2 text-center font-bold text-xs">
                            <?php 
                                $st = strtoupper($row['status']);
                                if($st == 'SELESAI') echo '✅ SELESAI';
                                elseif($st == 'DIPROSES') echo '🔄 PROSES';
                                else echo '⏳ PENDING';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="mt-4 text-sm font-sans">
                <p><b>Total Data:</b> <?php echo count($laporanList); ?> Laporan</p>
            </div>

            <div class="flex justify-end mt-10">
                <div class="text-center w-60">
                    <p>Mengetahui,</p>
                    <p>Kepala Sekolah / Koordinator BK</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">( ..................................... )</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php endif; ?>

</body>
</html>