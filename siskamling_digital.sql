-- =====================================================
-- Database: Siskamling Digital (Keamanan Lingkungan Warga)
-- SDG 11 (Sustainable Cities and Communities) + SDG 16 (Peace, Justice and Strong Institutions)
-- Import lewat phpMyAdmin > tab SQL
-- Semua akun contoh memakai password: password
-- =====================================================

CREATE DATABASE IF NOT EXISTS siskamling_digital
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE siskamling_digital;

-- Modul 1: Autentikasi (admin RW, ketua ronda, warga)
CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','ketua_ronda','warga') NOT NULL DEFAULT 'warga',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Modul 3: Wilayah (RT/RW/blok)
CREATE TABLE wilayah (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama_wilayah VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Modul 2: Data warga dan rumah
CREATE TABLE warga (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  wilayah_id INT(11) NOT NULL,
  alamat VARCHAR(255) NOT NULL,
  no_hp VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (wilayah_id) REFERENCES wilayah(id)
) ENGINE=InnoDB;

-- Modul 4: Jadwal ronda
CREATE TABLE jadwal_ronda (
  id INT(11) NOT NULL AUTO_INCREMENT,
  wilayah_id INT(11) NOT NULL,
  warga_id INT(11) NOT NULL,
  tanggal DATE NOT NULL,
  jam_mulai TIME NOT NULL,
  jam_selesai TIME NOT NULL,
  status ENUM('terjadwal','hadir','tidak_hadir') NOT NULL DEFAULT 'terjadwal',
  PRIMARY KEY (id),
  FOREIGN KEY (wilayah_id) REFERENCES wilayah(id),
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modul 5: Kategori dan laporan kejadian
CREATE TABLE kategori_kejadian (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama_kategori VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Alur status: menunggu -> diverifikasi / ditolak -> ditangani -> selesai
CREATE TABLE laporan_kejadian (
  id INT(11) NOT NULL AUTO_INCREMENT,
  warga_id INT(11) NOT NULL,
  kategori_id INT(11) NOT NULL,
  wilayah_id INT(11) NOT NULL,
  judul VARCHAR(150) NOT NULL,
  deskripsi VARCHAR(1000) NOT NULL,
  lokasi VARCHAR(255) NOT NULL,
  status ENUM('menunggu','diverifikasi','ditolak','ditangani','selesai') NOT NULL DEFAULT 'menunggu',
  tanggal_lapor TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE,
  FOREIGN KEY (kategori_id) REFERENCES kategori_kejadian(id),
  FOREIGN KEY (wilayah_id) REFERENCES wilayah(id)
) ENGINE=InnoDB;

-- Modul 6: Verifikasi dan tindak lanjut ketua ronda
CREATE TABLE tindak_lanjut (
  id INT(11) NOT NULL AUTO_INCREMENT,
  laporan_id INT(11) NOT NULL,
  petugas_id INT(11) NOT NULL,
  keterangan VARCHAR(500) NOT NULL,
  status ENUM('ditangani','selesai') NOT NULL DEFAULT 'ditangani',
  tanggal TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (laporan_id) REFERENCES laporan_kejadian(id) ON DELETE CASCADE,
  FOREIGN KEY (petugas_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Modul 7: Lapor tamu / pendatang (1x24 jam)
CREATE TABLE tamu (
  id INT(11) NOT NULL AUTO_INCREMENT,
  warga_id INT(11) NOT NULL,
  nama_tamu VARCHAR(100) NOT NULL,
  asal VARCHAR(100) NOT NULL,
  keperluan VARCHAR(255) NOT NULL,
  tanggal_datang DATE NOT NULL,
  tanggal_pulang DATE DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (warga_id) REFERENCES warga(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Modul 8: Pengumuman dan info darurat
CREATE TABLE pengumuman (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  judul VARCHAR(150) NOT NULL,
  isi VARCHAR(1000) NOT NULL,
  tingkat ENUM('biasa','penting','darurat') NOT NULL DEFAULT 'biasa',
  tanggal TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- =====================================================
-- DATA CONTOH
-- =====================================================

INSERT INTO wilayah (nama_wilayah) VALUES
('RT 01 RW 05'),
('RT 02 RW 05'),
('RT 03 RW 05');

-- Password semua akun: password
INSERT INTO users (nama, username, password, role) VALUES
('Pak RW', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Pak Ketua Ronda', 'ketua', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ketua_ronda'),
('Budi Santoso', 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'warga'),
('Siti Aminah', 'siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'warga');

INSERT INTO warga (user_id, wilayah_id, alamat, no_hp) VALUES
(3, 1, 'Jl. Melati No. 5', '081234567890'),
(4, 2, 'Jl. Mawar No. 12', '081298765432');

INSERT INTO jadwal_ronda (wilayah_id, warga_id, tanggal, jam_mulai, jam_selesai, status) VALUES
(1, 1, '2026-11-14', '22:00:00', '04:00:00', 'terjadwal'),
(2, 2, '2026-11-15', '22:00:00', '04:00:00', 'terjadwal');

INSERT INTO kategori_kejadian (nama_kategori) VALUES
('Pencurian'),
('Orang Mencurigakan'),
('Kecelakaan'),
('Kebakaran'),
('Keributan');

INSERT INTO laporan_kejadian (warga_id, kategori_id, wilayah_id, judul, deskripsi, lokasi, status) VALUES
(1, 2, 1, 'Orang asing berkeliling gang malam hari', 'Ada dua orang tidak dikenal berkeliling gang sambil memperhatikan rumah warga sekitar pukul 23.00.', 'Gang 3 Jl. Melati', 'ditangani'),
(2, 1, 2, 'Helm hilang di teras rumah', 'Helm yang ditaruh di teras hilang pagi tadi, diduga diambil saat dini hari.', 'Jl. Mawar No. 12', 'menunggu');

INSERT INTO tindak_lanjut (laporan_id, petugas_id, keterangan, status) VALUES
(1, 2, 'Ronda diperketat di sekitar Gang 3 dan warga diminta mengunci pagar.', 'ditangani');

INSERT INTO tamu (warga_id, nama_tamu, asal, keperluan, tanggal_datang, tanggal_pulang) VALUES
(1, 'Andi Wijaya', 'Semarang', 'Mengunjungi keluarga', '2026-11-09', NULL);

INSERT INTO pengumuman (user_id, judul, isi, tingkat) VALUES
(1, 'Jadwal ronda minggu ini', 'Harap warga yang terjadwal hadir tepat waktu pukul 22.00 di pos ronda.', 'biasa'),
(2, 'Waspada pencurian helm', 'Mohon tidak meninggalkan barang berharga di teras saat malam hari.', 'penting');
