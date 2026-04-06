<?php

use Illuminate\Support\Facades\Route;

Route::get('/check-seed', function () {
    $debug = [];
    
    // Check storage path
    $debug['storage_path'] = storage_path('app');
    $debug['storage_exists'] = is_dir(storage_path('app'));
    
    // Check CSV file
    $csvPath = storage_path('app/Data_Transaksi_2026-03-12_03-27-45.csv');
    $debug['csv_path'] = $csvPath;
    $debug['csv_exists'] = file_exists($csvPath);
    $debug['csv_readable'] = is_readable($csvPath);
    
    // List storage/app contents
    if (is_dir(storage_path('app'))) {
        $debug['storage_files'] = scandir(storage_path('app'));
    }
    
    // Test truncate
    try {
        \App\Models\Transaksi::truncate();
        $debug['truncate_success'] = true;
    } catch (\Exception $e) {
        $debug['truncate_error'] = $e->getMessage();
    }
    
    return response()->json($debug);
})->name('check.seed');
