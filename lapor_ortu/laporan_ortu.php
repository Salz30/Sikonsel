<?php
/**
 * Halaman Pengaduan Orang Tua (Public Access)
 * SECURITY UPDATE: Force Logout & Session Destroy
 */

// 1. MULAI SESI UNTUK MENGECEK STATUS
session_start();

// 2. SECURITY: FORCE LOGOUT (Hapus semua sesi aktif)
// Ini mencegah Admin yang lupa logout 'terbawa' status loginnya ke halaman ini
if (isset($_SESSION['user_id']) || isset($_SESSION['role'])) {
    // Kosongkan array session
    $_SESSION = array();

    // Hapus cookie session dari browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Hancurkan sesi di server
    session_destroy();
}

// 3. SECURITY: ANTI-CACHE HEADERS
// Mencegah tombol 'Back' browser menampilkan data form sebelumnya
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// 4. Include Config
require_once '../config/database.php';
require_once '../includes/encryption.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi Input (Mencegah XSS)
    $nama_siswa  = trim(htmlspecialchars($_POST['nama_siswa']));
    $kelas       = htmlspecialchars($_POST['kelas']);
    $tgl_lahir   = htmlspecialchars($_POST['tgl_lahir']); 
    $alamat_ortu = htmlspecialchars($_POST['alamat_ortu']);
    $no_wa       = htmlspecialchars($_POST['no_wa']);
    $isi_masalah = htmlspecialchars($_POST['isi']);

    // Validasi Input Kosong
    if(empty($nama_siswa) || empty($kelas) || empty($no_wa) || empty($isi_masalah)) {
        $error = "Mohon lengkapi semua data wajib.";
    } else {
        try {
            // 5. CARI DATA SISWA (JOIN TABLE USERS)
            // Menggunakan Parameter Binding untuk mencegah SQL Injection
            $stmt = $conn->prepare("SELECT siswa.id_siswa 
                                    FROM siswa 
                                    JOIN users ON siswa.user_id = users.id_user 
                                    WHERE users.nama_lengkap = ? AND siswa.kelas = ?");
            $stmt->execute([$nama_siswa, $kelas]);
            $siswa = $stmt->fetch();

            if ($siswa) {
                // Susun Isi Laporan
                $isi_lengkap = "--- DATA PELAPOR (ORANG TUA) ---\n";
                $isi_lengkap .= "Nama Siswa: $nama_siswa ($kelas)\n";
                $isi_lengkap .= "Tgl Lahir: $tgl_lahir\n";
                $isi_lengkap .= "Alamat Ortu: $alamat_ortu\n";
                $isi_lengkap .= "No WhatsApp: $no_wa\n";
                $isi_lengkap .= "-----------------------------------\n\n";
                $isi_lengkap .= "ISI LAPORAN:\n" . $isi_masalah;

                // Enkripsi
                $isiTerenkripsi = encryptData($isi_lengkap);

                // Simpan ke Database
                $sql = "INSERT INTO laporan_bk (id_siswa, judul_laporan, isi_laporan, kategori, status, tgl_laporan) 
                        VALUES (?, ?, ?, 'Pribadi', 'Pending', NOW())";
                
                $judul = "Laporan Orang Tua: " . substr($isi_masalah, 0, 30) . "...";
                $stmtInsert = $conn->prepare($sql);

                if ($stmtInsert->execute([$siswa['id_siswa'], $judul, $isiTerenkripsi])) {
                    $success = "Laporan berhasil dikirim! Guru BK akan menghubungi Anda via WhatsApp jika diperlukan.";
                } else {
                    $error = "Terjadi kesalahan sistem saat menyimpan laporan.";
                }
            } else {
                $error = "Data Siswa tidak ditemukan! Pastikan Nama Lengkap (sesuai Absen) dan Kelas sudah benar.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: Hubungi Admin."; // Jangan tampilkan error asli ke user publik
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Orang Tua | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-8">

    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800">Layanan Orang Tua</h1>
            <p class="text-slate-500">Sampaikan masalah anak atau konsultasikan dengan Guru BK.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
            
            <?php if ($success): ?>
                <div class="bg-green-100 p-8 text-center">
                    <div class="w-16 h-16 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">✓</div>
                    <h3 class="text-xl font-bold text-green-800 mb-2">Terima Kasih</h3>
                    <p class="text-green-700 mb-6"><?= $success ?></p>
                    <a href="../views/auth/login.php" class="inline-block px-6 py-2 bg-white text-green-700 font-bold rounded-lg hover:bg-green-50 shadow-sm">Kembali ke Beranda</a>
                </div>
            <?php else: ?>

            <div class="p-8 md:p-10">
                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-bold text-sm text-center border border-red-100">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    
                    <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                        <h3 class="font-bold text-blue-800 mb-4 text-sm uppercase tracking-wider">Data Anak (Verifikasi)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Nama Lengkap Siswa</label>
                                <input type="text" name="nama_siswa" required placeholder="Sesuai Absen Sekolah" class="w-full border border-blue-200 rounded-lg p-3">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Kelas Saat Ini</label>
                                <select name="kelas" required class="w-full border border-blue-200 rounded-lg p-3 bg-white">
                                    <option value="">Pilih Kelas...</option>
                                    <option>VII-A</option><option>VII-B</option>
                                    <option>VIII-A</option><option>VIII-B</option>
                                    <option>IX-A</option><option>IX-B</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Tanggal Lahir Siswa</label>
                                <input type="date" name="tgl_lahir" required class="w-full border border-blue-200 rounded-lg p-3">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wider">Isi Laporan / Konsultasi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">No. WhatsApp Aktif (Ortu)</label>
                                <input type="text" name="no_wa" required placeholder="08..." class="w-full border border-slate-300 rounded-lg p-3">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Alamat Domisili Ortu</label>
                                <input type="text" name="alamat_ortu" required class="w-full border border-slate-300 rounded-lg p-3">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 mb-1">Jelaskan Masalah</label>
                            <textarea name="isi" rows="5" required placeholder="Ceritakan masalahnya secara detail..." class="w-full border border-slate-300 rounded-lg p-3"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row gap-4 pt-4 border-t border-slate-100">
                        <a href="../views/auth/login.php" class="w-full md:w-1/3 py-4 bg-slate-100 text-slate-600 font-bold rounded-xl text-center hover:bg-slate-200">Batal</a>
                        <button type="submit" class="w-full md:w-2/3 py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition">Kirim Laporan</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>