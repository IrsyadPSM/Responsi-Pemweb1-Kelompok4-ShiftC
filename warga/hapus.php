<?php
// Modul 2 (DELETE)
require_once "../includes/auth.php";
wajib_login(['admin']);
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "DELETE FROM warga WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
try {
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
} catch (mysqli_sql_exception $e) {
    echo "Data tidak bisa dihapus karena masih dipakai data lain. <a href='index.php'>Kembali</a>";
}
exit;
