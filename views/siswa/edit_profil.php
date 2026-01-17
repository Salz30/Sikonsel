<?php
// MUNDUR 3 LANGKAH KE ROOT (profil -> siswa -> views -> root)
require_once '../../../includes/auth.php';
require_once '../../../config/database.php';

// Cek Login
$user = checkLogin('../../auth/login.php');

// AMBIL DATA SISWA DARI DATABASE
// Kita perlu mengambil data terbaru agar form tidak kosong
$stmt = $conn->prepare("SELECT s.*, u.nama_lengkap 
                        FROM siswa s 
                        JOIN users u ON s.user_id = u.id_user 
                        WHERE s.user_id = ?");
$stmt->execute([$user['user_id']]);
$siswa = $stmt->fetch();

// JIKA TOMBOL SIMPAN DITEKAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat     = $_POST['alamat'];
    $nama_ortu  = $_POST['nama_ortu'];
    $no_hp_ortu = $_POST['no_hp_ortu'];
    $id_siswa   = $siswa['id_siswa'];

    // Query Update
    $update = $conn->prepare("UPDATE siswa SET alamat=?, nama_ortu=?, no_hp_ortu=? WHERE id_siswa=?");
    
    if($update->execute([$alamat, $nama_ortu, $no_hp_ortu, $id_siswa])){
        echo "<script>
                alert('Data berhasil disimpan!'); 
                window.location='edit_profil.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menyimpan data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-8 py-4 sticky top-0 z-10 flex items-center gap-4">
        <a href="../dashboard_siswa.php" class="text-slate-500 hover:text-purple-600 font-bold transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali ke Dashboard
        </a>
        <h1 class="text-xl font-bold text-slate-800 border-l pl-4 border-slate-300">Profil Saya</h1>
    </nav>

    <main class="p-6 max-w-lg mx-auto mt-6">
        <div class="bg-white p-8 rounded-3xl shadow-lg border border-slate-100 relative overflow-hidden">
            
            <div class="text-center mb-8 relative z-10">
                <div class="w-24 h-24 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-4xl mx-auto mb-4 shadow-sm border-4 border-white">
                    🎓
                </div>
                <h2 class="font-bold text-2xl text-slate-800"><?= htmlspecialchars($siswa['nama_lengkap']) ?></h2>
                <p class="text-slate-500 text-sm font-medium mt-1">
                    NISN: <?= htmlspecialchars($siswa['nisn']) ?> | Kelas: <?= htmlspecialchars($siswa['kelas']) ?>
                </p>
            </div>

            <form method="POST" class="space-y-5 relative z-10">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wide">Alamat Rumah</label>
                    <textarea name="alamat" rows="2" class="w-full border border-slate-200 rounded-xl p-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-slate-700 bg-slate-50"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
                </div>
                
                <div class="p-5 bg-purple-50 rounded-2xl border border-purple-100 space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xl">👨‍👩‍👧</span>
                        <p class="text-sm font-bold text-purple-700 uppercase tracking-wide">Data Orang Tua / Wali</p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Nama Orang Tua</label>
                        <input type="text" name="nama_ortu" value="<?= htmlspecialchars($siswa['nama_ortu'] ?? '') ?>" placeholder="Nama Ayah/Ibu" class="w-full border border-purple-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">WhatsApp Orang Tua (Gunakan 62..)</label>
                        <input type="number" name="no_hp_ortu" value="<?= htmlspecialchars($siswa['no_hp_ortu'] ?? '') ?>" placeholder="Contoh: 62812345678" class="w-full border border-purple-200 rounded-xl p-3 text-sm focus:ring-2 focus:ring-purple-400 outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition shadow-lg shadow-purple-200 active:scale-95 transform duration-150">
                    💾 Simpan Perubahan
                </button>
            </form>

        </div>
    </main>

</body>
</html>