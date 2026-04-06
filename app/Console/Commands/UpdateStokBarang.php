<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Console\Command;

class UpdateStokBarang extends Command
{
    protected $signature = 'barang:update-stok';
    protected $description = 'Update stok barang berdasarkan riwayat transaksi terbaru';

    public function handle()
    {
        $this->info('Mengupdate stok barang...');
        
        $barangs = Barang::all();
        $updated = 0;
        
        foreach ($barangs as $barang) {
            $totalMasuk = Transaksi::where('barang_id', $barang->id)->sum('jumlah_masuk');
            $totalKeluar = Transaksi::where('barang_id', $barang->id)->sum('jumlah_keluar');
            $stokBaru = $totalMasuk - $totalKeluar;
            
            if ($barang->stok != $stokBaru) {
                $barang->stok = $stokBaru;
                $barang->save();
                $updated++;
                $this->line("Updated: {$barang->nama_barang} => {$stokBaru}");
            }
        }
        
        $this->info("Selesai! {$updated} barang diupdate.");
        
        return 0;
    }
}