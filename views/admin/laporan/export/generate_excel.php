<?php
/**
 * Generate Excel Laporan BK
 * Mendukung: Filter Status, Nama, dan Bulanan (Sinkron dengan export.php).
 */
require_once '../../../../includes/auth.php';
require_once '../../../../includes/laporan_controller.php';

$user = checkLogin();
if ($user['role'] !== 'guru_bk') {
    header("Location: ../../sikonsel.php");
    exit;
}

// Ambil Parameter Filter dari URL (GET) agar hasil download sesuai dengan apa yang difilter di layar
$filter_status = $_GET['status'] ?? null;
$filter_search = $_GET['search'] ?? null;
$filter_month  = $_GET['month'] ?? null;
$filter_year   = $_GET['year'] ?? date('Y');

// Query Filtered Data
$query = "SELECT laporan_bk.*, siswa.nisn, users.nama_lengkap as nama_siswa 
          FROM laporan_bk 
          JOIN siswa ON laporan_bk.id_siswa = siswa.id_siswa
          JOIN users ON siswa.user_id = users.id_user
          WHERE 1=1";
$params = [];

if ($filter_status) {
    $query .= " AND laporan_bk.status = ?";
    $params[] = $filter_status;
}
if ($filter_search) {
    $query .= " AND users.nama_lengkap LIKE ?";
    $params[] = "%$filter_search%";
}
if ($filter_month) {
    $query .= " AND MONTH(laporan_bk.tgl_laporan) = ? AND YEAR(laporan_bk.tgl_laporan) = ?";
    $params[] = $filter_month;
    $params[] = $filter_year;
}

$query .= " ORDER BY laporan_bk.tgl_laporan DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$laporanList = $stmt->fetchAll();

// Header Excel agar browser mendownload file sebagai .xls
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Rekap_BK_Sikonsel_".date('Ymd_His').".xls");
header("Pragma: no-cache");
header("Expires: 0");

/**
 * Trik Excel: Tabel HTML akan otomatis dibaca sebagai baris dan kolom oleh Excel.
 */
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="8" style="font-size: 14pt; font-weight: bold; text-align: center;">REKAPITULASI LAPORAN BIMBINGAN KONSELING</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-weight: bold;">SMP NEGERI 4 RANCAEKEK</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 10pt;">Tanggal Unduh: <?php echo date('d-m-Y H:i:s'); ?></th>
        </tr>
        <tr><th colspan="8"></th></tr>
        <tr style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">
            <th>No</th>
            <th>Tanggal Laporan</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            <th>Kategori</th>
            <th>Judul Masalah</th>
            <th>Isi Pesan (Teks Asli)</th>
            <th>Status Akhir</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        if (empty($laporanList)) {
            echo "<tr><td colspan='8' style='text-align:center;'>Tidak ada data laporan ditemukan.</td></tr>";
        } else {
            foreach ($laporanList as $row): 
                // Dekripsi data secara otomatis untuk laporan Excel
                $isi_asli = decryptData($row['isi_laporan']);
        ?>
        <tr>
            <td style="text-align: center;"><?php echo $no++; ?></td>
            <td style="text-align: center;"><?php echo date('d/m/Y H:i', strtotime($row['tgl_laporan'])); ?></td>
            <td style="mso-number-format:'\@';"><?php echo $row['nisn']; ?></td> <!-- Trik agar NISN tidak terbaca angka saintifik -->
            <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
            <td><?php echo htmlspecialchars($row['judul_laporan']); ?></td>
            <td><?php echo htmlspecialchars($isi_asli); ?></td>
            <td style="text-align: center; font-weight: bold;"><?php echo strtoupper($row['status']); ?></td>
        </tr>
        <?php endforeach; 
        } ?>
    </tbody>
</table>