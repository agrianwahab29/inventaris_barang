<?php
/**
 * Check existing data and insert with valid foreign keys
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Checking existing data...\n\n";

// Check existing barangs
$barangs = DB::table('barangs')->get();
echo "✅ Existing Barangs: " . count($barangs) . "\n";
foreach ($barangs as $b) {
    echo "   ID {$b->id}: {$b->nama_barang}\n";
}

echo "\n";

// Check existing ruangans
$ruangans = DB::table('ruangans')->get();
echo "✅ Existing Ruangans: " . count($ruangans) . "\n";
foreach ($ruangans as $r) {
    echo "   ID {$r->id}: {$r->nama_ruangan}\n";
}

echo "\n";

// Check existing users
$users = DB::table('users')->get();
echo "✅ Existing Users: " . count($users) . "\n";
foreach ($users as $u) {
    echo "   ID {$u->id}: {$u->name}\n";
}

echo "\n";
echo "📊 Using available IDs:\n";
echo "   Barang IDs: " . implode(', ', $barangs->pluck('id')->toArray()) . "\n";
echo "   Ruangan IDs: " . implode(', ', $ruangans->pluck('id')->toArray()) . "\n";
echo "   User IDs: " . implode(', ', $users->pluck('id')->toArray()) . "\n";
