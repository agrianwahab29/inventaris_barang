<?php

// Testing Bootstrap File
// This file ensures all test dependencies are loaded correctly

require_once __DIR__ . '/../vendor/autoload.php';

// Set testing environment
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
putenv('CACHE_DRIVER=array');
putenv('SESSION_DRIVER=array');
putenv('QUEUE_CONNECTION=sync');

echo "Testing environment configured successfully.\n";
echo "Run tests with: vendor/bin/phpunit\n";
