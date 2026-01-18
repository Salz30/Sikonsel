<?php
session_start();
require_once '../../config/database.php';
// Panggil auth.php untuk akses fungsi setAppLogin()
require_once '../../includes/auth.php'; 

// Cek apakah user sudah login? (Cek Session & Cookie)
// Kita panggil fungsi checkLogin() tapi dengan mode 'silent' (tidak redirect jika gagal)
// Tujuannya hanya untuk redirect user yang SUDAH login agar tidak bisa buka halaman login lagi.
if (isset($_SESSION['user_id'])) {
    // Jika ada session, langsung arahkan ke dashboard masing-masing
    if ($_SESSION['role'] == 'guru_bk' || $_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard_admin.php");
    } else {
        header("Location: ../siswa/dashboard_siswa.php");
    }
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Ambil data user dari database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            // --- MODIFIKASI UTAMA ---
            // Gunakan fungsi setAppLogin() dari auth.php
            // Fungsi ini otomatis membuat Session DAN Cookie (Syarat Ujian)
            setAppLogin($user); 
            // ------------------------

            // Redirect sesuai role
            if ($user['role'] == 'guru_bk' || $user['role'] == 'admin') {
                header("Location: ../admin/dashboard_admin.php");
            } elseif ($user['role'] == 'siswa') {
                header("Location: ../siswa/dashboard_siswa.php");
            } else {
                header("Location: ../../index.php"); 
            }
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    } catch (Exception $e) {
        $error = "Terjadi kesalahan sistem: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-600 via-indigo-600 to-emerald-600 min-h-screen flex items-center justify-center p-4">

    <div class="glass-card w-full max-w-md p-8 rounded-3xl shadow-2xl relative overflow-hidden">
        
        <div class="absolute -top-10 -left-10 w-32 h-32 bg-blue-400 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob"></div>
        <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-emerald-400 rounded-full mix-blend-multiply filter blur-2xl opacity-30 animate-blob animation-delay-2000"></div>

        <div class="text-center mb-8 relative z-10">
            <div class="w-24 h-24 mx-auto bg-white rounded-full p-2 shadow-lg mb-4 flex items-center justify-center border-2 border-slate-100">
                <img src="../../assets/img/logo_sikonsel.png" 
                     alt="Logo Sikonsel" 
                     class="w-full h-full object-contain rounded-full"
                     onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='flex'">
                
                <div id="logo-fallback" class="hidden w-full h-full bg-indigo-600 text-white rounded-full items-center justify-center font-bold text-2xl">
                    BK
                </div>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Selamat Datang</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Informasi Konseling Sekolah</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-600 p-3 rounded-lg text-sm mb-6 flex items-center gap-2 shadow-sm animate-pulse">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5 relative z-10">
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-2 ml-1">Username / NISN</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input type="text" 
                           name="username" 
                           required 
                           placeholder="Masukkan username anda" 
                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-2 ml-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" 
                           name="password" 
                           required 
                           placeholder="Masukkan password" 
                           class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all shadow-sm text-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-8 text-center relative z-10">
            <p class="text-xs text-slate-400">&copy; 2026 Sikonsel - Layanan BK Digital</p>
        </div>

    </div>

</body>
</html>