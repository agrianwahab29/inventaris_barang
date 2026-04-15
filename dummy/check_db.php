<?php
/**
 * Manual SQL Import via Laravel DB
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking database...\n";

// Get all tables
$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
echo "Tables found: " . count($tables) . "\n";
foreach ($tables as $table) {
    echo "  - {$table->name}\n";
}

echo "\n";

// Check transaksis table columns
$columns = DB::select('PRAGMA table_info(transaksis)');
echo "Columns in transaksis table:\n";
foreach ($columns as $col) {
    echo "  - {$col->name} ({$col->type})\n";
}

echo "\n";

// Check transaksis table
try {
    $transCount = DB::table('transaksis')->count();
    echo "✅ transaksis table exists with {$transCount} records\n";
} catch (Exception $e) {
    echo "❌ transaksis table error: " . $e->getMessage() . "\n";
}
