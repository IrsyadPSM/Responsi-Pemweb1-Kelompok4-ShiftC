<?php
// Hapus akun. Admin tidak boleh menghapus dirinya sendiri.
require_once "../includes/auth.php";
wajib_login(['admin']);
$id = (int)($_GET['id'] ?? 0);
if ($id === (int)$_SESSION['user']['id']) die("Anda tidak bisa menghapus akun sendiri. <a href='index.php'>Kembali</a>");
$stmt = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
try {
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
} catch (mysqli_sql_exception $e) {
    echo "Akun tidak bisa dihapus karena masih dipakai data lain (misalnya tindak lanjut atau pengumuman). <a href='index.php'>Kembali</a>";
}
exit;
