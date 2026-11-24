<?php
// Modul 2 (CREATE + UPDATE) oleh admin. ?id= berarti edit.
require_once "../includes/auth.php";
wajib_login(['admin']);
$id = (int)($_GET['id'] ?? 0);
$user_id = 0; $nama_akun = ""; $wilayah_id = 0; $alamat = ""; $no_hp = ""; $error = "";

if ($id) {
    $stmt = mysqli_prepare($koneksi, "SELECT w.*, u.nama FROM warga w JOIN users u ON u.id = w.user_id WHERE w.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$r) die("Data tidak ditemukan.");
    $user_id = $r['user_id']; $nama_akun = $r['nama']; $wilayah_id = $r['wilayah_id']; $alamat = $r['alamat']; $no_hp = $r['no_hp'];
}

if (isset($_POST['simpan'])) {
    if (!$id) $user_id = (int)$_POST['user_id'];
    $wilayah_id = (int)$_POST['wilayah_id']; $alamat = trim($_POST['alamat']); $no_hp = trim($_POST['no_hp']);
    if (!$user_id || !$wilayah_id || $alamat === "") {
        $error = "Semua field wajib diisi.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $no_hp)) {
        $error = "No HP harus 8-15 digit angka.";
    } else {
        if ($id) {
            $stmt = mysqli_prepare($koneksi, "UPDATE warga SET wilayah_id = ?, alamat = ?, no_hp = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "issi", $wilayah_id, $alamat, $no_hp, $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO warga (user_id, wilayah_id, alamat, no_hp) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiss", $user_id, $wilayah_id, $alamat, $no_hp);
        }
        mysqli_stmt_execute($stmt);
        header("Location: index.php");
        exit;
    }
}
$wilayah = mysqli_query($koneksi, "SELECT * FROM wilayah ORDER BY nama_wilayah");
// Akun warga yang belum punya data warga
$akun = mysqli_query($koneksi, "SELECT id, nama, username FROM users WHERE role = 'warga' AND id NOT IN (SELECT user_id FROM warga)");
$judul = $id ? "Edit Data Warga" : "Tambah Data Warga";
include "../includes/header.php";
?>
<h2><?= $judul ?></h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST" onsubmit="return cekHp()">
  Akun
  <?php if ($id): ?>
    <input type="text" value="<?= e($nama_akun) ?>" disabled>
  <?php else: ?>
    <select name="user_id" required>
      <option value="">-- pilih akun warga --</option>
      <?php while ($a = mysqli_fetch_assoc($akun)): ?>
        <option value="<?= $a['id'] ?>" <?= $a['id'] == $user_id ? 'selected' : '' ?>><?= e($a['nama']) ?> (<?= e($a['username']) ?>)</option>
      <?php endwhile; ?>
    </select>
  <?php endif; ?>
  <?php include "_fields.php"; ?>
  <button class="btn" type="submit" name="simpan">Simpan</button>
  <a href="index.php">Batal</a>
</form>
<?php include "../includes/footer.php"; ?>
