<?php
// Modul 8 (CREATE + UPDATE) pengumuman. Hanya admin dan ketua_ronda.
require_once "../includes/auth.php";
wajib_login(['admin', 'ketua_ronda']);
$id = (int)($_GET['id'] ?? 0);
$judul_p = ""; $isi = ""; $tingkat = "biasa"; $error = "";
$daftarTingkat = ['biasa', 'penting', 'darurat'];

if ($id) {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM pengumuman WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$r) die("Data tidak ditemukan.");
    $judul_p = $r['judul']; $isi = $r['isi']; $tingkat = $r['tingkat'];
}

if (isset($_POST['simpan'])) {
    $judul_p = trim($_POST['judul']); $isi = trim($_POST['isi']); $tingkat = $_POST['tingkat'];
    if ($judul_p === "" || $isi === "") {
        $error = "Judul dan isi wajib diisi.";
    } elseif (strlen($judul_p) > 150 || strlen($isi) > 1000) {
        $error = "Judul maksimal 150 karakter dan isi maksimal 1000 karakter.";
    } elseif (!in_array($tingkat, $daftarTingkat)) {
        $error = "Tingkat tidak valid.";
    } else {
        if ($id) {
            $stmt = mysqli_prepare($koneksi, "UPDATE pengumuman SET judul = ?, isi = ?, tingkat = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $judul_p, $isi, $tingkat, $id);
        } else {
            $uid = $_SESSION['user']['id'];
            $stmt = mysqli_prepare($koneksi, "INSERT INTO pengumuman (user_id, judul, isi, tingkat) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "isss", $uid, $judul_p, $isi, $tingkat);
        }
        mysqli_stmt_execute($stmt);
        header("Location: index.php");
        exit;
    }
}
$judul = $id ? "Edit Pengumuman" : "Buat Pengumuman";
include "../includes/header.php";
?>
<h2><?= $judul ?></h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST">
  Judul <input type="text" name="judul" maxlength="150" value="<?= e($judul_p) ?>" required>
  Tingkat
  <select name="tingkat">
    <?php foreach ($daftarTingkat as $t): ?>
      <option value="<?= $t ?>" <?= $t === $tingkat ? 'selected' : '' ?>><?= $t ?></option>
    <?php endforeach; ?>
  </select>
  Isi
  <textarea id="isi" name="isi" rows="6" style="width:100%" onkeyup="hitung()" required><?= e($isi) ?></textarea>
  <p>Sisa karakter: <b id="sisa">1000</b></p>
  <button class="btn" type="submit" name="simpan">Simpan</button>
  <a href="index.php">Batal</a>
</form>
<script>
// Penghitung karakter (pola Modul 4 JavaScript, Percobaan 6)
function hitung() {
  var max = 1000;
  var el = document.getElementById('isi');
  if (el.value.length > max) {
    alert('Maaf, batas maksimum pengisian adalah ' + max + ' karakter!');
    el.value = el.value.substring(0, max);
  }
  document.getElementById('sisa').textContent = max - el.value.length;
}
hitung();
</script>
<?php include "../includes/footer.php"; ?>
