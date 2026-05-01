<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'admin@gmail.com')->with('role.permissions')->first();

if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: " . $user->email . "\n";
echo "Role: " . ($user->role ? $user->role->role : 'NULL') . "\n";
echo "Is Global Admin: " . ($user->isGlobalAdmin() ? 'YES' : 'NO') . "\n";
echo "Permissions Count: " . ($user->role ? $user->role->permissions->count() : 0) . "\n";

foreach ($user->role->permissions as $perm) {
    if ($perm->subject == 'importations') {
        echo " - Permission: " . $perm->action . " " . $perm->subject . "\n";
    }
}
