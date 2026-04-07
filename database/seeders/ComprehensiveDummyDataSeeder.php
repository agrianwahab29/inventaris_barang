<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ComprehensiveDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🚀 Starting Comprehensive Dummy Data Seeder...');
        
        // Create users with different roles
        $this->createUsers();
        
        // Create rooms
        $this->createRuangans();
        
        // Create items with various stock conditions
        $this->createBarangs();
        
        // Generate one year of transactions with various scenarios
        $this->generateOneYearTransactions();
        
        $this->command->info('✅ Dummy data created successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('- Users: ' . User::count());
        $this->command->info('- Rooms: ' . Ruangan::count());
        $this->command->info('- Items: ' . Barang::count());
        $this->command->info('- Transactions: ' . Transaksi::count());
    }
    
    private function createUsers()
    {
        $this->command->info('👥 Creating users...');
        
        // Admin user - updateOrCreate to avoid duplicate errors
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'aktif', // Admin is active
            ]
        );
        
        // Regular users - set status to nonaktif for dummy users
        $users = [
            ['name' => 'Budi Santoso', 'username' => 'budi', 'email' => 'budi@example.com'],
            ['name' => 'Ani Wijaya', 'username' => 'ani', 'email' => 'ani@example.com'],
            ['name' => 'Dedi Kurniawan', 'username' => 'dedi', 'email' => 'dedi@example.com'],
            ['name' => 'Siti Aminah', 'username' => 'siti', 'email' => 'siti@example.com'],
            ['name' => 'Rudi Hartono', 'username' => 'rudi', 'email' => 'rudi@example.com'],
        ];
        
        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'password' => Hash::make('password'),
                    'role' => 'pengguna',
                    'status' => 'nonaktif', // Set status to nonaktif for dummy users
                ]
            );
        }
        
        $this->command->info('✅ Admin password set to: admin123');
    }
    
    private function createRuangans()
    {
        $this->command->info('🏢 Creating rooms...');
        
        $ruangans = [
            ['nama_ruangan' => 'Ruang Direktur', 'keterangan' => 'Kantor Direktur Utama'],
            ['nama_ruangan' => 'Ruang Rapat A', 'keterangan' => 'Ruang rapat kapasitas 20 orang'],
            ['nama_ruangan' => 'Ruang Rapat B', 'keterangan' => 'Ruang rapat kapasitas 10 orang'],
            ['nama_ruangan' => 'Ruang Staff IT', 'keterangan' => 'Workstation IT Department'],
            ['nama_ruangan' => 'Ruang Staff HR', 'keterangan' => 'Workstation HR Department'],
            ['nama_ruangan' => 'Ruang Staff Finance', 'keterangan' => 'Workstation Finance Department'],
            ['nama_ruangan' => 'Ruang Arsip', 'keterangan' => 'Penyimpanan dokumen dan arsip'],
            ['nama_ruangan' => 'Pantry', 'keterangan' => 'Area dapur dan minum'],
            ['nama_ruangan' => 'Ruang Serbaguna', 'keterangan' => 'Ruang multi-fungsi'],
            ['nama_ruangan' => 'Lobby', 'keterangan' => 'Area depan kantor'],
        ];
        
        foreach ($ruangans as $ruangan) {
            Ruangan::updateOrCreate(
                ['nama_ruangan' => $ruangan['nama_ruangan']],
                $ruangan
            );
        }
    }
    
    private function createBarangs()
    {
        $this->command->info('📦 Creating items...');
        
        $barangs = [
            // ATK - Alat Tulis Kantor
            ['nama_barang' => 'Pulpen Standard', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 150, 'stok_minimum' => 20],
            ['nama_barang' => 'Pensil 2B', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 200, 'stok_minimum' => 30],
            ['nama_barang' => 'Penghapus', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 100, 'stok_minimum' => 15],
            ['nama_barang' => 'Kertas A4 80gsm', 'kategori' => 'ATK', 'satuan' => 'Rim', 'stok' => 50, 'stok_minimum' => 10],
            ['nama_barang' => 'Kertas F4 80gsm', 'kategori' => 'ATK', 'satuan' => 'Rim', 'stok' => 30, 'stok_minimum' => 5],
            ['nama_barang' => 'Map Folder', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 80, 'stok_minimum' => 10],
            ['nama_barang' => 'Amplop C4', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 120, 'stok_minimum' => 20],
            ['nama_barang' => 'Tipe-X', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 60, 'stok_minimum' => 10],
            ['nama_barang' => 'Stapler Besar', 'kategori' => 'ATK', 'satuan' => 'Buah', 'stok' => 15, 'stok_minimum' => 3],
            ['nama_barang' => 'Isi Staples', 'kategori' => 'ATK', 'satuan' => 'Box', 'stok' => 40, 'stok_minimum' => 5],
            
            // Perlengkapan - Elektronik & Office Supplies
            ['nama_barang' => 'Tinta Printer HP Black', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 25, 'stok_minimum' => 5],
            ['nama_barang' => 'Tinta Printer HP Color', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 20, 'stok_minimum' => 4],
            ['nama_barang' => 'Kabel HDMI 3m', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 12, 'stok_minimum' => 2],
            ['nama_barang' => 'Kabel USB Printer', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 15, 'stok_minimum' => 3],
            ['nama_barang' => 'Mouse Wireless', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 20, 'stok_minimum' => 4],
            ['nama_barang' => 'Keyboard USB', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 18, 'stok_minimum' => 3],
            ['nama_barang' => 'Baterai AA', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 100, 'stok_minimum' => 20],
            ['nama_barang' => 'Baterai AAA', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 80, 'stok_minimum' => 15],
            
            // Kebersihan
            ['nama_barang' => 'Tisu Toilet', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 60, 'stok_minimum' => 12],
            ['nama_barang' => 'Sabun Cuci Tangan', 'kategori' => 'Kebersihan', 'satuan' => 'Botol', 'stok' => 25, 'stok_minimum' => 5],
            ['nama_barang' => 'Sampah Plastik 60L', 'kategori' => 'Kebersihan', 'satuan' => 'Buah', 'stok' => 20, 'stok_minimum' => 4],
            ['nama_barang' => 'Kantong Sampah Besar', 'kategori' => 'Kebersihan', 'satuan' => 'Pak', 'stok' => 30, 'stok_minimum' => 6],
            
            // Konsumsi - Minuman & Konsumsi
            ['nama_barang' => 'Air Galon 19L', 'kategori' => 'Konsumsi', 'satuan' => 'Galon', 'stok' => 15, 'stok_minimum' => 3],
            ['nama_barang' => 'Kopi Sachet', 'kategori' => 'Konsumsi', 'satuan' => 'Box', 'stok' => 20, 'stok_minimum' => 4],
            ['nama_barang' => 'Teh Celup', 'kategori' => 'Konsumsi', 'satuan' => 'Box', 'stok' => 25, 'stok_minimum' => 5],
            ['nama_barang' => 'Gula Pasir 1kg', 'kategori' => 'Konsumsi', 'satuan' => 'Buah', 'stok' => 12, 'stok_minimum' => 2],
            ['nama_barang' => 'Gelas Plastik', 'kategori' => 'Konsumsi', 'satuan' => 'Pak', 'stok' => 40, 'stok_minimum' => 8],
            
            // Perlengkapan - Furniture & Perlengkapan
            ['nama_barang' => 'Kursi Kantor', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 10, 'stok_minimum' => 2],
            ['nama_barang' => 'Meja Lipat', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 8, 'stok_minimum' => 2],
            ['nama_barang' => 'Lemari Arsip', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 5, 'stok_minimum' => 1],
            ['nama_barang' => 'Whiteboard 120x90', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 4, 'stok_minimum' => 1],
            ['nama_barang' => 'Spidol Whiteboard', 'kategori' => 'Perlengkapan', 'satuan' => 'Box', 'stok' => 15, 'stok_minimum' => 3],
            ['nama_barang' => 'Penghapus Whiteboard', 'kategori' => 'Perlengkapan', 'satuan' => 'Buah', 'stok' => 10, 'stok_minimum' => 2],
            
            // Lainnya - Safety & Security
            ['nama_barang' => 'APAR (Alat Pemadam)', 'kategori' => 'Lainnya', 'satuan' => 'Buah', 'stok' => 8, 'stok_minimum' => 2],
            ['nama_barang' => 'Kotak P3K', 'kategori' => 'Lainnya', 'satuan' => 'Buah', 'stok' => 6, 'stok_minimum' => 1],
            ['nama_barang' => 'Isi P3K', 'kategori' => 'Lainnya', 'satuan' => 'Box', 'stok' => 10, 'stok_minimum' => 2],
        ];
        
        foreach ($barangs as $barang) {
            Barang::updateOrCreate(
                ['nama_barang' => $barang['nama_barang']],
                $barang
            );
        }
    }
    
    private function generateOneYearTransactions()
    {
        $this->command->info('📅 Generating one year of transactions...');
        
        $users = User::where('role', 'pengguna')->get();
        $ruangans = Ruangan::all();
        $barangs = Barang::all();
        
        // Generate transactions from 1 year ago until now
        $startDate = Carbon::now()->subYear()->startOfMonth();
        $endDate = Carbon::now();
        
        $transactionCount = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Determine how many transactions for this day (0-8 transactions)
            $transactionsToday = $this->getRandomTransactionCount($currentDate);
            
            for ($i = 0; $i < $transactionsToday; $i++) {
                $this->createRandomTransaction($currentDate, $users, $ruangans, $barangs);
                $transactionCount++;
                
                if ($transactionCount % 100 == 0) {
                    $this->command->info("  Created {$transactionCount} transactions...");
                }
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("✅ Total transactions created: {$transactionCount}");
    }
    
    private function getRandomTransactionCount($date)
    {
        // More transactions on weekdays, fewer on weekends
        $isWeekend = $date->isWeekend();
        
        // Special high-activity days (month start, month end)
        $isMonthStart = $date->day <= 3;
        $isMonthEnd = $date->day >= 28;
        $isMidMonth = $date->day >= 14 && $date->day <= 16;
        
        if ($isWeekend) {
            return rand(0, 2); // 0-2 transactions on weekends
        } elseif ($isMonthStart || $isMonthEnd) {
            return rand(4, 8); // High activity at month boundaries
        } elseif ($isMidMonth) {
            return rand(3, 6); // Medium activity mid-month
        } else {
            return rand(1, 4); // Normal days
        }
    }
    
    private function createRandomTransaction($date, $users, $ruangans, $barangs)
    {
        $user = $users->random();
        $barang = $barangs->random();
        $ruangan = $ruangans->random();
        
        // Random time during working hours (08:00 - 17:00)
        $hour = rand(8, 16);
        $minute = rand(0, 59);
        $transactionDate = $date->copy()->setTime($hour, $minute);
        
        // Determine transaction type
        $transactionType = $this->determineTransactionType($barang);
        
        $jumlahMasuk = 0;
        $jumlahKeluar = 0;
        $stokSebelum = $barang->stok;
        $stokSetelahMasuk = $stokSebelum;
        $sisaStok = $stokSebelum;
        $tanggalKeluar = null;
        $namaPengambil = null;
        $tipePengambil = null;
        
        switch ($transactionType) {
            case 'masuk':
                $jumlahMasuk = $this->getRandomMasukAmount($barang);
                $stokSetelahMasuk = $stokSebelum + $jumlahMasuk;
                $sisaStok = $stokSetelahMasuk;
                break;
                
            case 'keluar':
                $jumlahKeluar = $this->getRandomKeluarAmount($barang);
                if ($stokSebelum >= $jumlahKeluar) {
                    $sisaStok = $stokSebelum - $jumlahKeluar;
                    $tanggalKeluar = $transactionDate->copy()->addDays(rand(0, 2));
                    $namaPengambil = $this->getRandomPengambil();
                    $tipePengambil = 'nama_ruangan';
                } else {
                    // Not enough stock, skip this transaction
                    return;
                }
                break;
                
            case 'masuk_keluar':
                $jumlahMasuk = $this->getRandomMasukAmount($barang);
                $stokSetelahMasuk = $stokSebelum + $jumlahMasuk;
                $jumlahKeluar = min($this->getRandomKeluarAmount($barang), $stokSetelahMasuk);
                $sisaStok = $stokSetelahMasuk - $jumlahKeluar;
                if ($jumlahKeluar > 0) {
                    $tanggalKeluar = $transactionDate->copy()->addDays(rand(0, 2));
                    $namaPengambil = $this->getRandomPengambil();
                    $tipePengambil = 'nama_ruangan';
                }
                break;
        }
        
        // Create transaction
        $transaksi = Transaksi::create([
            'barang_id' => $barang->id,
            'user_id' => $user->id,
            'tipe' => $transactionType,
            'jumlah_masuk' => $jumlahMasuk,
            'jumlah_keluar' => $jumlahKeluar,
            'jumlah' => max($jumlahMasuk, $jumlahKeluar),
            'stok_sebelum' => $stokSebelum,
            'stok_setelah_masuk' => $stokSetelahMasuk,
            'sisa_stok' => $sisaStok,
            'tanggal' => $transactionDate,
            'tanggal_keluar' => $tanggalKeluar,
            'ruangan_id' => ($jumlahKeluar > 0) ? $ruangan->id : null,
            'nama_pengambil' => $namaPengambil,
            'tipe_pengambil' => $tipePengambil,
            'keterangan' => $this->getRandomKeterangan($transactionType, $barang),
        ]);
        
        // Update barang stock
        $barang->stok = $sisaStok;
        $barang->save();
    }
    
    private function determineTransactionType($barang)
    {
        $stok = $barang->stok;
        $stokMinimum = $barang->stok_minimum;
        
        // If stock is critically low, prioritize incoming
        if ($stok <= $stokMinimum) {
            $weights = ['masuk' => 70, 'keluar' => 10, 'masuk_keluar' => 20];
        } elseif ($stok <= $stokMinimum * 2) {
            $weights = ['masuk' => 50, 'keluar' => 20, 'masuk_keluar' => 30];
        } elseif ($stok >= $stokMinimum * 10) {
            // High stock, prioritize outgoing
            $weights = ['masuk' => 20, 'keluar' => 50, 'masuk_keluar' => 30];
        } else {
            // Normal stock, balanced
            $weights = ['masuk' => 35, 'keluar' => 35, 'masuk_keluar' => 30];
        }
        
        return $this->weightedRandom($weights);
    }
    
    private function weightedRandom($weights)
    {
        $total = array_sum($weights);
        $random = rand(1, $total);
        
        foreach ($weights as $type => $weight) {
            $random -= $weight;
            if ($random <= 0) {
                return $type;
            }
        }
        
        return 'masuk';
    }
    
    private function getRandomMasukAmount($barang)
    {
        // Different amounts based on item type
        $kode = substr($barang->kode, 0, 3);
        
        switch ($kode) {
            case 'ATK': // ATK - small quantities
                return rand(10, 50);
            case 'ELK': // Elektronik - medium quantities
                return rand(5, 20);
            case 'KBS': // Kebersihan - medium quantities
                return rand(5, 15);
            case 'MNM': // Minuman - larger quantities
                return rand(10, 30);
            case 'FUR': // Furniture - small quantities
                return rand(1, 5);
            case 'SFT': // Safety - small quantities
                return rand(2, 8);
            default:
                return rand(5, 20);
        }
    }
    
    private function getRandomKeluarAmount($barang)
    {
        // Smaller amounts for outgoing
        $kode = substr($barang->kode, 0, 3);
        
        switch ($kode) {
            case 'ATK':
                return rand(1, 10);
            case 'ELK':
                return rand(1, 5);
            case 'KBS':
                return rand(2, 8);
            case 'MNM':
                return rand(3, 12);
            case 'FUR':
                return rand(1, 2);
            case 'SFT':
                return rand(1, 3);
            default:
                return rand(1, 5);
        }
    }
    
    private function getRandomPengambil()
    {
        $pengambils = [
            'Budi Santoso',
            'Ani Wijaya',
            'Dedi Kurniawan',
            'Siti Aminah',
            'Rudi Hartono',
            'Pak Dirman',
            'Bu Sari',
            'Mas Joko',
            'Mbak Rina',
            'Pak Ahmad',
            'Staff IT',
            'Staff HR',
            'Staff Finance',
            'Office Boy',
            'Security',
        ];
        
        return $pengambils[array_rand($pengambils)];
    }
    
    private function getRandomKeterangan($type, $barang)
    {
        $keterangans = [
            'masuk' => [
                'Pembelian rutin',
                'Restock bulanan',
                'Pembelian darurat',
                'Stok awal bulan',
                'Pengadaan baru',
                'Pembelian dari supplier X',
                'Restock habis pakai',
                'Pengadaan semesteran',
            ],
            'keluar' => [
                'Pemakaian rutin',
                'Kebutuhan rapat',
                'Pemakaian staff',
                'Kebutuhan khusus',
                'Peminjaman sementara',
                'Pemakaian harian',
                'Kebutuhan event',
                'Pemakaian departemen',
            ],
            'masuk_keluar' => [
                'Stok opname adjustment',
                'Retur dan penggantian',
                'Peminjaman dan pengembalian',
                'Transfer antar ruang',
                'Koreksi stok',
                'Pergantian barang rusak',
            ],
        ];
        
        $list = $keterangans[$type] ?? $keterangans['masuk'];
        return $list[array_rand($list)];
    }
}
