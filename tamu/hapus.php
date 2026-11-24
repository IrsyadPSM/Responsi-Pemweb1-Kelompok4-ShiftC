<?php
// Modul 7 (DELETE): warga hanya boleh menghapus tamunya sendiri; admin boleh semua.
require_once "../includes/auth.php";
wajib_login(['warga', 'admin']);
$id = (int)($_GET['id'] ?? 0);
if ($_SESSION['user']['role'] === 'warga') {
    $w = warga_saat_ini();
    if (!$w) die("Data diri belum lengkap.");
    $stmt = mysqli_prepare($koneksi, "DELETE FROM tamu WHERE id = ? AND warga_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $w['id']);
} else {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM tamu WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
}
mysqli_stmt_execute($stmt);
header("Location: index.php");
exit;
