<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'users=' . App\Models\User::query()->count() . PHP_EOL;
echo 'roles=' . App\Models\Role::query()->count() . PHP_EOL;
foreach (App\Models\Role::query()->get(['id','role']) as $role) {
    echo 'role|' . $role->id . '|' . $role->role . PHP_EOL;
}
