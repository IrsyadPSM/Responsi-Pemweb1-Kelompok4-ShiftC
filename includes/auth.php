<?php
// Dipanggil di setiap halaman yang butuh login (Modul 7: session).
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../config/koneksi.php";

// $roles = daftar role yang boleh masuk. Kosong = semua role yang sudah login.
function wajib_login($roles = []) {
    if (!isset($_SESSION['user'])) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
    if ($roles && !in_array($_SESSION['user']['role'], $roles)) {
        http_response_code(403);
        die("Akses ditolak untuk role Anda.");
    }
}
// Cegah XSS saat menampilkan data
function e($teks) { return htmlspecialchars($teks, ENT_QUOTES, 'UTF-8'); }

// Ambil baris tabel warga milik user yang sedang login.
// Mengembalikan null jika user belum melengkapi data diri (warga/profil.php).
function warga_saat_ini() {
    global $koneksi;
    $uid = $_SESSION['user']['id'] ?? 0;
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM warga WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}
