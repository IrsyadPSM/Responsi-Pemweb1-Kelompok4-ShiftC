<?php
// Modul 1 (kelola akun): admin melihat semua akun dan dapat mengubah role (misalnya jadi ketua_ronda).
require_once "../includes/auth.php";
wajib_login(['admin']);
$data = mysqli_query($koneksi, "SELECT id, nama, username, role, created_at FROM users ORDER BY id");
$judul = "Kelola Akun";
include "../includes/header.php";
?>
<h2>Kelola Akun</h2>
<a class="btn" href="form.php">+ Tambah Akun</a>
<table>
  <tr><th>ID</th><th>Nama</th><th>Username</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr>
  <?php while ($u = mysqli_fetch_assoc($data)): ?>
  <tr>
    <td><?= $u['id'] ?></td><td><?= e($u['nama']) ?></td><td><?= e($u['username']) ?></td>
    <td><?= e($u['role']) ?></td><td><?= e(substr($u['created_at'], 0, 10)) ?></td>
    <td>
      <a class="btn" href="form.php?id=<?= $u['id'] ?>">Edit</a>
      <?php if ($u['id'] != $_SESSION['user']['id']): ?>
        <a class="btn btn-hapus" href="hapus.php?id=<?= $u['id'] ?>" onclick="return confirm('Hapus akun ini?')">Hapus</a>
      <?php endif; ?>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
