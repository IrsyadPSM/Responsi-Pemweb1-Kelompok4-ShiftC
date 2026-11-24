<?php
// Modul 6: verifikasi. Hanya boleh mengubah status dari 'menunggu' menjadi 'diverifikasi' atau 'ditolak'.
require_once "../includes/auth.php";
wajib_login(['admin', 'ketua_ronda']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: index.php"); exit; }
$id = (int)$_POST['id'];
$aksi = $_POST['aksi'] ?? '';
if ($aksi === 'verifikasi') { $baru = 'diverifikasi'; }
elseif ($aksi === 'tolak') { $baru = 'ditolak'; }
else { die("Aksi tidak valid."); }
// Syarat AND status='menunggu' mencegah status loncat atau diubah dua kali
$stmt = mysqli_prepare($koneksi, "UPDATE laporan_kejadian SET status = ? WHERE id = ? AND status = 'menunggu'");
mysqli_stmt_bind_param($stmt, "si", $baru, $id);
mysqli_stmt_execute($stmt);
header("Location: detail.php?id=" . $id);
exit;
