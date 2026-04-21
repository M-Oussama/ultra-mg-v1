<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    $kernel->call('migrate', ['--force' => true]);
    echo "Migrated successfully";
} catch (\Exception $e) {
    file_put_contents('full_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
}
