-- =====================================================
-- DUMMY DATA SEEDER FOR TESTING EXPORT TRANSAKSI
-- Sistem Inventaris Kantor - QA Testing Dataset
-- =====================================================
-- 
-- INSTRUKSI PENGGUNAAN:
-- 1. Backup database terlebih dahulu!
-- 2. Jalankan: sqlite3 database/database.sqlite < dummy/transaksi_dummy_seeder.sql
-- 3. Atau import via phpMyAdmin/DB tool favorit Anda
--
-- CATATAN: Data ini bersifat SIMULASI untuk testing export
-- Nama-nama digunakan tanpa gelar sesuai permintaan

-- =====================================================
-- USERS (8 users)
-- =====================================================
INSERT INTO users (id, name, username, password, role, created_at, updated_at) VALUES 
(1, 'Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, 'Budi Santoso', 'budi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(3, 'Dewi Kusuma', 'dewi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(4, 'Ahmad Hidayat', 'ahmad', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(5, 'Siti Rahayu', 'siti', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(6, 'Rudi Hartono', 'rudi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(7, 'Nina Wulandari', 'nina', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(8, 'Eko Prasetyo', 'eko', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengguna', '2026-01-01 00:00:00', '2026-01-01 00:00:00');

-- =====================================================
-- RUANGANS (8 ruangan)
-- =====================================================
INSERT INTO ruangans (id, nama_ruangan, kode_ruangan, keterangan, created_at, updated_at) VALUES 
(1, 'Ruang Direktur', 'RD01', 'Ruang kerja direktur', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(2, 'Ruang Sekretaris', 'RS01', 'Ruang sekretariat', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(3, 'Ruang Keuangan', 'RK01', 'Bagian keuangan', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(4, 'Ruang Rapat Besar', 'RR01', 'Ruang rapat kapasitas 20 orang', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(5, 'Ruang UKBI', 'RU01', 'Unit Kegiatan Bahasa Indonesia', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(6, 'Ruang Alih Daya', 'RA01', 'Ruang tenaga alih daya', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(7, 'Gudang Utama', 'GU01', 'Penyimpanan barang', '2026-01-01 00:00:00', '2026-01-01 00:00:00'),
(8, 'Ruang Arsip', 'RA02', 'Penyimpanan arsip', '2026-01-01 00:00:00', '2026-01-01 00:00:00');

-- =====================================================
-- BARANGS (20 barang)
-- =====================================================
INSERT INTO barangs (id, nama_barang, satuan, stok, stok_minimum, created_at, updated_at) VALUES 
(1, 'Kertas A4 80gr', 'Rim', 50, 10, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(2, 'Kertas A4 70gr', 'Rim', 30, 10, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(3, 'Tinta Printer HP', 'Pcs', 15, 5, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(4, 'Tinta Printer Canon', 'Pcs', 12, 5, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(5, 'Stapler Besar', 'Pcs', 8, 3, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(6, 'Staples No. 10', 'Box', 25, 10, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(7, 'Bolpoin Hitam', 'Pcs', 100, 20, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(8, 'Bolpoin Biru', 'Pcs', 80, 20, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(9, 'Pensil 2B', 'Pcs', 60, 15, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(10, 'Penghapus', 'Pcs', 40, 10, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(11, 'Penggaris 30cm', 'Pcs', 20, 5, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(12, 'Lem Kertas', 'Pcs', 18, 5, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(13, 'Tipe-X', 'Pcs', 12, 5, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(14, 'Map Folder Kuning', 'Pcs', 200, 50, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(15, 'Map Folder Biru', 'Pcs', 150, 50, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(16, 'Klip Kertas Besar', 'Box', 10, 3, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(17, 'Klip Kertas Kecil', 'Box', 15, 5, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(18, 'Gunting Besar', 'Pcs', 6, 2, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(19, 'Gunting Kecil', 'Pcs', 10, 3, '2026-01-01 00:00:00', '2026-07-10 00:00:00'),
(20, 'Kalkulator', 'Pcs', 8, 3, '2026-01-01 00:00:00', '2026-07-10 00:00:00');

-- =====================================================
-- TRANSAKSIS (50 transaksi - Jan 2026 s/d Jul 2026)
-- =====================================================

-- Januari 2026 (8 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(1, 1, 1, 1, 20, 0, 50, '2026-01-05', '2026-01-05', 'Stok awal tahun', NULL, '2026-01-05 08:00:00', '2026-01-05 08:00:00'),
(2, 2, 1, 1, 15, 0, 30, '2026-01-05', '2026-01-05', 'Stok awal tahun', NULL, '2026-01-05 08:00:00', '2026-01-05 08:00:00'),
(3, 1, 2, 2, 0, 5, 45, '2026-01-08', '2026-01-08', 'Penggunaan rutin', 'Dewi Kusuma', '2026-01-08 09:30:00', '2026-01-08 09:30:00'),
(4, 3, 1, 1, 10, 0, 15, '2026-01-10', '2026-01-10', 'Pembelian tinta', NULL, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
(5, 7, 3, 3, 0, 10, 90, '2026-01-12', '2026-01-12', 'Kebutuhan keuangan', 'Ahmad Hidayat', '2026-01-12 11:00:00', '2026-01-12 11:00:00'),
(6, 8, 3, 3, 0, 8, 72, '2026-01-12', '2026-01-12', 'Kebutuhan keuangan', 'Ahmad Hidayat', '2026-01-12 11:00:00', '2026-01-12 11:00:00'),
(7, 14, 4, 4, 50, 0, 200, '2026-01-15', '2026-01-15', 'Stok rapat', NULL, '2026-01-15 13:00:00', '2026-01-15 13:00:00'),
(8, 14, 5, 5, 0, 20, 180, '2026-01-18', '2026-01-18', 'Dokumen UKBI', 'Rudi Hartono', '2026-01-18 14:00:00', '2026-01-18 14:00:00');

-- Februari 2026 (8 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(9, 4, 1, 1, 8, 0, 12, '2026-02-03', '2026-02-03', 'Restock tinta', NULL, '2026-02-03 08:00:00', '2026-02-03 08:00:00'),
(10, 5, 2, 2, 0, 2, 6, '2026-02-05', '2026-02-05', 'Rusak', 'Nina Wulandari', '2026-02-05 09:00:00', '2026-02-05 09:00:00'),
(11, 9, 5, 5, 30, 0, 60, '2026-02-08', '2026-02-08', 'Stok pensil', NULL, '2026-02-08 10:00:00', '2026-02-08 10:00:00'),
(12, 10, 5, 5, 20, 0, 40, '2026-02-08', '2026-02-08', 'Stok penghapus', NULL, '2026-02-08 10:00:00', '2026-02-08 10:00:00'),
(13, 11, 6, 6, 10, 0, 20, '2026-02-10', '2026-02-10', 'Stok penggaris', NULL, '2026-02-10 11:00:00', '2026-02-10 11:00:00'),
(14, 1, 3, 3, 0, 8, 37, '2026-02-12', '2026-02-12', 'Cetak laporan', 'Eko Prasetyo', '2026-02-12 13:00:00', '2026-02-12 13:00:00'),
(15, 12, 4, 4, 0, 5, 13, '2026-02-15', '2026-02-15', 'Keperluan rapat', 'Budi Santoso', '2026-02-15 14:00:00', '2026-02-15 14:00:00'),
(16, 15, 7, 7, 100, 0, 150, '2026-02-20', '2026-02-20', 'Restock folder', NULL, '2026-02-20 15:00:00', '2026-02-20 15:00:00');

-- Maret 2026 (9 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(17, 16, 7, 1, 5, 0, 10, '2026-03-02', '2026-03-02', 'Restock klip', NULL, '2026-03-02 08:00:00', '2026-03-02 08:00:00'),
(18, 17, 7, 1, 8, 0, 15, '2026-03-02', '2026-03-02', 'Restock klip kecil', NULL, '2026-03-02 08:00:00', '2026-03-02 08:00:00'),
(19, 18, 8, 8, 4, 0, 6, '2026-03-05', '2026-03-05', 'Restock gunting', NULL, '2026-03-05 09:00:00', '2026-03-05 09:00:00'),
(20, 19, 8, 8, 6, 0, 10, '2026-03-05', '2026-03-05', 'Restock gunting kecil', NULL, '2026-03-05 09:00:00', '2026-03-05 09:00:00'),
(21, 20, 3, 3, 3, 0, 8, '2026-03-08', '2026-03-08', 'Restock kalkulator', NULL, '2026-03-08 10:00:00', '2026-03-08 10:00:00'),
(22, 13, 2, 2, 5, 0, 12, '2026-03-10', '2026-03-10', 'Restock Tipe-X', NULL, '2026-03-10 11:00:00', '2026-03-10 11:00:00'),
(23, 6, 1, 1, 15, 0, 25, '2026-03-12', '2026-03-12', 'Restock staples', NULL, '2026-03-12 13:00:00', '2026-03-12 13:00:00'),
(24, 3, 5, 5, 0, 3, 12, '2026-03-15', '2026-03-15', 'Ganti tinta printer', 'Dewi Kusuma', '2026-03-15 14:00:00', '2026-03-15 14:00:00'),
(25, 7, 6, 6, 50, 0, 100, '2026-03-18', '2026-03-18', 'Restock bolpoin', NULL, '2026-03-18 15:00:00', '2026-03-18 15:00:00');

-- April 2026 (10 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(26, 1, 4, 4, 0, 10, 27, '2026-04-02', '2026-04-02', 'Dokumen rapat akhir bulan', 'Siti Rahayu', '2026-04-02 08:00:00', '2026-04-02 08:00:00'),
(27, 2, 3, 3, 0, 5, 25, '2026-04-03', '2026-04-03', 'Keperluan administrasi', 'Rudi Hartono', '2026-04-03 09:00:00', '2026-04-03 09:00:00'),
(28, 14, 2, 2, 0, 15, 165, '2026-04-05', '2026-04-05', 'Pengarsipan', 'Nina Wulandari', '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(29, 15, 2, 2, 0, 12, 138, '2026-04-05', '2026-04-05', 'Pengarsipan', 'Nina Wulandari', '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(30, 9, 5, 5, 0, 8, 52, '2026-04-08', '2026-04-08', 'Keperluan UKBI', 'Eko Prasetyo', '2026-04-08 11:00:00', '2026-04-08 11:00:00'),
(31, 10, 6, 6, 0, 6, 34, '2026-04-08', '2026-04-08', 'Keperluan alih daya', 'Budi Santoso', '2026-04-08 11:00:00', '2026-04-08 11:00:00'),
(32, 11, 3, 3, 0, 3, 17, '2026-04-10', '2026-04-10', 'Keperluan keuangan', 'Ahmad Hidayat', '2026-04-10 13:00:00', '2026-04-10 13:00:00'),
(33, 20, 4, 4, 0, 2, 6, '2026-04-10', '2026-04-10', 'Keperluan rapat', 'Dewi Kusuma', '2026-04-10 14:00:00', '2026-04-10 14:00:00'),
(34, 12, 1, 1, 10, 0, 18, '2026-04-12', '2026-04-12', 'Restock lem', NULL, '2026-04-12 15:00:00', '2026-04-12 15:00:00'),
(35, 13, 8, 8, 0, 4, 8, '2026-04-12', '2026-04-12', 'Keperluan arsip', 'Siti Rahayu', '2026-04-12 15:00:00', '2026-04-12 15:00:00');

-- Mei 2026 (5 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(36, 4, 1, 1, 10, 0, 15, '2026-05-05', '2026-05-05', 'Restock besar', NULL, '2026-05-05 08:00:00', '2026-05-05 08:00:00'),
(37, 5, 2, 2, 5, 0, 9, '2026-05-08', '2026-05-08', 'Restock stapler', NULL, '2026-05-08 09:00:00', '2026-05-08 09:00:00'),
(38, 7, 3, 3, 30, 0, 115, '2026-05-10', '2026-05-10', 'Restock bolpoin', NULL, '2026-05-10 10:00:00', '2026-05-10 10:00:00'),
(39, 8, 4, 4, 25, 0, 85, '2026-05-10', '2026-05-10', 'Restock bolpoin biru', NULL, '2026-05-10 10:00:00', '2026-05-10 10:00:00'),
(40, 1, 5, 5, 0, 12, 15, '2026-05-12', '2026-05-12', 'Keperluan UKBI', 'Rudi Hartono', '2026-05-12 11:00:00', '2026-05-12 11:00:00');

-- Juni 2026 (7 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(41, 2, 6, 6, 0, 8, 17, '2026-06-02', '2026-06-02', 'Keperluan alih daya', 'Nina Wulandari', '2026-06-02 08:00:00', '2026-06-02 08:00:00'),
(42, 3, 7, 7, 8, 0, 17, '2026-06-05', '2026-06-05', 'Restock tinta', NULL, '2026-06-05 09:00:00', '2026-06-05 09:00:00'),
(43, 6, 8, 8, 0, 10, 15, '2026-06-08', '2026-06-08', 'Keperluan arsip', 'Eko Prasetyo', '2026-06-08 10:00:00', '2026-06-08 10:00:00'),
(44, 14, 1, 1, 50, 0, 215, '2026-06-10', '2026-06-10', 'Restock folder', NULL, '2026-06-10 11:00:00', '2026-06-10 11:00:00'),
(45, 15, 1, 1, 40, 0, 178, '2026-06-10', '2026-06-10', 'Restock folder biru', NULL, '2026-06-10 11:00:00', '2026-06-10 11:00:00'),
(46, 16, 2, 2, 5, 0, 15, '2026-06-12', '2026-06-12', 'Restock klip', NULL, '2026-06-12 13:00:00', '2026-06-12 13:00:00'),
(47, 17, 3, 3, 8, 0, 23, '2026-06-12', '2026-06-12', 'Restock klip kecil', NULL, '2026-06-12 13:00:00', '2026-06-12 13:00:00');

-- Juli 2026 (3 transaksi)
INSERT INTO transaksis (id, barang_id, ruangan_id, user_id, jumlah_masuk, jumlah_keluar, sisa_stok, tanggal, tgl, keterangan, pengambil, created_at, updated_at) VALUES
(48, 18, 4, 4, 4, 0, 10, '2026-07-05', '2026-07-05', 'Restock gunting', NULL, '2026-07-05 08:00:00', '2026-07-05 08:00:00'),
(49, 19, 5, 5, 6, 0, 16, '2026-07-08', '2026-07-08', 'Restock gunting kecil', NULL, '2026-07-08 09:00:00', '2026-07-08 09:00:00'),
(50, 20, 6, 6, 4, 0, 10, '2026-07-10', '2026-07-10', 'Restock kalkulator', NULL, '2026-07-10 10:00:00', '2026-07-10 10:00:00');

-- =====================================================
-- VERIFICATION QUERY (Run this to verify data)
-- =====================================================
-- Total transaksi per bulan:
-- SELECT strftime('%Y-%m', tanggal) as bulan, COUNT(*) as jumlah FROM transaksis GROUP BY bulan;
-- 
-- Expected result:
-- 2026-01 | 8
-- 2026-02 | 8
-- 2026-03 | 9
-- 2026-04 | 10
-- 2026-05 | 5
-- 2026-06 | 7
-- 2026-07 | 3
-- Total: 50 transaksi
-- =====================================================
