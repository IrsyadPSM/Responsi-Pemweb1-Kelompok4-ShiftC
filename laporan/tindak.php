<?php
// Modul 6: tindak lanjut ketua ronda/admin.
// Aturan: diverifikasi -> ditangani; ditangani -> ditangani atau selesai.
require_once "../includes/auth.php";
wajib_login(['admin', 'ketua_ronda']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: index.php"); exit; }
$id = (int)$_POST['id'];
$keterangan = trim($_POST['keterangan']);
$baru = $_POST['status'] ?? '';

$stmt = mysqli_prepare($koneksi, "SELECT status FROM laporan_kejadian WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$l = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$l) die("Laporan tidak ditemukan.");

$sekarang = $l['status'];
if ($sekarang === 'diverifikasi' && $baru === 'ditangani') { $boleh = true; }
elseif ($sekarang === 'ditangani' && in_array($baru, ['ditangani', 'selesai'])) { $boleh = true; }
else { $boleh = false; }
if (!$boleh || $keterangan === "") die("Perubahan status tidak valid atau keterangan kosong. <a href='detail.php?id=$id'>Kembali</a>");

$petugas = $_SESSION['user']['id'];
$stmt = mysqli_prepare($koneksi, "INSERT INTO tindak_lanjut (laporan_id, petugas_id, keterangan, status) VALUES (?,?,?,?)");
mysqli_stmt_bind_param($stmt, "iiss", $id, $petugas, $keterangan, $baru);
mysqli_stmt_execute($stmt);
$stmt = mysqli_prepare($koneksi, "UPDATE laporan_kejadian SET status = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $baru, $id);
mysqli_stmt_execute($stmt);
header("Location: detail.php?id=" . $id);
exit;
