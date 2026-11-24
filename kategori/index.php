<?php
// READ: tampilkan semua kategori. Pola ini dipakai ulang di semua modul CRUD.
require_once "../includes/auth.php";
wajib_login(['admin']);
$data = mysqli_query($koneksi, "SELECT * FROM kategori_kejadian ORDER BY id DESC");
$judul = "Data Kategori Kejadian";
include "../includes/header.php";
?>
<h2>Data Kategori Kejadian</h2>
<a class="btn" href="form.php">+ Tambah Kategori Kejadian</a>
<table>
  <tr><th>ID</th><th>Nama Kategori</th><th>Aksi</th></tr>
  <?php while ($w = mysqli_fetch_assoc($data)): ?>
  <tr>
    <td><?= $w['id'] ?></td>
    <td><?= e($w['nama_kategori']) ?></td>
    <td>
      <a class="btn" href="form.php?id=<?= $w['id'] ?>">Edit</a>
      <a class="btn btn-hapus" href="hapus.php?id=<?= $w['id'] ?>" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
