<?php
// File: views/admin/siswa/detail.php

require_once '../../../includes/auth.php';
require_once '../../../config/database.php';
require_once '../../../includes/siswa_controller.php';
require_once '../../../includes/laporan_controller.php';
require_once '../../../includes/reservasi_controller.php';
require_once '../../../includes/encryption.php';

// 1. Cek Login & Role (PENTING: Agar aman dari akses ilegal)
$user = checkLogin('../../auth/login.php');
if ($user['role'] !== 'guru_bk') {
    header("Location: ../../../index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: list_siswa.php"); exit; }

$siswa = getSiswaById($conn, $id);
if (!$siswa) die("Siswa tidak ditemukan.");

$historyLaporan   = getLaporanBySiswa($conn, $id);
$historyReservasi = getReservasiBySiswa($conn, $id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail - <?= htmlspecialchars($siswa['nama_lengkap']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen pb-10">
    <nav class="bg-white border-b px-8 py-4 sticky top-0 z-20 shadow-sm flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="list_siswa.php" class="text-slate-500 hover:text-blue-600 font-bold text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
            <h1 class="text-xl font-bold text-slate-800">Profil Siswa</h1>
        </div>
        <a href="edit.php?id=<?= $id ?>" class="px-4 py-2 bg-yellow-500 text-white rounded-lg text-xs font-bold hover:bg-yellow-600 transition shadow-sm flex items-center gap-2">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit Data
        </a>
    </nav>

    <main class="p-6 max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-20 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                <div class="relative w-24 h-24 mx-auto bg-white rounded-full p-1 shadow-lg -mt-4 mb-4">
                    <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-3xl">
                        🎓
                    </div>
                </div>
                <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($siswa['nama_lengkap']) ?></h2>
                <p class="text-slate-500 text-sm font-medium"><?= htmlspecialchars($siswa['kelas']) ?> | <?= htmlspecialchars($siswa['nisn']) ?></p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <h3 class="font-bold text-slate-800 text-sm uppercase mb-4 border-b pb-2 tracking-wider">Detail Informasi</h3>
                <ul class="space-y-4 text-sm">
                    <li>
    <span class="block text-xs text-slate-400 font-bold uppercase mb-1">Alamat</span>
    <p class="text-slate-700"><?= htmlspecialchars($siswa['alamat'] ?: '-') ?></p>
</li>

<li>
    <span class="block text-xs text-slate-400 font-bold uppercase mb-1">Nama Orang Tua / Wali</span>
    <p class="text-slate-700 font-medium"><?= htmlspecialchars($siswa['nama_ortu'] ?? $siswa['nama_wali'] ?? '-') ?></p>
</li>

<li>
    <span class="block text-xs text-slate-400 font-bold uppercase mb-1">Kontak Ortu (HP)</span>
    <div class="flex items-center gap-2">
        <p class="text-slate-700"><?= htmlspecialchars($siswa['no_hp_ortu'] ?? $siswa['no_telp_ortu'] ?? '-') ?></p>
        
        <?php if(!empty($siswa['no_hp_ortu'])): ?>
            <a href="https://wa.me/<?= preg_replace('/^0/', '62', $siswa['no_hp_ortu']) ?>" target="_blank" class="text-green-600 hover:text-green-700 text-xs font-bold bg-green-50 px-2 py-1 rounded border border-green-200">
                Chat WA
            </a>
        <?php endif; ?>
    </div>
</li>
<li>
    <span class="block text-xs text-slate-400 font-bold uppercase mb-1">Akun User</span>
    </li>
                </ul>
            </div>
        </div>

        <div class="lg:col-span-2" x-data="{ activeTab: 'riwayat' }">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-2 flex mb-6">
                <button @click="activeTab = 'riwayat'" 
                        :class="activeTab === 'riwayat' ? 'bg-blue-100 text-blue-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" 
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2">
                    📂 Riwayat Konseling
                </button>
                <button @click="activeTab = 'jadwal'" 
                        :class="activeTab === 'jadwal' ? 'bg-pink-100 text-pink-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" 
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 flex items-center justify-center gap-2">
                    📅 Jadwal Temu
                </button>
            </div>

            <div x-show="activeTab === 'riwayat'" class="space-y-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <?php if(empty($historyLaporan)): ?>
                    <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-slate-400">
                        <div class="text-4xl mb-2">📭</div>
                        <p>Belum ada riwayat laporan.</p>
                    </div>
                <?php else: foreach($historyLaporan as $lap): ?>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase rounded border border-blue-100">
                                <?= htmlspecialchars($lap['kategori']) ?>
                            </span>
                            <span class="text-xs text-slate-400 font-medium">
                                <?= date('d M Y', strtotime($lap['tgl_laporan'])) ?>
                            </span>
                        </div>
                        <h4 class="font-bold text-slate-800 text-lg"><?= htmlspecialchars($lap['judul_laporan']) ?></h4>
                        <p class="text-sm text-slate-500 mt-2 line-clamp-2">
                            <?= htmlspecialchars(substr($lap['isi_laporan_dekripsi'], 0, 150)) ?>...
                        </p>
                        <div class="mt-4 pt-3 border-t border-slate-50 flex justify-between items-center">
                            <span class="text-xs font-bold px-2 py-1 rounded 
                                <?= $lap['status'] == 'Selesai' ? 'bg-green-100 text-green-700' : ($lap['status'] == 'Diproses' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                <?= $lap['status'] ?>
                            </span>
                            <a href="../laporan/detail_laporan.php?id=<?= $lap['id_laporan'] ?>" class="text-blue-600 text-xs font-bold hover:underline">Lihat Detail →</a>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <div x-show="activeTab === 'jadwal'" style="display: none;" class="space-y-4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <?php if(empty($historyReservasi)): ?>
                    <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-slate-200 text-slate-400">
                        <div class="text-4xl mb-2">📅</div>
                        <p>Belum ada riwayat janji temu.</p>
                    </div>
                <?php else: foreach($historyReservasi as $res): ?>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-pink-50 text-pink-600 rounded-xl flex flex-col items-center justify-center font-bold text-xs leading-tight border border-pink-100">
                                <span class="text-lg"><?= date('d', strtotime($res['tgl_temu'])) ?></span>
                                <span class="uppercase text-[10px]"><?= date('M', strtotime($res['tgl_temu'])) ?></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800"><?= htmlspecialchars($res['keperluan']) ?></h4>
                                <p class="text-xs text-slate-500 font-medium">Pukul <?= date('H:i', strtotime($res['jam_temu'])) ?> WIB</p>
                                <?php if($res['catatan_guru']): ?>
                                    <p class="text-[10px] text-slate-400 mt-1 italic">"<?= htmlspecialchars($res['catatan_guru']) ?>"</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                             <span class="text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider
                                <?= $res['status'] == 'Disetujui' ? 'bg-green-100 text-green-700' : 
                                   ($res['status'] == 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                                <?= $res['status'] ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>

        </div>
    </main>
</body>
</html>