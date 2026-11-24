<?php
// Modul 7 (UPDATE): tandai tamu sudah pulang. Warga hanya boleh untuk tamunya sendiri.
require_once "../includes/auth.php";
wajib_login(['warga', 'admin']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: index.php"); exit; }
$id = (int)$_POST['id'];
if ($_SESSION['user']['role'] === 'warga') {
    $w = warga_saat_ini();
    if (!$w) die("Data diri belum lengkap.");
    $stmt = mysqli_prepare($koneksi, "UPDATE tamu SET tanggal_pulang = CURDATE() WHERE id = ? AND warga_id = ? AND tanggal_pulang IS NULL");
    mysqli_stmt_bind_param($stmt, "ii", $id, $w['id']);
} else {
    $stmt = mysqli_prepare($koneksi, "UPDATE tamu SET tanggal_pulang = CURDATE() WHERE id = ? AND tanggal_pulang IS NULL");
    mysqli_stmt_bind_param($stmt, "i", $id);
}
mysqli_stmt_execute($stmt);
header("Location: index.php");
exit;
