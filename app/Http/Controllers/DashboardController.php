<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache dashboard data for 5 minutes to improve performance
        $cacheKey = 'dashboard_data_' . auth()->id();
        
        $data = Cache::remember($cacheKey, 300, function () {
            $totalBarang = Barang::count();
            $totalStok = Barang::sum('stok');
            $stokRendah = Barang::whereColumn('stok', '<=', 'stok_minimum')
                ->where('stok', '>', 0)
                ->count();
            $stokHabis = Barang::where('stok', '<=', 0)->count();
            
            $transaksiHariIni = Transaksi::whereDate('tanggal', Carbon::today())->count();
            
            // Barang stok rendah - optimized query with specific columns
            $barangStokRendah = Barang::select(['id', 'nama_barang', 'kategori', 'satuan', 'stok', 'stok_minimum'])
                ->where(function($q) {
                    $q->whereColumn('stok', '<=', 'stok_minimum')
                      ->orWhere('stok', '<=', 0);
                })
                ->orderBy('stok', 'asc')
                ->limit(10)
                ->get();
            
            // 10 transaksi terakhir - eager load with specific columns
            $transaksiTerakhir = Transaksi::select(['id', 'barang_id', 'tipe', 'jumlah', 'tanggal', 'ruangan_id', 'user_id'])
                ->with([
                    'barang:id,nama_barang,satuan',
                    'ruangan:id,nama_ruangan',
                    'user:id,name'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Data untuk grafik (7 hari terakhir) - optimized
            $tanggalLabels = [];
            $dataMasuk = [];
            $dataKeluar = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $tanggalLabels[] = $date->format('d M');
                
                $dataMasuk[] = (int) Transaksi::whereDate('tanggal', $date)
                    ->sum('jumlah_masuk');
                    
                $dataKeluar[] = (int) Transaksi::whereDate('tanggal', $date)
                    ->sum('jumlah_keluar');
            }

            return [
                'totalBarang' => $totalBarang,
                'totalStok' => $totalStok,
                'stokRendah' => $stokRendah,
                'stokHabis' => $stokHabis,
                'transaksiHariIni' => $transaksiHariIni,
                'barangStokRendah' => $barangStokRendah,
                'transaksiTerakhir' => $transaksiTerakhir,
                'tanggalLabels' => $tanggalLabels,
                'dataMasuk' => $dataMasuk,
                'dataKeluar' => $dataKeluar,
            ];
        });

        // Clear cache when there's new transaction
        if (request()->get('refresh')) {
            Cache::forget($cacheKey);
            return redirect()->route('dashboard')->with('success', 'Cache telah di-refresh');
        }

        return view('dashboard.index', $data);
    }
}
