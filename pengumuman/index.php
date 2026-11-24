<?php
// Modul 8 (READ): semua role membaca; admin dan ketua_ronda menulis dan mengelola.
require_once "../includes/auth.php";
wajib_login();
$kelola = in_array($_SESSION['user']['role'], ['admin', 'ketua_ronda']);
$data = mysqli_query($koneksi, "SELECT p.*, u.nama FROM pengumuman p JOIN users u ON u.id = p.user_id
    ORDER BY (p.tingkat = 'darurat') DESC, p.tanggal DESC");   // darurat selalu di atas
$judul = "Pengumuman";
include "../includes/header.php";
?>
<h2>Pengumuman dan Info Darurat</h2>
<?php if ($kelola): ?><a class="btn" href="form.php">+ Buat Pengumuman</a><?php endif; ?>
<?php while ($p = mysqli_fetch_assoc($data)): ?>
  <div class="info">
    <p><span class="badge tg-<?= e($p['tingkat']) ?>"><?= e($p['tingkat']) ?></span> <b><?= e($p['judul']) ?></b></p>
    <p><?= nl2br(e($p['isi'])) ?></p>
    <p><small>Oleh <?= e($p['nama']) ?> | <?= e($p['tanggal']) ?></small></p>
    <?php if ($kelola): ?>
      <a class="btn" href="form.php?id=<?= $p['id'] ?>">Edit</a>
      <a class="btn btn-hapus" href="hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus pengumuman ini?')">Hapus</a>
    <?php endif; ?>
  </div>
<?php endwhile; ?>
<?php include "../includes/footer.php"; ?>
