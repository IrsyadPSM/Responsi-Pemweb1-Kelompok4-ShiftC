<?php
// DELETE. Jika data masih dipakai tabel lain (foreign key), tampilkan pesan, bukan error.
require_once "../includes/auth.php";
wajib_login(['admin']);
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "DELETE FROM kategori_kejadian WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
try {
    mysqli_stmt_execute($stmt);
    header("Location: index.php");
} catch (mysqli_sql_exception $e) {
    echo "Data tidak bisa dihapus karena masih dipakai data lain. <a href='index.php'>Kembali</a>";
}
exit;
