<?php

use App\Models\Role;
use App\Models\Permission;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "Role: " . $role->role . " (ID: " . $role->id . ")\n";
    echo "Permissions: " . $role->permissions->count() . "\n";
    // foreach ($role->permissions as $p) {
    //     echo "  - " . $p->action . "," . $p->subject . "\n";
    // }
    echo "-------------------\n";
}
