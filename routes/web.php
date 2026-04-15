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
use App\Http\Controllers\BerkasTransaksiController;

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
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->middleware('throttle:5,1');
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

    // Berkas Transaksi Routes
    Route::get('/berkas-transaksi', [BerkasTransaksiController::class, 'index'])->name('berkas-transaksi.index');
    Route::get('/berkas-transaksi/create', [BerkasTransaksiController::class, 'create'])->name('berkas-transaksi.create');
    Route::post('/berkas-transaksi', [BerkasTransaksiController::class, 'store'])->name('berkas-transaksi.store');
    Route::get('/berkas-transaksi/{berkasTransaksi}', [BerkasTransaksiController::class, 'show'])->name('berkas-transaksi.show');
    Route::get('/berkas-transaksi/{berkasTransaksi}/edit', [BerkasTransaksiController::class, 'edit'])->name('berkas-transaksi.edit');
    Route::put('/berkas-transaksi/{berkasTransaksi}', [BerkasTransaksiController::class, 'update'])->name('berkas-transaksi.update');
    Route::delete('/berkas-transaksi/{berkasTransaksi}', [BerkasTransaksiController::class, 'destroy'])->name('berkas-transaksi.destroy');
    Route::get('/berkas-transaksi/{berkasTransaksi}/download', [BerkasTransaksiController::class, 'download'])->name('berkas-transaksi.download');
    
    // Bulk Delete Routes for Berkas Transaksi
    Route::post('/berkas-transaksi/bulk-delete', [BerkasTransaksiController::class, 'bulkDelete'])->name('berkas-transaksi.bulk-delete');
    Route::post('/berkas-transaksi/delete-all', [BerkasTransaksiController::class, 'deleteAll'])->name('berkas-transaksi.delete-all');
    Route::post('/berkas-transaksi/delete-by-month', [BerkasTransaksiController::class, 'deleteByMonth'])->name('berkas-transaksi.delete-by-month');
    Route::post('/berkas-transaksi/delete-by-range', [BerkasTransaksiController::class, 'deleteByRange'])->name('berkas-transaksi.delete-by-range');

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

    // User Management (Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [AuthController::class, 'indexUsers'])->name('users.index');
        Route::get('/users/create', [AuthController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AuthController::class, 'storeUser'])->name('users.store')->middleware('throttle:10,1');
        Route::get('/users/{user}/edit', [AuthController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AuthController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AuthController::class, 'destroyUser'])->name('users.destroy');
        Route::delete('/users/bulk/delete', [AuthController::class, 'bulkDeleteUsers'])->name('users.bulkDelete');
    });
});
