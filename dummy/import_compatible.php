<?php

/**
 * COMPATIBLE DUMMY DATA IMPORT
 * Sesuai dengan struktur tabel yang ada
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n";
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║     IMPORT DUMMY DATA (Compatible Schema)              ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n";
echo "\n";

// Data dummy 50 transaksi
$dummyData = [
    // Januari 2026 - Barang Masuk (stok awal)
    ['barang_id' => 1, 'tipe' => 'masuk', 'jumlah_masuk' => 50, 'jumlah_keluar' => 0, 'jumlah' => 50, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 50, 'sisa_stok' => 50, 'tanggal' => '2026-01-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok awal tahun - Kertas A4 80gr', 'created_at' => '2026-01-05 08:00:00', 'updated_at' => '2026-01-05 08:00:00'],
    ['barang_id' => 2, 'tipe' => 'masuk', 'jumlah_masuk' => 30, 'jumlah_keluar' => 0, 'jumlah' => 30, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 30, 'sisa_stok' => 30, 'tanggal' => '2026-01-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok awal tahun - Kertas A4 70gr', 'created_at' => '2026-01-05 08:00:00', 'updated_at' => '2026-01-05 08:00:00'],
    ['barang_id' => 3, 'tipe' => 'masuk', 'jumlah_masuk' => 20, 'jumlah_keluar' => 0, 'jumlah' => 20, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 20, 'sisa_stok' => 20, 'tanggal' => '2026-01-08', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Pembelian tinta HP', 'created_at' => '2026-01-08 09:30:00', 'updated_at' => '2026-01-08 09:30:00'],
    ['barang_id' => 7, 'tipe' => 'masuk', 'jumlah_masuk' => 100, 'jumlah_keluar' => 0, 'jumlah' => 100, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 100, 'sisa_stok' => 100, 'tanggal' => '2026-01-10', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok bolpoin hitam', 'created_at' => '2026-01-10 10:00:00', 'updated_at' => '2026-01-10 10:00:00'],
    ['barang_id' => 14, 'tipe' => 'masuk', 'jumlah_masuk' => 200, 'jumlah_keluar' => 0, 'jumlah' => 200, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 200, 'sisa_stok' => 200, 'tanggal' => '2026-01-12', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok map folder kuning', 'created_at' => '2026-01-12 11:00:00', 'updated_at' => '2026-01-12 11:00:00'],
    
    // Januari 2026 - Barang Keluar
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'jumlah' => 5, 'stok_sebelum' => 50, 'stok_setelah_masuk' => 50, 'sisa_stok' => 45, 'tanggal' => '2026-01-15', 'ruangan_id' => 1, 'user_id' => 2, 'nama_pengambil' => 'Budi Santoso', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-01-15', 'keterangan' => 'Penggunaan ruang direktur', 'created_at' => '2026-01-15 13:00:00', 'updated_at' => '2026-01-15 13:00:00'],
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 3, 'jumlah' => 3, 'stok_sebelum' => 45, 'stok_setelah_masuk' => 45, 'sisa_stok' => 42, 'tanggal' => '2026-01-18', 'ruangan_id' => 3, 'user_id' => 3, 'nama_pengambil' => 'Dewi Kusuma', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-01-18', 'keterangan' => 'Keperluan keuangan', 'created_at' => '2026-01-18 14:00:00', 'updated_at' => '2026-01-18 14:00:00'],
    ['barang_id' => 7, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 10, 'jumlah' => 10, 'stok_sebelum' => 100, 'stok_setelah_masuk' => 100, 'sisa_stok' => 90, 'tanggal' => '2026-01-20', 'ruangan_id' => 2, 'user_id' => 2, 'nama_pengambil' => 'Ahmad Hidayat', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-01-20', 'keterangan' => 'Peminjaman bolpoin', 'created_at' => '2026-01-20 08:00:00', 'updated_at' => '2026-01-20 08:00:00'],
    
    // Februari 2026
    ['barang_id' => 4, 'tipe' => 'masuk', 'jumlah_masuk' => 15, 'jumlah_keluar' => 0, 'jumlah' => 15, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 15, 'sisa_stok' => 15, 'tanggal' => '2026-02-03', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Restock tinta Canon', 'created_at' => '2026-02-03 08:00:00', 'updated_at' => '2026-02-03 08:00:00'],
    ['barang_id' => 8, 'tipe' => 'masuk', 'jumlah_masuk' => 80, 'jumlah_keluar' => 0, 'jumlah' => 80, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 80, 'sisa_stok' => 80, 'tanggal' => '2026-02-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok bolpoin biru', 'created_at' => '2026-02-05 09:00:00', 'updated_at' => '2026-02-05 09:00:00'],
    ['barang_id' => 9, 'tipe' => 'masuk', 'jumlah_masuk' => 60, 'jumlah_keluar' => 0, 'jumlah' => 60, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 60, 'sisa_stok' => 60, 'tanggal' => '2026-02-08', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok pensil', 'created_at' => '2026-02-08 10:00:00', 'updated_at' => '2026-02-08 10:00:00'],
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'jumlah' => 8, 'stok_sebelum' => 42, 'stok_setelah_masuk' => 42, 'sisa_stok' => 34, 'tanggal' => '2026-02-10', 'ruangan_id' => 4, 'user_id' => 4, 'nama_pengambil' => 'Siti Rahayu', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-02-10', 'keterangan' => 'Rapat bulanan', 'created_at' => '2026-02-10 11:00:00', 'updated_at' => '2026-02-10 11:00:00'],
    ['barang_id' => 2, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'jumlah' => 5, 'stok_sebelum' => 30, 'stok_setelah_masuk' => 30, 'sisa_stok' => 25, 'tanggal' => '2026-02-12', 'ruangan_id' => 3, 'user_id' => 3, 'nama_pengambil' => 'Rudi Hartono', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-02-12', 'keterangan' => 'Cetak dokumen', 'created_at' => '2026-02-12 13:00:00', 'updated_at' => '2026-02-12 13:00:00'],
    ['barang_id' => 14, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 20, 'jumlah' => 20, 'stok_sebelum' => 200, 'stok_setelah_masuk' => 200, 'sisa_stok' => 180, 'tanggal' => '2026-02-15', 'ruangan_id' => 1, 'user_id' => 2, 'nama_pengambil' => 'Nina Wulandari', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-02-15', 'keterangan' => 'Pengarsipan', 'created_at' => '2026-02-15 14:00:00', 'updated_at' => '2026-02-15 14:00:00'],
    
    // Maret 2026
    ['barang_id' => 10, 'tipe' => 'masuk', 'jumlah_masuk' => 40, 'jumlah_keluar' => 0, 'jumlah' => 40, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 40, 'sisa_stok' => 40, 'tanggal' => '2026-03-02', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok penghapus', 'created_at' => '2026-03-02 08:00:00', 'updated_at' => '2026-03-02 08:00:00'],
    ['barang_id' => 11, 'tipe' => 'masuk', 'jumlah_masuk' => 20, 'jumlah_keluar' => 0, 'jumlah' => 20, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 20, 'sisa_stok' => 20, 'tanggal' => '2026-03-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok penggaris', 'created_at' => '2026-03-05 09:00:00', 'updated_at' => '2026-03-05 09:00:00'],
    ['barang_id' => 3, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'jumlah' => 5, 'stok_sebelum' => 20, 'stok_setelah_masuk' => 20, 'sisa_stok' => 15, 'tanggal' => '2026-03-08', 'ruangan_id' => 2, 'user_id' => 2, 'nama_pengambil' => 'Eko Prasetyo', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-03-08', 'keterangan' => 'Ganti tinta printer', 'created_at' => '2026-03-08 10:00:00', 'updated_at' => '2026-03-08 10:00:00'],
    ['barang_id' => 7, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 15, 'jumlah' => 15, 'stok_sebelum' => 90, 'stok_setelah_masuk' => 90, 'sisa_stok' => 75, 'tanggal' => '2026-03-10', 'ruangan_id' => 3, 'user_id' => 3, 'nama_pengambil' => 'Budi Santoso', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-03-10', 'keterangan' => 'Keperluan rutin', 'created_at' => '2026-03-10 11:00:00', 'updated_at' => '2026-03-10 11:00:00'],
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 6, 'jumlah' => 6, 'stok_sebelum' => 34, 'stok_setelah_masuk' => 34, 'sisa_stok' => 28, 'tanggal' => '2026-03-12', 'ruangan_id' => 4, 'user_id' => 4, 'nama_pengambil' => 'Dewi Kusuma', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-03-12', 'keterangan' => 'Cetak laporan', 'created_at' => '2026-03-12 13:00:00', 'updated_at' => '2026-03-12 13:00:00'],
    ['barang_id' => 14, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 15, 'jumlah' => 15, 'stok_sebelum' => 180, 'stok_setelah_masuk' => 180, 'sisa_stok' => 165, 'tanggal' => '2026-03-15', 'ruangan_id' => 5, 'user_id' => 5, 'nama_pengambil' => 'Ahmad Hidayat', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-03-15', 'keterangan' => 'Dokumen UKBI', 'created_at' => '2026-03-15 14:00:00', 'updated_at' => '2026-03-15 14:00:00'],
    ['barang_id' => 9, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'jumlah' => 8, 'stok_sebelum' => 60, 'stok_setelah_masuk' => 60, 'sisa_stok' => 52, 'tanggal' => '2026-03-18', 'ruangan_id' => 6, 'user_id' => 6, 'nama_pengambil' => 'Siti Rahayu', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-03-18', 'keterangan' => 'Keperluan alih daya', 'created_at' => '2026-03-18 15:00:00', 'updated_at' => '2026-03-18 15:00:00'],
    
    // April 2026
    ['barang_id' => 15, 'tipe' => 'masuk', 'jumlah_masuk' => 150, 'jumlah_keluar' => 0, 'jumlah' => 150, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 150, 'sisa_stok' => 150, 'tanggal' => '2026-04-02', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok map folder biru', 'created_at' => '2026-04-02 08:00:00', 'updated_at' => '2026-04-02 08:00:00'],
    ['barang_id' => 5, 'tipe' => 'masuk', 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'jumlah' => 10, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 10, 'sisa_stok' => 10, 'tanggal' => '2026-04-03', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok stapler', 'created_at' => '2026-04-03 09:00:00', 'updated_at' => '2026-04-03 09:00:00'],
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 10, 'jumlah' => 10, 'stok_sebelum' => 28, 'stok_setelah_masuk' => 28, 'sisa_stok' => 18, 'tanggal' => '2026-04-05', 'ruangan_id' => 1, 'user_id' => 2, 'nama_pengambil' => 'Rudi Hartono', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-04-05', 'keterangan' => 'Rapat besar', 'created_at' => '2026-04-05 10:00:00', 'updated_at' => '2026-04-05 10:00:00'],
    ['barang_id' => 2, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'jumlah' => 8, 'stok_sebelum' => 25, 'stok_setelah_masuk' => 25, 'sisa_stok' => 17, 'tanggal' => '2026-04-05', 'ruangan_id' => 2, 'user_id' => 2, 'nama_pengambil' => 'Nina Wulandari', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-04-05', 'keterangan' => 'Keperluan sekretariat', 'created_at' => '2026-04-05 10:00:00', 'updated_at' => '2026-04-05 10:00:00'],
    ['barang_id' => 7, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 12, 'jumlah' => 12, 'stok_sebelum' => 75, 'stok_setelah_masuk' => 75, 'sisa_stok' => 63, 'tanggal' => '2026-04-08', 'ruangan_id' => 3, 'user_id' => 3, 'nama_pengambil' => 'Eko Prasetyo', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-04-08', 'keterangan' => 'Keperluan keuangan', 'created_at' => '2026-04-08 11:00:00', 'updated_at' => '2026-04-08 11:00:00'],
    ['barang_id' => 8, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 10, 'jumlah' => 10, 'stok_sebelum' => 80, 'stok_setelah_masuk' => 80, 'sisa_stok' => 70, 'tanggal' => '2026-04-08', 'ruangan_id' => 4, 'user_id' => 4, 'nama_pengambil' => 'Budi Santoso', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-04-08', 'keterangan' => 'Rapat koordinasi', 'created_at' => '2026-04-08 11:00:00', 'updated_at' => '2026-04-08 11:00:00'],
    ['barang_id' => 14, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 25, 'jumlah' => 25, 'stok_sebelum' => 165, 'stok_setelah_masuk' => 165, 'sisa_stok' => 140, 'tanggal' => '2026-04-10', 'ruangan_id' => 2, 'user_id' => 2, 'nama_pengambil' => 'Dewi Kusuma', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-04-10', 'keterangan' => 'Pengarsipan dokumen', 'created_at' => '2026-04-10 13:00:00', 'updated_at' => '2026-04-10 13:00:00'],
    
    // Mei 2026
    ['barang_id' => 16, 'tipe' => 'masuk', 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'jumlah' => 10, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 10, 'sisa_stok' => 10, 'tanggal' => '2026-05-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok klip besar', 'created_at' => '2026-05-05 08:00:00', 'updated_at' => '2026-05-05 08:00:00'],
    ['barang_id' => 17, 'tipe' => 'masuk', 'jumlah_masuk' => 15, 'jumlah_keluar' => 0, 'jumlah' => 15, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 15, 'sisa_stok' => 15, 'tanggal' => '2026-05-08', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok klip kecil', 'created_at' => '2026-05-08 09:00:00', 'updated_at' => '2026-05-08 09:00:00'],
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'jumlah' => 8, 'stok_sebelum' => 18, 'stok_setelah_masuk' => 18, 'sisa_stok' => 10, 'tanggal' => '2026-05-10', 'ruangan_id' => 5, 'user_id' => 5, 'nama_pengambil' => 'Ahmad Hidayat', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-05-10', 'keterangan' => 'Keperluan UKBI', 'created_at' => '2026-05-10 10:00:00', 'updated_at' => '2026-05-10 10:00:00'],
    ['barang_id' => 2, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'jumlah' => 5, 'stok_sebelum' => 17, 'stok_setelah_masuk' => 17, 'sisa_stok' => 12, 'tanggal' => '2026-05-12', 'ruangan_id' => 6, 'user_id' => 6, 'nama_pengambil' => 'Siti Rahayu', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-05-12', 'keterangan' => 'Keperluan alih daya', 'created_at' => '2026-05-12 11:00:00', 'updated_at' => '2026-05-12 11:00:00'],
    ['barang_id' => 7, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 15, 'jumlah' => 15, 'stok_sebelum' => 63, 'stok_setelah_masuk' => 63, 'sisa_stok' => 48, 'tanggal' => '2026-05-15', 'ruangan_id' => 3, 'user_id' => 3, 'nama_pengambil' => 'Rudi Hartono', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-05-15', 'keterangan' => 'Cetak massal', 'created_at' => '2026-05-15 13:00:00', 'updated_at' => '2026-05-15 13:00:00'],
    
    // Juni 2026
    ['barang_id' => 18, 'tipe' => 'masuk', 'jumlah_masuk' => 8, 'jumlah_keluar' => 0, 'jumlah' => 8, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 8, 'sisa_stok' => 8, 'tanggal' => '2026-06-02', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok gunting besar', 'created_at' => '2026-06-02 08:00:00', 'updated_at' => '2026-06-02 08:00:00'],
    ['barang_id' => 19, 'tipe' => 'masuk', 'jumlah_masuk' => 12, 'jumlah_keluar' => 0, 'jumlah' => 12, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 12, 'sisa_stok' => 12, 'tanggal' => '2026-06-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok gunting kecil', 'created_at' => '2026-06-05 09:00:00', 'updated_at' => '2026-06-05 09:00:00'],
    ['barang_id' => 20, 'tipe' => 'masuk', 'jumlah_masuk' => 10, 'jumlah_keluar' => 0, 'jumlah' => 10, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 10, 'sisa_stok' => 10, 'tanggal' => '2026-06-08', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Stok kalkulator', 'created_at' => '2026-06-08 10:00:00', 'updated_at' => '2026-06-08 10:00:00'],
    ['barang_id' => 1, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'jumlah' => 5, 'stok_sebelum' => 10, 'stok_setelah_masuk' => 10, 'sisa_stok' => 5, 'tanggal' => '2026-06-10', 'ruangan_id' => 4, 'user_id' => 4, 'nama_pengambil' => 'Nina Wulandari', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-06-10', 'keterangan' => 'Rapat semester', 'created_at' => '2026-06-10 11:00:00', 'updated_at' => '2026-06-10 11:00:00'],
    ['barang_id' => 2, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 6, 'jumlah' => 6, 'stok_sebelum' => 12, 'stok_setelah_masuk' => 12, 'sisa_stok' => 6, 'tanggal' => '2026-06-12', 'ruangan_id' => 1, 'user_id' => 2, 'nama_pengambil' => 'Eko Prasetyo', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-06-12', 'keterangan' => 'Keperluan direktur', 'created_at' => '2026-06-12 13:00:00', 'updated_at' => '2026-06-12 13:00:00'],
    ['barang_id' => 3, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 5, 'jumlah' => 5, 'stok_sebelum' => 15, 'stok_setelah_masuk' => 15, 'sisa_stok' => 10, 'tanggal' => '2026-06-15', 'ruangan_id' => 2, 'user_id' => 2, 'nama_pengambil' => 'Budi Santoso', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-06-15', 'keterangan' => 'Ganti tinta', 'created_at' => '2026-06-15 14:00:00', 'updated_at' => '2026-06-15 14:00:00'],
    
    // Juli 2026
    ['barang_id' => 4, 'tipe' => 'masuk', 'jumlah_masuk' => 12, 'jumlah_keluar' => 0, 'jumlah' => 12, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 12, 'sisa_stok' => 12, 'tanggal' => '2026-07-05', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Restock tinta Canon', 'created_at' => '2026-07-05 08:00:00', 'updated_at' => '2026-07-05 08:00:00'],
    ['barang_id' => 5, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 3, 'jumlah' => 3, 'stok_sebelum' => 10, 'stok_setelah_masuk' => 10, 'sisa_stok' => 7, 'tanggal' => '2026-07-08', 'ruangan_id' => 3, 'user_id' => 3, 'nama_pengambil' => 'Dewi Kusuma', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-07-08', 'keterangan' => 'Keperluan keuangan', 'created_at' => '2026-07-08 09:00:00', 'updated_at' => '2026-07-08 09:00:00'],
    ['barang_id' => 6, 'tipe' => 'masuk', 'jumlah_masuk' => 20, 'jumlah_keluar' => 0, 'jumlah' => 20, 'stok_sebelum' => 0, 'stok_setelah_masuk' => 20, 'sisa_stok' => 20, 'tanggal' => '2026-07-10', 'ruangan_id' => 7, 'user_id' => 1, 'nama_pengambil' => null, 'tipe_pengambil' => null, 'tanggal_keluar' => null, 'keterangan' => 'Restock staples', 'created_at' => '2026-07-10 10:00:00', 'updated_at' => '2026-07-10 10:00:00'],
    ['barang_id' => 9, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 8, 'jumlah' => 8, 'stok_sebelum' => 52, 'stok_setelah_masuk' => 52, 'sisa_stok' => 44, 'tanggal' => '2026-07-12', 'ruangan_id' => 5, 'user_id' => 5, 'nama_pengambil' => 'Ahmad Hidayat', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-07-12', 'keterangan' => 'Keperluan UKBI', 'created_at' => '2026-07-12 11:00:00', 'updated_at' => '2026-07-12 11:00:00'],
    ['barang_id' => 10, 'tipe' => 'keluar', 'jumlah_masuk' => 0, 'jumlah_keluar' => 6, 'jumlah' => 6, 'stok_sebelum' => 40, 'stok_setelah_masuk' => 40, 'sisa_stok' => 34, 'tanggal' => '2026-07-15', 'ruangan_id' => 6, 'user_id' => 6, 'nama_pengambil' => 'Siti Rahayu', 'tipe_pengambil' => 'internal', 'tanggal_keluar' => '2026-07-15', 'keterangan' => 'Keperluan alih daya', 'created_at' => '2026-07-15 13:00:00', 'updated_at' => '2026-07-15 13:00:00'],
];

// Insert data
$successCount = 0;
$errorCount = 0;
$errors = [];

echo "📊 Inserting " . count($dummyData) . " dummy transactions...\n\n";

foreach ($dummyData as $index => $data) {
    try {
        DB::table('transaksis')->insert($data);
        $successCount++;
        if (($index + 1) % 10 == 0) {
            echo "✅ Progress: " . ($index + 1) . "/" . count($dummyData) . " inserted\n";
        }
    } catch (Exception $e) {
        $errorCount++;
        $errors[] = $e->getMessage();
        echo "⚠️  Error on record " . ($index + 1) . ": " . substr($e->getMessage(), 0, 50) . "\n";
    }
}

echo "\n";
echo str_repeat("=", 60) . "\n";
echo "IMPORT SUMMARY\n";
echo str_repeat("=", 60) . "\n";
echo "✅ Successfully inserted: {$successCount}\n";
echo "❌ Failed: {$errorCount}\n";
echo "\n";

if (!empty($errors)) {
    echo "Error details (first 3):\n";
    for ($i = 0; $i < min(3, count($errors)); $i++) {
        echo "  - " . $errors[$i] . "\n";
    }
    echo "\n";
}

// Verify counts
$transaksiCount = DB::table('transaksis')->count();
echo "📊 TOTAL TRANSACTIONS IN DATABASE: {$transaksiCount}\n\n";

// Show monthly breakdown
$monthly = DB::select("SELECT strftime('%Y-%m', tanggal) as bulan, COUNT(*) as jumlah FROM transaksis GROUP BY bulan ORDER BY bulan");

if ($monthly) {
    echo "📅 TRANSACTIONS BY MONTH:\n";
    foreach ($monthly as $m) {
        echo "   {$m->bulan}: {$m->jumlah} transaksi\n";
    }
    echo "\n";
}

// Count by type
$masuk = DB::table('transaksis')->where('tipe', 'masuk')->count();
$keluar = DB::table('transaksis')->where('tipe', 'keluar')->count();
echo "📊 BY TYPE:\n";
echo "   Barang Masuk: {$masuk}\n";
echo "   Barang Keluar: {$keluar}\n\n";

if ($transaksiCount >= 50) {
    echo "🎉 SUCCESS! Database now has {$transaksiCount} transactions\n";
    echo "✅ Ready for comprehensive QA testing with all 6 export types\n";
    exit(0);
} else {
    echo "⚠️  Current count: {$transaksiCount} (expected 50+)\n";
    echo "ℹ️  You may already have some data, total is what matters\n";
    exit(0);
}
