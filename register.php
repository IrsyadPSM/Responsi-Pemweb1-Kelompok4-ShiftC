<?php
// REGISTER (Modul 7). Pendaftar baru otomatis berrole 'warga'.
session_start();
require_once "config/koneksi.php";
require_once "includes/auth.php";
// Kalau sudah login, tidak perlu melihat halaman ini lagi
if (isset($_SESSION['user'])) { header("Location: index.php"); exit; }
$error = ""; $sukses = "";
if (isset($_POST['register'])) {
    $nama = trim($_POST['nama']); $username = trim($_POST['username']);
    $pass = $_POST['password']; $konfirmasi = $_POST['konfirmasi'];
    if ($nama === "" || $username === "" || $pass === "") {
        $error = "Semua field wajib diisi.";
    } elseif (strlen($pass) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($pass !== $konfirmasi) {
        $error = "Konfirmasi password tidak sama.";
    } else {
        // Cek username sudah dipakai atau belum
        $cek = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        if (mysqli_num_rows(mysqli_stmt_get_result($cek)) > 0) {
            $error = "Username sudah dipakai, pilih yang lain.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = mysqli_prepare($koneksi, "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, 'warga')");
            mysqli_stmt_bind_param($ins, "sss", $nama, $username, $hash);
            mysqli_stmt_execute($ins);
            $sukses = "Registrasi berhasil, silakan login.";
        }
    }
}
$judul = "Register";
include "includes/header.php";
?>
<div class="box-auth">
  <h2>Registrasi Warga</h2>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <?php if ($sukses): ?><div class="alert sukses"><?= e($sukses) ?> <a href="login.php">Ke login</a></div><?php endif; ?>
  <form method="POST">
    Nama Lengkap <input type="text" name="nama" required>
    Username <input type="text" name="username" required>
    Password <input type="password" name="password" required>
    Konfirmasi Password <input type="password" name="konfirmasi" required>
    <button class="btn" type="submit" name="register">Daftar</button>
  </form>
  <p>Sudah punya akun? <a href="login.php">Login</a></p>
</div>
<?php include "includes/footer.php"; ?>
