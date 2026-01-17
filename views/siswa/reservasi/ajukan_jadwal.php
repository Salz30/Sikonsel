<?php
// Path: views/siswa/reservasi/ajukan_jadwal.php
require_once '../../../includes/auth.php';
require_once '../../../includes/reservasi_controller.php';
require_once '../../../includes/encryption.php';

$user = checkLogin();

// Ambil ID Siswa
$stmt = $conn->prepare("SELECT id_siswa FROM siswa WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$siswa = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (insertReservasi($conn, $siswa['id_siswa'], $_POST['tanggal'], $_POST['jam'], $_POST['keperluan'])) {
        // Redirect ke file JADWAL yang baru
        header("Location: jadwal_saya.php?msg=success");
        exit;
    } else {
        $error = "Gagal membuat janji.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Janji Temu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-pink-50 min-h-screen p-6">
    
    <nav class="max-w-md mx-auto mb-6 flex justify-between items-center">
        <a href="jadwal_saya.php" class="text-slate-500 hover:text-pink-600 font-medium transition">← Jadwal</a>
        <h1 class="text-xl font-bold text-slate-800">Buat Janji Temu</h1>
        <a href="../dashboard_siswa.php" class="text-slate-500 hover:text-pink-600 font-medium transition">Dashboard</a>
    </nav>

    <div class="max-w-md w-full mx-auto bg-white rounded-3xl shadow-xl p-8 border border-pink-100">
        <h2 class="text-2xl font-bold text-slate-800 mb-1">Buat Janji Temu</h2>
        <p class="text-sm text-slate-500 mb-6">Pilih waktu untuk bertemu Guru BK.</p>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" required class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-pink-500 outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Jam (WIB)</label>
                <input type="time" name="jam" required class="w-full border rounded-xl p-3 focus:ring-2 focus:ring-pink-500 outline-none">
                <p class="text-[10px] text-slate-400 mt-1">*Jam operasional: 08:00 - 14:00</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Keperluan</label>
                <select name="keperluan" class="w-full border rounded-xl p-3 bg-white outline-none">
                    <option>Konseling Pribadi</option>
                    <option>Masalah Belajar</option>
                    <option>Lainnya</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="jadwal_saya.php" class="w-1/2 text-center py-3 border rounded-xl text-slate-600 font-bold hover:bg-slate-50">Batal</a>
                <button type="submit" class="w-1/2 bg-pink-600 text-white py-3 rounded-xl font-bold hover:bg-pink-700 shadow-lg shadow-pink-200">Ajukan</button>
            </div>
        </form>
    </div>
</body>
</html>