<?php
/**
 * File: index.php (Landing Page Revisi Final)
 * Author: Salman Azhar Latisio
 */
session_start();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sikonsel | SMPN 4 Rancaekek</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-nav { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
    </style>
</head>
<body class="bg-white text-slate-900">

    <nav class="fixed w-full z-50 glass-nav border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="assets/img/logo_sikonsel.png" alt="Logo" class="h-10">
                <span class="text-2xl font-black text-indigo-900 tracking-tighter italic">SIKONSEL</span>
            </div>
            <div class="hidden md:flex space-x-8 font-bold text-sm uppercase tracking-widest text-slate-600">
                <a href="#home" class="hover:text-indigo-600 transition">Beranda</a>
                <a href="#tentang" class="hover:text-indigo-600 transition">Visi & Misi</a>
                <a href="#layanan" class="hover:text-indigo-600 transition">Layanan</a>
                <a href="views/auth/login.php" class="bg-indigo-600 text-white px-6 py-2 rounded-full hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition">Login</a>
            </div>
        </div>
    </nav>

    <section id="home" class="min-h-screen flex items-center pt-20 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center relative z-10">
            <div>
                <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest mb-6 inline-block">Selamat Datang di Sikonsel</span>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">Wadah Digital <br><span class="text-indigo-600">Siswa Hebat</span></h1>
                <p class="text-lg text-slate-600 mb-8 leading-relaxed">Platform resmi Bimbingan Konseling SMPN 4 Rancaekek. Tempat terbaik untuk berbagi cerita, mencari solusi, dan mengembangkan potensi dirimu bersama guru BK kami.</p>
                <div class="flex space-x-4">
                    <a href="views/auth/login.php" class="bg-indigo-600 text-white px-8 py-4 rounded-2xl font-bold shadow-xl hover:bg-indigo-700 transition">Mulai Curhat</a>
                    <a href="https://www.instagram.com/smpn4rck.official/" target="_blank" class="bg-white border border-slate-200 px-8 py-4 rounded-2xl font-bold flex items-center hover:bg-slate-50 transition">
                        <i class="fab fa-instagram mr-2 text-pink-600"></i> Instagram Sekolah
                    </a>
                </div>
            </div>
            <div class="relative">
                <img src="assets/img/gambar 1.jpg" alt="Siswa SMPN 4 Rancaekek" class="rounded-[3rem] shadow-2xl border-[12px] border-white w-full h-[500px] object-cover">
            </div>
        </div>
    </section>

    <section id="tentang" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold mb-4">Mengenal Sikonsel</h2>
                <p class="text-slate-500">Membangun karakter siswa melalui teknologi yang humanis.</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <img src="assets/img/gambar 5.jpg" alt="Visi Misi" class="rounded-[2.5rem] shadow-xl h-[400px] w-full object-cover">
                <div>
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-indigo-600 mb-3"><i class="fas fa-bullseye mr-2"></i> Tujuan</h3>
                        <p class="text-slate-600 leading-relaxed italic">"Mempermudah siswa dalam menyampaikan aspirasi dan masalah pribadi tanpa rasa takut, serta membantu guru BK dalam manajemen data konseling yang efisien dan terenkripsi."</p>
                    </div>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-bold mb-3">Visi</h3>
                            <p class="text-sm text-slate-500">Menjadi wadah digital terdepan dalam mendukung kesehatan mental dan perkembangan karakter siswa SMPN 4 Rancaekek.</p>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-3">Misi</h3>
                            <ul class="text-sm text-slate-500 space-y-2">
                                <li>• Akses konseling mudah & rahasia.</li>
                                <li>• Integrasi teknologi dalam BK.</li>
                                <li>• Komunikasi harmonis siswa & guru.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-10 rounded-[3rem] shadow-sm hover:shadow-xl transition border border-slate-100">
                    <img src="assets/img/reservasi_jadwal.jpg" alt="Jadwal" class="rounded-2xl mb-6 h-40 w-full object-cover">
                    <h3 class="text-xl font-bold mb-3">Reservasi Jadwal</h3>
                    <p class="text-sm text-slate-500">Atur pertemuan tatap muka dengan guru BK tanpa harus mengantri di ruang BK.</p>
                </div>
                <div class="bg-white p-10 rounded-[3rem] shadow-sm hover:shadow-xl transition border border-slate-100">
                    <img src="assets/img/gambar 2.jpg" alt="Curhat" class="rounded-2xl mb-6 h-40 w-full object-cover">
                    <h3 class="text-xl font-bold mb-3">Laporan & Curhat</h3>
                    <p class="text-sm text-slate-500">Sampaikan masalahmu secara digital. Privasi terjamin aman dengan enkripsi AES-256.</p>
                </div>
                <div class="bg-white p-10 rounded-[3rem] shadow-sm hover:shadow-xl transition border border-slate-100">
                    <img src="assets/img/update_informasi.jpg" alt="Info" class="rounded-2xl mb-6 h-40 w-full object-cover">
                    <h3 class="text-xl font-bold mb-3">Update Informasi</h3>
                    <p class="text-sm text-slate-500">Dapatkan berita beasiswa, agenda sekolah, dan tips menarik untuk belajarmu.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-indigo-900 text-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl font-bold mb-8">Terhubung dengan Kami</h2>
            <p class="text-indigo-200 mb-12 max-w-2xl mx-auto">Kami tidak memiliki website utama sekolah, segala informasi kegiatan resmi dapat kamu lihat melalui akun Instagram kami.</p>
            <a href="https://www.instagram.com/smpn4rck.official/" target="_blank" class="inline-flex items-center bg-white text-indigo-900 px-10 py-5 rounded-3xl font-bold text-xl hover:bg-indigo-50 transition">
                <i class="fab fa-instagram mr-3 text-3xl text-pink-600"></i> Follow @smpn4rck.official
            </a>
            
            <div class="mt-20 grid md:grid-cols-2 gap-8 text-left border-t border-indigo-800 pt-16">
                <div>
                    <h4 class="font-bold text-lg mb-4">Lokasi Sekolah</h4>
                    <p class="text-indigo-300 text-sm">Jl. Bojongloa No.241, Bojongloa, Kec. Rancaekek, Kabupaten Bandung, Jawa Barat 40394.</p>
                </div>
                <div class="md:text-right">
                    <h4 class="font-bold text-lg mb-4">Jam Layanan BK</h4>
                    <p class="text-indigo-300 text-sm">Senin - Jumat: 07.30 - 16.00 WIB<br>Sabtu - Minggu: Tutup</p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-800 rounded-full opacity-50"></div>
    </section>

    <footer class="bg-white py-8 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[10px] md:text-xs text-slate-400 font-bold tracking-[0.2em] uppercase leading-relaxed">
                @copyright by 23552011046_Salman Azhar Latisio_RP 23 CNS A_UASWEB 1
            </p>
        </div>
    </footer>

</body>
</html>