<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportDummyDataSeeder extends Seeder
{
    // Nama-nama pengambil dari file
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

    // Data dari CSV yang sudah diimport
    private $csvData = [
        // Format: ['tanggal', 'nama_barang', 'jumlah_masuk', 'jumlah_keluar', 'sisa_stok', 'satuan', 'tanggal_keluar', 'pengambil']
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Step 1: Clear all existing data
        $this->command->info('Menghapus semua data transaksi lama...');
        DB::table('transaksis')->truncate();
        
        $this->command->info('Menghapus semua data barang lama...');
        DB::table('barangs')->delete();
        
        // Step 2: Read CSV file
        $this->command->info('Membaca file CSV...');
        $csvPath = database_path('../dummy/Data_Dummy_Transaksi_Q1_2026_v2.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: $csvPath");
            return;
        }

        $csvContent = file_get_contents($csvPath);
        // Remove BOM if exists
        $csvContent = str_replace("\xEF\xBB\xBF", '', $csvContent);
        $lines = explode("\n", $csvContent);
        array_shift($lines); // Remove header

        // Get user and ruangans
        $user = User::where('role', 'admin')->first() ?? User::first();
        $ruangans = Ruangan::all()->keyBy('nama_ruangan')->toArray();

        // Track barang items
        $barangCache = [];
        $transaksiData = [];
        $rowNum = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $rowNum++;

            // Parse CSV line (semicolon separator)
            $fields = str_getcsv($line, ';');
            if (count($fields) < 8) continue;

            $tanggal = $this->parseDate($fields[0]);
            $namaBarang = trim($fields[1], '"');
            $jumlahMasuk = $fields[2] === '-' ? 0 : (int)$fields[2];
            $jumlahKeluar = $fields[3] === '-' ? 0 : (int)$fields[3];
            $sisaStok = (int)$fields[4];
            $satuan = trim($fields[5], '"');
            $tanggalKeluar = $fields[6] === '-' ? null : $this->parseDate($fields[6]);
            $pengambil = trim($fields[7], '"');

            // Create or get barang
            if (!isset($barangCache[$namaBarang])) {
                $barang = Barang::where('nama_barang', $namaBarang)->first();
                if (!$barang) {
                    $barang = Barang::create([
                        'nama_barang' => $namaBarang,
                        'kategori' => $this->guessKategori($namaBarang),
                        'satuan' => $satuan,
                        'stok' => 0,
                        'stok_minimum' => 5,
                        'catatan' => null,
                    ]);
                }
                $barangCache[$namaBarang] = $barang->id;
            }
            $barangId = $barangCache[$namaBarang];

            // Parse pengambil
            $namaPengambil = null;
            $ruanganId = null;
            $tipePengambil = null;

            if ($jumlahKeluar > 0 && $pengambil && $pengambil !== '-') {
                // Parse "Nama / Ruangan" format
                if (strpos($pengambil, '/') !== false) {
                    $parts = explode('/', $pengambil);
                    $namaPengambil = trim($parts[0]);
                    $namaRuangan = trim($parts[1]);
                    
                    // Find ruangan
                    foreach ($ruangans as $ruangan) {
                        if (strpos($ruangan['nama_ruangan'], $namaRuangan) !== false) {
                            $ruanganId = $ruangan['id'];
                            break;
                        }
                    }
                    $tipePengambil = 'nama_ruangan';
                } else {
                    $namaPengambil = $pengambil;
                    $tipePengambil = 'nama_ruangan';
                    // Try to match ruangan
                    foreach ($ruangans as $ruangan) {
                        if (strpos($pengambil, $ruangan['nama_ruangan']) !== false) {
                            $ruanganId = $ruangan['id'];
                            break;
                        }
                    }
                }
            }

            // Determine tipe
            if ($jumlahMasuk > 0 && $jumlahKeluar > 0) {
                $tipe = 'masuk_keluar';
            } elseif ($jumlahMasuk > 0) {
                $tipe = 'masuk';
            } else {
                $tipe = 'keluar';
            }

            // Get current stok before this transaction
            $stokSebelum = 0;
            
            // Find previous transaction for this barang
            foreach (array_reverse($transaksiData) as $prevTrans) {
                if ($prevTrans['barang_id'] == $barangId) {
                    $stokSebelum = $prevTrans['sisa_stok'];
                    break;
                }
            }

            // Calculate stok setelah masuk
            $stokSetelahMasuk = $stokSebelum + $jumlahMasuk;

            // Determine jumlah field
            if ($tipe == 'masuk') {
                $jumlah = $jumlahMasuk;
            } elseif ($tipe == 'keluar') {
                $jumlah = $jumlahKeluar;
            } else {
                $jumlah = $stokSetelahMasuk;
            }

            // Create transaction record
            $transaksiData[] = [
                'barang_id' => $barangId,
                'tipe' => $tipe,
                'jumlah_masuk' => $jumlahMasuk,
                'jumlah_keluar' => $jumlahKeluar,
                'jumlah' => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_setelah_masuk' => $stokSetelahMasuk,
                'sisa_stok' => $sisaStok,
                'tanggal' => $tanggal,
                'ruangan_id' => $ruanganId,
                'user_id' => $user->id,
                'nama_pengambil' => $namaPengambil,
                'tipe_pengambil' => $tipePengambil,
                'tanggal_keluar' => $tanggalKeluar,
                'keterangan' => null,
                'created_at' => Carbon::parse($tanggal)->setTime(rand(8, 16), rand(0, 59)),
                'updated_at' => Carbon::parse($tanggal)->setTime(rand(8, 16), rand(0, 59)),
            ];

            if ($rowNum % 50 == 0) {
                $this->command->info("Diproses: $rowNum baris...");
            }
        }

        // Insert transactions in batches
        $this->command->info('Menyimpan transaksi ke database...');
        $chunks = array_chunk($transaksiData, 100);
        foreach ($chunks as $chunk) {
            DB::table('transaksis')->insert($chunk);
        }

        // Update barang stock from last transaction
        $this->command->info('Mengupdate stok barang...');
        $barangStok = [];
        foreach (array_reverse($transaksiData) as $trans) {
            if (!isset($barangStok[$trans['barang_id']])) {
                $barangStok[$trans['barang_id']] = $trans['sisa_stok'];
            }
        }
        
        foreach ($barangStok as $barangId => $stok) {
            Barang::where('id', $barangId)->update(['stok' => $stok]);
        }

        $this->command->info('=== IMPORT SELESAI ===');
        $this->command->info('Total Transaksi: ' . count($transaksiData));
        $this->command->info('Total Barang: ' . count($barangCache));
    }

    private function parseDate($dateStr)
    {
        $parts = explode('/', $dateStr);
        if (count($parts) == 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $dateStr;
    }

    private function guessKategori($namaBarang)
    {
        $atkKeywords = ['pulpen', 'pensil', 'kertas', 'buku', 'stapler', 'hekter', 'map', 'ordner', 'amplop', 'stabillo', 'spidol', 'tinta', 'toner', 'penghapus', 'tip-ex', 'lem', 'guna', 'gunting', 'batrai', 'kalkulator', 'penjepit', 'staples', 'map'];
        $kebersihanKeywords = ['sapu', 'pel', 'kain', 'keset', 'kanebo', 'spon', 'pembersih', 'pengharum', 'sikat', 'wiper', 'embutuh', 'tempat sampah'];
        $konsumsiKeywords = ['aqua', 'air', 'minuman', 'teh', 'kopeng', 'gula', 'kopi', 'gelas', 'piring', 'sendok', 'garpu', 'snack', 'kacang', 'buavita'];
        $perlengkapanKeywords = ['termos', 'bingkai', 'gantungan', 'wadah', 'tempat', 'lampu', 'stop kontak'];

        $namaLower = strtolower($namaBarang);

        foreach ($atkKeywords as $keyword) {
            if (strpos($namaLower, $keyword) !== false) return 'ATK';
        }
        foreach ($kebersihanKeywords as $keyword) {
            if (strpos($namaLower, $keyword) !== false) return 'Kebersihan';
        }
        foreach ($konsumsiKeywords as $keyword) {
            if (strpos($namaLower, $keyword) !== false) return 'Konsumsi';
        }
        foreach ($perlengkapanKeywords as $keyword) {
            if (strpos($namaLower, $keyword) !== false) return 'Perlengkapan';
        }

        return 'Lainnya';
    }
}
