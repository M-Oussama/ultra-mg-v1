<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$perm = App\Models\Permission::query()->where('action', 'list')->where('subject', 'importations')->first();
if ($perm) {
    echo 'perm_id=' . $perm->id . PHP_EOL;
    foreach ($perm->roles()->get(['roles.id','roles.role']) as $role) {
        echo 'role_with_perm|' . $role->id . '|' . $role->role . PHP_EOL;
    }
}
