<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Ruangan;
use Carbon\Carbon;

echo "=== IMPORT DATA TRANSAKSI DARI CSV ===\n\n";

// Mapping ruang dari CSV ke ruang di database
$ruangMapping = [
    'RUANG PERPUSTAKAAN' => 'Ruang Sekretaris',
    'RUANG LAYANAN' => 'Ruang Laika',
    'RUANG PIMPINAN' => 'Ruang Kepala Kantor',
    'RUANG PERLENGKAPAN' => 'Ruang Perlengkapan',
    'RUANG KEUANGAN' => 'Ruang Keuangan',
];

// Mapping barang untuk normalisasi satuan ke enum yang valid
$barangMapping = [
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
    'Pengharum Ruangan Metic' => ['kategori' => 'Kebersihan', 'satuan' => 'Botol'], // mapped dari Buah
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

// Hapus semua transaksi yang ada
$count = Transaksi::count();
echo "Menghapus {$count} data transaksi yang ada...\n";
DB::statement('DELETE FROM transaksis');
echo "Data transaksi berhasil dihapus.\n\n";

// Parse CSV
$csvPath = base_path('PESANAN ATK 2026 (1).csv');
$content = file_get_contents($csvPath);
$lines = explode("\n", $content);

$forms = [];
$currentForm = null;
$inForm = false;
$currentDate = null;
$currentLokasi = null;

function parseTanggalIndonesia($tanggalStr) {
    $bulan = [
        'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
        'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
        'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
    ];
    foreach ($bulan as $indo => $angka) {
        $tanggalStr = str_replace($indo, $angka, $tanggalStr);
    }
    return Carbon::createFromFormat('d m Y', $tanggalStr);
}

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // Cek tanggal dengan format "Kendari, 8 Januari 2026"
    if (preg_match('/Kendari,\s*(\d+)\s+([\w\s]+)\s+2026/', $line, $matches)) {
        $tanggalStr = $matches[1] . ' ' . trim($matches[2]) . ' 2026';
        $tanggalStr = preg_replace('/\s+/', ' ', $tanggalStr);
        $currentDate = parseTanggalIndonesia($tanggalStr);
    }

    // Cek lokasi dari header form
    if (preg_match('/RUANG ([\w\s]+)/',$line, $matches)) {
        $currentLokasi = 'RUANG ' . trim($matches[1]);
    }

    // Awal form data
    if (strpos($line, 'NO;NAMA BARANG;') !== false) {
        $inForm = true;
        $currentForm = [
            'tanggal' => $currentDate,
            'lokasi' => $currentLokasi,
            'items' => []
        ];
        continue;
    }

    // Akhir form
    if ($inForm && strpos($line, 'Mengetahui,') !== false) {
        if ($currentForm && count($currentForm['items']) > 0 && $currentForm['tanggal']) {
            $forms[] = $currentForm;
        }
        $inForm = false;
        $currentLokasi = null;
        continue;
    }

    // Parse data barang
    if ($inForm && preg_match('/^;(\d+);(.+?);{4}([^;]+);{3}$/', $line, $matches)) {
        $namaBarang = trim($matches[2]);
        $jumlahSatuan = trim($matches[3]);
        $parts = explode(' ', $jumlahSatuan);
        $jumlah = (int) $parts[0];
        
        $currentForm['items'][] = [
            'nama_barang' => $namaBarang,
            'jumlah' => $jumlah
        ];
    }
}

echo "Parsing file CSV...\n";
echo "Menemukan " . count($forms) . " form permintaan.\n\n";

// Proses setiap form
$totalItems = 0;
$ruanganDefault = Ruangan::where('nama_ruangan', 'Ruang Perlengkapan')->first();

foreach ($forms as $index => $form) {
    echo "Form #" . ($index + 1) . " - Tanggal: " . $form['tanggal']->format('d F Y') . "\n";
    echo "  Lokasi: " . ($form['lokasi'] ?? 'Tidak disebutkan') . "\n";
    echo "  Jumlah item: " . count($form['items']) . "\n";

    // Mapping ruangan
    $ruanganId = $ruanganDefault ? $ruanganDefault->id : null;
    if ($form['lokasi'] && isset($ruangMapping[$form['lokasi']])) {
        $ruang = Ruangan::where('nama_ruangan', $ruangMapping[$form['lokasi']])->first();
        if ($ruang) $ruanganId = $ruang->id;
    }

    foreach ($form['items'] as $item) {
        // Cari atau buat barang
        $barang = Barang::whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($item['nama_barang']) . '%'])->first();
        
        if (!$barang) {
            // Buat barang baru
            $data = $barangMapping[$item['nama_barang']] ?? ['kategori' => 'Lainnya', 'satuan' => 'Buah'];
            $barang = Barang::create([
                'nama_barang' => $item['nama_barang'],
                'kategori' => $data['kategori'],
                'satuan' => $data['satuan'],
                'stok' => 0,
                'stok_minimum' => 5
            ]);
            echo "    + Barang baru: " . $item['nama_barang'] . "\n";
        }

        // Buat transaksi sebagai BARANG MASUK (revisi)
        Transaksi::create([
            'barang_id' => $barang->id,
            'tipe' => 'masuk',
            'jumlah_masuk' => $item['jumlah'],
            'jumlah_keluar' => 0,
            'jumlah' => $item['jumlah'],
            'stok_sebelum' => 0,
            'stok_setelah_masuk' => $item['jumlah'],
            'sisa_stok' => $item['jumlah'], // Sisa mengikuti jumlah masuk
            'tanggal' => $form['tanggal'],
            'ruangan_id' => null, // Barang masuk tidak ada ruangan tujuan
            'user_id' => 1,
            'nama_pengambil' => null, // Barang masuk tidak ada pengambil
            'tipe_pengambil' => null,
            'tanggal_keluar' => null,
            'keterangan' => 'Penerimaan ATK bulan Januari 2026',
        ]);

        $totalItems++;
    }
    echo "\n";
}

echo "=====================================\n";
echo "Import SELESAI!\n";
echo "Total form: " . count($forms) . "\n";
echo "Total transaksi: " . $totalItems . "\n";
echo "=====================================\n";