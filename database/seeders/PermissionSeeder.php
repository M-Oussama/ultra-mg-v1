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
        $role = Role::firstOrCreate([
            'role' => 'admin',
        ]);

        foreach (Permission::ADMIN_PERMISSIONS as $permission) {
            $perm = Permission::firstOrCreate([
                'action' => $permission['ACTION'],
                'subject' => $permission['SUBJECT']
            ]);

            RoleHasPermissions::firstOrCreate([
                'role_id' => $role->id,
                'permission_id' => $perm->id
            ]);
        }

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'ADMIN',
                'password' => bcrypt('ultraOussama141998'),
                'role_id' => $role->id
            ]
        );
    }
}
