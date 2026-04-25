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

        // 2. Safely sync all explicitly coded Action_Subject combinations
        foreach (Permission::ADMIN_PERMISSIONS as $permissionArray) {
            $perm = Permission::firstOrCreate([
                'action' => $permissionArray['ACTION'],
                'subject' => $permissionArray['SUBJECT']
            ]);

            // 3. Link the capability structurally to the admin matrix
            RoleHasPermissions::firstOrCreate([
                'role_id' => $role->id,
                'permission_id' => $perm->id
            ]);
        }
    }
}
