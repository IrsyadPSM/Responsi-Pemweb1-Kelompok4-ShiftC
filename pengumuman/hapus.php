<?php
// Modul 8 (DELETE)
require_once "../includes/auth.php";
wajib_login(['admin', 'ketua_ronda']);
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "DELETE FROM pengumuman WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
header("Location: index.php");
exit;
