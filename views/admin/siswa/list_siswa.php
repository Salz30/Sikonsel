<?php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';
require_once '../../../includes/encryption.php';

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
        
        <div class="flex gap-3">
            <button onclick="document.getElementById('modalImport').classList.remove('hidden')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import CSV
            </button>

            <a href="tambah.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Siswa
            </a>
        </div>
    </nav>

    <main class="p-8 max-w-7xl mx-auto">
        
        <?php if (isset($_GET['msg'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const msg = "<?= $_GET['msg'] ?>";
                    if(msg === 'added') Swal.fire('Berhasil!', 'Data siswa berhasil ditambahkan.', 'success');
                    if(msg === 'updated') Swal.fire('Berhasil!', 'Data siswa berhasil diperbarui.', 'success');
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
                            <td class="p-4 text-center flex justify-center gap-2">
                                <a href="detail.php?id=<?= $row['id_siswa']; ?>" class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition" title="Lihat Profil Lengkap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <a href="edit.php?id=<?= $row['id_siswa']; ?>" class="w-8 h-8 flex items-center justify-center rounded-full bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white transition" title="Edit Biodata">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
    
                                <button type="button" onclick="confirmDelete(<?= $row['id_siswa']; ?>, '<?= addslashes($row['nama_lengkap']); ?>')" class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </main>

    <div id="modalImport" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl transform transition-all animate-[fadeIn_0.3s_ease-out]">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-slate-800">Import Data Siswa</h3>
                <button onclick="document.getElementById('modalImport').classList.add('hidden')" class="text-slate-400 hover:text-red-500 text-2xl">&times;</button>
            </div>
            
            <form action="proses_import.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm text-blue-800">
                    <p class="font-bold mb-1">Panduan Format CSV:</p>
                    <ul class="list-disc pl-4 text-xs space-y-1">
                        <li>Gunakan format <b>.CSV</b> (Comma Delimited).</li>
                        <li>Urutan Kolom: <b>NISN, Nama, Kelas, Alamat, Nama Ortu, No HP</b>.</li>
                        <li>Baris pertama adalah header (tidak akan diinput).</li>
                        <li><a href="download_template.php" class="underline font-bold hover:text-blue-600 text-blue-700">Download Template Siap Isi (.CSV)</a></li>
                    </ul>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih File CSV</label>
                    <input type="file" name="file_csv" required accept=".csv" class="w-full border border-slate-300 rounded-lg p-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalImport').classList.add('hidden')" class="px-4 py-2 text-slate-500 hover:bg-slate-50 rounded-lg font-bold text-sm">Batal</button>
                    <button type="submit" name="import" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-bold text-sm hover:bg-emerald-700 shadow-lg shadow-emerald-200">Upload & Proses</button>
                </div>
            </form>
        </div>
    </div>

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

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkUI();
            });
        }

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