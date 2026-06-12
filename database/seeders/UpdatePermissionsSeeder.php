<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermissions;
use Illuminate\Database\Seeder;

class UpdatePermissionsSeeder extends Seeder
{
    /**
     * Safely updates existing admin roles with newly mapped UI components.
     * Prevents unique constraint compilation failures.
     *
     * @return void
     */
    public function run()
    {
        // 1. Fetch or initialize the root structural admin
        $role = Role::firstOrCreate(['role' => 'admin']);

        // 2. Sync every known permission to admin so newly added modules are inherited automatically
        foreach (Permission::all() as $perm) {
            RoleHasPermissions::firstOrCreate([
                'role_id' => $role->id,
                'permission_id' => $perm->id,
            ]);
        }
    }
}
