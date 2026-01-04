<?php
require_once '../../../includes/auth.php';
require_once '../../../includes/siswa_controller.php';

checkLogin();

// 1. Cek Single Delete (Method GET)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (deleteSiswa($conn, $id)) {
        header("Location: list_siswa.php?msg=deleted");
    } else {
        header("Location: list_siswa.php?msg=error");
    }
    exit;
}

// 2. Cek Bulk Delete (Method POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids']; // Ini berbentuk Array
    if (!empty($ids)) {
        if (deleteBulkSiswa($conn, $ids)) {
            header("Location: list_siswa.php?msg=bulk_deleted");
        } else {
            header("Location: list_siswa.php?msg=error");
        }
    } else {
        header("Location: list_siswa.php");
    }
    exit;
}
?>