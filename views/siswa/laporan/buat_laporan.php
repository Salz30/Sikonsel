<?php
// Path: views/siswa/laporan/buat_laporan.php
// Mundur 3 langkah: laporan -> siswa -> views -> root
require_once '../../../includes/auth.php';
require_once '../../../includes/laporan_controller.php';
require_once '../../../includes/siswa_controller.php';

$user = checkLogin();

// Keamanan: Pastikan user benar-benar siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$siswaCurrent = $stmt->fetch();

if (!$siswaCurrent) {
    echo "<script>alert('Akses ditolak.'); window.location='../../dashboard_siswa.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'id_siswa' => $siswaCurrent['id_siswa'],
        'judul'    => $_POST['judul'],
        'kategori' => $_POST['kategori'],
        'isi'      => $_POST['isi']
    ];

    if (tambahLaporan($conn, $data)) {
        // Redirect ke file RIWAYAT yang baru
        header("Location: riwayat_saya.php?msg=sent"); 
        exit;
    } else {
        $error = "Gagal mengirim laporan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tulis Curhatan | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-blue-50 min-h-screen p-6 flex justify-center items-center">
    
    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl p-8 border border-blue-100">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Ruang Cerita</h2>
            <a href="../dashboard_siswa.php" class="w-8 h-8 flex items-center justify-center bg-slate-100 rounded-full text-slate-500 hover:bg-red-100 hover:text-red-500 transition">✕</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kamu</label>
                <input type="text" value="<?= htmlspecialchars($user['nama']); ?>" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-slate-500 font-bold cursor-not-allowed">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Topik Masalah</label>
                    <select name="judul" required class="w-full border border-slate-200 rounded-xl p-3 bg-white focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="" disabled selected>Pilih Topik...</option>
                        <option value="Saya merasa dibully">Perundungan / Bullying</option>
                        <option value="Masalah Belajar">Kesulitan Belajar</option>
                        <option value="Masalah Teman">Hubungan Pertemanan</option>
                        <option value="Masalah Keluarga">Keluarga</option>
                        <option value="Kecemasan / Sedih">Kecemasan / Emosi</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="kategori" required class="w-full border border-slate-200 rounded-xl p-3 bg-white focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="Pribadi">Pribadi</option>
                        <option value="Sosial">Sosial</option>
                        <option value="Belajar">Belajar</option>
                        <option value="Karir">Karir</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Ceritakan Selengkapnya</label>
                <textarea name="isi" rows="6" required placeholder="Tuliskan apa yang kamu rasakan..." class="w-full border border-slate-200 rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
                <p class="text-xs text-slate-400 mt-2 text-right">🔒 Pesanmu dienkripsi dan aman.</p>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1">
                Kirim Curhatan
            </button>
        </form>
    </div>
</body>
</html>