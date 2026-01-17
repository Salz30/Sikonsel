<?php
// File: views/admin/info/edit.php
session_start();
require_once '../../../config/database.php';

// Cek Role harus guru_bk
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guru_bk') {
    header("Location: ../../auth/login.php");
    exit();
}

$id = $_GET['id'];
// Ambil data lama
$stmt = $conn->prepare("SELECT * FROM info_sekolah WHERE id_info = ?");
$stmt->execute([$id]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$info) die("Data tidak ditemukan");

// Proses Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $isi = $_POST['isi_info'];
    $tgl = $_POST['tgl_posting'];

    try {
        $stmt = $conn->prepare("UPDATE info_sekolah SET judul=?, kategori=?, isi_info=?, tgl_posting=? WHERE id_info=?");
        $stmt->execute([$judul, $kategori, $isi, $tgl, $id]);
        header("Location: list_info.php");
        exit();
    } catch (PDOException $e) {
        $error = "Gagal update: " . $e->getMessage();
    }
}
$namaUser = $_SESSION['nama'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Info | Sikonsel Admin</title>
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
             <a href="list_info.php" class="flex items-center gap-3 px-6 py-3 text-emerald-400 bg-slate-800 border-r-4 border-emerald-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span class="font-medium">Kembali ke List</span>
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center z-10">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Edit Informasi</h2>
                <p class="text-xs text-slate-400">Perbarui data agenda atau beasiswa.</p>
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
            <div class="max-w-3xl mx-auto">
                <?php if(isset($error)) echo "<div class='bg-red-50 text-red-600 p-4 rounded-xl mb-4 border border-red-200'>$error</div>"; ?>

                <form method="POST" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Informasi</label>
                        <input type="text" name="judul" required value="<?= htmlspecialchars($info['judul']) ?>"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                            <select name="kategori" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                <option value="Agenda" <?= $info['kategori'] == 'Agenda' ? 'selected' : '' ?>>Agenda Sekolah</option>
                                <option value="Beasiswa" <?= $info['kategori'] == 'Beasiswa' ? 'selected' : '' ?>>Info Beasiswa</option>
                                <option value="Pengumuman" <?= $info['kategori'] == 'Pengumuman' ? 'selected' : '' ?>>Pengumuman Umum</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Posting</label>
                            <input type="date" name="tgl_posting" required value="<?= $info['tgl_posting'] ?>"
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Isi Detail Informasi</label>
                        <textarea name="isi_info" rows="6" required
                                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($info['isi_info']) ?></textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all">
                            Simpan Perubahan
                        </button>
                        <a href="list_info.php" class="text-slate-500 hover:text-slate-800 font-bold px-4">Batal</a>
                    </div>

                </form>
            </div>
        </main>
    </div>
</body>
</html>