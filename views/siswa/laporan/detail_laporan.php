<?php
// Path: views/siswa/laporan/detail_laporan.php
// Mundur 3 langkah: laporan -> siswa -> views -> root
require_once '../../../includes/auth.php';
require_once '../../../includes/laporan_controller.php';

$user = checkLogin();
$id = $_GET['id'] ?? null;

// Jika tidak ada ID, kembalikan ke riwayat
if (!$id) { 
    header("Location: riwayat_saya.php"); 
    exit; 
}

$laporan = getLaporanById($conn, $id);

// 1. Cek apakah laporan ada
if (!$laporan) { 
    die("Laporan tidak ditemukan."); 
}

// 2. Keamanan: Pastikan siswa hanya bisa melihat laporannya sendiri
// Cek apakah NISN di laporan sama dengan Username user yang login
if ($user['role'] == 'siswa' && $laporan['nisn'] !== $user['username']) {
    die("⛔ AKSES DITOLAK: Ini bukan laporan Anda.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="riwayat_saya.php" class="text-slate-500 hover:text-blue-600 font-medium transition">← Riwayat</a>
            <h1 class="text-xl font-bold text-slate-800">Detail Laporan</h1>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto p-6 sm:p-10">
        <a href="riwayat_saya.php" class="inline-flex items-center text-slate-400 hover:text-blue-600 font-bold text-xs mb-6 transition">
            <span class="mr-2">←</span> KEMBALI KE RIWAYAT
        </a>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="bg-slate-50/50 px-8 py-8 border-b border-slate-100">
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">
                    <?= htmlspecialchars($laporan['kategori']); ?>
                </span>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight leading-tight">
                    <?= htmlspecialchars($laporan['judul_laporan']); ?>
                </h1>
                <p class="text-slate-400 text-sm mt-2">
                    <?= date('d M Y, H:i', strtotime($laporan['tgl_laporan'])); ?> WIB
                </p>
            </div>

            <div class="p-8">
                <div class="mb-10">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Isi Curhatan Kamu (Terenkripsi)</label>
                    <div class="bg-white p-6 rounded-2xl border-2 border-blue-50 text-slate-700 leading-relaxed shadow-inner">
                        <?= nl2br(htmlspecialchars($laporan['isi_laporan_dekripsi'])); ?>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-50">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Status Respon Guru BK</label>
                    <?php 
                        $statusClass = match($laporan['status']) {
                            'Pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'Diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Selesai' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-slate-50'
                        };
                        $pesan = match($laporan['status']) {
                            'Pending' => 'Laporanmu sudah masuk sistem. Mohon tunggu, Guru BK akan segera membacanya.',
                            'Diproses' => 'Laporan sedang ditindaklanjuti. Kamu mungkin akan dipanggil ke ruang BK atau dihubungi.',
                            'Selesai' => 'Sesi konseling untuk masalah ini telah dinyatakan selesai.',
                            default => '-'
                        };
                    ?>
                    <div class="flex flex-col sm:flex-row gap-4 p-4 rounded-2xl border <?= $statusClass ?>">
                        <div class="font-bold text-lg uppercase tracking-wider self-start sm:self-center">
                            <?= htmlspecialchars($laporan['status']); ?>
                        </div>
                        <div class="text-xs opacity-90 border-l border-current/20 pl-4 leading-relaxed">
                            <?= $pesan; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>