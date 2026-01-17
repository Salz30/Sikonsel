<?php
// File: Sikonsel/views/admin/reservasi/kelola_jadwal.php

require_once '../../../includes/auth.php';
require_once '../../../includes/reservasi_controller.php';

$user = checkLogin('../../auth/login.php');

if ($user['role'] !== 'guru_bk') {
    header("Location: ../../../index.php");
    exit;
}

// Proses Update Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_id'])) {
    $id = $_POST['action_id'];
    $status = $_POST['action_status'];
    $catatan = $_POST['action_note'];
    
    // Pastikan fungsi updateStatusReservasi ada di controller
    if (updateStatusReservasi($conn, $id, $status, $catatan)) {
        $msg = "Status janji temu berhasil diperbarui.";
    }
}

// Ambil semua data reservasi terbaru
$listJadwal = getAllReservasi($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
   <title>Kelola Jadwal | Admin BK</title>
   <script src="https://cdn.tailwindcss.com"></script>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
   <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="../dashboard_admin.php" class="text-slate-500 hover:text-blue-600 transition font-medium">← Dashboard</a>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Janji Temu</h1>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        <?php if (isset($msg)): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-6 font-bold text-sm shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <?= $msg ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-3xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase border-b font-bold tracking-wider">
                    <tr>
                        <th class="p-4 w-40">Waktu</th>
                        <th class="p-4 w-48">Siswa</th>
                        <th class="p-4">Keperluan</th>
                        <th class="p-4 w-32">Status</th>
                        <th class="p-4 w-72">Tindakan / Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                     <?php if (empty($listJadwal)): ?>
                        <tr><td colspan="5" class="p-8 text-center text-slate-400">Belum ada pengajuan janji temu.</td></tr>
                    <?php else: foreach ($listJadwal as $row): ?>
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="p-4">
                                <div class="font-bold text-slate-700"><?php echo date('d M Y', strtotime($row['tgl_temu'])); ?></div>
                                <div class="text-xs text-blue-600 font-bold"><?php echo date('H:i', strtotime($row['jam_temu'])); ?> WIB</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800"><?php echo htmlspecialchars($row['nama_lengkap']); ?></div>
                                <div class="text-xs text-slate-500 font-medium"><?php echo htmlspecialchars($row['kelas']); ?></div>
                            </td>
                            <td class="p-4 text-sm text-slate-600 font-medium"><?php echo htmlspecialchars($row['keperluan']); ?></td>
                            <td class="p-4">
                                <?php 
                                    // PERBAIKAN 1: Sesuaikan kata kunci dengan Database ('Pending')
                                    // Jika status kosong/salah, kita anggap Pending biar aman
                                    $statusDB = $row['status'] ?: 'Pending'; 

                                    $bgStatus = match($statusDB) {
                                        'Pending'   => 'bg-yellow-100 text-yellow-700', // SINKRONISASI DISINI
                                        'Disetujui' => 'bg-green-100 text-green-700',
                                        'Ditolak'   => 'bg-red-100 text-red-700',
                                        'Selesai'   => 'bg-blue-100 text-blue-700',
                                        default     => 'bg-slate-100 text-slate-600'
                                    };
                                ?>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide <?= $bgStatus ?>"><?php echo $statusDB; ?></span>
                            </td>
                            <td class="p-4">
                                <?php 
                                // PERBAIKAN 2: Logic tombol muncul jika status 'Pending' (Bukan 'Menunggu')
                                // Kita juga handle jika status kosong (data lama error)
                                if ($row['status'] == 'Pending' || empty($row['status'])): 
                                ?>
                                    <form method="POST" class="flex flex-col gap-2">
                                        <input type="hidden" name="action_id" value="<?= $row['id_reservasi'] ?>">
                                        <input type="text" name="action_note" placeholder="Tulis pesan/lokasi..." class="text-xs border border-slate-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($row['catatan_guru'] ?? '') ?>">
                                        <div class="flex gap-2">
                                            <button type="submit" name="action_status" value="Disetujui" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-xs font-bold transition shadow-sm">
                                                ✓ Terima
                                            </button>
                                            <button type="submit" name="action_status" value="Ditolak" class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold transition">
                                                ✕ Tolak
                                            </button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="flex flex-col gap-1 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="text-[10px] uppercase font-bold text-slate-400">Catatan Guru:</span>
                                        <p class="text-sm text-slate-700 italic">
                                            <?= $row['catatan_guru'] ? '"'.htmlspecialchars($row['catatan_guru']).'"' : '- Tidak ada catatan -' ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>