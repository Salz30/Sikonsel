<?php
// File: Sikonsel/views/siswa/profil/ganti_password.php

// Mundur 3 langkah ke root (views -> siswa -> profil -> root)
require_once '../../../includes/auth.php';
require_once '../../../config/database.php';

// Cek Login
$user = checkLogin('../../auth/login.php');

$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    // 1. Ambil password user saat ini dari database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->execute([$user['user_id']]);
    $currentUser = $stmt->fetch();

    // 2. Validasi Password Lama
    if (!password_verify($pass_lama, $currentUser['password'])) {
        $msg = "Password lama salah!";
        $msg_type = "error";
    } 
    // 3. Validasi Password Baru
    elseif ($pass_baru !== $konfirmasi) {
        $msg = "Konfirmasi password baru tidak cocok!";
        $msg_type = "error";
    } 
    elseif (strlen($pass_baru) < 6) {
        $msg = "Password baru minimal 6 karakter!";
        $msg_type = "error";
    }
    // 4. Proses Update Password
    else {
        $hashBaru = password_hash($pass_baru, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        
        if ($update->execute([$hashBaru, $user['user_id']])) {
            $msg = "Password berhasil diubah! Silakan ingat password baru Anda.";
            $msg_type = "success";
        } else {
            $msg = "Terjadi kesalahan sistem.";
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password | Sikonsel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-white border-b px-6 py-4 sticky top-0 z-10 flex items-center gap-4 shadow-sm">
        <a href="../dashboard_siswa.php" class="text-slate-500 hover:text-emerald-600 font-bold transition flex items-center gap-2 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali
        </a>
        <h1 class="text-lg font-bold text-slate-800 border-l pl-4 border-slate-300">Keamanan Akun</h1>
    </nav>

    <main class="p-4 md:p-6 max-w-md mx-auto mt-6">
        <div class="bg-white p-6 md:p-8 rounded-3xl shadow-lg border border-slate-100">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-3">
                    🔒
                </div>
                <h2 class="font-bold text-xl text-slate-800">Ganti Password</h2>
                <p class="text-slate-500 text-xs mt-1">Demi keamanan, ganti password default Anda.</p>
            </div>

            <?php if ($msg): ?>
                <div class="p-3 rounded-xl mb-5 text-xs font-bold text-center <?= $msg_type == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>">
                    <?= $msg ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Password Lama</label>
                    <input type="password" name="pass_lama" required placeholder="Masukkan password saat ini" class="w-full border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-emerald-500 transition text-sm">
                </div>
                
                <hr class="border-slate-100 my-2">

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Password Baru</label>
                    <input type="password" name="pass_baru" required placeholder="Minimal 6 karakter" class="w-full border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-emerald-500 transition text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ulangi Password Baru</label>
                    <input type="password" name="konfirmasi" required placeholder="Ketik ulang password baru" class="w-full border border-slate-200 rounded-xl p-3 outline-none focus:ring-2 focus:ring-emerald-500 transition text-sm">
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 mt-2 text-sm">
                    Simpan Password Baru
                </button>
            </form>

        </div>
    </main>

</body>
</html>