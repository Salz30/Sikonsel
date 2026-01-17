<?php
// views/admin/siswa/tambah.php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';
checkLogin();

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'nisn' => $_POST['nisn'],
        'nama' => $_POST['nama'],
        'kelas' => $_POST['kelas'],
        'alamat' => $_POST['alamat']
    ];

    // Panggil fungsi tambahSiswa
    $result = tambahSiswa($conn, $data);

    if ($result['success']) {
        header("Location: list_siswa.php?msg=added");
        exit;
    } else {
        // Tampilkan pesan error
        $error = "Gagal: " . $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Siswa | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-6">Tambah Siswa Baru</h2>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg mb-4 text-sm font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">NISN (Hanya Angka)</label>
                <input type="text" 
                       name="nisn" 
                       required 
                       inputmode="numeric" 
                       pattern="[0-9]*"
                       maxlength="20"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       placeholder="Contoh: 0056789012"
                       class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none">
                <p class="text-[10px] text-slate-400 mt-1">*Digunakan sebagai Username Login. Password default: <b>123456</b></p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Kelas</label>
                <select name="kelas" class="w-full border rounded-lg p-3 bg-white">
                    <option>VII-A</option><option>VII-B</option>
                    <option>VIII-A</option><option>VIII-B</option>
                    <option>IX-A</option><option>IX-B</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>
            
            <div class="flex gap-4 pt-4">
                <a href="list_siswa.php" class="w-1/2 text-center py-3 border rounded-xl text-slate-600 font-bold hover:bg-slate-50">Batal</a>
                <button type="submit" class="w-1/2 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700">Simpan Data</button>
            </div>
        </form>
    </div>

</body>
</html>