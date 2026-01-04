<?php
/**
 * Export Center Sikonsel - Halaman Utama
 * Berisi Formulir Filter dan Tombol Pilihan (PDF/Excel)
 */

require_once '../../../../includes/auth.php';
require_once '../../../../includes/laporan_controller.php';

// 2. Cek Login & Role
$user = checkLogin('../../../auth/login.php'); 
if ($user['role'] !== 'guru_bk') {
    header("Location: ../../../../index.php");
    exit;
}

// 3. Ambil Parameter Filter dari URL (agar form terisi kembali setelah filter)
$filter_status = $_GET['status'] ?? '';
$filter_search = $_GET['search'] ?? '';
$filter_month  = $_GET['month'] ?? '';
$filter_year   = $_GET['year'] ?? date('Y');

// 4. Query untuk Pratinjau Data di Tabel Bawah
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
$previewData = $stmt->fetchAll();

// 5. Build query string untuk link tombol download (agar filter terbawa)
$queryString = http_build_query([
    'status' => $filter_status,
    'search' => $filter_search,
    'month'  => $filter_month,
    'year'   => $filter_year
]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Center | Admin BK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="../../dashboard_admin.php" class="text-slate-500 hover:text-blue-600 font-medium transition">← Dashboard</a>
            <h1 class="text-xl font-bold text-slate-800">Export Center</h1>
        </div>
    </nav>

    <main class="p-6 sm:p-10 max-w-6xl mx-auto">
        
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 p-8 border border-slate-100 mb-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z"></path></svg>
                Filter Data Laporan
            </h2>
            
            <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Cari Nama Siswa</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($filter_search) ?>" placeholder="Ketik nama..." class="w-full border border-slate-200 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Status</label>
                    <select name="status" class="w-full border border-slate-200 rounded-xl p-3 text-sm outline-none bg-white">
                        <option value="">Semua Status</option>
                        <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Diproses" <?= $filter_status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Bulan</label>
                    <select name="month" class="w-full border border-slate-200 rounded-xl p-3 text-sm outline-none bg-white">
                        <option value="">Semua Bulan</option>
                        <?php
                        $months = ['01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April', '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus', '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                        foreach ($months as $m => $name): ?>
                            <option value="<?= $m ?>" <?= $filter_month == $m ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 text-white font-bold py-3 rounded-xl hover:bg-slate-900 transition-all">Terapkan</button>
                    <a href="center.php" class="p-3 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all">Reset</a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="generate_pdf.php?<?= $queryString ?>" target="_blank" class="flex items-center justify-between p-6 bg-white border-2 border-red-50 rounded-3xl hover:border-red-500 transition-all group shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center">PDF</div>
                    <div><h3 class="font-bold text-slate-800">Cetak Rekap PDF</h3><p class="text-[10px] text-slate-400 font-bold uppercase">Sesuai Filter</p></div>
                </div>
                <span class="text-red-500 font-bold">UNDUH →</span>
            </a>
            <a href="generate_excel.php?<?= $queryString ?>" class="flex items-center justify-between p-6 bg-white border-2 border-emerald-50 rounded-3xl hover:border-emerald-500 transition-all group shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center">XLS</div>
                    <div><h3 class="font-bold text-slate-800">Ekspor Excel</h3><p class="text-[10px] text-slate-400 font-bold uppercase">Sesuai Filter</p></div>
                </div>
                <span class="text-emerald-500 font-bold">UNDUH →</span>
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800">Pratinjau Data (<?= count($previewData) ?> Laporan)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b">
                        <tr>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Nama Siswa</th>
                            <th class="p-4">Masalah</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Aksi Individu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if (empty($previewData)): ?>
                            <tr><td colspan="5" class="p-10 text-center text-slate-400">Tidak ada data yang cocok dengan filter.</td></tr>
                        <?php else: foreach ($previewData as $row): ?>
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="p-4 text-xs text-slate-500"><?= date('d/m/y', strtotime($row['tgl_laporan'])) ?></td>
                                <td class="p-4 font-bold text-slate-700 text-sm"><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                <td class="p-4 text-xs text-slate-600 italic">"<?= htmlspecialchars($row['judul_laporan']) ?>"</td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-1 rounded-md text-[9px] font-bold uppercase bg-slate-100 text-slate-600"><?= $row['status'] ?></span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="generate_pdf.php?id=<?= $row['id_laporan'] ?>" target="_blank" 
                                       class="text-[10px] font-bold text-red-500 hover:text-red-700 uppercase">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>