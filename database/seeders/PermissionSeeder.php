<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermissions;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Setup Admin Role
        $adminRole = Role::firstOrCreate(['role' => 'admin']);
        foreach (Permission::ADMIN_PERMISSIONS as $permission) {
            $perm = Permission::firstOrCreate([
                'action' => $permission['ACTION'],
                'subject' => $permission['SUBJECT']
            ]);

            RoleHasPermissions::firstOrCreate([
                'role_id' => $adminRole->id,
                'permission_id' => $perm->id
            ]);
        }

        // 2. Setup Sales Role (Crucial for unblocking Sales users)
        $salesRole = Role::firstOrCreate(['role' => 'Sales']);
        foreach (Permission::SALES_PERMISSIONS as $permission) {
            $perm = Permission::firstOrCreate([
                'action' => $permission['ACTION'],
                'subject' => $permission['SUBJECT']
            ]);

            RoleHasPermissions::firstOrCreate([
                'role_id' => $salesRole->id,
                'permission_id' => $perm->id
            ]);
        }

        // 3. Sync Root Admin User
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'ADMIN',
                'password' => bcrypt('ultraOussama141998'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
