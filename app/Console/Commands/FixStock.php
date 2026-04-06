<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fixing transaksis...');
        \Illuminate\Support\Facades\DB::table('transaksis')
            ->where('tipe', 'keluar')
            ->update(['jumlah_masuk' => 0]);

        $this->info('Recalculating stock...');
        $barangs = \App\Models\Barang::all();
        foreach ($barangs as $barang) {
            $masuk = \App\Models\Transaksi::where('barang_id', $barang->id)->sum('jumlah_masuk');
            $keluar = \App\Models\Transaksi::where('barang_id', $barang->id)->sum('jumlah_keluar');
            $barang->stok = $masuk - $keluar;
            $barang->save();
        }
        
        $this->info('Done!');
        return 0;
    }
}
