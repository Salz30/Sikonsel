<?php
// MUNDUR 2 LANGKAH untuk cari includes
require_once '../../includes/auth.php';
require_once '../../config/database.php';

// Cek Login (Path login mundur 2 langkah)
$user = checkLogin('../auth/login.php');

// Proteksi: Siswa dilarang masuk
if ($user['role'] !== 'guru_bk') {
    header("Location: ../../index.php");
    exit;
}

// Logika Statistik (Mengambil data jumlah laporan)
$stats = ['pending' => 0, 'proses' => 0, 'selesai' => 0];
try {
    $stmtStats = $conn->query("SELECT status, COUNT(*) as total FROM laporan_bk GROUP BY status");
    while ($row = $stmtStats->fetch()) {
        if ($row['status'] == 'Pending') $stats['pending'] = $row['total'];
        if ($row['status'] == 'Diproses') $stats['proses'] = $row['total'];
        if ($row['status'] == 'Selesai') $stats['selesai'] = $row['total'];
    }
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Guru BK | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md">S</div>
            <h1 class="font-bold text-slate-800 text-lg">Sikonsel <span class="text-blue-600">Admin</span></h1>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-xs font-bold text-slate-800"><?= htmlspecialchars($user['nama']) ?></p>
                <p class="text-[10px] text-slate-400 uppercase tracking-tighter">Administrator BK</p>
            </div>
            <a href="../auth/logout.php" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition">Keluar</a>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Overview Hari Ini</h2>
            <p class="text-slate-500 text-sm">Ringkasan aktivitas konseling siswa.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl border border-amber-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 font-bold text-xl"><?= $stats['pending'] ?></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Laporan Pending</p>
                    <p class="text-sm text-slate-600">Butuh respons</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-blue-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 font-bold text-xl"><?= $stats['proses'] ?></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Sedang Diproses</p>
                    <p class="text-sm text-slate-600">Dalam penanganan</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-emerald-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 font-bold text-xl"><?= $stats['selesai'] ?></div>
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase">Terselesaikan</p>
                    <p class="text-sm text-slate-600">Kasus ditutup</p>
                </div>
            </div>
        </div>

        <h3 class="text-lg font-bold text-slate-800 mb-4">Menu Utama</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="siswa/list_siswa.php" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-blue-500 hover:shadow-md transition group">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-800 group-hover:text-blue-600">Data Siswa</h4>
                <p class="text-xs text-slate-400 mt-1">Kelola akun siswa</p>
            </a>

            <a href="laporan/masuk_laporan.php" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-emerald-500 hover:shadow-md transition group">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-800 group-hover:text-emerald-600">Laporan Masuk</h4>
                <p class="text-xs text-slate-400 mt-1">Baca curhatan siswa</p>
            </a>

            <a href="reservasi/kelola_jadwal.php" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-pink-500 hover:shadow-md transition group">
                <div class="w-10 h-10 bg-pink-50 text-pink-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-pink-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-800 group-hover:text-pink-600">Jadwal Temu</h4>
                <p class="text-xs text-slate-400 mt-1">Atur janji tatap muka</p>
            </a>

            <a href="laporan/export/center.php" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:border-purple-500 hover:shadow-md transition group">
                <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="font-bold text-slate-800 group-hover:text-purple-600">Cetak Laporan</h4>
                <p class="text-xs text-slate-400 mt-1">Unduh rekap PDF/Excel</p>
            </a>

        </div>
    </main>
</body>
</html>