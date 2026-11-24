<?php
// Modul 2 (READ): daftar warga. JOIN dengan tabel users dan wilayah.
require_once "../includes/auth.php";
wajib_login(['admin']);
$data = mysqli_query($koneksi, "SELECT w.id, u.nama, u.username, wl.nama_wilayah, w.alamat, w.no_hp
    FROM warga w JOIN users u ON u.id = w.user_id JOIN wilayah wl ON wl.id = w.wilayah_id ORDER BY w.id DESC");
$judul = "Data Warga";
include "../includes/header.php";
?>
<h2>Data Warga</h2>
<a class="btn" href="form.php">+ Tambah Data Warga</a>
<table>
  <tr><th>ID</th><th>Nama</th><th>Username</th><th>Wilayah</th><th>Alamat</th><th>No HP</th><th>Aksi</th></tr>
  <?php while ($w = mysqli_fetch_assoc($data)): ?>
  <tr>
    <td><?= $w['id'] ?></td><td><?= e($w['nama']) ?></td><td><?= e($w['username']) ?></td>
    <td><?= e($w['nama_wilayah']) ?></td><td><?= e($w['alamat']) ?></td><td><?= e($w['no_hp']) ?></td>
    <td>
      <a class="btn" href="form.php?id=<?= $w['id'] ?>">Edit</a>
      <a class="btn btn-hapus" href="hapus.php?id=<?= $w['id'] ?>" onclick="return confirm('Hapus data warga ini?')">Hapus</a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
