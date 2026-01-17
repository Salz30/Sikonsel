<?php
// File: Sikonsel/views/siswa/profil/edit_profil.php

require_once '../../../includes/auth.php';
require_once '../../../config/database.php';

$user = checkLogin('../../auth/login.php');
$msg = "";
$msg_type = "";

// 1. Ambil Data Siswa + Nama Lengkap dari Users (JOIN TABLE)
// Kita gabungkan tabel 'siswa' dan 'users' agar nama_lengkap terbaca
$stmt = $conn->prepare("SELECT s.*, u.nama_lengkap 
                        FROM siswa s 
                        JOIN users u ON s.user_id = u.id_user 
                        WHERE s.user_id = ?");
$stmt->execute([$user['user_id']]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "<div class='p-10 text-center'>Data siswa belum terhubung. Hubungi Admin.</div>";
    exit;
}

// 2. Proses Update Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $alamat       = trim($_POST['alamat']);
    $nama_ortu    = trim($_POST['nama_ortu']);
    $no_hp_ortu   = trim($_POST['no_hp_ortu']);
    
    try {
        $conn->beginTransaction(); // Mulai transaksi agar aman

        // A. Update tabel SISWA (Data Tambahan)
        // Kolom nama_lengkap KITA HAPUS dari sini karena tidak ada di tabel siswa
        $query = "UPDATE siswa SET alamat = ?, nama_ortu = ?, no_hp_ortu = ? WHERE id_siswa = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$alamat, $nama_ortu, $no_hp_ortu, $siswa['id_siswa']]);

        // B. Update tabel USERS (Data Nama)
        $stmtUser = $conn->prepare("UPDATE users SET nama_lengkap = ? WHERE id_user = ?");
        $stmtUser->execute([$nama_lengkap, $user['user_id']]);

        $conn->commit(); // Simpan permanen

        // Refresh Session Nama
        $_SESSION['nama'] = $nama_lengkap;
        
        $msg = "Profil berhasil diperbarui!";
        $msg_type = "success";
        
        // Refresh Data untuk Tampilan (Ambil ulang dari DB)
        $stmt->execute([$alamat, $nama_ortu, $no_hp_ortu, $siswa['id_siswa']]); // Re-execute update param is safe or just refetch
        
        // Fetch ulang data agar form terupdate
        $stmt = $conn->prepare("SELECT s.*, u.nama_lengkap FROM siswa s JOIN users u ON s.user_id = u.id_user WHERE s.user_id = ?");
        $stmt->execute([$user['user_id']]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        $conn->rollBack(); // Batalkan jika ada error
        $msg = "Gagal menyimpan: " . $e->getMessage();
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil & Data Ortu | Sikonsel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen p-6">

    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <div class="bg-emerald-600 p-6 flex justify-between items-center text-white">
            <div>
                <h1 class="text-xl font-bold">Edit Profil Siswa</h1>
                <p class="text-emerald-100 text-sm">Perbarui data diri dan orang tua</p>
            </div>
            <a href="../dashboard_siswa.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition">
                &larr; Kembali
            </a>
        </div>

        <div class="p-8">
            
            <?php if ($msg): ?>
                <div class="mb-6 p-4 rounded-xl text-sm font-bold <?= $msg_type == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>">
                    <?= $msg ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">👤 Data Pribadi</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NISN (Tidak bisa diubah)</label>
                        <input type="text" value="<?= htmlspecialchars($siswa['nisn'] ?? '-') ?>" disabled class="w-full bg-slate-100 border border-slate-200 rounded-lg p-2.5 text-slate-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($siswa['nama_lengkap']) ?>" required class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kelas</label>
                        <input type="text" value="<?= htmlspecialchars($siswa['kelas'] ?? '') ?>" disabled class="w-full bg-slate-100 border border-slate-200 rounded-lg p-2.5 text-slate-500 text-sm">
                        <p class="text-[10px] text-red-400 mt-1">*Hubungi admin jika kelas salah</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alamat Domisili</label>
                        <textarea name="alamat" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition text-sm"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-2 mb-4">👨‍👩‍👧 Data Orang Tua / Wali</h3>
                    
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 text-xs text-yellow-800 mb-2">
                        Mohon isi data orang tua dengan benar untuk keperluan komunikasi sekolah.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Orang Tua / Wali</label>
                        <input type="text" name="nama_ortu" value="<?= htmlspecialchars($siswa['nama_ortu'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No. HP Orang Tua (WhatsApp)</label>
                        <input type="number" name="no_hp_ortu" value="<?= htmlspecialchars($siswa['no_hp_ortu'] ?? '') ?>" required placeholder="08xxxxx" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition text-sm">
                    </div>
                </div>

                <div class="md:col-span-2 pt-4 border-t mt-2 flex gap-3">
                    <button type="submit" class="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                    
                    <a href="ganti_password.php" class="bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Ganti Password
                    </a>
                </div>

            </form>
        </div>
    </div>

</body>
</html>