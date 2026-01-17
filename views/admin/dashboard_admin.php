<?php
// File: Sikonsel/views/admin/dashboard_admin.php
require_once '../../includes/auth.php';
require_once '../../config/database.php';

// 1. Cek Login & Role
$user = checkLogin('../auth/login.php');
if ($user['role'] !== 'guru_bk') {
    header("Location: ../../index.php");
    exit;
}

// 2. Inisialisasi Variabel
$totalSiswa = 0;
$totalLaporanPending = 0;
$jadwalHariIni = 0;
$laporanTerbaru = [];
$error = "";

try {
    // A. Hitung Total Siswa
    $stmt = $conn->query("SELECT COUNT(*) FROM siswa");
    $totalSiswa = $stmt->fetchColumn();

    // B. Hitung Laporan Pending
    $stmt = $conn->query("SELECT COUNT(*) FROM laporan_bk WHERE status = 'Pending'");
    $totalLaporanPending = $stmt->fetchColumn();

    // C. Hitung Reservasi Hari Ini
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reservasi WHERE tgl_temu = ? AND status = 'Disetujui'");
    $stmt->execute([$today]);
    $jadwalHariIni = $stmt->fetchColumn();

    // D. Ambil 5 Laporan Terbaru
    $stmt = $conn->query("SELECT l.*, s.nama_lengkap 
                          FROM laporan_bk l 
                          JOIN siswa w ON l.id_siswa = w.id_siswa 
                          JOIN users s ON w.user_id = s.id_user 
                          ORDER BY l.tgl_laporan DESC LIMIT 5");
    $laporanTerbaru = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Database Error: " . $e->getMessage();
}

// 3. Logic Sapaan Waktu
$jam = date('H');
if ($jam < 12) $sapaan = "Selamat Pagi";
elseif ($jam < 15) $sapaan = "Selamat Siang";
elseif ($jam < 18) $sapaan = "Selamat Sore";
else $sapaan = "Selamat Malam";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Admin | Sikonsel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar-item:hover { background: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl z-20 hidden md:flex">
        
        <div class="p-6 flex items-center gap-3 border-b border-slate-800">
            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-800 border border-slate-700 p-1 overflow-hidden shadow-sm">
                <img src="../../assets/img/logo_sikonsel.png" 
                     alt="Logo" 
                     class="w-full h-full object-contain rounded-full"
                     onerror="this.parentElement.style.display='none'; document.getElementById('logo-fallback').style.display='flex'">
            </div>
            
            <div id="logo-fallback" class="w-10 h-10 bg-emerald-500 rounded-xl hidden items-center justify-center font-bold text-white shadow-lg shadow-emerald-500/50">BK</div>
            
            <div>
                <h1 class="text-xl font-bold tracking-wide leading-none">Sikonsel</h1>
                <p class="text-[10px] text-slate-400 font-medium tracking-wider mt-1">ADMINISTRATOR</p>
            </div>
        </div>

        <nav class="flex-1 py-6 space-y-2 overflow-y-auto">
            <p class="px-6 text-xs font-bold text-slate-500 uppercase mb-2">Menu Utama</p>
            
            <a href="dashboard_admin.php" class="flex items-center gap-3 px-6 py-3 text-emerald-400 bg-slate-800 border-r-4 border-emerald-500 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="siswa/list_siswa.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="font-medium">Data Siswa</span>
            </a>

            <a href="laporan/masuk_laporan.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Laporan Masuk</span>
                <?php if($totalLaporanPending > 0): ?>
                    <span class="bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full ml-auto animate-pulse"><?= $totalLaporanPending ?></span>
                <?php endif; ?>
            </a>

            <a href="reservasi/kelola_jadwal.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                <span class="font-medium">Jadwal Konseling</span>
            </a>

            <a href="info/list_info.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                <span class="font-medium">Info & Agenda</span>
            </a>

            <p class="px-6 text-xs font-bold text-slate-500 uppercase mt-6 mb-2">Akun</p>
            <a href="../auth/logout.php" class="sidebar-item flex items-center gap-3 px-6 py-3 text-red-400 hover:bg-red-500/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="font-medium">Logout</span>
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center z-10">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-slate-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <div>
                    <h2 class="text-xl font-bold text-slate-800"><?= $sapaan ?>, Pak/Bu Guru! 👋</h2>
                    <p class="text-xs text-slate-400">Semoga harimu menyenangkan.</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-700"><?= htmlspecialchars($user['nama']) ?></p>
                    <p class="text-xs text-emerald-600 font-medium">Administrator BK</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-600 font-bold border border-slate-200 shadow-sm">
                    <?= substr($user['nama'], 0, 1) ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
            
            <?php if($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 animate-pulse">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="text-sm font-bold"><?= $error ?></span>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
                            <h3 class="text-3xl font-bold text-slate-800 mt-1"><?= $totalSiswa ?></h3>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Laporan Pending</p>
                            <h3 class="text-3xl font-bold text-red-600 mt-1"><?= $totalLaporanPending ?></h3>
                        </div>
                        <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jadwal Hari Ini</p>
                            <h3 class="text-3xl font-bold text-emerald-600 mt-1"><?= $jadwalHariIni ?></h3>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                </div>

                 <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-lg text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold text-indigo-200 uppercase tracking-wider">Waktu Server</p>
                            <h3 class="text-3xl font-bold mt-1" id="realtimeClock">--:--</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-xs text-indigo-100 font-medium"><?= date('l, d F Y') ?></p>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-slate-800">Aktivitas Laporan Terbaru</h3>
                        <a href="laporan/masuk_laporan.php" class="text-sm text-blue-600 hover:underline font-medium">Lihat Semua</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-100">
                                    <th class="pb-3 pl-2">Siswa</th>
                                    <th class="pb-3">Kategori</th>
                                    <th class="pb-3">Tanggal</th>
                                    <th class="pb-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <?php if(empty($laporanTerbaru)): ?>
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-slate-400 italic">Belum ada laporan masuk.</td>
                                    </tr>
                                <?php else: foreach($laporanTerbaru as $row): ?>
                                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                        <td class="py-4 pl-2 font-bold text-slate-700"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                        <td class="py-4">
                                            <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded text-xs font-bold">
                                                <?= htmlspecialchars($row['kategori']) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 text-slate-500 text-xs"><?= date('d M Y', strtotime($row['tgl_laporan'])) ?></td>
                                        <td class="py-4 text-center">
                                            <?php 
                                            $statusColor = match($row['status']) {
                                                'Pending' => 'bg-yellow-100 text-yellow-700',
                                                'Diproses' => 'bg-blue-100 text-blue-700',
                                                'Selesai' => 'bg-green-100 text-green-700',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                            ?>
                                            <span class="<?= $statusColor ?> px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-slate-800">Akses Cepat</h3>
                        <a href="laporan/export/center.php" class="text-xs font-bold text-blue-600 hover:underline" title="Menu Filter Laporan">
                            Export Center &rarr;
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">  
                        <a href="siswa/tambah.php" class="flex flex-col items-center justify-center p-4 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition cursor-pointer group text-center border border-blue-100">
                            <svg class="w-6 h-6 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            <span class="text-[10px] font-bold">Tambah Siswa</span>
                        </a>

                        <a href="info/list_info.php" class="flex flex-col items-center justify-center p-4 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition cursor-pointer group text-center border border-indigo-100">
                            <svg class="w-6 h-6 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            <span class="text-[10px] font-bold">Kelola Info</span>
                        </a>

                        <a href="laporan/export/generate_pdf.php" target="_blank" class="flex flex-col items-center justify-center p-4 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition cursor-pointer group text-center border border-rose-100">
                            <svg class="w-6 h-6 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span class="text-[10px] font-bold">Rekap PDF</span>
                        </a>

                        <a href="laporan/export/generate_excel.php" class="flex flex-col items-center justify-center p-4 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-100 transition cursor-pointer group text-center border border-emerald-100">
                            <svg class="w-6 h-6 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-[10px] font-bold">Unduh Excel</span>
                        </a>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('realtimeClock').textContent = `${hours}:${minutes}`;
        }
        setInterval(updateClock, 1000); 
        updateClock(); 
    </script>
</body>
</html>