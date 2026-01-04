<?php
require_once '../../includes/auth.php';

// Cek jika sudah ada cookie valid, lempar ke sikonsel.php
if (isset($_COOKIE['sikonsel_session'])) {
    if (verifyToken($_COOKIE['sikonsel_session'])) {
        header("Location: ../../index.php");
        exit;
    }
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Logika Login
    require_once '../../config/database.php';
    
    // ... (Kode logika login sama seperti sebelumnya, tapi ubah redirect di bawah ini) ...
    // Gunakan fungsi login() dari auth.php atau logika manual Anda
    
    // CONTOH PEMANGGILAN LOGIN SEDERHANA (SESUAIKAN DENGAN KODE ANDA YANG SUDAH BERHASIL)
    if (login($username, $password, $conn)) {
         header("Location: ../../index.php"); // UPDATE DISINI
         exit;
    } else {
         $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <!-- Tampilan sama seperti sebelumnya -->
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 border border-slate-100">
        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold text-blue-600">Sikonsel Essp4r</h1>
            <p class="text-slate-500 text-sm">SMPN 4 Rancaekek</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-3 rounded-xl border">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700">Masuk</button>
        </form>
        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-400 mb-2">Anda Orang Tua / Wali Murid?</p>
            <a href="../../lapor_ortu/laporan_ortu.php" class="inline-block w-full border-2 border-slate-200 text-slate-600 font-bold py-3 rounded-xl hover:border-blue-500 hover:text-blue-600 transition">
            Lapor / Konsultasi Anak →
            </a>
        </div>
    </div>
</body>
</html>