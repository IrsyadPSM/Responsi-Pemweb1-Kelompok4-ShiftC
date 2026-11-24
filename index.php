<?php
// Modul 9: Dashboard dan rekap. Isi menyesuaikan role (admin/ketua lihat semua, warga lihat miliknya).
require_once "includes/auth.php";
wajib_login();
$role = $_SESSION['user']['role'];
$kelola = in_array($role, ['admin', 'ketua_ronda']);

// Hitung satu angka dari query COUNT. Parameter opsional untuk prepared statement.
function angka($koneksi, $sql, $tipe = "", $nilai = []) {
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($nilai) mysqli_stmt_bind_param($stmt, $tipe, ...$nilai);
    mysqli_stmt_execute($stmt);
    $baris = mysqli_fetch_row(mysqli_stmt_get_result($stmt));
    return (int)$baris[0];
}
// Ambil hasil query sebagai array (untuk rekap)
function rekap($koneksi, $sql) {
    $hasil = mysqli_query($koneksi, $sql);
    $out = [];
    while ($r = mysqli_fetch_assoc($hasil)) $out[] = $r;
    return $out;
}
// Tampilkan rekap sebagai bar sederhana (lebar div sebanding dengan jumlah)
function bar_rekap($judul, $rows) {
    echo "<h3>" . e($judul) . "</h3>";
    $max = 1;
    foreach ($rows as $r) $max = max($max, (int)$r['jml']);
    foreach ($rows as $r) {
        $lebar = round($r['jml'] / $max * 300);
        echo '<div class="bar-baris"><span class="bar-label">' . e($r['label']) . '</span>'
           . '<div class="bar-isi" style="width:' . $lebar . 'px"></div><b>' . (int)$r['jml'] . '</b></div>';
    }
    if (!$rows) echo "<p>Belum ada data.</p>";
}

$kartu = [];
$belumLengkap = false;
if ($kelola) {
    $kartu = [
        ['Total warga', angka($koneksi, "SELECT COUNT(*) FROM warga")],
        ['Laporan menunggu verifikasi', angka($koneksi, "SELECT COUNT(*) FROM laporan_kejadian WHERE status = 'menunggu'")],
        ['Laporan sedang ditangani', angka($koneksi, "SELECT COUNT(*) FROM laporan_kejadian WHERE status IN ('diverifikasi','ditangani')")],
        ['Laporan selesai', angka($koneksi, "SELECT COUNT(*) FROM laporan_kejadian WHERE status = 'selesai'")],
        ['Tamu aktif', angka($koneksi, "SELECT COUNT(*) FROM tamu WHERE tanggal_pulang IS NULL")],
    ];
} else {
    $w = warga_saat_ini();
    if ($w) {
        $kartu = [
            ['Laporan saya', angka($koneksi, "SELECT COUNT(*) FROM laporan_kejadian WHERE warga_id = ?", "i", [$w['id']])],
            ['Laporan saya selesai', angka($koneksi, "SELECT COUNT(*) FROM laporan_kejadian WHERE warga_id = ? AND status = 'selesai'", "i", [$w['id']])],
            ['Tamu aktif saya', angka($koneksi, "SELECT COUNT(*) FROM tamu WHERE warga_id = ? AND tanggal_pulang IS NULL", "i", [$w['id']])],
        ];
    } else { $belumLengkap = true; }
}
$ronda = rekap($koneksi, "SELECT j.jam_mulai, j.jam_selesai, j.status, u.nama, wl.nama_wilayah FROM jadwal_ronda j
    JOIN warga w ON w.id = j.warga_id JOIN users u ON u.id = w.user_id JOIN wilayah wl ON wl.id = j.wilayah_id
    WHERE j.tanggal = CURDATE() ORDER BY j.jam_mulai");
$info = rekap($koneksi, "SELECT judul, isi, tingkat, tanggal FROM pengumuman ORDER BY (tingkat = 'darurat') DESC, tanggal DESC LIMIT 3");

$judul = "Dashboard";
include "includes/header.php";
?>
<h2>Dashboard, <?= e($_SESSION['user']['nama']) ?></h2>
<p>Masuk sebagai <b><?= e($role) ?></b>.
<?php if ($kelola): ?><button class="btn no-print" onclick="window.print()">Cetak Laporan</button><?php endif; ?></p>

<?php if ($belumLengkap): ?>
  <div class="alert error">Data diri Anda belum lengkap. <a href="warga/profil.php">Lengkapi sekarang</a> agar bisa membuat laporan dan melapor tamu.</div>
<?php endif; ?>

<div class="kartu-grid">
  <?php foreach ($kartu as $k): ?><div class="kartu"><b><?= $k[1] ?></b><?= e($k[0]) ?></div><?php endforeach; ?>
</div>

<h3>Pengumuman Terbaru</h3>
<?php foreach ($info as $p): ?>
  <div class="info"><p><span class="badge tg-<?= e($p['tingkat']) ?>"><?= e($p['tingkat']) ?></span> <b><?= e($p['judul']) ?></b></p>
  <p><?= e($p['isi']) ?></p></div>
<?php endforeach; ?>
<?php if (!$info): ?><p>Belum ada pengumuman.</p><?php endif; ?>

<h3>Jadwal Ronda Hari Ini</h3>
<?php if (!$ronda): ?><p>Tidak ada jadwal ronda hari ini.</p><?php else: ?>
<table>
  <tr><th>Jam</th><th>Petugas</th><th>Wilayah</th><th>Status</th></tr>
  <?php foreach ($ronda as $r): ?>
  <tr><td><?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?></td>
      <td><?= e($r['nama']) ?></td><td><?= e($r['nama_wilayah']) ?></td>
      <td><span class="badge st-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($kelola):
  bar_rekap("Laporan per Kategori", rekap($koneksi, "SELECT k.nama_kategori AS label, COUNT(l.id) AS jml
      FROM kategori_kejadian k LEFT JOIN laporan_kejadian l ON l.kategori_id = k.id GROUP BY k.id, k.nama_kategori ORDER BY jml DESC"));
  bar_rekap("Laporan per Wilayah (kerawanan)", rekap($koneksi, "SELECT wl.nama_wilayah AS label, COUNT(l.id) AS jml
      FROM wilayah wl LEFT JOIN laporan_kejadian l ON l.wilayah_id = wl.id GROUP BY wl.id, wl.nama_wilayah ORDER BY jml DESC"));
  bar_rekap("Laporan per Status", rekap($koneksi, "SELECT status AS label, COUNT(*) AS jml FROM laporan_kejadian GROUP BY status ORDER BY jml DESC"));
endif; ?>
<?php include "includes/footer.php"; ?>
