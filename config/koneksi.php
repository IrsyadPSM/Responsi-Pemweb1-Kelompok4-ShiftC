<?php
// Koneksi database (Modul 6). Ubah password jika MySQL kalian memakai password.
define('BASE_URL', '/siskamling'); // nama folder di htdocs
$koneksi = mysqli_connect("localhost", "root", "", "siskamling_digital") or die("Gagal koneksi database");
mysqli_set_charset($koneksi, "utf8mb4");
