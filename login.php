<?php
// LOGIN (Modul 7). Password dicek dengan password_verify, query pakai prepared statement.
session_start();
require_once "config/koneksi.php";
require_once "includes/auth.php";
// Kalau sudah login, tidak perlu melihat halaman ini lagi
if (isset($_SESSION['user'])) { header("Location: index.php"); exit; }
$error = "";
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $stmt = mysqli_prepare($koneksi, "SELECT id, nama, password, role FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($user && password_verify($_POST['password'], $user['password'])) {
        session_regenerate_id(true); // keamanan: ganti session id setelah login
        $_SESSION['user'] = ['id' => $user['id'], 'nama' => $user['nama'], 'role' => $user['role']];
        header("Location: index.php");
        exit;
    }
    $error = "Username atau password salah.";
}
$judul = "Login";
include "includes/header.php";
?>
<div class="box-auth">
  <h2>Login NyongSiaga</h2>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="POST">
    Username <input type="text" name="username" required>
    Password <input type="password" name="password" required>
    <button class="btn" type="submit" name="login">Login</button>
  </form>
  <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
<?php include "includes/footer.php"; ?>
