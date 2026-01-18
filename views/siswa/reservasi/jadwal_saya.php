<?php
/**
 * File: Sikonsel/views/siswa/reservasi/jadwal_saya.php
 * Perbaikan: Mengoreksi nama kolom tgl_temu dan jam_temu sesuai database.
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../includes/auth.php';

// Cek keberadaan file feedback_controller sebelum di-require
if (file_exists('../../../includes/feedback_controller.php')) {
    require_once '../../../includes/feedback_controller.php';
}

// 1. Cek Login & Timeout (10 Menit)
$user = checkLogin('../../../views/auth/login.php');

// 2. Ambil ID Siswa dari session berdasarkan kolom user_id di tabel siswa
$id_user_session = $_SESSION['user_id'];
$stmt_siswa = $conn->prepare("SELECT id_siswa FROM siswa WHERE user_id = ?");
$stmt_siswa->execute([$id_user_session]);
$siswa = $stmt_siswa->fetch(PDO::FETCH_ASSOC);

$id_siswa = ($siswa) ? $siswa['id_siswa'] : 0;

// 3. Query Ambil Riwayat Jadwal - MENGGUNAKAN tgl_temu DAN jam_temu
$query = "SELECT * FROM reservasi WHERE id_siswa = ? ORDER BY tgl_temu DESC, jam_temu DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$id_siswa]);
$reservasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Saya | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-indigo-700 text-white hidden md:block">
            <div class="p-6">
                <h1 class="text-2xl font-bold italic">Sikonsel</h1>
            </div>
            <nav class="mt-6 px-4">
                <a href="../dashboard_siswa.php" class="flex items-center p-3 hover:bg-indigo-600 rounded-xl transition mb-2">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="jadwal_saya.php" class="flex items-center p-3 bg-indigo-800 rounded-xl transition mb-2">
                    <i class="fas fa-calendar-alt mr-3"></i> Jadwal Saya
                </a>
                <a href="../../auth/logout.php" class="flex items-center p-3 hover:bg-red-600 rounded-xl transition mt-10">
                    <i class="fas fa-sign-out-alt mr-3"></i> Keluar
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <div class="max-w-5xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-800">Riwayat Jadwal</h2>
                        <p class="text-slate-500 text-sm">Pantau status pengajuan konseling Anda.</p>
                    </div>
                    <a href="ajukan_jadwal.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg transition flex items-center">
                        <i class="fas fa-plus mr-2"></i> Ajukan Baru
                    </a>
                </div>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'feedback_success'): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-xl shadow-sm">
                        <p class="font-bold text-sm">Terima Kasih!</p>
                        <p class="text-xs">Rating dan masukan Anda telah kami terima.</p>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="p-4 text-xs font-bold text-slate-600 uppercase">Waktu Konseling</th>
                                    <th class="p-4 text-xs font-bold text-slate-600 uppercase">Keperluan</th>
                                    <th class="p-4 text-xs font-bold text-slate-600 uppercase">Status</th>
                                    <th class="p-4 text-xs font-bold text-slate-600 uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($reservasi)): ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-slate-400 italic">Belum ada pengajuan jadwal.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reservasi as $row): ?>
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="p-4">
                                                <div class="font-bold text-slate-800 text-sm"><?= date('d M Y', strtotime($row['tgl_temu'])) ?></div>
                                                <div class="text-xs text-slate-500"><?= substr($row['jam_temu'], 0, 5) ?> WIB</div>
                                            </td>
                                            <td class="p-4 text-sm text-slate-600">
                                                <?= htmlspecialchars($row['keperluan']) ?>
                                            </td>
                                            <td class="p-4">
                                                <?php 
                                                    $statusStyles = [
                                                        'Pending'   => 'bg-amber-100 text-amber-700',
                                                        'Disetujui' => 'bg-blue-100 text-blue-700',
                                                        'Selesai'   => 'bg-green-100 text-green-700',
                                                        'Ditolak'   => 'bg-red-100 text-red-700'
                                                    ];
                                                    $style = $statusStyles[$row['status']] ?? 'bg-slate-100 text-slate-700';
                                                ?>
                                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= $style ?>">
                                                    <?= $row['status'] ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <?php if ($row['status'] == 'Selesai'): ?>
                                                    <?php 
                                                        $sudahRating = false;
                                                        if (function_exists('checkFeedbackExists')) {
                                                            $sudahRating = checkFeedbackExists($conn, $row['id_reservasi']);
                                                        }
                                                    ?>
                                                    
                                                    <?php if (!$sudahRating): ?>
                                                        <a href="beri_feedback.php?id=<?= $row['id_reservasi'] ?>" 
                                                           class="inline-flex items-center text-[10px] bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition font-bold uppercase shadow-sm">
                                                           <i class="fas fa-star mr-1"></i> Beri Rating
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-[10px] text-green-600 font-bold bg-green-50 px-3 py-1.5 rounded-lg border border-green-200">
                                                            <i class="fas fa-check-circle mr-1"></i> Terkirim
                                                        </span>
                                                    <?php endif; ?>

                                                <?php elseif ($row['status'] == 'Pending'): ?>
                                                    <span class="text-xs text-slate-400 italic">Menunggu...</span>
                                                <?php else: ?>
                                                    <span class="text-xs text-slate-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>