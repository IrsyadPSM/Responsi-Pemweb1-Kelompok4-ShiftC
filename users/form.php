<?php
// Modul 1 (CREATE + UPDATE akun) oleh admin. Password selalu disimpan dengan password_hash.
require_once "../includes/auth.php";
wajib_login(['admin']);
$id = (int)($_GET['id'] ?? 0);
$nama = ""; $username = ""; $role = "warga"; $error = "";
$daftarRole = ['admin', 'ketua_ronda', 'warga'];
$diriSendiri = ($id === (int)$_SESSION['user']['id']);   // admin tidak boleh menurunkan role dirinya sendiri

if ($id) {
    $stmt = mysqli_prepare($koneksi, "SELECT nama, username, role FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$r) die("Akun tidak ditemukan.");
    $nama = $r['nama']; $username = $r['username']; $role = $r['role'];
}

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']); $pass = $_POST['password'];
    if (!$diriSendiri) $role = $_POST['role'];
    if (!$id) $username = trim($_POST['username']);

    if ($nama === "" || (!$id && $username === "")) {
        $error = "Nama dan username wajib diisi.";
    } elseif (!in_array($role, $daftarRole)) {
        $error = "Role tidak valid.";
    } elseif ((!$id && strlen($pass) < 6) || ($id && $pass !== "" && strlen($pass) < 6)) {
        $error = "Password minimal 6 karakter.";
    } else {
        if (!$id) {   // cek username unik untuk akun baru
            $cek = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
            mysqli_stmt_bind_param($cek, "s", $username);
            mysqli_stmt_execute($cek);
            if (mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0) $error = "Username sudah dipakai.";
        }
        if (!$error) {
            if ($id && $pass !== "") {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($koneksi, "UPDATE users SET nama = ?, role = ?, password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "sssi", $nama, $role, $hash, $id);
            } elseif ($id) {
                $stmt = mysqli_prepare($koneksi, "UPDATE users SET nama = ?, role = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "ssi", $nama, $role, $id);
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($koneksi, "INSERT INTO users (nama, username, password, role) VALUES (?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $hash, $role);
            }
            mysqli_stmt_execute($stmt);
            if ($diriSendiri) $_SESSION['user']['nama'] = $nama;
            header("Location: index.php");
            exit;
        }
    }
}
$judul = $id ? "Edit Akun" : "Tambah Akun";
include "../includes/header.php";
?>
<h2><?= $judul ?></h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="POST">
  Nama <input type="text" name="nama" value="<?= e($nama) ?>" required>
  Username <input type="text" name="username" value="<?= e($username) ?>" <?= $id ? 'disabled' : 'required' ?>>
  Role
  <select name="role" <?= $diriSendiri ? 'disabled' : '' ?>>
    <?php foreach ($daftarRole as $r): ?>
      <option value="<?= $r ?>" <?= $r === $role ? 'selected' : '' ?>><?= $r ?></option>
    <?php endforeach; ?>
  </select>
  Password <?= $id ? '(kosongkan jika tidak diganti)' : '' ?>
  <input type="password" name="password" <?= $id ? '' : 'required' ?>>
  <button class="btn" type="submit" name="simpan">Simpan</button>
  <a href="index.php">Batal</a>
</form>
<?php include "../includes/footer.php"; ?>
