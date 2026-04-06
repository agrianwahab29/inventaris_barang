<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/seed-transaksi', function () {
    // Only allow in local/development or with secret key
    // For production, add ?secret=YOUR_SECRET_KEY
    $secret = request('secret');
    $expectedSecret = env('TRANSAKSI_SEED_SECRET', 'seed-safety-2026');
    
    if ($secret !== $expectedSecret) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Provide ?secret=YOUR_SECRET_KEY'
        ], 403);
    }
    
    try {
        Artisan::call('db:seed', ['--class' => 'TransaksiCsvSeeder']);
        
        return response()->json([
            'success' => true,
            'message' => 'Transactions seeded successfully',
            'output' => Artisan::output()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
})->name('seed.transaksi');
