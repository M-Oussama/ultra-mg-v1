<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::query()->select('id','name','email','role_id')->with('role')->get();
foreach ($users as $u) {
    $role = $u->role?->role ?? 'null';
    echo $u->id . "|" . $u->name . "|" . $u->email . "|role_id=" . ($u->role_id ?? 'null') . "|role=" . $role . PHP_EOL;
}
