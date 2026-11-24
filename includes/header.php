<?php // Navbar dipakai bersama lewat include (Modul 5). Set $judul sebelum include. ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= e($judul ?? 'NyongSiaga') ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="navbar">
  <a class="brand" href="<?= BASE_URL ?>/index.php">NyongSiaga</a>
  <?php if (isset($_SESSION['user'])): $role = $_SESSION['user']['role']; ?>
    <div class="menu">
      <a href="<?= BASE_URL ?>/index.php">Beranda</a>
      <?php if ($role === 'admin'): ?>
        <a href="<?= BASE_URL ?>/wilayah/index.php">Wilayah</a>
        <a href="<?= BASE_URL ?>/warga/index.php">Warga</a>
        <a href="<?= BASE_URL ?>/kategori/index.php">Kategori</a>
        <a href="<?= BASE_URL ?>/users/index.php">Akun</a>
        <!-- Tambah menu modul lain di sini -->
      <?php elseif ($role === 'warga'): ?>
        <a href="<?= BASE_URL ?>/warga/profil.php">Data Diri</a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/ronda/index.php">Jadwal Ronda</a>
      <a href="<?= BASE_URL ?>/laporan/index.php">Laporan</a>
      <a href="<?= BASE_URL ?>/tamu/index.php">Tamu</a>
      <a href="<?= BASE_URL ?>/pengumuman/index.php">Pengumuman</a>
      <span class="user"><?= e($_SESSION['user']['nama']) ?> (<?= e($role) ?>)</span>
      <a class="btn-keluar" href="<?= BASE_URL ?>/logout.php">Keluar</a>
    </div>
  <?php endif; ?>
</nav>
<main class="container">
