<?php
// MUNDUR 3 LANGKAH: laporan -> admin -> views -> root
require_once '../../../includes/auth.php';
require_once '../../../includes/laporan_controller.php';
require_once '../../../includes/siswa_controller.php';
require_once '../../../includes/encryption.php';

// Cek Login
$user = checkLogin('../../auth/login.php');

if ($user['role'] !== 'guru_bk') { header("Location: ../../../index.php"); exit; }

$laporanList = getAllLaporan($conn);

// Proses Update Status Cepat (Quick Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quick_id']) && isset($_POST['quick_status'])) {
    $id = $_POST['quick_id'];
    $status = $_POST['quick_status'];
    
    // PERBAIKAN 1: Mengambil ID User dengan key yang benar ('id_user' atau 'id')
    $id_guru = $user['id_user'] ?? $user['id'];

    if (updateStatusLaporan($conn, $id, $status, $id_guru)) {
        header("Location: masuk_laporan.php?msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Masuk | Admin BK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="../dashboard_admin.php" class="text-slate-500 hover:text-blue-600 font-medium transition">← Dashboard</a>
            <h1 class="text-xl font-bold text-slate-800">Manajemen Laporan Masuk</h1>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        
        <?php if (isset($_GET['msg'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const msg = "<?= $_GET['msg'] ?>";
                    if(msg === 'updated') Swal.fire({icon: 'success', title: 'Berhasil', text: 'Status laporan diperbarui', timer: 1500, showConfirmButton: false});
                    if(msg === 'deleted') Swal.fire('Terhapus!', 'Laporan berhasil dihapus.', 'success');
                    if(msg === 'bulk_deleted') Swal.fire('Berhasil!', 'Laporan terpilih berhasil dihapus.', 'success');
                    if(msg === 'error') Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                });
            </script>
        <?php endif; ?>

        <form action="delete_handler.php" method="POST" id="bulkDeleteForm">

            <div class="mb-4 flex justify-between items-center h-10">
                <div id="bulkActionContainer" class="hidden transition-all duration-300">
                    <button type="button" onclick="confirmBulkDelete()" class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-600 hover:text-white transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Terpilih (<span id="countSelected">0</span>)
                    </button>
                </div>
                <div class="text-xs text-slate-400 font-bold ml-auto">
                    Total Laporan: <?= count($laporanList) ?>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase border-b">
                        <tr>
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="p-4 w-32">Tanggal</th>
                            <th class="p-4 w-48">Siswa</th>
                            <th class="p-4">Masalah</th>
                            <th class="p-4 w-32">Kategori</th>
                            <th class="p-4 w-48">Status</th>
                            <th class="p-4 w-32 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($laporanList)): ?>
                            <tr><td colspan="7" class="p-8 text-center text-slate-400">Belum ada laporan masuk.</td></tr>
                        <?php else: ?>
                            <?php foreach ($laporanList as $row): ?>
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="p-4 text-center">
                                    <input type="checkbox" name="ids[]" value="<?= $row['id_laporan']; ?>" class="item-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </td>
                                
                                <td class="p-4 text-sm text-slate-500"><?php echo date('d/m/Y', strtotime($row['tgl_laporan'])); ?></td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-700"><?php echo htmlspecialchars($row['nama_siswa'] ?? 'Tanpa Nama'); ?></div>
                                    <div class="text-xs text-slate-400"><?php echo htmlspecialchars($row['nisn'] ?? '-'); ?></div>
                                </td>
                                <td class="p-4 text-slate-800 font-medium"><?php echo htmlspecialchars(decryptData($row['judul_laporan'])); ?></td>
                                
                                <td class="p-4">
                                    <?php if (!empty($row['kategori'])): ?>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                                            <?php echo htmlspecialchars($row['kategori']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-300 text-xs italic">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="p-4">
                                    <div onclick="event.stopPropagation()"> 
                                        <form method="POST" action="masuk_laporan.php" id="statusForm<?= $row['id_laporan'] ?>">
                                            <input type="hidden" name="quick_id" value="<?php echo $row['id_laporan']; ?>">
                                            <?php 
                                                $borderColor = match($row['status']) {
                                                    'Pending' => 'border-yellow-400 bg-yellow-50',
                                                    'Diproses' => 'border-blue-400 bg-blue-50',
                                                    'Selesai' => 'border-green-400 bg-green-50',
                                                    default => 'border-slate-200'
                                                };
                                            ?>
                                            <select name="quick_status" onchange="document.getElementById('statusForm<?= $row['id_laporan'] ?>').submit()" 
                                                    class="w-full text-xs font-bold py-2 pl-2 pr-6 rounded-lg border-2 outline-none cursor-pointer <?php echo $borderColor; ?>">
                                                <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                                <option value="Diproses" <?php echo $row['status'] == 'Diproses' ? 'selected' : ''; ?>>🔄 Proses</option>
                                                <option value="Selesai" <?php echo $row['status'] == 'Selesai' ? 'selected' : ''; ?>>✅ Selesai</option>
                                            </select>
                                        </form>
                                    </div>
                                </td>
                                <td class="p-4 text-center flex justify-center gap-2">
                                    <a href="detail_laporan.php?id=<?php echo $row['id_laporan']; ?>" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-all" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <button type="button" onclick="confirmDelete(<?= $row['id_laporan']; ?>)" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-red-500 hover:bg-red-500 hover:text-white transition-all" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </main>

    <script>
        // Checkbox Select All Logic
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkActionContainer = document.getElementById('bulkActionContainer');
        const countSelected = document.getElementById('countSelected');

        function updateBulkUI() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            countSelected.textContent = checkedCount;
            if (checkedCount > 0) {
                bulkActionContainer.classList.remove('hidden');
            } else {
                bulkActionContainer.classList.add('hidden');
            }
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkUI();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkUI);
        });

        // Konfirmasi Hapus Single
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Laporan ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_handler.php?id=${id}`;
                }
            })
        }

        // Konfirmasi Hapus Massal
        function confirmBulkDelete() {
            Swal.fire({
                title: 'Hapus Terpilih?',
                text: "Semua laporan yang dicentang akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulkDeleteForm').submit();
                }
            })
        }
    </script>
</body>
</html>