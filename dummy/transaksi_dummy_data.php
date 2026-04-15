<?php

/**
 * Dummy Data Generator untuk Testing Export Transaksi
 * 
 * File ini berisi data dummy yang dapat digunakan untuk mensimulasikan
 * berbagai skenario transaksi dalam berbagai rentang waktu.
 * 
 * Usage: php dummy/generate_dummy_data.php
 */

return [
    'metadata' => [
        'description' => 'Data dummy untuk testing sistem export transaksi',
        'created_at' => '2026-04-15',
        'version' => '1.0',
        'total_records' => 50
    ],
    
    // Data Pengguna (tanpa gelar)
    'users' => [
        ['id' => 1, 'name' => 'Administrator', 'username' => 'admin', 'role' => 'admin'],
        ['id' => 2, 'name' => 'Budi Santoso', 'username' => 'budi', 'role' => 'pengguna'],
        ['id' => 3, 'name' => 'Dewi Kusuma', 'username' => 'dewi', 'role' => 'pengguna'],
        ['id' => 4, 'name' => 'Ahmad Hidayat', 'username' => 'ahmad', 'role' => 'pengguna'],
        ['id' => 5, 'name' => 'Siti Rahayu', 'username' => 'siti', 'role' => 'pengguna'],
        ['id' => 6, 'name' => 'Rudi Hartono', 'username' => 'rudi', 'role' => 'pengguna'],
        ['id' => 7, 'name' => 'Nina Wulandari', 'username' => 'nina', 'role' => 'pengguna'],
        ['id' => 8, 'name' => 'Eko Prasetyo', 'username' => 'eko', 'role' => 'pengguna'],
    ],
    
    // Data Ruangan
    'ruangans' => [
        ['id' => 1, 'nama' => 'Ruang Direktur', 'kode' => 'RD01'],
        ['id' => 2, 'nama' => 'Ruang Sekretaris', 'kode' => 'RS01'],
        ['id' => 3, 'nama' => 'Ruang Keuangan', 'kode' => 'RK01'],
        ['id' => 4, 'nama' => 'Ruang Rapat Besar', 'kode' => 'RR01'],
        ['id' => 5, 'nama' => 'Ruang UKBI', 'kode' => 'RU01'],
        ['id' => 6, 'nama' => 'Ruang Alih Daya', 'kode' => 'RA01'],
        ['id' => 7, 'nama' => 'Gudang Utama', 'kode' => 'GU01'],
        ['id' => 8, 'nama' => 'Ruang Arsip', 'kode' => 'RA02'],
    ],
    
    // Data Barang
    'barangs' => [
        ['id' => 1, 'nama' => 'Kertas A4 80gr', 'satuan' => 'Rim', 'stok' => 50, 'stok_minimum' => 10],
        ['id' => 2, 'nama' => 'Kertas A4 70gr', 'satuan' => 'Rim', 'stok' => 30, 'stok_minimum' => 10],
        ['id' => 3, 'nama' => 'Tinta Printer HP', 'satuan' => 'Pcs', 'stok' => 15, 'stok_minimum' => 5],
        ['id' => 4, 'nama' => 'Tinta Printer Canon', 'satuan' => 'Pcs', 'stok' => 12, 'stok_minimum' => 5],
        ['id' => 5, 'nama' => 'Stapler Besar', 'satuan' => 'Pcs', 'stok' => 8, 'stok_minimum' => 3],
        ['id' => 6, 'nama' => 'Staples No. 10', 'satuan' => 'Box', 'stok' => 25, 'stok_minimum' => 10],
        ['id' => 7, 'nama' => 'Bolpoin Hitam', 'satuan' => 'Pcs', 'stok' => 100, 'stok_minimum' => 20],
        ['id' => 8, 'nama' => 'Bolpoin Biru', 'satuan' => 'Pcs', 'stok' => 80, 'stok_minimum' => 20],
        ['id' => 9, 'nama' => 'Pensil 2B', 'satuan' => 'Pcs', 'stok' => 60, 'stok_minimum' => 15],
        ['id' => 10, 'nama' => 'Penghapus', 'satuan' => 'Pcs', 'stok' => 40, 'stok_minimum' => 10],
        ['id' => 11, 'nama' => 'Penggaris 30cm', 'satuan' => 'Pcs', 'stok' => 20, 'stok_minimum' => 5],
        ['id' => 12, 'nama' => 'Lem Kertas', 'satuan' => 'Pcs', 'stok' => 18, 'stok_minimum' => 5],
        ['id' => 13, 'nama' => 'Tipe-X', 'satuan' => 'Pcs', 'stok' => 12, 'stok_minimum' => 5],
        ['id' => 14, 'nama' => 'Map Folder Kuning', 'satuan' => 'Pcs', 'stok' => 200, 'stok_minimum' => 50],
        ['id' => 15, 'nama' => 'Map Folder Biru', 'satuan' => 'Pcs', 'stok' => 150, 'stok_minimum' => 50],
        ['id' => 16, 'nama' => 'Klip Kertas Besar', 'satuan' => 'Box', 'stok' => 10, 'stok_minimum' => 3],
        ['id' => 17, 'nama' => 'Klip Kertas Kecil', 'satuan' => 'Box', 'stok' => 15, 'stok_minimum' => 5],
        ['id' => 18, 'nama' => 'Gunting Besar', 'satuan' => 'Pcs', 'stok' => 6, 'stok_minimum' => 2],
        ['id' => 19, 'nama' => 'Gunting Kecil', 'satuan' => 'Pcs', 'stok' => 10, 'stok_minimum' => 3],
        ['id' => 20, 'nama' => 'Kalkulator', 'satuan' => 'Pcs', 'stok' => 8, 'stok_minimum' => 3],
    ],
    
    // Data Transaksi (50 records across different dates and scenarios)
    'transaksis' => [
        // Januari 2026
        ['id' => 1, 'barang_id' => 1, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 20, 'jumlah_keluar' => 0, 'sisa_stok' => 50, 'tanggal' => '2026-01-05', 'tgl' => '2026-01-05', 'keterangan' => 'Stok awal tahun', 'pengambil' => null],
        ['id' => 2, 'barang_id' => 2, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 15, 'jumlah_keluar' => 0, 'sisa_stok' => 30, 'tanggal' => '2026-01-05', 'tgl' => '2026-01-05', 'keterangan' => 'Stok awal tahun', 'pengambil' => null],
        ['id' => 3, 'barang_id' => 1, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'sisa_stok' => 45, 'tanggal' => '2026-01-08', 'tgl' => '2026-01-08', 'keterangan' => 'Penggunaan rutin', 'pengambil' => 'Dewi Kusuma'],
        ['id' => 4, 'barang_id' => 3, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'sisa_stok' => 15, 'tanggal' => '2026-01-10', 'tgl' => '2026-01-10', 'keterangan' => 'Pembelian tinta', 'pengambil' => null],
        ['id' => 5, 'barang_id' => 7, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 0, 'jumlah_keluar' => 10, 'sisa_stok' => 90, 'tanggal' => '2026-01-12', 'tgl' => '2026-01-12', 'keterangan' => 'Kebutuhan keuangan', 'pengambil' => 'Ahmad Hidayat'],
        ['id' => 6, 'barang_id' => 8, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'sisa_stok' => 72, 'tanggal' => '2026-01-12', 'tgl' => '2026-01-12', 'keterangan' => 'Kebutuhan keuangan', 'pengambil' => 'Ahmad Hidayat'],
        ['id' => 7, 'barang_id' => 14, 'ruangan_id' => 4, 'user_id' => 4, 'jumlah_masuk' => 50, 'jumlah_keluar' => 0, 'sisa_stok' => 200, 'tanggal' => '2026-01-15', 'tgl' => '2026-01-15', 'keterangan' => 'Stok rapat', 'pengambil' => null],
        ['id' => 8, 'barang_id' => 14, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 0, 'jumlah_keluar' => 20, 'sisa_stok' => 180, 'tanggal' => '2026-01-18', 'tgl' => '2026-01-18', 'keterangan' => 'Dokumen UKBI', 'pengambil' => 'Rudi Hartono'],
        
        // Februari 2026
        ['id' => 9, 'barang_id' => 4, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 8, 'jumlah_keluar' => 0, 'sisa_stok' => 12, 'tanggal' => '2026-02-03', 'tgl' => '2026-02-03', 'keterangan' => 'Restock tinta', 'pengambil' => null],
        ['id' => 10, 'barang_id' => 5, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 0, 'jumlah_keluar' => 2, 'sisa_stok' => 6, 'tanggal' => '2026-02-05', 'tgl' => '2026-02-05', 'keterangan' => 'Rusak', 'pengambil' => 'Nina Wulandari'],
        ['id' => 11, 'barang_id' => 9, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 30, 'jumlah_keluar' => 0, 'sisa_stok' => 60, 'tanggal' => '2026-02-08', 'tgl' => '2026-02-08', 'keterangan' => 'Stok pensil', 'pengambil' => null],
        ['id' => 12, 'barang_id' => 10, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 20, 'jumlah_keluar' => 0, 'sisa_stok' => 40, 'tanggal' => '2026-02-08', 'tgl' => '2026-02-08', 'keterangan' => 'Stok penghapus', 'pengambil' => null],
        ['id' => 13, 'barang_id' => 11, 'ruangan_id' => 6, 'user_id' => 6, 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'sisa_stok' => 20, 'tanggal' => '2026-02-10', 'tgl' => '2026-02-10', 'keterangan' => 'Stok penggaris', 'pengambil' => null],
        ['id' => 14, 'barang_id' => 1, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'sisa_stok' => 37, 'tanggal' => '2026-02-12', 'tgl' => '2026-02-12', 'keterangan' => 'Cetak laporan', 'pengambil' => 'Eko Prasetyo'],
        ['id' => 15, 'barang_id' => 12, 'ruangan_id' => 4, 'user_id' => 4, 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'sisa_stok' => 13, 'tanggal' => '2026-02-15', 'tgl' => '2026-02-15', 'keterangan' => 'Keperluan rapat', 'pengambil' => 'Budi Santoso'],
        ['id' => 16, 'barang_id' => 15, 'ruangan_id' => 7, 'user_id' => 7, 'jumlah_masuk' => 100, 'jumlah_keluar' => 0, 'sisa_stok' => 150, 'tanggal' => '2026-02-20', 'tgl' => '2026-02-20', 'keterangan' => 'Restock folder', 'pengambil' => null],
        
        // Maret 2026
        ['id' => 17, 'barang_id' => 16, 'ruangan_id' => 7, 'user_id' => 1, 'jumlah_masuk' => 5, 'jumlah_keluar' => 0, 'sisa_stok' => 10, 'tanggal' => '2026-03-02', 'tgl' => '2026-03-02', 'keterangan' => 'Restock klip', 'pengambil' => null],
        ['id' => 18, 'barang_id' => 17, 'ruangan_id' => 7, 'user_id' => 1, 'jumlah_masuk' => 8, 'jumlah_keluar' => 0, 'sisa_stok' => 15, 'tanggal' => '2026-03-02', 'tgl' => '2026-03-02', 'keterangan' => 'Restock klip kecil', 'pengambil' => null],
        ['id' => 19, 'barang_id' => 18, 'ruangan_id' => 8, 'user_id' => 8, 'jumlah_masuk' => 4, 'jumlah_keluar' => 0, 'sisa_stok' => 6, 'tanggal' => '2026-03-05', 'tgl' => '2026-03-05', 'keterangan' => 'Restock gunting', 'pengambil' => null],
        ['id' => 20, 'barang_id' => 19, 'ruangan_id' => 8, 'user_id' => 8, 'jumlah_masuk' => 6, 'jumlah_keluar' => 0, 'sisa_stok' => 10, 'tanggal' => '2026-03-05', 'tgl' => '2026-03-05', 'keterangan' => 'Restock gunting kecil', 'pengambil' => null],
        ['id' => 21, 'barang_id' => 20, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 3, 'jumlah_keluar' => 0, 'sisa_stok' => 8, 'tanggal' => '2026-03-08', 'tgl' => '2026-03-08', 'keterangan' => 'Restock kalkulator', 'pengambil' => null],
        ['id' => 22, 'barang_id' => 13, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 5, 'jumlah_keluar' => 0, 'sisa_stok' => 12, 'tanggal' => '2026-03-10', 'tgl' => '2026-03-10', 'keterangan' => 'Restock Tipe-X', 'pengambil' => null],
        ['id' => 23, 'barang_id' => 6, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 15, 'jumlah_keluar' => 0, 'sisa_stok' => 25, 'tanggal' => '2026-03-12', 'tgl' => '2026-03-12', 'keterangan' => 'Restock staples', 'pengambil' => null],
        ['id' => 24, 'barang_id' => 3, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 0, 'jumlah_keluar' => 3, 'sisa_stok' => 12, 'tanggal' => '2026-03-15', 'tgl' => '2026-03-15', 'keterangan' => 'Ganti tinta printer', 'pengambil' => 'Dewi Kusuma'],
        ['id' => 25, 'barang_id' => 7, 'ruangan_id' => 6, 'user_id' => 6, 'jumlah_masuk' => 50, 'jumlah_keluar' => 0, 'sisa_stok' => 100, 'tanggal' => '2026-03-18', 'tgl' => '2026-03-18', 'keterangan' => 'Restock bolpoin', 'pengambil' => null],
        
        // April 2026
        ['id' => 26, 'barang_id' => 1, 'ruangan_id' => 4, 'user_id' => 4, 'jumlah_masuk' => 0, 'jumlah_keluar' => 10, 'sisa_stok' => 27, 'tanggal' => '2026-04-02', 'tgl' => '2026-04-02', 'keterangan' => 'Dokumen rapat akhir bulan', 'pengambil' => 'Siti Rahayu'],
        ['id' => 27, 'barang_id' => 2, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'sisa_stok' => 25, 'tanggal' => '2026-04-03', 'tgl' => '2026-04-03', 'keterangan' => 'Keperluan administrasi', 'pengambil' => 'Rudi Hartono'],
        ['id' => 28, 'barang_id' => 14, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 0, 'jumlah_keluar' => 15, 'sisa_stok' => 165, 'tanggal' => '2026-04-05', 'tgl' => '2026-04-05', 'keterangan' => 'Pengarsipan', 'pengambil' => 'Nina Wulandari'],
        ['id' => 29, 'barang_id' => 15, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 0, 'jumlah_keluar' => 12, 'sisa_stok' => 138, 'tanggal' => '2026-04-05', 'tgl' => '2026-04-05', 'keterangan' => 'Pengarsipan', 'pengambil' => 'Nina Wulandari'],
        ['id' => 30, 'barang_id' => 9, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'sisa_stok' => 52, 'tanggal' => '2026-04-08', 'tgl' => '2026-04-08', 'keterangan' => 'Keperluan UKBI', 'pengambil' => 'Eko Prasetyo'],
        ['id' => 31, 'barang_id' => 10, 'ruangan_id' => 6, 'user_id' => 6, 'jumlah_masuk' => 0, 'jumlah_keluar' => 6, 'sisa_stok' => 34, 'tanggal' => '2026-04-08', 'tgl' => '2026-04-08', 'keterangan' => 'Keperluan alih daya', 'pengambil' => 'Budi Santoso'],
        ['id' => 32, 'barang_id' => 11, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 0, 'jumlah_keluar' => 3, 'sisa_stok' => 17, 'tanggal' => '2026-04-10', 'tgl' => '2026-04-10', 'keterangan' => 'Keperluan keuangan', 'pengambil' => 'Ahmad Hidayat'],
        ['id' => 33, 'barang_id' => 20, 'ruangan_id' => 4, 'user_id' => 4, 'jumlah_masuk' => 0, 'jumlah_keluar' => 2, 'sisa_stok' => 6, 'tanggal' => '2026-04-10', 'tgl' => '2026-04-10', 'keterangan' => 'Keperluan rapat', 'pengambil' => 'Dewi Kusuma'],
        ['id' => 34, 'barang_id' => 12, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'sisa_stok' => 18, 'tanggal' => '2026-04-12', 'tgl' => '2026-04-12', 'keterangan' => 'Restock lem', 'pengambil' => null],
        ['id' => 35, 'barang_id' => 13, 'ruangan_id' => 8, 'user_id' => 8, 'jumlah_masuk' => 0, 'jumlah_keluar' => 4, 'sisa_stok' => 8, 'tanggal' => '2026-04-12', 'tgl' => '2026-04-12', 'keterangan' => 'Keperluan arsip', 'pengambil' => 'Siti Rahayu'],
        
        // Mei 2026 (untuk testing rentang bulan)
        ['id' => 36, 'barang_id' => 4, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'sisa_stok' => 15, 'tanggal' => '2026-05-05', 'tgl' => '2026-05-05', 'keterangan' => 'Restock besar', 'pengambil' => null],
        ['id' => 37, 'barang_id' => 5, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 5, 'jumlah_keluar' => 0, 'sisa_stok' => 9, 'tanggal' => '2026-05-08', 'tgl' => '2026-05-08', 'keterangan' => 'Restock stapler', 'pengambil' => null],
        ['id' => 38, 'barang_id' => 7, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 30, 'jumlah_keluar' => 0, 'sisa_stok' => 115, 'tanggal' => '2026-05-10', 'tgl' => '2026-05-10', 'keterangan' => 'Restock bolpoin', 'pengambil' => null],
        ['id' => 39, 'barang_id' => 8, 'ruangan_id' => 4, 'user_id' => 4, 'jumlah_masuk' => 25, 'jumlah_keluar' => 0, 'sisa_stok' => 85, 'tanggal' => '2026-05-10', 'tgl' => '2026-05-10', 'keterangan' => 'Restock bolpoin biru', 'pengambil' => null],
        ['id' => 40, 'barang_id' => 1, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 0, 'jumlah_keluar' => 12, 'sisa_stok' => 15, 'tanggal' => '2026-05-12', 'tgl' => '2026-05-12', 'keterangan' => 'Keperluan UKBI', 'pengambil' => 'Rudi Hartono'],
        
        // Juni 2026
        ['id' => 41, 'barang_id' => 2, 'ruangan_id' => 6, 'user_id' => 6, 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'sisa_stok' => 17, 'tanggal' => '2026-06-02', 'tgl' => '2026-06-02', 'keterangan' => 'Keperluan alih daya', 'pengambil' => 'Nina Wulandari'],
        ['id' => 42, 'barang_id' => 3, 'ruangan_id' => 7, 'user_id' => 7, 'jumlah_masuk' => 8, 'jumlah_keluar' => 0, 'sisa_stok' => 17, 'tanggal' => '2026-06-05', 'tgl' => '2026-06-05', 'keterangan' => 'Restock tinta', 'pengambil' => null],
        ['id' => 43, 'barang_id' => 6, 'ruangan_id' => 8, 'user_id' => 8, 'jumlah_masuk' => 0, 'jumlah_keluar' => 10, 'sisa_stok' => 15, 'tanggal' => '2026-06-08', 'tgl' => '2026-06-08', 'keterangan' => 'Keperluan arsip', 'pengambil' => 'Eko Prasetyo'],
        ['id' => 44, 'barang_id' => 14, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 50, 'jumlah_keluar' => 0, 'sisa_stok' => 215, 'tanggal' => '2026-06-10', 'tgl' => '2026-06-10', 'keterangan' => 'Restock folder', 'pengambil' => null],
        ['id' => 45, 'barang_id' => 15, 'ruangan_id' => 1, 'user_id' => 1, 'jumlah_masuk' => 40, 'jumlah_keluar' => 0, 'sisa_stok' => 178, 'tanggal' => '2026-06-10', 'tgl' => '2026-06-10', 'keterangan' => 'Restock folder biru', 'pengambil' => null],
        ['id' => 46, 'barang_id' => 16, 'ruangan_id' => 2, 'user_id' => 2, 'jumlah_masuk' => 5, 'jumlah_keluar' => 0, 'sisa_stok' => 15, 'tanggal' => '2026-06-12', 'tgl' => '2026-06-12', 'keterangan' => 'Restock klip', 'pengambil' => null],
        ['id' => 47, 'barang_id' => 17, 'ruangan_id' => 3, 'user_id' => 3, 'jumlah_masuk' => 8, 'jumlah_keluar' => 0, 'sisa_stok' => 23, 'tanggal' => '2026-06-12', 'tgl' => '2026-06-12', 'keterangan' => 'Restock klip kecil', 'pengambil' => null],
        
        // Juli 2026 (untuk testing rentang tahun)
        ['id' => 48, 'barang_id' => 18, 'ruangan_id' => 4, 'user_id' => 4, 'jumlah_masuk' => 4, 'jumlah_keluar' => 0, 'sisa_stok' => 10, 'tanggal' => '2026-07-05', 'tgl' => '2026-07-05', 'keterangan' => 'Restock gunting', 'pengambil' => null],
        ['id' => 49, 'barang_id' => 19, 'ruangan_id' => 5, 'user_id' => 5, 'jumlah_masuk' => 6, 'jumlah_keluar' => 0, 'sisa_stok' => 16, 'tanggal' => '2026-07-08', 'tgl' => '2026-07-08', 'keterangan' => 'Restock gunting kecil', 'pengambil' => null],
        ['id' => 50, 'barang_id' => 20, 'ruangan_id' => 6, 'user_id' => 6, 'jumlah_masuk' => 4, 'jumlah_keluar' => 0, 'sisa_stok' => 10, 'tanggal' => '2026-07-10', 'tgl' => '2026-07-10', 'keterangan' => 'Restock kalkulator', 'pengambil' => null],
    ],
    
    // Test scenarios untuk validasi export
    'test_scenarios' => [
        'semua_data' => [
            'description' => 'Export semua 50 transaksi',
            'expected_count' => 50,
            'export_type' => 'all'
        ],
        'rentang_tanggal' => [
            'description' => 'Export transaksi 01 Apr 2026 - 30 Apr 2026',
            'expected_count' => 10,
            'date_range' => ['2026-04-01', '2026-04-30'],
            'export_type' => 'range'
        ],
        'per_tahun_2026' => [
            'description' => 'Export semua transaksi tahun 2026',
            'expected_count' => 50,
            'year' => 2026,
            'export_type' => 'year'
        ],
        'rentang_tahun' => [
            'description' => 'Export transaksi Jan 2026 - Jun 2026',
            'expected_count' => 47,
            'year_range' => [2026, 2026],
            'export_type' => 'year_range'
        ],
        'per_bulan_maret' => [
            'description' => 'Export transaksi Maret 2026',
            'expected_count' => 9,
            'month' => ['2026', '03'],
            'export_type' => 'month'
        ],
        'rentang_bulan' => [
            'description' => 'Export transaksi Jan 2026 - Mar 2026',
            'expected_count' => 25,
            'month_range' => [
                'from' => ['2026', '01'],
                'to' => ['2026', '03']
            ],
            'export_type' => 'month_range'
        ]
    ]
];
