<?php

namespace Database\Seeders;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ImportCsvDataSeeder extends Seeder
{
    /**
     * Kategori mapping berdasarkan nama barang
     */
    private function getKategori(string $nama): string
    {
        $konsumsi = ['buavita', 'minuman', 'aqua', 'teh kotak', 'permen', 'kacang', 'mente', 'kue kering'];
        $atk = ['kertas', 'pulpen', 'map', 'buku catatan', 'catridge', 'tinta printer', 'mata cutter', 'gunting'];
        $kebersihan = ['sapu', 'pengharum', 'sarung tangan', 'keset', 'botol sprayer'];
        $perlengkapan = ['hdmi', 'baterai', 'toples', 'colokan', 'klem kabel', 'parang', 'sendal'];

        $lower = strtolower($nama);
        foreach ($konsumsi as $k) { if (str_contains($lower, $k)) return 'Konsumsi'; }
        foreach ($atk as $k) { if (str_contains($lower, $k)) return 'ATK'; }
        foreach ($kebersihan as $k) { if (str_contains($lower, $k)) return 'Kebersihan'; }
        foreach ($perlengkapan as $k) { if (str_contains($lower, $k)) return 'Perlengkapan'; }

        // Khusus
        if (str_contains($lower, 'tanah humus')) return 'Perlengkapan';
        if (str_contains($lower, 'tissue')) return 'Kebersihan';

        return 'Lainnya';
    }

    public function run()
    {
        // 1. Hapus semua transaksi dan barang
        Transaksi::query()->delete();
        Barang::query()->delete();

        $this->command->info('Data lama dihapus.');

        // 2. Baca CSV
        $csvFile = base_path('Data_Transaksi_2026-03-12_03-27-45.csv');
        if (!file_exists($csvFile)) {
            throw new \Exception("CSV tidak ditemukan: {$csvFile}");
        }

        $handle = fopen($csvFile, 'r');
        fgetcsv($handle, 2000, ';'); // skip header

        $rows = [];
        while (($data = fgetcsv($handle, 2000, ';')) !== false) {
            if (count($data) < 10) continue;
            $rows[] = $data;
        }
        fclose($handle);

        $this->command->info('CSV dibaca: ' . count($rows) . ' baris.');

        // 3. Extract barang unik dan buat
        $barangMap = []; // nama_barang => Barang model
        foreach ($rows as $row) {
            $nama = trim($row[2]);
            $satuan = trim($row[6]);
            if (!isset($barangMap[$nama])) {
                $barangMap[$nama] = Barang::create([
                    'nama_barang' => $nama,
                    'kategori' => $this->getKategori($nama),
                    'satuan' => $satuan,
                    'stok' => 0,
                    'stok_minimum' => 5,
                ]);
            }
        }
        $this->command->info('Barang dibuat: ' . count($barangMap));

        // 4. Pastikan ruangan "Ruang Sekretaris" ada
        $ruanganSekretaris = Ruangan::where('nama_ruangan', 'Ruang Sekretaris')->first();
        if (!$ruanganSekretaris) {
            $ruanganSekretaris = Ruangan::create([
                'nama_ruangan' => 'Ruang Sekretaris',
                'keterangan' => 'Sekretaris',
            ]);
        }

        // 5. Ambil user Administrator
        $admin = User::where('name', 'Administrator')->first();
        if (!$admin) {
            $admin = User::first(); // fallback
        }

        // 6. Import transaksi
        $count = 0;
        foreach ($rows as $row) {
            $nama = trim($row[2]);
            $barang = $barangMap[$nama];
            $jumlahMasuk = (int)$row[3];
            $jumlahKeluar = (int)$row[4];
            $sisaStok = (int)$row[5];
            $tanggalKeluar = trim($row[7]);
            $pengambil = trim($row[8]);

            // Tipe transaksi
            if ($jumlahMasuk > 0 && $jumlahKeluar > 0) {
                $tipe = 'masuk_keluar';
            } elseif ($jumlahMasuk > 0) {
                $tipe = 'masuk';
            } else {
                $tipe = 'keluar';
            }

            // Parse tanggal
            $tanggal = null;
            try {
                $tanggal = Carbon::createFromFormat('d/m/Y', trim($row[1]))->format('Y-m-d');
            } catch (\Exception $e) {}

            $tglKeluar = null;
            if ($tanggalKeluar && $tanggalKeluar !== '-') {
                try {
                    $tglKeluar = Carbon::createFromFormat('d/m/Y', $tanggalKeluar)->format('Y-m-d');
                } catch (\Exception $e) {}
            }

            // Ruangan & pengambil
            $ruanganId = null;
            $namaPengambil = null;
            $tipePengambil = null;
            if ($pengambil && $pengambil !== '-') {
                // Format: "Hilda - Ruang Sekretaris"
                if (str_contains($pengambil, ' - ')) {
                    $parts = explode(' - ', $pengambil, 2);
                    $namaPengambil = trim($parts[0]);
                    $namaRuang = trim($parts[1]);
                    $ruangan = Ruangan::where('nama_ruangan', $namaRuang)->first();
                    if ($ruangan) {
                        $ruanganId = $ruangan->id;
                    }
                    $tipePengambil = 'nama_ruangan';
                } else {
                    $namaPengambil = $pengambil;
                    $tipePengambil = 'nama_ruangan';
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
                'ruangan_id' => $ruanganId,
                'user_id' => $admin ? $admin->id : 1,
                'sisa_stok' => $sisaStok,
                'nama_pengambil' => $namaPengambil,
                'tipe_pengambil' => $tipePengambil,
                'tanggal_keluar' => $tglKeluar,
                'keterangan' => null,
            ]);
            $count++;
        }
        $this->command->info("Transaksi diimport: {$count}");

        // 7. Update stok barang berdasarkan transaksi terakhir (sisa_stok)
        foreach ($barangMap as $nama => $barang) {
            // Ambil transaksi terakhir berdasarkan tanggal untuk barang ini
            $lastTransaksi = Transaksi::where('barang_id', $barang->id)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastTransaksi) {
                $barang->update(['stok' => $lastTransaksi->sisa_stok]);
            }
        }
        $this->command->info('Stok barang diupdate berdasarkan transaksi terakhir.');
        $this->command->info('Selesai!');
    }
}
