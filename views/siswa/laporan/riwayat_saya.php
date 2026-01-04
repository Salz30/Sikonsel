<?php
// Lokasi File: views/siswa/laporan/riwayat_saya.php

// 1. Mundur 3 langkah untuk mencari folder includes
require_once '../../../includes/auth.php';
require_once '../../../includes/laporan_controller.php';

// 2. Cek Login (Jika belum login, lempar ke ../../auth/login.php)
$user = checkLogin('../../auth/login.php');

// 3. Ambil Data Siswa (ID Siswa) berdasarkan User yang login
$stmt = $conn->prepare("SELECT id_siswa FROM siswa WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$siswaData = $stmt->fetch();

$laporanList = [];
if ($siswaData) {
    // Ambil laporan HANYA milik siswa ini
    $laporanList = getLaporanBySiswa($conn, $siswaData['id_siswa']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Laporan Saya | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="../dashboard_siswa.php" class="text-slate-500 hover:text-blue-600 font-medium transition">← Dashboard</a>
            <h1 class="text-xl font-bold text-slate-800">Riwayat Laporan</h1>
        </div>
        <a href="buat_laporan.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-md">
            + Tulis Baru
        </a>
    </nav>

    <main class="p-8 max-w-5xl mx-auto">
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sent'): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 text-sm font-bold shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Laporan berhasil dikirim! Guru BK akan segera membacanya.
            </div>
        <?php endif; ?>

        <div class="space-y-4">
            <?php if (empty($laporanList)): ?>
                <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                    <div class="text-4xl mb-4">📭</div>
                    <p class="text-slate-500 font-bold">Belum ada riwayat laporan.</p>
                    <p class="text-xs text-slate-400 mt-1">Curhatanmu akan muncul di sini setelah dikirim.</p>
                </div>
            <?php else: ?>
                <?php foreach ($laporanList as $row): ?>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                <?= date('d F Y', strtotime($row['tgl_laporan'])) ?>
                            </div>
                            <h3 class="font-bold text-slate-800 text-lg">
                                <?= htmlspecialchars($row['judul_laporan']) ?>
                            </h3>
                            <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500 font-medium mt-2 inline-block border border-slate-200">
                                <?= htmlspecialchars($row['kategori']) ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                            <?php 
                                $statusClass = match($row['status']) {
                                    'Pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Selesai' => 'bg-green-100 text-green-700 border-green-200',
                                    default => 'bg-slate-100'
                                };
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $statusClass ?>">
                                <?= $row['status'] ?>
                            </span>
                            
                            <a href="detail_laporan.php?id=<?= $row['id_laporan'] ?>" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white transition shadow-sm border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>