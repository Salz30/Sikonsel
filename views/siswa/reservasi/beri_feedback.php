<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/feedback_controller.php';

$user = checkLogin('../../../views/auth/login.php');
$id_reservasi = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $komentar = $_POST['komentar'];
    if (insertFeedback($conn, $id_reservasi, $rating, $komentar)) {
        header("Location: jadwal_saya.php?msg=feedback_success");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beri Feedback | Sikonsel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Bagaimana layanannya?</h2>
        <p class="text-sm text-slate-500 mb-6">Masukan Anda membantu kami meningkatkan layanan BK.</p>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Rating (1-5 Bintang)</label>
                <select name="rating" required class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                    <option value="4">⭐⭐⭐⭐ (Puas)</option>
                    <option value="3">⭐⭐⭐ (Cukup)</option>
                    <option value="2">⭐⭐ (Kurang)</option>
                    <option value="1">⭐ (Sangat Kurang)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Komentar / Saran</label>
                <textarea name="komentar" rows="4" class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Tuliskan pengalaman Anda..."></textarea>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition">
                Kirim Masukan
            </button>
        </form>
    </div>
</body>
</html>