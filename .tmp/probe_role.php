<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$role = App\Models\Role::query()->where('role', 'admin')->first();
if (!$role) {
    echo "no admin role\n";
    exit;
}

echo "admin_role_id=" . $role->id . PHP_EOL;
$perms = $role->permissions()->where('subject', 'importations')->get(['action','subject']);
foreach ($perms as $p) {
    echo $p->action . "|" . $p->subject . PHP_EOL;
}
