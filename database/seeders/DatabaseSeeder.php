<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@kantor.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Pengguna User
        User::create([
            'name' => 'Pengguna Inventaris',
            'username' => 'user',
            'email' => 'user@kantor.com',
            'password' => Hash::make('user123'),
            'role' => 'pengguna',
        ]);

        // Create Ruangan
        $ruangans = [
            ['nama_ruangan' => 'Ruang Alih Daya', 'keterangan' => 'Bagian Alih Daya'],
            ['nama_ruangan' => 'Ruang Perlengkapan', 'keterangan' => 'Bagian Perlengkapan'],
            ['nama_ruangan' => 'Ruang KSU', 'keterangan' => 'Kepala Satuan Unit'],
            ['nama_ruangan' => 'Ruang Keuangan', 'keterangan' => 'Bagian Keuangan'],
            ['nama_ruangan' => 'Ruang Kepala Kantor', 'keterangan' => 'Kepala Kantor'],
            ['nama_ruangan' => 'Ruang Sekretaris', 'keterangan' => 'Sekretaris'],
            ['nama_ruangan' => 'Ruang Malige', 'keterangan' => 'Bagian Malige'],
            ['nama_ruangan' => 'Ruang Laika', 'keterangan' => 'Bagian Laika'],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::create($ruangan);
        }

        // Data barang dari Excel
        $barangData = [
            ['nama_barang' => 'Keset Kaki', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 5],
            ['nama_barang' => 'Kanebo', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Gantungan Kamar Mandi', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Wiper Kaca', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Sikat kecil', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Sikat Kamar Mandi', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Sikat Toilet', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 3],
            ['nama_barang' => 'Kain Lap Meja', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 3],
            ['nama_barang' => 'Taplak Meja', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Tempat Tissue', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 3],
            ['nama_barang' => 'Wiper Lantai Karet', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Kain Pel Dorong', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Kain Pencuci Piring', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Bingkai Foto', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Poster Presiden dan Wakil Presiden', 'kategori' => 'Perlengkapan', 'satuan' => 'Pasang', 'stok' => 0],
            ['nama_barang' => 'Talenan', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Timba Kamar Mandi', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Sendok Spatula', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Termos', 'kategori' => 'Konsumsi', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Sendok Makan', 'kategori' => 'Perlengkapan', 'satuan' => 'Lusin', 'stok' => 3],
            ['nama_barang' => 'Ember', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Sendok Sup', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Sendok Nasi', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 2],
            ['nama_barang' => 'Kertas HVS Warna Biru', 'kategori' => 'ATK', 'satuan' => 'Rim', 'stok' => 0],
            ['nama_barang' => 'Kertas HVS A4', 'kategori' => 'ATK', 'satuan' => 'Rim', 'stok' => 2],
            ['nama_barang' => 'Aqua Botol 330ML', 'kategori' => 'Konsumsi', 'satuan' => 'Dos', 'stok' => 2],
            ['nama_barang' => 'Tissue Basah', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Air Galon Le Mineral', 'kategori' => 'Konsumsi', 'satuan' => 'Galon', 'stok' => 1],
            ['nama_barang' => 'Cairan Pengharum Ruangan', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 1],
            ['nama_barang' => 'Cairan Pembersih Piring', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 0],
            ['nama_barang' => 'Cairan Pembersih Kaca', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 0],
            ['nama_barang' => 'Cairan Pembasmi Nyamuk', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 0],
            ['nama_barang' => 'Cairan Pembersih Tangan', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 0],
            ['nama_barang' => 'Tissue Isi Ulang', 'kategori' => 'Kebersihan', 'satuan' => 'Pak', 'stok' => 1],
            ['nama_barang' => 'Gula Sachet', 'kategori' => 'Konsumsi', 'satuan' => 'Pak', 'stok' => 0],
            ['nama_barang' => 'Kopi', 'kategori' => 'Konsumsi', 'satuan' => 'Bungkus', 'stok' => 1],
            ['nama_barang' => 'Kopi Sachet', 'kategori' => 'Konsumsi', 'satuan' => 'Gantung', 'stok' => 3],
            ['nama_barang' => 'Vixal 600ML', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 1],
            ['nama_barang' => 'Cairan Pembersih Piring (Bungkus)', 'kategori' => 'Kebersihan', 'satuan' => 'Bungkus', 'stok' => 0],
            ['nama_barang' => 'Cairan Pembersih Kamar Mandi', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 0],
            ['nama_barang' => 'Cairan Anti Sumbat', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 0],
            ['nama_barang' => 'Serbet Gantung', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 1],
            ['nama_barang' => 'Cairan Pengharus Ruangan Metic', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 0],
            ['nama_barang' => 'Teh', 'kategori' => 'Konsumsi', 'satuan' => 'Box', 'stok' => 1],
            ['nama_barang' => 'Tissue Gulung', 'kategori' => 'Kebersihan', 'satuan' => 'Pak', 'stok' => 0],
            ['nama_barang' => 'Gunting', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 0],
        ];

        foreach ($barangData as $barang) {
            Barang::create($barang);
        }

        // Create sample transactions for testing
        $this->createSampleTransactions();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin / admin123');
        $this->command->info('User: user / user123');
        $this->command->info('Sample transactions created!');
    }

    /**
     * Create sample transactions for testing
     */
    private function createSampleTransactions()
    {
        $admin = User::where('username', 'admin')->first();
        $ruangan = Ruangan::first();
        
        // Get some barang for transactions
        $barang1 = Barang::where('nama_barang', 'Kertas HVS A4')->first();
        $barang2 = Barang::where('nama_barang', 'Kopi')->first();
        $barang3 = Barang::where('nama_barang', 'Aqua Botol 330ML')->first();

        if ($barang1 && $admin) {
            // Transaction 1: Barang Masuk
            Transaksi::create([
                'barang_id' => $barang1->id,
                'user_id' => $admin->id,
                'tipe' => 'masuk',
                'jumlah_masuk' => 10,
                'jumlah_keluar' => 0,
                'jumlah' => 10,
                'stok_sebelum' => 2,
                'stok_setelah_masuk' => 12,
                'sisa_stok' => 12,
                'tanggal' => Carbon::now()->subDays(5),
                'keterangan' => 'Pengadaan rutin bulanan',
            ]);

            // Transaction 2: Barang Keluar
            Transaksi::create([
                'barang_id' => $barang1->id,
                'user_id' => $admin->id,
                'ruangan_id' => $ruangan ? $ruangan->id : null,
                'tipe' => 'keluar',
                'jumlah_masuk' => 0,
                'jumlah_keluar' => 3,
                'jumlah' => 3,
                'stok_sebelum' => 12,
                'sisa_stok' => 9,
                'tanggal' => Carbon::now()->subDays(3),
                'tanggal_keluar' => Carbon::now()->subDays(3),
                'nama_pengambil' => 'Budi Santoso',
                'tipe_pengambil' => 'nama_ruangan',
                'keterangan' => 'Keperluan rapat',
            ]);
        }

        if ($barang2 && $admin) {
            // Transaction 3: Barang Masuk
            Transaksi::create([
                'barang_id' => $barang2->id,
                'user_id' => $admin->id,
                'tipe' => 'masuk',
                'jumlah_masuk' => 5,
                'jumlah_keluar' => 0,
                'jumlah' => 5,
                'stok_sebelum' => 1,
                'stok_setelah_masuk' => 6,
                'sisa_stok' => 6,
                'tanggal' => Carbon::now()->subDays(7),
                'keterangan' => 'Restock kopi kantor',
            ]);
        }

        if ($barang3 && $admin && $ruangan) {
            // Transaction 4: Barang Keluar with ruangan
            Transaksi::create([
                'barang_id' => $barang3->id,
                'user_id' => $admin->id,
                'ruangan_id' => $ruangan->id,
                'tipe' => 'keluar',
                'jumlah_masuk' => 0,
                'jumlah_keluar' => 1,
                'jumlah' => 1,
                'stok_sebelum' => 2,
                'sisa_stok' => 1,
                'tanggal' => Carbon::now()->subDays(2),
                'tanggal_keluar' => Carbon::now()->subDays(2),
                'nama_pengambil' => 'Dewi Kusuma',
                'tipe_pengambil' => 'ruangan_saja',
                'keterangan' => 'Tamu instansi',
            ]);
        }
    }
}
