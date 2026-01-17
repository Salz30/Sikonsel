<?php
// File: views/admin/info/list_info.php
session_start();
require_once '../../../config/database.php';

// Cek Role harus guru_bk
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru_bk') {
    header("Location: ../../auth/login.php");
    exit();
}

// Ambil Data
$stmt = $conn->query("SELECT * FROM info_sekolah ORDER BY tgl_posting DESC");
$infos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Data User untuk Header
$namaUser = $_SESSION['nama'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Info | Sikonsel Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl z-20 hidden md:flex">
        <div class="p-6 flex items-center gap-3 border-b border-slate-800">
            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-800 border border-slate-700 p-1">
                <span class="font-bold text-emerald-500">BK</span>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-wide">Sikonsel</h1>
                <p class="text-[10px] text-slate-400 font-medium tracking-wider mt-1">ADMINISTRATOR</p>
            </div>
        </div>
        <nav class="flex-1 py-6 space-y-2 overflow-y-auto">
            <p class="px-6 text-xs font-bold text-slate-500 uppercase mb-2">Menu Utama</p>
            
            <a href="../dashboard_admin.php" class="flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="../siswa/list_siswa.php" class="flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium">Data Siswa</span>
            </a>

            <a href="../laporan/masuk_laporan.php" class="flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Laporan Masuk</span>
            </a>

            <a href="../reservasi/kelola_jadwal.php" class="flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                <span class="font-medium">Jadwal Konseling</span>
            </a>

            <a href="list_info.php" class="flex items-center gap-3 px-6 py-3 text-emerald-400 bg-slate-800 border-r-4 border-emerald-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span class="font-medium">Info & Agenda</span>
            </a>

            <p class="px-6 text-xs font-bold text-slate-500 uppercase mt-6 mb-2">Akun</p>
            <a href="../../auth/logout.php" class="flex items-center gap-3 px-6 py-3 text-red-400 hover:bg-red-500/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="font-medium">Logout</span>
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center z-10">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Info Sekolah & Beasiswa</h2>
                <p class="text-xs text-slate-400">Kelola pengumuman untuk siswa.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($namaUser) ?></p>
                    <p class="text-xs text-emerald-600 font-medium">Administrator BK</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-bold border border-slate-200">
                    <?= substr($namaUser, 0, 1) ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
            
            <div class="mb-6 flex justify-between items-center">
                <a href="tambah.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Info Baru
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                            <th class="px-6 py-4">Tanggal Posting</th>
                            <th class="px-6 py-4">Judul Informasi</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Preview Isi</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        <?php if (empty($infos)): ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada data informasi.</td></tr>
                        <?php else: foreach($infos as $info): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    <?= date('d M Y', strtotime($info['tgl_posting'])) ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700">
                                    <?= htmlspecialchars($info['judul']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($info['kategori'] == 'Beasiswa'): ?>
                                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200">Beasiswa</span>
                                    <?php else: ?>
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold border border-indigo-200"><?= $info['kategori'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-slate-500 max-w-xs truncate">
                                    <?= htmlspecialchars(substr($info['isi_info'], 0, 50)) ?>...
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="edit.php?id=<?= $info['id_info'] ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 border border-yellow-200 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <a href="delete_handler.php?id=<?= $info['id_info'] ?>" onclick="return confirm('Yakin ingin menghapus informasi ini?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 border border-red-200 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>
</html>