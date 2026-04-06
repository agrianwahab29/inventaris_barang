<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportTransaksiCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:transaksi-csv {file} {--truncate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data transaksi dari file CSV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $truncate = $this->option('truncate');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        // Confirm if truncate
        if ($truncate) {
            if (!$this->confirm('Apakah Anda yakin ingin menghapus semua data barang dan transaksi yang ada?')) {
                $this->info('Import dibatalkan.');
                return 0;
            }
            
            $this->info('Menghapus data lama...');
            // SQLite doesn't support FOREIGN_KEY_CHECKS, just delete in order
            Transaksi::query()->delete();
            Barang::query()->delete();
        }

        $this->info('Membaca file CSV...');
        
        $content = file_get_contents($filePath);
        $lines = preg_split('/\r?\n/', $content);
        
        // Get header
        $header = str_getcsv(array_shift($lines), ';');
        $header = array_map('trim', $header);
        
        $this->info('Header: ' . implode(', ', $header));
        
        // Get admin user
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            // Fallback to first user
            $adminUser = User::first();
        }
        
        if (!$adminUser) {
            $this->error('Tidak ada user ditemukan. Buat user terlebih dahulu.');
            return 1;
        }
        
        $this->info("Menggunakan user: {$adminUser->name} (ID: {$adminUser->id})");
        
        // Data structures
        $barangCache = []; // nama_barang => barang object
        $barangStok = []; // nama_barang => current stock tracking
        $ruanganCache = []; // nama_ruangan => ruangan object
        
        $stats = [
            'barang_baru' => 0,
            'transaksi' => 0,
            'baris_dilewati' => 0,
        ];

        // Progress bar
        $this->output->progressStart(count($lines));
        
        DB::beginTransaction();
        try {
            foreach ($lines as $lineNum => $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                
                $data = str_getcsv($line, ';');
                $data = array_map('trim', $data);
                
                // Map CSV columns
                $row = [
                    'id' => $data[0] ?? null,
                    'tanggal_input' => $data[1] ?? null,
                    'nama_barang' => $data[2] ?? null,
                    'jumlah_masuk' => $data[3] ?? 0,
                    'jumlah_keluar' => $data[4] ?? 0,
                    'sisa_stok' => $data[5] ?? 0,
                    'satuan' => $data[6] ?? 'Buah',
                    'tanggal_keluar' => $data[7] ?? null,
                    'pengambil' => $data[8] ?? null,
                    'user_input' => $data[9] ?? 'Administrator',
                ];
                
                // Skip if no nama_barang
                if (empty($row['nama_barang'])) {
                    $stats['baris_dilewati']++;
                    $this->output->progressAdvance();
                    continue;
                }
                
                // Parse dates (format: DD/MM/YYYY)
                $tanggalInput = $this->parseDate($row['tanggal_input']);
                $tanggalKeluar = $this->parseDate($row['tanggal_keluar']);
                
                // Find or create Barang
                if (!isset($barangCache[$row['nama_barang']])) {
                    $barang = Barang::where('nama_barang', $row['nama_barang'])->first();
                    
                    if (!$barang) {
                        // Determine kategori based on nama_barang
                        $kategori = $this->determineKategori($row['nama_barang']);
                        
                        $barang = Barang::create([
                            'nama_barang' => $row['nama_barang'],
                            'kategori' => $kategori,
                            'satuan' => $row['satuan'],
                            'stok' => 0, // Will be updated later
                            'stok_minimum' => 1,
                            'catatan' => 'Diimport dari CSV',
                        ]);
                        $stats['barang_baru']++;
                        
                        $this->info("\nBarang baru: {$row['nama_barang']} ({$kategori})");
                    }
                    
                    $barangCache[$row['nama_barang']] = $barang;
                    $barangStok[$row['nama_barang']] = 0; // Track running stock
                }
                
                $barang = $barangCache[$row['nama_barang']];
                
                $jumlahMasuk = (int) $row['jumlah_masuk'];
                $jumlahKeluar = (int) $row['jumlah_keluar'];
                
                // Calculate stok_sebelum and sisa_stok for this transaction
                $stokSebelum = $barangStok[$row['nama_barang']];
                $stokSetelah = $stokSebelum + $jumlahMasuk - $jumlahKeluar;
                $barangStok[$row['nama_barang']] = $stokSetelah;
                
                // Determine tipe
                $tipe = 'masuk';
                if ($jumlahMasuk > 0 && $jumlahKeluar > 0) {
                    $tipe = 'masuk'; // In the original, this seems to be treated as masuk
                } else if ($jumlahKeluar > 0) {
                    $tipe = 'keluar';
                }
                
                // Parse pengambil data (format: "Nama - Ruangan")
                $namaPengambil = null;
                $ruanganId = null;
                $tipePengambil = null;
                
                if (!empty($row['pengambil']) && $row['pengambil'] !== '-') {
                    $pengambilParts = explode(' - ', $row['pengambil']);
                    $namaPengambil = trim($pengambilParts[0]);
                    
                    if (count($pengambilParts) > 1) {
                        $namaRuangan = trim($pengambilParts[1]);
                        
                        // Find or create ruangan
                        if (!isset($ruanganCache[$namaRuangan])) {
                            $ruangan = Ruangan::where('nama_ruangan', $namaRuangan)->first();
                            if (!$ruangan) {
                                $ruangan = Ruangan::create([
                                    'nama_ruangan' => $namaRuangan,
                                    'keterangan' => 'Diimport dari CSV',
                                ]);
                            }
                            $ruanganCache[$namaRuangan] = $ruangan;
                        }
                        
                        $ruanganId = $ruanganCache[$namaRuangan]->id;
                        $tipePengambil = 'nama_ruangan';
                    } else {
                        $tipePengambil = 'ruangan_saja';
                    }
                }
                
                // Create transaction
                Transaksi::create([
                    'barang_id' => $barang->id,
                    'tipe' => $tipe,
                    'jumlah_masuk' => $jumlahMasuk,
                    'jumlah_keluar' => $jumlahKeluar,
                    'jumlah' => max($jumlahMasuk, $jumlahKeluar),
                    'stok_sebelum' => $stokSebelum,
                    'stok_setelah_masuk' => $stokSebelum + $jumlahMasuk,
                    'sisa_stok' => $stokSetelah,
                    'tanggal' => $tanggalInput,
                    'ruangan_id' => $ruanganId,
                    'user_id' => $adminUser->id,
                    'nama_pengambil' => $namaPengambil,
                    'tipe_pengambil' => $tipePengambil,
                    'tanggal_keluar' => ($tipe === 'keluar' && $tanggalKeluar) ? $tanggalKeluar : null,
                    'keterangan' => 'Diimport dari CSV - ID asli: ' . $row['id'],
                ]);
                
                $stats['transaksi']++;
                $this->output->progressAdvance();
            }
            
            // Update final stock for all barang
            foreach ($barangCache as $nama => $barang) {
                $barang->update(['stok' => $barangStok[$nama]]);
            }
            
            DB::commit();
            
            $this->output->progressFinish();
            
            $this->newLine();
            $this->info("Import selesai!");
            $this->info("- Barang baru: {$stats['barang_baru']}");
            $this->info("- Transaksi: {$stats['transaksi']}");
            $this->info("- Baris dilewati: {$stats['baris_dilewati']}");
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            $this->error("Line: " . $e->getLine());
            return 1;
        }
    }
    
    /**
     * Parse date from DD/MM/YYYY format
     */
    private function parseDate($dateStr)
    {
        if (empty($dateStr) || $dateStr === '-') {
            return now()->toDateString();
        }
        
        try {
            $parts = explode('/', $dateStr);
            if (count($parts) === 3) {
                $day = (int) $parts[0];
                $month = (int) $parts[1];
                $year = (int) $parts[2];
                
                // Handle 2-digit year
                if ($year < 100) {
                    $year += 2000;
                }
                
                return Carbon::createFromDate($year, $month, $day)->toDateString();
            }
        } catch (\Exception $e) {
            // Fallback to today
        }
        
        return now()->toDateString();
    }
    
    /**
     * Determine kategori based on barang name
     */
    private function determineKategori($namaBarang)
    {
        $namaBarang = strtolower($namaBarang);
        
        // ATK
        $atkKeywords = ['kertas', 'pulpen', 'pensil', 'buku', 'map', 'gunting', 'stapler', 'cutter', 'tinta', 'catridge', 'printer', 'tipe x', 'spidol', 'pulpen', 'kertas foto', 'buku catatan', 'mata cutter'];
        
        // Kebersihan
        $kebersihanKeywords = ['sapu', 'pel', 'sapu', 'kemoc', 'tissue', 'sabun', 'pengharum', 'penyegar', 'wip', 'keset', 'sarung tangan', 'sarung tangan'];
        
        // Konsumsi
        $konsumsiKeywords = ['minuman', 'aqua', 'teh', 'kopi', 'kue', 'permen', 'kacang', 'menta', 'buavita', 'makanan', 'snack'];
        
        // Perlengkapan
        $perlengkapanKeywords = ['baterai', 'colokan', 'klem', 'kabel', 'botol sprayer', 'toples', 'sendal', 'hdmi', 'tanah humus', 'parang'];
        
        foreach ($atkKeywords as $keyword) {
            if (strpos($namaBarang, $keyword) !== false) {
                return 'ATK';
            }
        }
        
        foreach ($kebersihanKeywords as $keyword) {
            if (strpos($namaBarang, $keyword) !== false) {
                return 'Kebersihan';
            }
        }
        
        foreach ($konsumsiKeywords as $keyword) {
            if (strpos($namaBarang, $keyword) !== false) {
                return 'Konsumsi';
            }
        }
        
        foreach ($perlengkapanKeywords as $keyword) {
            if (strpos($namaBarang, $keyword) !== false) {
                return 'Perlengkapan';
            }
        }
        
        return 'Lainnya';
    }
}