<?php
// File: Sikonsel/views/admin/siswa/edit.php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';
$user = checkLogin('../../auth/login.php');

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: list_siswa.php"); exit; }

$siswa = getSiswaById($conn, $id);
$error = ""; // Variabel untuk menampung pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'nisn'   => $_POST['nisn'],
        'nama'   => $_POST['nama'],
        'kelas'  => $_POST['kelas'],
        'alamat' => $_POST['alamat'],
        'nama_ortu'  => $_POST['nama_ortu'],
        'no_hp_ortu' => $_POST['no_hp_ortu']
    ];

    // PERBAIKAN LOGIC: updateSiswa mengembalikan array, bukan boolean langsung
    $result = updateSiswa($conn, $id, $data);

    if ($result['success']) {
        header("Location: list_siswa.php?msg=updated");
        exit;
    } else {
        // Tampilkan pesan error jika validasi backend gagal
        $error = "Gagal: " . $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Siswa | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-2">Edit Data Siswa</h2>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg mb-4 text-sm font-medium">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">NISN (Angka)</label>
                    <input type="text" 
                           name="nisn" 
                           value="<?= $siswa['nisn'] ?>" 
                           required 
                           inputmode="numeric" 
                           pattern="[0-9]*"
                           maxlength="20"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase">Kelas</label>
                    <input type="text" name="kelas" value="<?= $siswa['kelas'] ?>" required class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $siswa['nama_lengkap'] ?>" required class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 space-y-4">
                <p class="text-xs font-bold text-blue-600 uppercase">Data Orang Tua / Wali</p>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500">Nama Orang Tua</label>
                    <input type="text" name="nama_ortu" 
                           value="<?= $siswa['nama_ortu'] ?? '' ?>" 
                           placeholder="Masukkan nama ayah/ibu" 
                           class="w-full border rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-500">No. WhatsApp (Gunakan 08...)</label>
                    <input type="text" name="no_hp_ortu" 
                           value="<?= $siswa['no_hp_ortu'] ?? '' ?>" 
                           inputmode="numeric"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           placeholder="Contoh: 0812345678" 
                           class="w-full border rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase">Alamat Rumah</label>
                <textarea name="alamat" rows="2" class="w-full border rounded-lg p-3 outline-none focus:ring-2 focus:ring-blue-500"><?= $siswa['alamat'] ?></textarea>
            </div>
            
            <div class="flex gap-4 pt-4">
                <a href="list_siswa.php" class="w-1/2 text-center py-3 border rounded-xl text-slate-600 font-bold hover:bg-slate-50">Batal</a>
                <button type="submit" class="w-1/2 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>