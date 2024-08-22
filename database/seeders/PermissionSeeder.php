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
       $role = Role::create([
            'role'=> 'admin',
        ]);

        foreach (Permission::ADMIN_PERMISSIONS as $permission) {
            $permission = Permission::create([
                'action'=> $permission['ACTION'],
                'subject'=> $permission['SUBJECT']
            ]);

            RoleHasPermissions::create([
                'role_id' => $role->id,
                'permission_id' => $permission->id
            ]);
        }

        $user = User::create([
            'name'=> 'ADMIN',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('ultraOussama141998'),
            'role_id' => $role->id
        ]);




    }
}
