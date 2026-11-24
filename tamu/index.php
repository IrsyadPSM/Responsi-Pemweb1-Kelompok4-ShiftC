<?php
// Modul 7 (READ): data tamu/pendatang. Warga melihat tamunya sendiri; admin dan ketua melihat semua.
require_once "../includes/auth.php";
wajib_login();
$role = $_SESSION['user']['role'];
$sql = "SELECT t.*, u.nama AS tuan_rumah FROM tamu t JOIN warga w ON w.id = t.warga_id JOIN users u ON u.id = w.user_id";
$tipe = ""; $nilai = [];
if ($role === 'warga') {
    $w = warga_saat_ini();
    if (!$w) { header("Location: ../warga/profil.php"); exit; }
    $sql .= " WHERE t.warga_id = ?"; $tipe = "i"; $nilai = [$w['id']];
}
$sql .= " ORDER BY (t.tanggal_pulang IS NULL) DESC, t.tanggal_datang DESC";   // tamu aktif tampil paling atas
$stmt = mysqli_prepare($koneksi, $sql);
if ($nilai) mysqli_stmt_bind_param($stmt, $tipe, ...$nilai);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$bisaUbah = in_array($role, ['warga', 'admin']);
$judul = "Lapor Tamu";
include "../includes/header.php";
?>
<h2>Lapor Tamu / Pendatang</h2>
<?php if ($role === 'warga'): ?><a class="btn" href="form.php">+ Lapor Tamu</a><?php endif; ?>
<table>
  <tr><th>Nama Tamu</th><th>Asal</th><th>Keperluan</th><th>Tuan Rumah</th><th>Datang</th><th>Status</th><?php if ($bisaUbah): ?><th>Aksi</th><?php endif; ?></tr>
  <?php while ($t = mysqli_fetch_assoc($data)): ?>
  <tr>
    <td><?= e($t['nama_tamu']) ?></td><td><?= e($t['asal']) ?></td><td><?= e($t['keperluan']) ?></td>
    <td><?= e($t['tuan_rumah']) ?></td><td><?= e($t['tanggal_datang']) ?></td>
    <td><?php if ($t['tanggal_pulang'] === null): ?><span class="badge st-aktif">aktif</span>
        <?php else: ?><span class="badge st-pulang">pulang <?= e($t['tanggal_pulang']) ?></span><?php endif; ?></td>
    <?php if ($bisaUbah): ?>
    <td>
      <?php if ($t['tanggal_pulang'] === null): ?>
        <form method="POST" action="pulang.php" style="display:inline">
          <input type="hidden" name="id" value="<?= $t['id'] ?>">
          <button class="btn" type="submit">Tandai Pulang</button>
        </form>
      <?php endif; ?>
      <a class="btn btn-hapus" href="hapus.php?id=<?= $t['id'] ?>" onclick="return confirm('Hapus data tamu ini?')">Hapus</a>
    </td>
    <?php endif; ?>
  </tr>
  <?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
