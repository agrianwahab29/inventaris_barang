<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransaksiCsvSeeder extends Seeder
{
    public function run()
    {
        // Delete all existing transactions
        Transaksi::truncate();
        
        // Try multiple possible paths for shared hosting
        $possiblePaths = [
            storage_path('app/Data_Transaksi_2026-03-12_03-27-45.csv'),
            base_path('storage/app/Data_Transaksi_2026-03-12_03-27-45.csv'),
            realpath(__DIR__ . '/../../storage/app/Data_Transaksi_2026-03-12_03-27-45.csv'),
        ];
        
        $csvFile = null;
        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path)) {
                $csvFile = $path;
                break;
            }
        }
        
        if (!$csvFile) {
            throw new \Exception('CSV file not found. Please upload to storage/app folder');
        }
        
        $handle = fopen($csvFile, 'r');
        
        if (!$handle) {
            throw new \Exception('Cannot open CSV file. Check permissions');
        }
        
        // Skip header row
        fgetcsv($handle, 1000, ';');
        
        $count = 0;
        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            // Map CSV columns to model fields
            // CSV: ID;Tanggal Input;Nama Barang;Jumlah Barang Masuk;Jumlah Barang Keluar;Sisa Stok Barang;Satuan;Tanggal Keluar;Nama atau Bagian/Ruang yang Mengambil;User Input;Paraf
            
            $barangNama = $data[2] ?? null;
            $barang = Barang::where('nama_barang', $barangNama)->first();
            
            if (!$barang) {
                continue;
            }
            
            $jumlahMasuk = (int)($data[3] ?? 0);
            $jumlahKeluar = (int)($data[4] ?? 0);
            $sisaStok = (int)($data[5] ?? 0);
            $satuan = $data[6] ?? 'Unit';
            $tanggalKeluar = $data[7] ?? null;
            $namaPengambil = $data[8] ?? null;
            $userInput = $data[9] ?? 'Administrator';
            
            $user = User::where('name', $userInput)->first();
            
            // Determine tipe based on data
            if ($jumlahMasuk > 0 && $jumlahKeluar > 0) {
                $tipe = 'masuk_keluar';
            } elseif ($jumlahMasuk > 0) {
                $tipe = 'masuk';
            } else {
                $tipe = 'keluar';
            }
            
            // Parse tanggal input
            $tanggalInput = $data[1] ?? null;
            $tanggal = null;
            if ($tanggalInput) {
                try {
                    $tanggal = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalInput)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggal = null;
                }
            }
            
            // Parse tanggal keluar
            $tglKeluarFormatted = null;
            if ($tanggalKeluar && $tanggalKeluar !== '-') {
                try {
                    $tglKeluarFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $tanggalKeluar)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tglKeluarFormatted = null;
                }
            }
            
            Transaksi::create([
                'barang_id' => $barang->id,
                'tipe' => $tipe,
                'jumlah' => $jumlahMasuk > 0 ? $jumlahMasuk : $jumlahKeluar,
                'jumlah_masuk' => $jumlahMasuk,
                'jumlah_keluar' => $jumlahKeluar,
                'stok_sebelum' => 0,
                'stok_setelah_masuk' => $jumlahMasuk > 0 ? $sisaStok : 0,
                'tanggal' => $tanggal,
                'ruangan_id' => null,
                'user_id' => $user ? $user->id : null,
                'sisa_stok' => $sisaStok,
                'nama_pengambil' => $namaPengambil !== '-' ? $namaPengambil : null,
                'tipe_pengambil' => $namaPengambil ? 'nama_ruangan' : null,
                'tanggal_keluar' => $tglKeluarFormatted,
                'keterangan' => null,
            ]);
            
            $count++;
        }
        
        fclose($handle);
        
        $this->command->info("Successfully imported {$count} transactions from CSV");
    }
}
