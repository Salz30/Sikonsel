<?php
/**
 * File: Sikonsel/views/siswa/laporan/detail_laporan.php
 * Perbaikan: Mengganti validasi akses dari username ke id_siswa (Sesuai DB).
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/encryption.php';

// 1. Cek Login & Timeout (10 Menit)
$user = checkLogin('../../../views/auth/login.php');

// 2. Ambil ID Siswa dari session (Sesuai kolom user_id di tabel siswa)
$id_user_session = $_SESSION['user_id'];
$stmt_siswa = $conn->prepare("SELECT id_siswa FROM siswa WHERE user_id = ?");
$stmt_siswa->execute([$id_user_session]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);
$id_siswa_login = ($siswa) ? $siswa['id_siswa'] : 0;

// 3. Ambil Detail Laporan
$id_laporan = $_GET['id'] ?? null;
if (!$id_laporan) {
    header("Location: riwayat_saya.php");
    exit;
}

$stmt = $conn->prepare("SELECT l.*, s.nama_lengkap FROM laporan_bk l JOIN siswa s ON l.id_siswa = s.id_siswa WHERE l.id_laporan = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch(PDO::FETCH_ASSOC);

// 4. Validasi Keberadaan Laporan
if (!$laporan) {
    header("Location: riwayat_saya.php?msg=laporan_tidak_ditemukan");
    exit;
}

// 5. VALIDASI AKSES (PENGGANTI LOGIKA USERNAME YANG ERROR)
// Pastikan id_siswa di laporan sama dengan id_siswa yang sedang login
if ($laporan['id_siswa'] != $id_siswa_login) {
    header("Location: riwayat_saya.php?msg=tidak_ada_akses");
    exit;
}

// Dekripsi isi laporan untuk ditampilkan
$isi_laporan = decryptData($laporan['isi_laporan']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-8">

    <div class="max-w-3xl mx-auto">
        <a href="riwayat_saya.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 mb-6 transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
        </a>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="text-xl font-bold text-slate-800">Detail Laporan BK</h2>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase 
                    <?= $laporan['status'] == 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                    <?= $laporan['status'] ?>
                </span>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Kategori Masalah</label>
                        <p class="text-slate-700 font-medium"><?= htmlspecialchars($laporan['kategori']) ?></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tanggal Laporan</label>
                        <p class="text-slate-700 font-medium"><?= date('d F Y', strtotime($laporan['tgl_laporan'])) ?></p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Isi Laporan / Curhatan</label>
                    <div class="bg-slate-50 p-4 rounded-xl text-slate-700 leading-relaxed italic border border-slate-100">
                        "<?= nl2br(htmlspecialchars($isi_laporan)) ?>"
                    </div>
                </div>

                <?php if ($laporan['tanggapan']): ?>
                    <div class="pt-4 border-t-2 border-dashed border-indigo-100">
                        <label class="block text-xs font-bold text-indigo-600 uppercase mb-2">
                            <i class="fas fa-reply mr-1"></i> Tanggapan Guru BK
                        </label>
                        <div class="bg-indigo-50 p-4 rounded-xl text-indigo-900 border border-indigo-100">
                            <?= nl2br(htmlspecialchars($laporan['tanggapan'])) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="pt-4 text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-sm text-slate-400">Belum ada tanggapan dari Guru BK.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>