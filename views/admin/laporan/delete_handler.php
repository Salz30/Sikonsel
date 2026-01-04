<?php
require_once '../../../includes/auth.php';
require_once '../../../includes/laporan_controller.php';

checkLogin();

// Hapus Single (GET)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (deleteLaporan($conn, $id)) {
        header("Location: masuk_laporan.php?msg=deleted");
    } else {
        header("Location: masuk_laporan.php?msg=error");
    }
    exit;
}

// Hapus Massal (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids'];
    if (!empty($ids)) {
        if (deleteBulkLaporan($conn, $ids)) {
            header("Location: masuk_laporan.php?msg=bulk_deleted");
        } else {
            header("Location: masuk_laporan.php?msg=error");
        }
    } else {
        header("Location: masuk_laporan.php");
    }
    exit;
}
?>