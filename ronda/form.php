<?php
// Modul 4 (CREATE + UPDATE) jadwal ronda. Hanya admin dan ketua_ronda.
require_once "../includes/auth.php";
wajib_login(['admin', 'ketua_ronda']);
$id = (int)($_GET['id'] ?? 0);
$warga_id = 0; $wilayah_id = 0; $tanggal = ""; $mulai = "22:00"; $selesai = "04:00"; $status = "terjadwal"; $error = "";
$daftarStatus = ['terjadwal', 'hadir', 'tidak_hadir'];

if ($id) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM jadwal_ronda WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$r) die("Data tidak ditemukan.");
    $warga_id = $r['warga_id']; $wilayah_id = $r['wilayah_id']; $tanggal = $r['tanggal'];
    $mulai = substr($r['jam_mulai'], 0, 5); $selesai = substr($r['jam_selesai'], 0, 5); $status = $r['status'];
}

if (isset($_POST['simpan'])) {
    $warga_id = (int)$_POST['warga_id']; $wilayah_id = (int)$_POST['wilayah_id'];
    $tanggal = $_POST['tanggal']; $mulai = $_POST['jam_mulai']; $selesai = $_POST['jam_selesai']; $status = $_POST['status'];
    if (!$warga_id || !$wilayah_id || $tanggal === "" || $mulai === "" || $selesai === "") {
        $error = "Semua field wajib diisi.";
    } elseif ($mulai === $selesai) {
        $error = "Jam mulai dan jam selesai tidak boleh sama.";
    } elseif (!in_array($status, $daftarStatus)) {
        $error = "Status tidak valid.";
    } else {
        if ($id) {
            $stmt = mysqli_prepare($koneksi, "UPDATE jadwal_ronda SET wilayah_id=?, warga_id=?, tanggal=?, jam_mulai=?, jam_selesai=?, status=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "iissssi", $wilayah_id, $warga_id, $tanggal, $mulai, $selesai, $status, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO jadwal_ronda (wilayah_id, warga_id, tanggal, jam_mulai, jam_selesai, status) VALUES (?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "iissss", $wilayah_id, $warga_id, $tanggal, $mulai, $selesai, $status);
        }
        mysqli_stmt_execute($stmt);
        header("Location: index.php");
        exit;
    }
}
$wargaList = mysqli_query($koneksi, "SELECT w.id, u.nama FROM warga w JOIN users u ON u.id = w.user_id ORDER BY u.nama");
$wilayah = mysqli_query($koneksi, "SELECT * FROM wilayah ORDER BY nama_wilayah");
$judul = $id ? "Edit Jadwal Ronda" : "Tambah Jadwal Ronda";
include "../includes/header.php";
?>
<h2><?= $judul ?></h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST">
  Petugas Ronda
  <select name="warga_id" required>
    <option value="">-- pilih warga --</option>
    <?php while ($w = mysqli_fetch_assoc($wargaList)): ?>
      <option value="<?= $w['id'] ?>" <?= $w['id'] == $warga_id ? 'selected' : '' ?>><?= e($w['nama']) ?></option>
    <?php endwhile; ?>
  </select>
  Wilayah
  <select name="wilayah_id" required>
    <option value="">-- pilih wilayah --</option>
    <?php while ($wl = mysqli_fetch_assoc($wilayah)): ?>
      <option value="<?= $wl['id'] ?>" <?= $wl['id'] == $wilayah_id ? 'selected' : '' ?>><?= e($wl['nama_wilayah']) ?></option>
    <?php endwhile; ?>
  </select>
  Tanggal <input type="date" name="tanggal" value="<?= e($tanggal) ?>" required>
  Jam Mulai <input type="time" name="jam_mulai" value="<?= e($mulai) ?>" required>
  Jam Selesai <input type="time" name="jam_selesai" value="<?= e($selesai) ?>" required>
  Status
  <select name="status">
    <?php foreach ($daftarStatus as $s): ?>
      <option value="<?= $s ?>" <?= $s === $status ? 'selected' : '' ?>><?= $s ?></option>
    <?php endforeach; ?>
  </select>
  <button class="btn" type="submit" name="simpan">Simpan</button>
  <a href="index.php">Batal</a>
</form>
<?php include "../includes/footer.php"; ?>
