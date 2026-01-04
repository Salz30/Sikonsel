<?php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';

$user = checkLogin('../../auth/login.php');

if ($user['role'] !== 'guru_bk') { header("Location: ../../../index.php"); exit; }

$dataSiswa = getAllSiswa($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Siswa | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <nav class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="../dashboard_admin.php" class="text-slate-500 hover:text-blue-600 transition font-medium">← Dashboard</a>
            <h1 class="text-xl font-bold text-slate-800">Data Siswa</h1>
        </div>
        <a href="tambah.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-blue-700 transition">
            + Tambah Siswa
        </a>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        
        <?php if (isset($_GET['msg'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const msg = "<?= $_GET['msg'] ?>";
                    if(msg === 'deleted') Swal.fire('Terhapus!', 'Data siswa berhasil dihapus.', 'success');
                    if(msg === 'bulk_deleted') Swal.fire('Berhasil!', 'Data terpilih berhasil dihapus.', 'success');
                    if(msg === 'error') Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                });
            </script>
        <?php endif; ?>

        <form action="delete_handler.php" method="POST" id="bulkDeleteForm">
            
            <div class="mb-4 flex justify-between items-center h-10">
                <div id="bulkActionContainer" class="hidden transition-all duration-300">
                    <button type="button" onclick="confirmBulkDelete()" class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-600 hover:text-white transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Data Terpilih (<span id="countSelected">0</span>)
                    </button>
                </div>
                <div class="text-xs text-slate-400 font-bold ml-auto">
                    Total Siswa: <?= count($dataSiswa) ?>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b text-xs uppercase text-slate-500 font-bold">
                        <tr>
                            <th class="p-4 w-10 text-center">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="p-4">NISN</th>
                            <th class="p-4">Nama Lengkap</th>
                            <th class="p-4">Kelas</th>
                            <th class="p-4">Alamat</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($dataSiswa)): ?>
                            <tr><td colspan="6" class="p-8 text-center text-slate-400">Belum ada data siswa.</td></tr>
                        <?php else: foreach ($dataSiswa as $row): ?>
                        <tr class="hover:bg-slate-50 group transition-colors">
                            <td class="p-4 text-center">
                                <input type="checkbox" name="ids[]" value="<?= $row['id_siswa']; ?>" class="item-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </td>
                            <td class="p-4 text-sm font-mono text-slate-500"><?= htmlspecialchars($row['nisn']); ?></td>
                            <td class="p-4 font-bold text-slate-700"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td class="p-4 text-sm">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded font-bold text-xs"><?= htmlspecialchars($row['kelas']); ?></span>
                            </td>
                            <td class="p-4 text-sm text-slate-500 truncate max-w-xs"><?= htmlspecialchars($row['alamat']); ?></td>
                            <td class="p-4 text-center flex justify-center gap-3">
                                <a href="edit.php?id=<?= $row['id_siswa']; ?>" class="text-slate-400 hover:text-yellow-600 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <button type="button" onclick="confirmDelete(<?= $row['id_siswa']; ?>, '<?= addslashes($row['nama_lengkap']); ?>')" class="text-slate-400 hover:text-red-600 transition" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </main>

    <script>
        // 1. Logic Checkbox Select All
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkActionContainer = document.getElementById('bulkActionContainer');
        const countSelected = document.getElementById('countSelected');

        // Fungsi Update Tampilan Tombol Bulk Delete
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

        // 2. Logic Konfirmasi Hapus Satu Data
        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Siswa?',
                text: `Anda yakin ingin menghapus data "${nama}"? Data yang dihapus tidak bisa dikembalikan!`,
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

        // 3. Logic Konfirmasi Hapus Banyak (Bulk)
        function confirmBulkDelete() {
            Swal.fire({
                title: 'Hapus Masal?',
                text: "Anda akan menghapus semua data yang dicentang. Tindakan ini berbahaya!",
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