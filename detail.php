<?php
// Detail laporan + riwayat tindak lanjut. Tombol aksi muncul sesuai role dan status (Modul 6).
require_once "../includes/auth.php";
wajib_login();
$role = $_SESSION['user']['role'];
$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($koneksi, "SELECT l.*, k.nama_kategori, u.nama AS pelapor, wl.nama_wilayah
    FROM laporan_kejadian l JOIN kategori_kejadian k ON k.id = l.kategori_id
    JOIN warga w ON w.id = l.warga_id JOIN users u ON u.id = w.user_id
    JOIN wilayah wl ON wl.id = l.wilayah_id WHERE l.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$l = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$l) die("Laporan tidak ditemukan.");

// Warga hanya boleh membuka laporannya sendiri
if ($role === 'warga') {
    $w = warga_saat_ini();
    if (!$w || $w['id'] != $l['warga_id']) { http_response_code(403); die("Akses ditolak."); }
}
$kelola = in_array($role, ['admin', 'ketua_ronda']);

$stmt = mysqli_prepare($koneksi, "SELECT t.*, u.nama FROM tindak_lanjut t JOIN users u ON u.id = t.petugas_id WHERE t.laporan_id = ? ORDER BY t.id");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$riwayat = mysqli_stmt_get_result($stmt);
$judul = "Detail Laporan";
include "../includes/header.php";
?>
<h2><?= e($l['judul']) ?> <span class="badge st-<?= e($l['status']) ?>"><?= e($l['status']) ?></span></h2>
<div class="info">
  <p><b>Pelapor:</b> <?= e($l['pelapor']) ?> | <b>Kategori:</b> <?= e($l['nama_kategori']) ?> | <b>Wilayah:</b> <?= e($l['nama_wilayah']) ?></p>
  <p><b>Lokasi:</b> <?= e($l['lokasi']) ?> | <b>Tanggal:</b> <?= e($l['tanggal_lapor']) ?></p>
  <p><?= nl2br(e($l['deskripsi'])) ?></p>
</div>

<h3>Riwayat Penanganan</h3>
<?php if (mysqli_num_rows($riwayat) === 0): ?><p>Belum ada tindak lanjut.</p><?php endif; ?>
<?php while ($t = mysqli_fetch_assoc($riwayat)): ?>
  <div class="info"><p><b><?= e($t['nama']) ?></b> (<?= e($t['tanggal']) ?>) <span class="badge st-<?= e($t['status']) ?>"><?= e($t['status']) ?></span></p>
  <p><?= e($t['keterangan']) ?></p></div>
<?php endwhile; ?>

<?php if ($kelola && $l['status'] === 'menunggu'): ?>
  <h3>Verifikasi Laporan</h3>
  <form method="POST" action="verifikasi.php">
    <input type="hidden" name="id" value="<?= $l['id'] ?>">
    <button class="btn" type="submit" name="aksi" value="verifikasi">Verifikasi</button>
    <button class="btn btn-hapus" type="submit" name="aksi" value="tolak" onclick="return confirm('Tolak laporan ini?')">Tolak</button>
  </form>
<?php elseif ($kelola && in_array($l['status'], ['diverifikasi', 'ditangani'])): ?>
  <h3>Tambah Tindak Lanjut</h3>
  <form method="POST" action="tindak.php">
    <input type="hidden" name="id" value="<?= $l['id'] ?>">
    Keterangan <input type="text" name="keterangan" maxlength="500" required>
    Status
    <select name="status">
      <option value="ditangani">ditangani</option>
      <?php if ($l['status'] === 'ditangani'): ?><option value="selesai">selesai</option><?php endif; ?>
    </select>
    <button class="btn" type="submit">Simpan</button>
  </form>
<?php endif; ?>
<p><a href="index.php">&laquo; Kembali ke daftar</a></p>
<?php include "../includes/footer.php"; ?>
