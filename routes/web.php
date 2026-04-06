<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\QuarterlyStockController;
use App\Http\Controllers\SuratTandaTerimaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Login Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang Routes (Admin & Pengguna)
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/export', [BarangController::class, 'export'])->name('barang.export');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::post('/barang/{barang}/update-stok', [BarangController::class, 'updateStok'])->name('barang.updateStok');
    
    // Barang Edit/Delete (Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::delete('/barang/bulk/delete', [BarangController::class, 'bulkDelete'])->name('barang.bulkDelete');
    });

    // Transaksi Routes
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/edit', [TransaksiController::class, 'edit'])->name('transaksi.edit');
    Route::put('/transaksi/{transaksi}', [TransaksiController::class, 'update'])->name('transaksi.update');
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
    Route::delete('/transaksi/bulk/delete', [TransaksiController::class, 'bulkDelete'])->name('transaksi.bulkDelete');

    // AJAX Routes
    Route::get('/api/barang/{id}/info', [TransaksiController::class, 'getBarangInfo'])->name('api.barang.info');
    Route::get('/api/transactions/check-updates', [TransaksiController::class, 'checkUpdates'])->name('api.transactions.check-updates');

    // Quarterly Stock Opname Routes
    Route::get('/quarterly-stock', [QuarterlyStockController::class, 'index'])->name('quarterly-stock.index');
    Route::post('/quarterly-stock/export', [QuarterlyStockController::class, 'exportDocx'])->name('quarterly-stock.export');

    // Surat Tanda Terima Routes
    Route::get('/surat-tanda-terima', [SuratTandaTerimaController::class, 'index'])->name('surat-tanda-terima.index');
    Route::get('/surat-tanda-terima/generate', [SuratTandaTerimaController::class, 'generateDocx'])->name('surat-tanda-terima.generate');

    // Ruangan Routes
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/ruangan/create', [RuanganController::class, 'create'])->name('ruangan.create')->middleware('role:admin');
    Route::post('/ruangan', [RuanganController::class, 'store'])->name('ruangan.store')->middleware('role:admin');
    Route::get('/ruangan/{ruangan}', [RuanganController::class, 'show'])->name('ruangan.show');
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/ruangan/{ruangan}/edit', [RuanganController::class, 'edit'])->name('ruangan.edit');
        Route::put('/ruangan/{ruangan}', [RuanganController::class, 'update'])->name('ruangan.update');
        Route::delete('/ruangan/{ruangan}', [RuanganController::class, 'destroy'])->name('ruangan.destroy');
        Route::delete('/ruangan/bulk/delete', [RuanganController::class, 'bulkDelete'])->name('ruangan.bulkDelete');
    });

    // Debug endpoint for shared hosting
    Route::get('/check-seed', function () {
        $debug = [];
        $debug['storage_path'] = storage_path('app');
        $debug['storage_exists'] = is_dir(storage_path('app'));
        
        $csvPath = storage_path('app/Data_Transaksi_2026-03-12_03-27-45.csv');
        $debug['csv_path'] = $csvPath;
        $debug['csv_exists'] = file_exists($csvPath);
        $debug['csv_readable'] = is_readable($csvPath);
        
        if (is_dir(storage_path('app'))) {
            $debug['storage_files'] = scandir(storage_path('app'));
        }
        
        try {
            \App\Models\Transaksi::truncate();
            $debug['truncate_success'] = true;
        } catch (\Exception $e) {
            $debug['truncate_error'] = $e->getMessage();
        }
        
        return response()->json($debug);
    });
    
    // Web-based seeder for shared hosting
    Route::get('/seed-transaksi', function () {
        $secret = request('secret');
        $expectedSecret = env('TRANSAKSI_SEED_SECRET', 'seed-safety-2026');
        
        if ($secret !== $expectedSecret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            Artisan::call('db:seed', ['--class' => 'TransaksiCsvSeeder']);
            $output = Artisan::output();
            return response()->json(['success' => true, 'message' => 'Seeding completed', 'output' => $output]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    });
    
    // User Management (Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [AuthController::class, 'indexUsers'])->name('users.index');
        Route::get('/users/create', [AuthController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AuthController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AuthController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AuthController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');
        Route::delete('/users/bulk/delete', [AuthController::class, 'bulkDeleteUsers'])->name('users.bulkDelete');
    });
});
