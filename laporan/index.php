<?php
// Modul 5 (READ): daftar laporan. Warga hanya melihat laporannya sendiri; admin dan ketua melihat semua.
require_once "../includes/auth.php";
wajib_login();
$role = $_SESSION['user']['role'];
$statusList = ['menunggu', 'diverifikasi', 'ditolak', 'ditangani', 'selesai'];
$sql = "SELECT l.id, l.judul, l.status, l.tanggal_lapor, k.nama_kategori, u.nama, wl.nama_wilayah
    FROM laporan_kejadian l
    JOIN kategori_kejadian k ON k.id = l.kategori_id
    JOIN warga w ON w.id = l.warga_id JOIN users u ON u.id = w.user_id
    JOIN wilayah wl ON wl.id = l.wilayah_id";
$tipe = ""; $nilai = []; $filter = $_GET['status'] ?? '';

if ($role === 'warga') {
    $w = warga_saat_ini();
    if (!$w) { header("Location: ../warga/profil.php"); exit; }   // wajib isi data diri dulu
    $sql .= " WHERE l.warga_id = ?"; $tipe = "i"; $nilai = [$w['id']];
} elseif (in_array($filter, $statusList)) {
    $sql .= " WHERE l.status = ?"; $tipe = "s"; $nilai = [$filter];
}
$sql .= " ORDER BY l.id DESC";
$stmt = mysqli_prepare($koneksi, $sql);
if ($nilai) mysqli_stmt_bind_param($stmt, $tipe, ...$nilai);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$judul = "Laporan Kejadian";
include "../includes/header.php";
?>
<h2>Laporan Kejadian</h2>
<?php if ($role === 'warga'): ?><a class="btn" href="form.php">+ Buat Laporan</a><?php endif; ?>
<?php if ($role !== 'warga'): ?>
<form method="GET">
  Filter status
  <select name="status" onchange="this.form.submit()">
    <option value="">Semua</option>
    <?php foreach ($statusList as $s): ?>
      <option value="<?= $s ?>" <?= $s === $filter ? 'selected' : '' ?>><?= $s ?></option>
    <?php endforeach; ?>
  </select>
</form>
<?php endif; ?>
<table>
  <tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Wilayah</th><th>Pelapor</th><th>Status</th><th>Aksi</th></tr>
  <?php while ($l = mysqli_fetch_assoc($data)): ?>
  <tr>
    <td><?= e(substr($l['tanggal_lapor'], 0, 10)) ?></td>
    <td><?= e($l['judul']) ?></td>
    <td><?= e($l['nama_kategori']) ?></td>
    <td><?= e($l['nama_wilayah']) ?></td>
    <td><?= e($l['nama']) ?></td>
    <td><span class="badge st-<?= e($l['status']) ?>"><?= e($l['status']) ?></span></td>
    <td><a class="btn" href="detail.php?id=<?= $l['id'] ?>">Detail</a></td>
  </tr>
  <?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
