<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\User;
use App\Models\Ruangan;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('username', 'admin')->first();
        $ruangan = Ruangan::first();
        
        if (!$admin) {
            $this->command->error('Admin user not found!');
            return;
        }

        // Get some barang for transactions
        $barang1 = Barang::where('nama_barang', 'Kertas HVS A4')->first();
        $barang2 = Barang::where('nama_barang', 'Kopi')->first();
        $barang3 = Barang::where('nama_barang', 'Aqua Botol 330ML')->first();
        $barang4 = Barang::where('nama_barang', 'Keset Kaki')->first();

        $transaksiCount = 0;

        if ($barang1) {
            // Transaction 1: Barang Masuk
            Transaksi::create([
                'barang_id' => $barang1->id,
                'user_id' => $admin->id,
                'tipe' => 'masuk',
                'jumlah_masuk' => 10,
                'jumlah_keluar' => 0,
                'jumlah' => 10,
                'stok_sebelum' => $barang1->stok,
                'stok_setelah_masuk' => $barang1->stok + 10,
                'sisa_stok' => $barang1->stok + 10,
                'tanggal' => Carbon::now()->subDays(5),
                'keterangan' => 'Pengadaan rutin bulanan',
            ]);
            $transaksiCount++;

            // Transaction 2: Barang Keluar
            Transaksi::create([
                'barang_id' => $barang1->id,
                'user_id' => $admin->id,
                'ruangan_id' => $ruangan ? $ruangan->id : null,
                'tipe' => 'keluar',
                'jumlah_masuk' => 0,
                'jumlah_keluar' => 3,
                'jumlah' => 3,
                'stok_sebelum' => $barang1->stok + 10,
                'sisa_stok' => $barang1->stok + 7,
                'tanggal' => Carbon::now()->subDays(3),
                'tanggal_keluar' => Carbon::now()->subDays(3),
                'nama_pengambil' => 'Budi Santoso',
                'tipe_pengambil' => 'nama_ruangan',
                'keterangan' => 'Keperluan rapat koordinasi',
            ]);
            $transaksiCount++;
        }

        if ($barang2) {
            // Transaction 3: Barang Masuk
            Transaksi::create([
                'barang_id' => $barang2->id,
                'user_id' => $admin->id,
                'tipe' => 'masuk',
                'jumlah_masuk' => 5,
                'jumlah_keluar' => 0,
                'jumlah' => 5,
                'stok_sebelum' => $barang2->stok,
                'stok_setelah_masuk' => $barang2->stok + 5,
                'sisa_stok' => $barang2->stok + 5,
                'tanggal' => Carbon::now()->subDays(7),
                'keterangan' => 'Restock kopi kantor',
            ]);
            $transaksiCount++;

            // Transaction 4: Barang Keluar
            Transaksi::create([
                'barang_id' => $barang2->id,
                'user_id' => $admin->id,
                'ruangan_id' => $ruangan ? $ruangan->id : null,
                'tipe' => 'keluar',
                'jumlah_masuk' => 0,
                'jumlah_keluar' => 2,
                'jumlah' => 2,
                'stok_sebelum' => $barang2->stok + 5,
                'sisa_stok' => $barang2->stok + 3,
                'tanggal' => Carbon::now()->subDays(4),
                'tanggal_keluar' => Carbon::now()->subDays(4),
                'nama_pengambil' => 'Ahmad Fauzi',
                'tipe_pengambil' => 'nama_ruangan',
                'keterangan' => 'Keperluan tamu dinas',
            ]);
            $transaksiCount++;
        }

        if ($barang3 && $ruangan) {
            // Transaction 5: Barang Keluar
            Transaksi::create([
                'barang_id' => $barang3->id,
                'user_id' => $admin->id,
                'ruangan_id' => $ruangan->id,
                'tipe' => 'keluar',
                'jumlah_masuk' => 0,
                'jumlah_keluar' => 1,
                'jumlah' => 1,
                'stok_sebelum' => $barang3->stok,
                'sisa_stok' => $barang3->stok - 1,
                'tanggal' => Carbon::now()->subDays(2),
                'tanggal_keluar' => Carbon::now()->subDays(2),
                'nama_pengambil' => 'Dewi Kusuma',
                'tipe_pengambil' => 'ruangan_saja',
                'keterangan' => 'Tamu instansi pemerintah',
            ]);
            $transaksiCount++;
        }

        if ($barang4 && $ruangan) {
            // Transaction 6: Barang Masuk & Keluar (masuk_keluar)
            Transaksi::create([
                'barang_id' => $barang4->id,
                'user_id' => $admin->id,
                'ruangan_id' => $ruangan->id,
                'tipe' => 'masuk_keluar',
                'jumlah_masuk' => 10,
                'jumlah_keluar' => 3,
                'jumlah' => 7,
                'stok_sebelum' => $barang4->stok,
                'stok_setelah_masuk' => $barang4->stok + 10,
                'sisa_stok' => $barang4->stok + 7,
                'tanggal' => Carbon::now()->subDays(1),
                'tanggal_keluar' => Carbon::now()->subDays(1),
                'nama_pengambil' => 'Siti Aminah',
                'tipe_pengambil' => 'nama_ruangan',
                'keterangan' => 'Pengadaan dan pemakaian hari yang sama',
            ]);
            $transaksiCount++;
        }

        $this->command->info("{$transaksiCount} sample transactions created successfully!");
    }
}
