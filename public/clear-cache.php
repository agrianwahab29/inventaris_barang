<?php
/**
 * Laravel Cache Clear Script
 * Untuk cPanel Shared Hosting (tanpa terminal access)
 * 
 * Cara pakai:
 * 1. Upload file ini ke folder root Laravel (sejajar dengan folder app/, bootstrap/, dll)
 * 2. Akses via browser: https://domain-anda.com/inventaris-atk-bbst/clear-cache.php
 * 3. Hapus file ini setelah digunakan untuk keamanan
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Cache Clear</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; border: 2px solid #ffc107; }
    </style>
</head>
<body>
    <h1>🔧 Laravel Cache Clear Tool</h1>
";

// Security check - allow only from same domain or with secret key
$secret_key = 'clear123'; // Ganti dengan key rahasia Anda
$provided_key = $_GET['key'] ?? '';

if ($provided_key !== $secret_key) {
    echo "<div class='error'>❌ Akses ditolak! Key tidak valid.<br>Usage: clear-cache.php?key=clear123</div>";
    echo "<p><strong>Untuk keamanan, ganti 'clear123' dengan key rahasia Anda.</strong></p>";
    exit;
}

try {
    // Load Laravel bootstrap
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "<div class='info'>🚀 Laravel berhasil di-load...</div>";

    // Clear various caches
    $results = [];

    // 1. Clear View Cache
    $viewPath = __DIR__ . '/../storage/framework/views';
    if (is_dir($viewPath)) {
        $files = glob($viewPath . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                unlink($file);
                $count++;
            }
        }
        $results[] = "✅ View cache dibersihkan ($count files deleted)";
    }

    // 2. Clear Route Cache
    $routeCache = __DIR__ . '/../bootstrap/cache/routes.php';
    if (file_exists($routeCache)) {
        unlink($routeCache);
        $results[] = "✅ Route cache dihapus";
    }

    // 3. Clear Config Cache  
    $configCache = __DIR__ . '/../bootstrap/cache/config.php';
    if (file_exists($configCache)) {
        unlink($configCache);
        $results[] = "✅ Config cache dihapus";
    }

    // 4. Clear Application Cache
    $cachePath = __DIR__ . '/../storage/framework/cache/data';
    if (is_dir($cachePath)) {
        $files = glob($cachePath . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        $results[] = "✅ Application cache dibersihkan ($count files deleted)";
    }

    // 5. Clear Compiled Classes
    $compiledPath = __DIR__ . '/../bootstrap/cache/compiled.php';
    if (file_exists($compiledPath)) {
        unlink($compiledPath);
        $results[] = "✅ Compiled classes dihapus";
    }

    // 6. Clear Services Cache
    $servicesPath = __DIR__ . '/../bootstrap/cache/services.php';
    if (file_exists($servicesPath)) {
        unlink($servicesPath);
        $results[] = "✅ Services cache dihapus";
    }

    // 7. Clear Packages Cache
    $packagesPath = __DIR__ . '/../bootstrap/cache/packages.php';
    if (file_exists($packagesPath)) {
        unlink($packagesPath);
        $results[] = "✅ Packages cache dihapus";
    }

    // Display results
    echo "<div class='success'>";
    echo "<h3>✅ Cache berhasil dibersihkan!</h3>";
    echo "<ul>";
    foreach ($results as $result) {
        echo "<li>$result</li>";
    }
    echo "</ul>";
    echo "</div>";

    echo "<div class='warning'>⚠️ <strong>PENTING:</strong> Hapus file ini (clear-cache.php) setelah digunakan untuk keamanan!</div>";

    echo "<div class='info'>📋 <strong>Next Steps:</strong><br>";
    echo "1. Hapus file clear-cache.php dari server<br>";
    echo "2. Refresh halaman aplikasi Anda (Ctrl + F5)<br>";
    echo "3. Perubahan blade files sekarang akan terlihat</div>";

} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='info'>💡 Tips: Pastikan file ini berada di folder public_html/ atau sejajar dengan vendor/</div>";
}

echo "</body></html>";
