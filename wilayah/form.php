<?php
// CREATE + UPDATE dalam satu file. Ada ?id= berarti edit, tidak ada berarti tambah.
require_once "../includes/auth.php";
wajib_login(['admin']);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nama = ""; $error = "";

if ($id) { // mode edit: ambil data lama
    $stmt = mysqli_prepare($koneksi, "SELECT nama_wilayah FROM wilayah WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) die("Data tidak ditemukan.");
    $nama = $row['nama_wilayah'];
}

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_wilayah']);
    if ($nama === "") {
        $error = "Nama wilayah wajib diisi.";
    } else {
        if ($id) {
            $stmt = mysqli_prepare($koneksi, "UPDATE wilayah SET nama_wilayah = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $nama, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO wilayah (nama_wilayah) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $nama);
        }
        mysqli_stmt_execute($stmt);
        header("Location: index.php");
        exit;
    }
}
$judul = $id ? "Edit Wilayah" : "Tambah Wilayah";
include "../includes/header.php";
?>
<h2><?= $judul ?></h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST">
  Nama Wilayah <input type="text" name="nama_wilayah" value="<?= e($nama) ?>" required>
  <button class="btn" type="submit" name="simpan">Simpan</button>
  <a href="index.php">Batal</a>
</form>
<?php include "../includes/footer.php"; ?>
