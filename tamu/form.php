<?php
// Modul 7 (CREATE): warga melaporkan tamu yang datang (1x24 jam).
require_once "../includes/auth.php";
wajib_login(['warga']);
$w = warga_saat_ini();
if (!$w) { header("Location: ../warga/profil.php"); exit; }
$nama = ""; $asal = ""; $keperluan = ""; $tanggal = date('Y-m-d'); $error = "";

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_tamu']); $asal = trim($_POST['asal']);
    $keperluan = trim($_POST['keperluan']); $tanggal = $_POST['tanggal_datang'];
    if ($nama === "" || $asal === "" || $keperluan === "" || $tanggal === "") {
        $error = "Semua field wajib diisi.";
    } elseif (!preg_match('/^[\p{L}\s.\'-]+$/u', $nama)) {
        $error = "Nama tamu tidak boleh mengandung angka atau simbol.";
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO tamu (warga_id, nama_tamu, asal, keperluan, tanggal_datang) VALUES (?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "issss", $w['id'], $nama, $asal, $keperluan, $tanggal);
        mysqli_stmt_execute($stmt);
        header("Location: index.php");
        exit;
    }
}
$judul = "Lapor Tamu";
include "../includes/header.php";
?>
<h2>Lapor Tamu / Pendatang</h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST" onsubmit="return cekNama()">
  Nama Tamu <input type="text" id="nama_tamu" name="nama_tamu" value="<?= e($nama) ?>" required>
  Asal <input type="text" name="asal" value="<?= e($asal) ?>" required>
  Keperluan <input type="text" name="keperluan" value="<?= e($keperluan) ?>" required>
  Tanggal Datang <input type="date" name="tanggal_datang" value="<?= e($tanggal) ?>" required>
  <button class="btn" type="submit" name="simpan">Simpan</button>
  <a href="index.php">Batal</a>
</form>
<script>
// Validasi JavaScript: nama hanya huruf, spasi, titik, apostrof, atau strip
function cekNama() {
  var nama = document.getElementById('nama_tamu').value;
  if (!/^[A-Za-z\s.'-]+$/.test(nama)) { alert('Nama tamu tidak boleh mengandung angka atau simbol!'); return false; }
  return true;
}
</script>
<?php include "../includes/footer.php"; ?>
