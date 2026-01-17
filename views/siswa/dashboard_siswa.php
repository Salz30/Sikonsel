<?php
// File: Sikonsel/views/siswa/dashboard_siswa.php

require_once '../../includes/auth.php';
require_once '../../config/database.php';

// 1. Cek Login
$user = checkLogin('../auth/login.php');

// 2. Proteksi Role (Hanya Siswa)
if ($user['role'] !== 'siswa') {
    header("Location: ../../index.php");
    exit;
}

// 3. FITUR KEAMANAN: Paksa Ganti Password
$stmtCek = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
$stmtCek->execute([$user['user_id']]);
$dbUser = $stmtCek->fetch();

if ($dbUser && password_verify('123456', $dbUser['password'])) {
    echo "<script>
        alert('PERINGATAN KEAMANAN: Anda masih menggunakan password default (123456). Demi keamanan data, Anda WAJIB mengganti password sekarang juga sebelum melanjutkan.');
        window.location.href = 'profil/ganti_password.php';
    </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Siswa | Sikonsel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen text-slate-800">

    <nav class="bg-white/80 backdrop-blur-md border-b border-indigo-100 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg shadow-blue-200">
                S
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight tracking-tight">Sikonsel</h1>
                <p class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">Dashboard Siswa</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden sm:block text-right">
                <span class="block text-sm font-bold text-slate-700">Halo, <?= htmlspecialchars($user['nama']) ?> 👋</span>
                <span class="block text-xs text-slate-400">Siswa Aktif</span>
            </div>
            <a href="../auth/logout.php" class="bg-white border border-red-100 text-red-500 hover:bg-red-50 px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow-md">
                Keluar
            </a>
        </div>
    </nav>

    <main class="p-6 md:p-10 max-w-6xl mx-auto">
        
        <div class="mb-12 text-center md:text-left md:flex items-end justify-between">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-2">
                    Selamat Datang di <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Ruang BK Digital</span>
                </h2>
                <p class="text-slate-500 max-w-xl leading-relaxed mt-2">
                    Kami siap mendengarkan ceritamu, menjaga rahasiamu, dan membantumu tumbuh lebih baik.
                </p>
            </div>
            <div class="hidden md:block text-right">
                <p class="text-3xl font-bold text-indigo-200"><?= date('d') ?></p>
                <p class="text-sm font-bold text-indigo-400 uppercase"><?= date('F Y') ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-12">

            <a href="laporan/buat_laporan.php" class="glass-card p-6 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border-b-4 border-blue-500">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 group-hover:text-blue-600 transition-colors">Curhat Online</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Ceritakan masalahmu secara privat.</p>
            </a>

            <a href="laporan/riwayat_saya.php" class="glass-card p-6 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border-b-4 border-orange-500">
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 group-hover:text-orange-600 transition-colors">Riwayat</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Pantau status laporanmu.</p>
            </a>

            <a href="reservasi/jadwal_saya.php" class="glass-card p-6 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border-b-4 border-pink-500">
                <div class="w-14 h-14 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 group-hover:text-pink-600 transition-colors">Janji Temu</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Buat jadwal tatap muka.</p>
            </a>

            <a href="profil/edit_profil.php" class="glass-card p-6 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border-b-4 border-purple-500">
                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 group-hover:text-purple-600 transition-colors">Data Ortu</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Update kontak wali murid.</p>
            </a>

            <a href="profil/ganti_password.php" class="glass-card p-6 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border-b-4 border-emerald-500">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-slate-800 group-hover:text-emerald-600 transition-colors">Keamanan</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Ubah password akunmu.</p>
            </a>
        </div>

        <div class="mt-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    Info Agenda & Beasiswa
                </h3>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <?php
                try {
                    // Fetch Data dari Tabel info_sekolah
                    $stmtInfo = $conn->query("SELECT * FROM info_sekolah ORDER BY tgl_posting DESC LIMIT 5");
                    
                    if ($stmtInfo->rowCount() > 0) {
                        while ($info = $stmtInfo->fetch(PDO::FETCH_ASSOC)) {
                            // Tentukan Style Berdasarkan Kategori
                            $isBeasiswa = ($info['kategori'] == 'Beasiswa');
                            
                            // Warna Icon Box
                            $iconBg = $isBeasiswa ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600';
                            
                            // Warna Badge Kategori
                            $badgeStyle = $isBeasiswa 
                                ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' 
                                : 'bg-indigo-100 text-indigo-700 border border-indigo-200';
                            
                            // Parsing Tanggal
                            $tgl = strtotime($info['tgl_posting']);
                            $dateNum = date('d', $tgl);
                            $monthName = date('M', $tgl);
                            ?>
                            
                            <div class="glass-card p-5 rounded-3xl flex flex-col sm:flex-row gap-5 items-start sm:items-center hover:bg-white/60 transition-colors duration-300">
                                <div class="shrink-0 w-16 h-16 rounded-2xl flex flex-col items-center justify-center shadow-sm <?php echo $iconBg; ?>">
                                    <span class="text-xl font-bold leading-none"><?php echo $dateNum; ?></span>
                                    <span class="text-[10px] font-bold uppercase mt-1"><?php echo $monthName; ?></span>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider <?php echo $badgeStyle; ?>">
                                            <?php echo htmlspecialchars($info['kategori']); ?>
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-slate-800 text-lg leading-snug">
                                        <?php echo htmlspecialchars($info['judul']); ?>
                                    </h4>
                                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                                        <?php echo nl2br(htmlspecialchars($info['isi_info'])); ?>
                                    </p>
                                </div>
                            </div>

                            <?php
                        }
                    } else {
                        // Tampilan Jika Kosong
                        echo '
                        <div class="text-center py-12 glass-card rounded-3xl">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-slate-400 font-medium">Belum ada informasi terbaru.</p>
                        </div>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="p-4 bg-red-50 text-red-500 rounded-xl text-sm">Gagal memuat info: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </div>

    </main>
</body>
</html>