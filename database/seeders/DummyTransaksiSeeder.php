<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyTransaksiSeeder extends Seeder
{
    // Nama-nama dari file (tanpa gelar)
    private $namaOrang = [
        'Jamaluddin M.',
        'Sukmawati',
        'Nurmiyanti',
        'Andi Herlina Nur',
        'Dwidayanto',
        'Indriati',
        'Hairil Muhammad Indra Jaya',
        'Siti Hajarah',
        'Amran Pamme',
        'Asri',
        'Zakiyah Mustafa Husba',
        'Riskawaty',
        'Mulawati',
        'Dwi Pratiwi Husba',
        'Mohammad Hanafi',
        'Noke Nofrianto',
        'Fitkha Maylana Rahayu',
        'Cahyo Waskito Pur Antomo',
        'I Made Ngurah Rai Febrianto',
        'Eka Rohmaniah Apriani',
        'Maulita Dewi Iskandar',
        'Untung Kustoro',
        'Mifta Huzaena',
        'Febriyani Rahayu',
        'Fadhilah Nurul Inayah Nasir',
        'Sainudin Husni',
        'Rahim Jamal',
        'Resgi Silvania',
        'Uzlah Marifat Ariqo',
        'Muhammad Jihad',
    ];

    // Tanggal libur nasional Jan-Mar 2026
    private $hariLibur = [
        '2026-01-01', // Tahun Baru Masehi
        '2026-01-16', // Isra Mikraj
        '2026-02-17', // Tahun Baru Imlek
        '2026-03-19', // Nyepi
        '2026-03-21', // Idul Fitri
        '2026-03-22', // Idul Fitri
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Clear existing transactions first
        DB::table('transaksis')->truncate();
        
        // Reset all barang stock to 0
        DB::table('barangs')->update(['stok' => 0]);

        // Get user (admin)
        $user = User::where('role', 'admin')->first();
        if (!$user) {
            $user = User::first();
        }

        // Get ruangans
        $ruangans = Ruangan::all()->pluck('id')->toArray();

        // Start date: January 2, 2026 (after New Year)
        $startDate = Carbon::create(2026, 1, 2);
        // End date: March 5, 2026 (today)
        $endDate = Carbon::create(2026, 3, 5);

        // Get all barangs
        $barangIds = Barang::pluck('id')->toArray();
        
        // Initialize stock for all items - START FROM 0
        $stockHistory = [];
        foreach ($barangIds as $id) {
            $stockHistory[$id] = 0;
        }

        $currentDate = clone $startDate;
        $transaksiData = [];
        $transactionCount = 0;
        $targetTransactions = 350; // Target more than 300 transactions

        while ($currentDate <= $endDate && $transactionCount < $targetTransactions) {
            // Skip weekends and holidays
            if ($currentDate->isWeekend() || in_array($currentDate->format('Y-m-d'), $this->hariLibur)) {
                $currentDate->addDay();
                continue;
            }

            // More transactions per day (5-10) to reach 300+
            $numTransactions = rand(5, 10);
            
            // Track used barangs for this day
            $usedBarangsToday = [];

            for ($i = 0; $i < $numTransactions && $transactionCount < $targetTransactions; $i++) {
                // Get random barang
                $barangId = $barangIds[array_rand($barangIds)];
                $barang = Barang::find($barangId);

                if (!$barang) continue;

                $currentStock = isset($stockHistory[$barangId]) ? $stockHistory[$barangId] : 0;
                $stokSebelum = $currentStock;

                // Determine transaction type
                if ($currentStock <= 2) {
                    // Stock is low or empty - NEED TO RESTOCK FIRST
                    // Then maybe someone takes some
                    $jumlahMasuk = rand(10, 20);
                    
                    // 60% chance someone takes after restocking
                    if (rand(1, 100) <= 60) {
                        $jumlahKeluar = rand(1, min(8, $jumlahMasuk));
                        $tipe = 'masuk_keluar';
                        $ruanganId = $ruangans[array_rand($ruangans)];
                        $namaPengambil = $this->namaOrang[array_rand($this->namaOrang)];
                        $tipePengambil = 'nama_ruangan';
                        $tanggalKeluar = $currentDate->format('Y-m-d');
                    } else {
                        $jumlahKeluar = 0;
                        $tipe = 'masuk';
                        $ruanganId = null;
                        $namaPengambil = null;
                        $tipePengambil = null;
                        $tanggalKeluar = null;
                    }
                    
                    $stokSetelahMasuk = $stokSebelum + $jumlahMasuk;
                    $sisaStok = $stokSetelahMasuk - $jumlahKeluar;
                    
                } else {
                    // Stock is sufficient - determine transaction
                    $transactionType = rand(1, 10);
                    
                    if ($transactionType <= 2) {
                        // 20% - masuk only (restocking)
                        $jumlahMasuk = rand(5, 15);
                        $jumlahKeluar = 0;
                        $stokSetelahMasuk = $stokSebelum + $jumlahMasuk;
                        $sisaStok = $stokSetelahMasuk;
                        $tipe = 'masuk';
                        $ruanganId = null;
                        $namaPengambil = null;
                        $tipePengambil = null;
                        $tanggalKeluar = null;
                        
                    } elseif ($transactionType <= 7) {
                        // 50% - keluar only
                        $jumlahMasuk = 0;
                        $jumlahKeluar = rand(1, min(6, $currentStock));
                        $stokSetelahMasuk = $stokSebelum;
                        $sisaStok = $stokSebelum - $jumlahKeluar;
                        $tipe = 'keluar';
                        $ruanganId = $ruangans[array_rand($ruangans)];
                        // ALWAYS have both name and room
                        $namaPengambil = $this->namaOrang[array_rand($this->namaOrang)];
                        $tipePengambil = 'nama_ruangan';
                        $tanggalKeluar = $currentDate->format('Y-m-d');
                        
                    } else {
                        // 30% - masuk dan keluar (restock and usage)
                        $jumlahMasuk = rand(8, 18);
                        $jumlahKeluar = rand(1, min(7, $jumlahMasuk));
                        $stokSetelahMasuk = $stokSebelum + $jumlahMasuk;
                        $sisaStok = $stokSetelahMasuk - $jumlahKeluar;
                        $tipe = 'masuk_keluar';
                        $ruanganId = $ruangans[array_rand($ruangans)];
                        // ALWAYS have both name and room
                        $namaPengambil = $this->namaOrang[array_rand($this->namaOrang)];
                        $tipePengambil = 'nama_ruangan';
                        $tanggalKeluar = $currentDate->format('Y-m-d');
                    }
                }

                // Update stock history
                $stockHistory[$barangId] = $sisaStok;

                // Calculate jumlah field based on tipe
                if ($tipe == 'masuk') {
                    $jumlah = $jumlahMasuk;
                } elseif ($tipe == 'keluar') {
                    $jumlah = $jumlahKeluar;
                } else {
                    // masuk_keluar: total masuk after adding existing stock
                    $jumlah = $stokSetelahMasuk;
                }

                // Add transaction
                $transaksiData[] = [
                    'barang_id' => $barangId,
                    'tipe' => $tipe,
                    'jumlah_masuk' => $jumlahMasuk,
                    'jumlah_keluar' => $jumlahKeluar,
                    'jumlah' => $jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_setelah_masuk' => $stokSetelahMasuk,
                    'sisa_stok' => $sisaStok,
                    'tanggal' => $currentDate->format('Y-m-d'),
                    'ruangan_id' => $ruanganId,
                    'user_id' => $user->id,
                    'nama_pengambil' => $namaPengambil,
                    'tipe_pengambil' => $tipePengambil,
                    'tanggal_keluar' => $tanggalKeluar,
                    'keterangan' => $this->generateKeterangan($tipe),
                    'created_at' => $currentDate->copy()->setTime(rand(8, 16), rand(0, 59))->format('Y-m-d H:i:s'),
                    'updated_at' => $currentDate->copy()->setTime(rand(8, 16), rand(0, 59))->format('Y-m-d H:i:s'),
                ];
                
                $transactionCount++;
            }

            $currentDate->addDay();
        }

        // Insert transactions in batches to avoid memory issues
        $chunks = array_chunk($transaksiData, 100);
        foreach ($chunks as $chunk) {
            DB::table('transaksis')->insert($chunk);
        }

        // Update barang stocks
        foreach ($stockHistory as $barangId => $finalStock) {
            Barang::where('id', $barangId)->update(['stok' => max(0, $finalStock)]);
        }

        $this->command->info('Dummy transaksi berhasil dibuat: ' . count($transaksiData) . ' transaksi');
        $this->command->info('Stok barang telah direset ke 0 dan dihitung ulang berdasarkan transaksi');
    }

    private function generateKeterangan($tipe)
    {
        $keterangan = [
            'masuk' => 'Pengadaan barang dari supplier',
            'keluar' => 'Permintaan barang dari unit kerja',
            'masuk_keluar' => 'Pengadaan dan penggunaan barang',
        ];
        
        return $keterangan[$tipe] ?? null;
    }
}
