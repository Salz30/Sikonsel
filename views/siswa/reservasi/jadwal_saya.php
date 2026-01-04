<?php
// Path: views/siswa/reservasi/jadwal_saya.php
require_once '../../../includes/auth.php';
require_once '../../../includes/reservasi_controller.php';

$user = checkLogin();

// Ambil ID Siswa
$stmt = $conn->prepare("SELECT id_siswa FROM siswa WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$siswa = $stmt->fetch();

$listReservasi = [];
if ($siswa) {
    $listReservasi = getReservasiBySiswa($conn, $siswa['id_siswa']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Konsultasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <a href="../dashboard_siswa.php" class="text-slate-500 hover:text-blue-600 transition">← Dashboard</a>
            <h1 class="text-xl font-bold text-slate-800">Jadwal Temu</h1>
        </div>
        <a href="ajukan_jadwal.php" class="bg-pink-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-pink-700 transition shadow-md shadow-pink-200">
            + Buat Janji Baru
        </a>
    </nav>

    <main class="p-8 max-w-4xl mx-auto">
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm font-bold">
                Permintaan janji temu berhasil dikirim.
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase border-b">
                    <tr>
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Keperluan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Catatan Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($listReservasi)): ?>
                        <tr><td colspan="4" class="p-8 text-center text-slate-400">Belum ada jadwal.</td></tr>
                    <?php else: foreach ($listReservasi as $row): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="p-4">
                                <div class="font-bold text-slate-700"><?php echo date('d M Y', strtotime($row['tgl_temu'])); ?></div>
                                <div class="text-xs text-blue-600 font-bold"><?php echo date('H:i', strtotime($row['jam_temu'])); ?> WIB</div>
                            </td>
                            <td class="p-4 text-sm text-slate-600"><?= htmlspecialchars($row['keperluan']); ?></td>
                            <td class="p-4">
                                <?php 
                                $color = match($row['status']) {
                                    'Menunggu' => 'bg-yellow-100 text-yellow-700',
                                    'Disetujui' => 'bg-green-100 text-green-700',
                                    'Ditolak' => 'bg-red-100 text-red-700',
                                    'Selesai' => 'bg-slate-100 text-slate-700',
                                };
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $color ?>"><?= $row['status']; ?></span>
                            </td>
                            <td class="p-4 text-sm text-slate-500 italic">
                                <?= $row['catatan_guru'] ? htmlspecialchars($row['catatan_guru']) : '-'; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>