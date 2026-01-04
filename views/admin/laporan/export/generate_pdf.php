<?php
/**
 * Generate PDF (Print View) Laporan BK
 * Mendukung: Cetak Individu, Cetak Bulanan, dan Filter Status/Nama.
 */
require_once '../../../../includes/auth.php';
require_once '../../../../includes/laporan_controller.php';

$user = checkLogin();
if ($user['role'] !== 'guru_bk') {
    header("Location: ../../../../index.php");
    exit;
}

// Ambil Parameter Filter dari URL (GET)
$id_single = $_GET['id'] ?? null;
$filter_status = $_GET['status'] ?? null;
$filter_search = $_GET['search'] ?? null;
$filter_month  = $_GET['month'] ?? null; // format: 01-12
$filter_year   = $_GET['year'] ?? date('Y');

$laporanList = [];
$is_single = false;

// LOGIKA PENGAMBILAN DATA
if ($id_single) {
    // Kasus 1: Cetak Satu Laporan Spesifik (Individu)
    $report = getLaporanById($conn, $id_single);
    if ($report) {
        $laporanList[] = $report;
        $is_single = true;
    }
} else {
    // Kasus 2: Cetak Daftar dengan Filter (Bulanan/Status/Nama)
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
}

// Helper Nama Bulan
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $is_single ? 'Laporan_Individu_Sikonsel' : 'Rekapitulasi_BK_Sikonsel'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media print {
            /* Sembunyikan Header dan Footer default browser (URL, Tanggal, Folder) */
            @page { 
                margin: 0; 
            }
            .no-print { 
                display: none !important; 
            }
            body { 
                background-color: white !important; 
                margin: 0 !important; 
                padding: 0 !important; 
            }
            .print-container { 
                box-shadow: none !important; 
                border: none !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                margin: 0 !important; 
                padding: 2cm !important; 
                min-height: 100vh;
            }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 md:p-10">

    <!-- Header Navigasi (Hanya muncul di Layar) -->
    <div class="no-print max-w-5xl mx-auto mb-8 flex flex-col md:flex-row justify-between items-center bg-blue-600 text-white p-5 rounded-2xl shadow-xl">
        <div class="mb-4 md:mb-0">
            <h1 class="font-bold text-lg">Pratinjau Cetak Laporan</h1>
            <p class="text-xs opacity-90">Lokasi file dan header browser telah disembunyikan secara otomatis.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-white text-blue-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-md active:scale-95 transition-all hover:bg-blue-50">
                Cetak / Simpan PDF
            </button>
            <a href="center.php" class="bg-blue-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-400 border border-blue-400 transition-all">
                Kembali
            </a>
        </div>
    </div>

    <!-- Kontainer Dokumen (Bagian yang akan dicetak) -->
    <div class="print-container max-w-5xl mx-auto bg-white p-12 md:p-16 shadow-2xl rounded-sm border border-slate-200">
        
        <!-- Kop Surat -->
        <div class="text-center border-b-4 border-double border-slate-800 pb-6 mb-10 flex items-center justify-center gap-8">
            <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-4xl font-bold">S</div>
            <div class="text-left">
                <h2 class="text-2xl font-bold uppercase tracking-tighter">SMP NEGERI 4 RANCAEKEK</h2>
                <p class="text-sm font-semibold italic text-slate-600">Layanan Bimbingan dan Konseling (BK) Digital</p>
                <p class="text-xs text-slate-400 mt-1">Jl. Rancaekek Kencana No.4, Kabupaten Bandung, Jawa Barat.</p>
            </div>
        </div>

        <?php if ($is_single): $report = $laporanList[0]; ?>
            <!-- TAMPILAN CETAK INDIVIDU -->
            <h3 class="text-center text-xl font-bold uppercase mb-10 underline decoration-2 underline-offset-8">LAPORAN HASIL KONSELING INDIVIDU</h3>
            
            <div class="grid grid-cols-4 gap-y-4 text-sm mb-10">
                <div class="font-bold">Nama Siswa</div><div class="col-span-3">: <?php echo htmlspecialchars($report['nama_siswa']); ?></div>
                <div class="font-bold">NISN</div><div class="col-span-3">: <?php echo htmlspecialchars($report['nisn']); ?></div>
                <div class="font-bold">Kategori</div><div class="col-span-3">: <?php echo htmlspecialchars($report['kategori']); ?></div>
                <div class="font-bold">Tanggal</div><div class="col-span-3">: <?php echo date('d F Y', strtotime($report['tgl_laporan'])); ?></div>
                <div class="font-bold">Status Laporan</div><div class="col-span-3">: <?php echo htmlspecialchars($report['status']); ?></div>
            </div>

            <div class="mb-10">
                <p class="font-bold text-sm mb-2">Pokok Permasalahan:</p>
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg italic text-sm">
                    "<?php echo htmlspecialchars($report['judul_laporan']); ?>"
                </div>
            </div>

            <div class="mb-10">
                <p class="font-bold text-sm mb-2">Uraian / Detail Laporan (Terdekripsi):</p>
                <div class="text-sm leading-relaxed text-justify whitespace-pre-wrap min-h-[200px] p-6 border border-slate-100 rounded-xl bg-white shadow-inner">
                    <?php echo nl2br(htmlspecialchars($report['isi_laporan_dekripsi'])); ?>
                </div>
            </div>

        <?php else: ?>
            <!-- TAMPILAN CETAK DAFTAR / BULANAN -->
            <h3 class="text-center text-xl font-bold uppercase mb-4 underline decoration-2 underline-offset-8">REKAPITULASI LAPORAN BK</h3>
            <p class="text-center text-sm font-bold text-slate-600 mb-8">
                <?php 
                    if ($filter_month) echo "Periode: " . $nama_bulan[$filter_month] . " " . $filter_year;
                    elseif ($filter_status) echo "Status Penanganan: " . $filter_status;
                    elseif ($filter_search) echo "Pencarian Nama: " . htmlspecialchars($filter_search);
                    else echo "Seluruh Data Laporan";
                ?>
            </p>

            <table class="w-full text-[10px] border-collapse border border-slate-400">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="border border-slate-400 p-2 w-8 text-center">No</th>
                        <th class="border border-slate-400 p-2 w-24 text-center">Tanggal</th>
                        <th class="border border-slate-400 p-2 w-32">Identitas Siswa</th>
                        <th class="border border-slate-400 p-2">Masalah & Cuplikan Laporan</th>
                        <th class="border border-slate-400 p-2 w-20 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporanList)): ?>
                        <tr><td colspan="5" class="p-4 text-center">Data tidak ditemukan sesuai filter.</td></tr>
                    <?php else: $no = 1; foreach ($laporanList as $row): $isi = decryptData($row['isi_laporan']); ?>
                        <tr>
                            <td class="border border-slate-400 p-2 text-center"><?php echo $no++; ?></td>
                            <td class="border border-slate-400 p-2 text-center"><?php echo date('d/m/Y', strtotime($row['tgl_laporan'])); ?></td>
                            <td class="border border-slate-400 p-2">
                                <span class="font-bold"><?php echo htmlspecialchars($row['nama_siswa']); ?></span><br>
                                <span class="text-[8px] text-slate-500">NISN: <?php echo htmlspecialchars($row['nisn']); ?></span>
                            </td>
                            <td class="border border-slate-400 p-2 text-justify">
                                <span class="font-bold"><?php echo htmlspecialchars($row['judul_laporan']); ?></span>
                                <span class="text-[8px] italic">(<?php echo htmlspecialchars($row['kategori']); ?>)</span><br>
                                <p class="mt-1 opacity-80"><?php echo nl2br(htmlspecialchars(substr($isi, 0, 150))); ?>...</p>
                            </td>
                            <td class="border border-slate-400 p-2 text-center font-bold text-[9px]"><?php echo strtoupper($row['status']); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>

            <?php if ($filter_month && !empty($laporanList)): ?>
                <!-- Ringkasan Statistik Laporan Bulanan -->
                <div class="mt-8 grid grid-cols-3 gap-4 no-print-inside">
                    <div class="p-3 border border-slate-200 text-center">
                        <p class="text-[10px] uppercase text-slate-400 font-bold">Total Laporan</p>
                        <p class="text-xl font-bold"><?php echo count($laporanList); ?></p>
                    </div>
                    <div class="p-3 border border-slate-200 text-center">
                        <p class="text-[10px] uppercase text-slate-400 font-bold">Terselesaikan</p>
                        <p class="text-xl font-bold">
                            <?php echo count(array_filter($laporanList, fn($x) => $x['status'] == 'Selesai')); ?>
                        </p>
                    </div>
                    <div class="p-3 border border-slate-200 text-center">
                        <p class="text-[10px] uppercase text-slate-400 font-bold">Belum Tuntas</p>
                        <p class="text-xl font-bold">
                            <?php echo count(array_filter($laporanList, fn($x) => $x['status'] !== 'Selesai')); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="mt-20 flex justify-end">
            <div class="text-center w-72">
                <p class="text-sm mb-20">Rancaekek, <?php echo date('d F Y'); ?></p>
                <p class="font-bold border-b border-slate-800 pb-1"><?php echo htmlspecialchars($user['nama']); ?></p>
                <p class="text-xs text-slate-500 mt-1 uppercase font-bold tracking-widest">Guru Bimbingan Konseling</p>
                <p class="text-[10px] text-slate-400 mt-1">NIP. .....................................</p>
            </div>
        </div>

        <div class="mt-10 border-t border-slate-100 pt-4 hidden print:block text-center text-[9px] text-slate-400 italic">
            Dokumen ini bersifat rahasia dan dihasilkan secara otomatis melalui Sikonsel SMPN 4 Rancaekek pada <?php echo date('d/m/Y H:i'); ?>.
        </div>
    </div>

</body>
</html>