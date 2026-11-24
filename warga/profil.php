<?php
// Warga melengkapi atau mengubah data dirinya sendiri.
// Wajib diisi dulu sebelum membuat laporan, jadwal, atau lapor tamu (butuh warga_id).
require_once "../includes/auth.php";
wajib_login(['warga']);
$w = warga_saat_ini();   // null jika belum pernah mengisi
$wilayah_id = $w['wilayah_id'] ?? 0; $alamat = $w['alamat'] ?? ""; $no_hp = $w['no_hp'] ?? "";
$error = ""; $sukses = "";

if (isset($_POST['simpan'])) {
    $wilayah_id = (int)$_POST['wilayah_id']; $alamat = trim($_POST['alamat']); $no_hp = trim($_POST['no_hp']);
    if (!$wilayah_id || $alamat === "") {
        $error = "Semua field wajib diisi.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $no_hp)) {
        $error = "No HP harus 8-15 digit angka.";
    } else {
        $uid = $_SESSION['user']['id'];
        if ($w) {
            $stmt = mysqli_prepare($koneksi, "UPDATE warga SET wilayah_id = ?, alamat = ?, no_hp = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "issi", $wilayah_id, $alamat, $no_hp, $uid);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO warga (user_id, wilayah_id, alamat, no_hp) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiss", $uid, $wilayah_id, $alamat, $no_hp);
        }
        mysqli_stmt_execute($stmt);
        $w = warga_saat_ini();
        $sukses = "Data diri berhasil disimpan.";
    }
}
$wilayah = mysqli_query($koneksi, "SELECT * FROM wilayah ORDER BY nama_wilayah");
$judul = "Data Diri";
include "../includes/header.php";
?>
<h2>Data Diri Warga</h2>
<?php if (!$w): ?><div class="alert error">Lengkapi data diri dulu agar dapat memakai fitur lain.</div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<?php if ($sukses): ?><div class="alert sukses"><?= e($sukses) ?></div><?php endif; ?>
<form method="POST" onsubmit="return cekHp()">
  <?php include "_fields.php"; ?>
  <button class="btn" type="submit" name="simpan">Simpan</button>
</form>
<?php include "../includes/footer.php"; ?>
