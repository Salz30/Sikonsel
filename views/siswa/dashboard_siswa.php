<?php
// MUNDUR 2 LANGKAH untuk cari includes
require_once '../../includes/auth.php';
require_once '../../config/database.php';

// Cek Login (Path login mundur 2 langkah)
$user = checkLogin('../auth/login.php');

// Proteksi: Guru dilarang masuk sini
if ($user['role'] !== 'siswa') {
    header("Location: ../../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Siswa | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">S</div>
            <h1 class="font-bold text-slate-800 text-lg">Sikonsel <span class="text-blue-600">Siswa</span></h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-slate-600 hidden sm:inline">Halo, <?= htmlspecialchars($user['nama']) ?> 👋</span>
            <a href="../auth/logout.php" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-100 transition">Keluar</a>
        </div>
    </nav>

    <main class="p-8 max-w-5xl mx-auto mt-6">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-800">Ruang BK Digital</h2>
            <p class="text-slate-500 mt-2">Jangan dipendam sendiri. Kami ada di sini untuk mendengarkan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-3xl shadow-lg shadow-blue-100 border border-blue-50 text-center hover:-translate-y-1 transition duration-300">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">📝</div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">Curhat Online</h3>
                <p class="text-sm text-slate-500 mb-6">Ceritakan masalahmu secara rahasia.</p>
                <a href="laporan/buat_laporan.php" class="block w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">Mulai Cerita</a>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center hover:border-blue-400 transition">
                <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">📂</div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">Riwayat Laporan</h3>
                <p class="text-sm text-slate-500 mb-6">Cek tanggapan dari Guru BK.</p>
                <a href="laporan/riwayat_saya.php" class="block w-full py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Lihat Status</a>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 text-center hover:border-pink-400 transition">
                <div class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">📅</div>
                <h3 class="font-bold text-xl text-slate-800 mb-2">Janji Temu</h3>
                <p class="text-sm text-slate-500 mb-6">Bertemu tatap muka dengan Guru.</p>
                <a href="reservasi/jadwal_saya.php" class="block w-full py-3 bg-pink-600 text-white rounded-xl font-bold hover:bg-pink-700 transition">Reservasi</a>
            </div>
        </div>
    </main>
</body>
</html>