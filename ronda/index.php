<?php
// Modul 4 (READ): jadwal ronda. Semua role boleh melihat; admin dan ketua_ronda boleh mengelola.
require_once "../includes/auth.php";
wajib_login();
$kelola = in_array($_SESSION['user']['role'], ['admin', 'ketua_ronda']);
$data = mysqli_query($koneksi, "SELECT j.*, u.nama, wl.nama_wilayah FROM jadwal_ronda j
    JOIN warga w ON w.id = j.warga_id JOIN users u ON u.id = w.user_id
    JOIN wilayah wl ON wl.id = j.wilayah_id ORDER BY j.tanggal DESC, j.jam_mulai");
$judul = "Jadwal Ronda";
include "../includes/header.php";
?>
<h2>Jadwal Ronda</h2>
<?php if ($kelola): ?><a class="btn" href="form.php">+ Tambah Jadwal</a><?php endif; ?>
<table>
  <tr><th>Tanggal</th><th>Jam</th><th>Petugas Ronda</th><th>Wilayah</th><th>Status</th><?php if ($kelola): ?><th>Aksi</th><?php endif; ?></tr>
  <?php while ($j = mysqli_fetch_assoc($data)): ?>
  <tr>
    <td><?= e($j['tanggal']) ?></td>
    <td><?= substr($j['jam_mulai'], 0, 5) ?> - <?= substr($j['jam_selesai'], 0, 5) ?></td>
    <td><?= e($j['nama']) ?></td>
    <td><?= e($j['nama_wilayah']) ?></td>
    <td><span class="badge st-<?= e($j['status']) ?>"><?= e($j['status']) ?></span></td>
    <?php if ($kelola): ?>
    <td>
      <a class="btn" href="form.php?id=<?= $j['id'] ?>">Edit</a>
      <a class="btn btn-hapus" href="hapus.php?id=<?= $j['id'] ?>" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
    </td>
    <?php endif; ?>
  </tr>
  <?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
