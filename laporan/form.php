<?php
// Modul 5 (CREATE): warga membuat laporan kejadian. Status awal selalu 'menunggu'.
require_once "../includes/auth.php";
wajib_login(['warga']);
$w = warga_saat_ini();
if (!$w) { header("Location: ../warga/profil.php"); exit; }
$kategori_id = 0; $wilayah_id = $w['wilayah_id']; $judul_lap = ""; $deskripsi = ""; $lokasi = ""; $error = "";

if (isset($_POST['simpan'])) {
    $kategori_id = (int)$_POST['kategori_id']; $wilayah_id = (int)$_POST['wilayah_id'];
    $judul_lap = trim($_POST['judul']); $deskripsi = trim($_POST['deskripsi']); $lokasi = trim($_POST['lokasi']);
    if (!$kategori_id || !$wilayah_id || $judul_lap === "" || $deskripsi === "" || $lokasi === "") {
        $error = "Semua field wajib diisi.";
    } elseif (strlen($judul_lap) > 150 || strlen($deskripsi) > 1000) {
        $error = "Judul maksimal 150 karakter dan deskripsi maksimal 1000 karakter.";
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO laporan_kejadian (warga_id, kategori_id, wilayah_id, judul, deskripsi, lokasi) VALUES (?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "iiisss", $w['id'], $kategori_id, $wilayah_id, $judul_lap, $deskripsi, $lokasi);
        mysqli_stmt_execute($stmt);
        header("Location: index.php");
        exit;
    }
}
$kategori = mysqli_query($koneksi, "SELECT * FROM kategori_kejadian ORDER BY nama_kategori");
$wilayah = mysqli_query($koneksi, "SELECT * FROM wilayah ORDER BY nama_wilayah");
$judul = "Buat Laporan";
include "../includes/header.php";
?>
<h2>Buat Laporan Kejadian</h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST">
  Kategori
  <select name="kategori_id" required>
    <option value="">-- pilih kategori --</option>
    <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
      <option value="<?= $k['id'] ?>" <?= $k['id'] == $kategori_id ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
    <?php endwhile; ?>
  </select>
  Wilayah
  <select name="wilayah_id" required>
    <?php while ($wl = mysqli_fetch_assoc($wilayah)): ?>
      <option value="<?= $wl['id'] ?>" <?= $wl['id'] == $wilayah_id ? 'selected' : '' ?>><?= e($wl['nama_wilayah']) ?></option>
    <?php endwhile; ?>
  </select>
  Judul <input type="text" name="judul" maxlength="150" value="<?= e($judul_lap) ?>" required>
  Lokasi kejadian <input type="text" name="lokasi" value="<?= e($lokasi) ?>" required>
  Deskripsi
  <textarea id="deskripsi" name="deskripsi" rows="6" style="width:100%" onkeyup="hitung()" required><?= e($deskripsi) ?></textarea>
  <p>Sisa karakter: <b id="sisa">1000</b></p>
  <button class="btn" type="submit" name="simpan">Kirim Laporan</button>
  <a href="index.php">Batal</a>
</form>
<script>
// Penghitung karakter (pola Modul 4 JavaScript, Percobaan 6)
function hitung() {
  var max = 1000;
  var el = document.getElementById('deskripsi');
  if (el.value.length > max) {
    alert('Maaf, batas maksimum pengisian adalah ' + max + ' karakter!');
    el.value = el.value.substring(0, max);
  }
  document.getElementById('sisa').textContent = max - el.value.length;
}
hitung();
</script>
<?php include "../includes/footer.php"; ?>
