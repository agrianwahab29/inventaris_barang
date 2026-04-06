<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Ruangan;
use Carbon\Carbon;

class ImportCSVTransaksi extends Command
{
    protected $signature = 'import:csv-transaksi';
    protected $description = 'Import data transaksi dari CSV PESANAN ATK 2026';

    // Mapping ruang dari CSV ke ruang di database
    private $ruangMapping = [
        'RUANG PERPUSTAKAAN' => 'Ruang Sekretaris',
        'RUANG LAYANAN' => 'Ruang Laika',
        'RUANG PIMPINAN' => 'Ruang Kepala Kantor',
        'RUANG PERLENGKAPAN' => 'Ruang Perlengkapan',
        'RUANG KEUANGAN' => 'Ruang Keuangan',
    ];

    // Mapping nama barang dari CSV ke database (untuk menormalisasi)
    // Satuan di-map ke enum yang valid: Buah, Rim, Dos, Lusin, Pak, Box, Galon, Botol, Bungkus, Kilo, Pasang, Warna, Jenis, Kotak, Gantung, Lembar
    private $barangMapping = [
        'Sapu Lidi' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Sendok Sampah' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Gelas Kopi Kertas' => ['kategori' => 'Konsumsi', 'satuan' => 'Bungkus'],
        'Kain Lap' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Tutup Gelas Kertas' => ['kategori' => 'Konsumsi', 'satuan' => 'Bungkus'],
        'Keset Kaki' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Sapu Laba-Laba Panjang' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Parang' => ['kategori' => 'Perlengkapan', 'satuan' => 'Buah'],
        'Aqua Botol 330 ML' => ['kategori' => 'Konsumsi', 'satuan' => 'Dos'],
        'Teh Kotak' => ['kategori' => 'Konsumsi', 'satuan' => 'Dos'],
        'Buavita' => ['kategori' => 'Konsumsi', 'satuan' => 'Buah'],
        'Minuman Botol' => ['kategori' => 'Konsumsi', 'satuan' => 'Buah'],
        'Minuman Kaleng' => ['kategori' => 'Konsumsi', 'satuan' => 'Buah'],
        'Kue Kering' => ['kategori' => 'Konsumsi', 'satuan' => 'Bungkus'],
        'Pengharum Ruangan Gantung' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Minuman Kotak' => ['kategori' => 'Konsumsi', 'satuan' => 'Buah'],
        'Mente Sangrai' => ['kategori' => 'Konsumsi', 'satuan' => 'Bungkus'],
        'Sarung Tangan Plastik' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Sarung Tangan Kain' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Toples Kaca Untuk Kue Ruang Pimpinan' => ['kategori' => 'Perlengkapan', 'satuan' => 'Pak'], // Set -> Pak
        'Toples Kaca Tempat Kopi, Gula, dan Teh' => ['kategori' => 'Perlengkapan', 'satuan' => 'Pak'], // Set -> Pak
        'Aqua Botol Pristine 400 ML' => ['kategori' => 'Konsumsi', 'satuan' => 'Dos'],
        'Aqua Gelas' => ['kategori' => 'Konsumsi', 'satuan' => 'Dos'],
        'Permen' => ['kategori' => 'Konsumsi', 'satuan' => 'Bungkus'],
        'Kacang Goreng' => ['kategori' => 'Konsumsi', 'satuan' => 'Bungkus'],
        'Pengharum Ruangan Metic' => ['kategori' => 'Kebersihan', 'satuan' => 'Botol', 'note' => 'mapped from Buah'],
        'Mata Cutter' => ['kategori' => 'ATK', 'satuan' => 'Buah'],
        'Kertas Foto F4' => ['kategori' => 'ATK', 'satuan' => 'Pak'],
        'Baterai AA' => ['kategori' => 'Perlengkapan', 'satuan' => 'Buah'],
        'Buku Catatan Folio' => ['kategori' => 'ATK', 'satuan' => 'Buah'],
        'Buku Catatan Kwarto' => ['kategori' => 'ATK', 'satuan' => 'Buah'],
        'Map Biasa' => ['kategori' => 'ATK', 'satuan' => 'Pak'],
        'Gunting' => ['kategori' => 'ATK', 'satuan' => 'Buah'],
        'Pulpen' => ['kategori' => 'ATK', 'satuan' => 'Pak'],
        'Tissue Kotak' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Sendal Jepit Untuk Masjid' => ['kategori' => 'Perlengkapan', 'satuan' => 'Pasang'],
        'Colokan' => ['kategori' => 'Perlengkapan', 'satuan' => 'Buah'],
        'Klem Kabel' => ['kategori' => 'Perlengkapan', 'satuan' => 'Bungkus'],
        'Botol Sprayer' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'HDMI Extender' => ['kategori' => 'Perlengkapan', 'satuan' => 'Buah'], // Unit -> Buah
        'Tinta Printer Epson 003 Hitam' => ['kategori' => 'ATK', 'satuan' => 'Botol'],
        'Tinta Printer Epson 003 Biru' => ['kategori' => 'ATK', 'satuan' => 'Botol'],
        'Tinta Printer Epson 003 Merah' => ['kategori' => 'ATK', 'satuan' => 'Botol'],
        'Tinta Printer Epson 003 Kuning' => ['kategori' => 'ATK', 'satuan' => 'Botol'],
        'Catridge 810' => ['kategori' => 'ATK', 'satuan' => 'Buah'],
        'Catridge 811' => ['kategori' => 'ATK', 'satuan' => 'Buah'],
        'Kunci Kontak' => ['kategori' => 'Perlengkapan', 'satuan' => 'Pak'], // Set -> Pak
        'Tanah Humus' => ['kategori' => 'Kebersihan', 'satuan' => 'Box'], // Karung -> Box
        'Bunga Melati Mini' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Bunga Putri Salju' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Bunga Legetan' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
        'Bunga Pangkas Merah' => ['kategori' => 'Kebersihan', 'satuan' => 'Buah'],
    ];

    public function handle()
    {
        $csvPath = base_path('PESANAN ATK 2026 (1).csv');
        
        if (!file_exists($csvPath)) {
            $this->error('File CSV tidak ditemukan: ' . $csvPath);
            return 1;
        }

        // Konfirmasi sebelum menghapus data
        $count = Transaksi::count();
        if ($count > 0) {
            if (!$this->confirm("Akan menghapus {$count} data transaksi yang ada. Lanjutkan?")) {
                return 0;
            }
            
            $this->info('Menghapus data transaksi yang ada...');
            Transaksi::truncate();
            $this->info('Data transaksi berhasil dihapus.\n');
        }

        // Parse CSV
        $this->info('Parsing file CSV...');
        $forms = $this->parseCSV($csvPath);
        
        $this->info('Menemukan ' . count($forms) . ' form permintaan.\n');

        // Proses setiap form
        $totalItems = 0;
        foreach ($forms as $index => $form) {
            $this->info('Form #' . ($index + 1) . ' - Tanggal: ' . $form['tanggal']->format('d F Y'));
            $this->info('  Lokasi: ' . ($form['lokasi'] ?? 'Tidak disebutkan'));
            $this->info('  Jumlah item: ' . count($form['items']));
            
            $ruanganId = $this->getRuanganId($form['lokasi']);
            
            foreach ($form['items'] as $item) {
                $barangId = $this->getOrCreateBarang($item['nama_barang']);
                
                Transaksi::create([
                    'barang_id' => $barangId,
                    'tipe' => 'keluar',
                    'jumlah_keluar' => $item['jumlah'],
                    'jumlah' => $item['jumlah'],
                    'stok_sebelum' => 0,
                    'stok_setelah_masuk' => 0,
                    'sisa_stok' => 0,
                    'tanggal' => $form['tanggal'],
                    'ruangan_id' => $ruanganId,
                    'user_id' => 1,
                    'nama_pengambil' => 'Bagian Perlengkapan',
                    'tipe_pengambil' => 'ruangan_saja',
                    'tanggal_keluar' => $form['tanggal'],
                    'keterangan' => 'Pengambilan ATK periode Januari 2026',
                ]);
                
                $totalItems++;
            }
            $this->newLine();
        }

        $this->info('=====================================');
        $this->info('Import selesai!');
        $this->info('Total form: ' . count($forms));
        $this->info('Total transaksi: ' . $totalItems);
        $this->info('=====================================');

        return 0;
    }

    private function parseCSV($path)
    {
        $content = file_get_contents($path);
        $lines = explode("\n", $content);
        $forms = [];
        $currentForm = null;
        $inForm = false;
        $currentDate = null;
        $currentLokasi = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Cek tanggal dengan format "Kendari, 8 Januari 2026"
            if (preg_match('/Kendari,\s*(\d+)\s+([\w\s]+)\s+2026/', $line, $matches)) {
                $tanggalStr = $matches[1] . ' ' . $matches[2] . ' 2026';
                $currentDate = $this->parseTanggalIndonesia($tanggalStr);
            }

            // Cek lokasi dari header form
            if (preg_match('/UNTUK PERBAIKAN.+RUANG ([\w\s]+)/i', $line, $matches)) {
                $currentLokasi = 'RUANG ' . trim($matches[1]);
            }
            if (preg_match('/UNTUK OPERASIONAL.+RUANG ([\w\s]+)/i', $line, $matches)) {
                $currentLokasi = 'RUANG ' . trim($matches[1]);
            }

            // Awal form data (setelah header DAFTAR PERMINTAAN)
            if (strpos($line, 'NO;NAMA BARANG;') !== false) {
                $inForm = true;
                $currentForm = [
                    'tanggal' => $currentDate,
                    'lokasi' => $currentLokasi,
                    'items' => []
                ];
                continue;
            }

            // Akhir form (ketemu baris kosong atau baris tanda tangan)
            if ($inForm && (strpos($line, 'Mengetahui,') !== false || strpos($line, ';;;;;;;;;') !== false)) {
                if ($currentForm && count($currentForm['items']) > 0) {
                    // Normalisasi tanggal jika belum ada
                    if (!$currentForm['tanggal']) {
                        $currentForm['tanggal'] = now();
                    }
                    $forms[] = $currentForm;
                }
                $inForm = false;
                $currentLokasi = null;
                continue;
            }

            // Parse data barang: ;1;Nama Barang;;;;4 Buah;;;
            if ($inForm && preg_match('/^;(\d+);(.+?);{4}([^;]+);{3}$/', $line, $matches)) {
                $namaBarang = trim($matches[2]);
                $jumlahSatuan = trim($matches[3]);
                
                // Parse jumlah dan satuan
                $parts = explode(' ', $jumlahSatuan);
                $jumlah = (int) $parts[0];
                $satuan = isset($parts[1]) ? $parts[1] : 'Buah';

                $currentForm['items'][] = [
                    'nama_barang' => $namaBarang,
                    'jumlah' => $jumlah,
                    'satuan' => $satuan
                ];
            }
        }

        return $forms;
    }

    private function parseTanggalIndonesia($tanggalStr)
    {
        $bulan = [
            'Januari' => '01',
            'Februari' => '02',
            'Maret' => '03',
            'April' => '04',
            'Mei' => '05',
            'Juni' => '06',
            'Juli' => '07',
            'Agustus' => '08',
            'September' => '09',
            'Oktober' => '10',
            'November' => '11',
            'Desember' => '12'
        ];

        foreach ($bulan as $indo => $angka) {
            $tanggalStr = str_replace($indo, $angka, $tanggalStr);
        }

        return Carbon::createFromFormat('d m Y', $tanggalStr);
    }

    private function getRuanganId($lokasi)
    {
        if (!$lokasi) {
            // Default ke Ruang Perlengkapan jika tidak ada lokasi
            $ruang = Ruangan::where('nama_ruangan', 'Ruang Perlengkapan')->first();
            return $ruang ? $ruang->id : 2;
        }

        $namaRuang = $this->ruangMapping[$lokasi] ?? 'Ruang Perlengkapan';
        $ruang = Ruangan::where('nama_ruangan', $namaRuang)->first();
        
        return $ruang ? $ruang->id : 2;
    }

    private function getOrCreateBarang($namaBarang)
    {
        // Cari barang yang sudah ada (case insensitive)
        $barang = Barang::whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($namaBarang) . '%'])->first();
        
        if ($barang) {
            return $barang->id;
        }

        // Buat barang baru
        $data = $this->barangMapping[$namaBarang] ?? ['kategori' => 'Lainnya', 'satuan' => 'Buah'];
        
        $barang = Barang::create([
            'nama_barang' => $namaBarang,
            'kategori' => $data['kategori'],
            'satuan' => $data['satuan'],
            'stok' => 0,
            'stok_minimum' => 5
        ]);

        $this->info('    + Barang baru: ' . $namaBarang);
        
        return $barang->id;
    }
}