<?php
/**
 * Import Dummy Data Seeder
 * Imports 50 dummy transactions for QA testing
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "\n";
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║     IMPORT DUMMY DATA - Sistem Inventaris              ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n";
echo "\n";

// Read SQL file
$sqlFile = __DIR__ . '/transaksi_dummy_seeder.sql';
if (!file_exists($sqlFile)) {
    echo "❌ Error: SQL file not found at {$sqlFile}\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);

// Extract INSERT statements using regex
preg_match_all('/INSERT INTO \w+ \([^)]+\) VALUES\s*\([^;]+;/', $sql, $matches);

if (empty($matches[0])) {
    echo "❌ Error: No INSERT statements found\n";
    exit(1);
}

echo "📊 Found " . count($matches[0]) . " INSERT statements\n\n";

// Disable foreign key checks temporarily
DB::statement('PRAGMA foreign_keys = OFF');

try {
    DB::beginTransaction();
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($matches[0] as $index => $insertSql) {
        try {
            DB::statement($insertSql);
            $successCount++;
            echo "✅ [" . ($index + 1) . "/" . count($matches[0]) . "] Inserted successfully\n";
        } catch (Exception $e) {
            $errorCount++;
            echo "⚠️  [" . ($index + 1) . "/" . count($matches[0]) . "] Skipped (may already exist): " . substr($e->getMessage(), 0, 50) . "...\n";
        }
    }
    
    DB::commit();
    
    // Re-enable foreign key checks
    DB::statement('PRAGMA foreign_keys = ON');
    
    echo "\n";
    echo str_repeat("=", 60) . "\n";
    echo "IMPORT SUMMARY\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ Successfully inserted: {$successCount}\n";
    echo "⚠️  Skipped (duplicates): {$errorCount}\n";
    echo "\n";
    
    // Verify counts
    $userCount = DB::table('users')->count();
    $barangCount = DB::table('barangs')->count();
    $ruanganCount = DB::table('ruangans')->count();
    $transaksiCount = DB::table('transaksis')->count();
    
    echo "📊 CURRENT DATABASE STATE:\n";
    echo "   Users:     {$userCount}\n";
    echo "   Barang:    {$barangCount}\n";
    echo "   Ruangan:   {$ruanganCount}\n";
    echo "   Transaksi: {$transaksiCount}\n";
    echo "\n";
    
    // Show monthly breakdown
    $monthly = DB::select("SELECT strftime('%Y-%m', tanggal) as bulan, COUNT(*) as jumlah FROM transaksis GROUP BY bulan ORDER BY bulan");
    
    if ($monthly) {
        echo "📅 TRANSACTIONS BY MONTH:\n";
        foreach ($monthly as $m) {
            echo "   {$m->bulan}: {$m->jumlah} transaksi\n";
        }
        echo "\n";
    }
    
    if ($transaksiCount >= 50) {
        echo "🎉 SUCCESS! Database now has {$transaksiCount} transactions\n";
        echo "✅ Ready for comprehensive QA testing\n";
        exit(0);
    } else {
        echo "⚠️  WARNING: Expected 50 transactions, got {$transaksiCount}\n";
        exit(1);
    }
    
} catch (Exception $e) {
    DB::rollBack();
    DB::statement('PRAGMA foreign_keys = ON');
    echo "❌ FATAL ERROR: {$e->getMessage()}\n";
    exit(1);
}
