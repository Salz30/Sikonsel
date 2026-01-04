<?php
// File: views/admin/siswa/edit.php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';
checkLogin();

$id = $_GET['id'] ?? null;
// PERBAIKAN 1: Jika tidak ada ID, kembali ke list_siswa.php (bukan index.php)
if (!$id) { header("Location: list_siswa.php"); exit; }

$siswa = getSiswaById($conn, $id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'nisn' => $_POST['nisn'],
        'nama' => $_POST['nama'],
        'kelas' => $_POST['kelas'],
        'alamat' => $_POST['alamat']
    ];

    if (updateSiswa($conn, $id, $data)) {
        // PERBAIKAN 2: Redirect sukses ke list_siswa.php (bukan index.php)
        header("Location: list_siswa.php?msg=updated");
        exit;
    } else {
        $error = "Gagal mengupdate data.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Siswa | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: sans-serif; }</style>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-lg w-full bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-slate-800 mb-6">Edit Data Siswa</h2>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">NISN</label>
                <input type="text" name="nisn" value="<?php echo htmlspecialchars($siswa['nisn']); ?>" required class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($siswa['nama_lengkap']); ?>" required class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Kelas</label>
                <input type="text" name="kelas" value="<?php echo htmlspecialchars($siswa['kelas']); ?>" required class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="3" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none"><?php echo htmlspecialchars($siswa['alamat']); ?></textarea>
            </div>
            
            <div class="flex gap-4 pt-4">
                <a href="list_siswa.php" class="w-1/2 text-center py-3 border rounded-xl text-slate-600 font-bold hover:bg-slate-50">Batal</a>
                <button type="submit" class="w-1/2 bg-yellow-600 text-white py-3 rounded-xl font-bold hover:bg-yellow-700">Update Data</button>
            </div>
        </form>
    </div>

</body>
</html>