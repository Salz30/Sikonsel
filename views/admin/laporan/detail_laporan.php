<?php
// Lokasi: views/admin/laporan/detail_laporan.php

// 1. Mundur 3 langkah ke root untuk ambil Controller
require_once '../../../includes/auth.php';
require_once '../../../includes/laporan_controller.php';

// 2. Cek Login & Pastikan Role = guru_bk
// Path ke login mundur 2 langkah (laporan -> admin -> auth)
$user = checkLogin('../../auth/login.php');

if ($user['role'] !== 'guru_bk') {
    header("Location: ../../../index.php");
    exit;
}

// 3. Ambil ID Laporan
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: masuk_laporan.php");
    exit;
}

// 4. Proses Form Update Status (Jika tombol Simpan ditekan)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_baru'])) {
    $status_baru = $_POST['status_baru'];
    $catatan_guru = $_POST['catatan_guru'] ?? ''; // Opsional jika mau ada catatan
    $id_guru = $user['user_id'];

    // Panggil fungsi update di controller
    // Pastikan fungsi updateStatusLaporan mendukung parameter ini
    if (updateStatusLaporan($conn, $id, $status_baru, $id_guru)) {
        $msg = "✅ Status laporan berhasil diperbarui.";
    } else {
        $msg = "❌ Gagal memperbarui status.";
    }
}

// 5. Ambil Data Detail Laporan
$laporan = getLaporanById($conn, $id);

if (!$laporan) {
    die("Laporan tidak ditemukan atau telah dihapus.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Laporan Siswa | Admin BK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="masuk_laporan.php" class="text-slate-500 hover:text-blue-600 font-bold text-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-800">Detail Laporan</h1>
        </div>
    </nav>

    <main class="p-6 sm:p-10 max-w-5xl mx-auto">

        <?php if ($msg): ?>
            <div class="bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl mb-6 font-bold text-sm">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="bg-slate-50 px-8 py-6 border-b border-slate-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold text-slate-800 leading-tight">
                                    <?= htmlspecialchars($laporan['judul_laporan']) ?>
                                </h2>
                                <div class="flex items-center gap-3 mt-3">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wide">
                                        <?= htmlspecialchars($laporan['kategori']) ?>
                                    </span>
                                    <span class="text-slate-400 text-xs">
                                        <?= date('d F Y, H:i', strtotime($laporan['tgl_laporan'])) ?> WIB
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">
                            Isi Curhatan Siswa (Terenkripsi & Terbaca)
                        </label>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 text-slate-700 leading-relaxed text-justify whitespace-pre-line">
                            <?= htmlspecialchars($laporan['isi_laporan_dekripsi']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Identitas Pelapor
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-slate-400">Nama Lengkap</p>
                            <p class="font-bold text-slate-700"><?= htmlspecialchars($laporan['nama_siswa']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">NISN</p>
                            <p class="font-mono text-slate-600"><?= htmlspecialchars($laporan['nisn']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Kelas</p>
                            <p class="text-slate-600"><?= htmlspecialchars($laporan['kelas'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-lg border border-blue-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>
                    
                    <h3 class="font-bold text-slate-800 mb-4 relative z-10">Tindak Lanjut</h3>
                    
                    <form method="POST" action="">
                        <div class="mb-4 relative z-10">
                            <label class="block text-xs font-bold text-slate-500 mb-2">Update Status</label>
                            <select name="status_baru" class="w-full border border-slate-300 rounded-xl p-3 bg-white focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700">
                                <option value="Pending" <?= $laporan['status'] == 'Pending' ? 'selected' : '' ?>>⏳ Pending (Menunggu)</option>
                                <option value="Diproses" <?= $laporan['status'] == 'Diproses' ? 'selected' : '' ?>>🔄 Diproses / Konseling</option>
                                <option value="Selesai" <?= $laporan['status'] == 'Selesai' ? 'selected' : '' ?>>✅ Selesai / Ditutup</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-md relative z-10">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

                <a href="export/generate_pdf.php?id=<?= $laporan['id_laporan'] ?>" target="_blank" class="block w-full text-center py-3 border-2 border-slate-200 text-slate-600 rounded-xl font-bold hover:border-red-500 hover:text-red-500 transition">
                    🖨️ Cetak Laporan Ini
                </a>

            </div>
        </div>

    </main>
</body>
</html>