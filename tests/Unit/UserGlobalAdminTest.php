<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class UserGlobalAdminTest extends TestCase
{
    public function test_admin_role_id_gets_global_access(): void
    {
        $user = new User();
        $user->role_id = Role::ADMIN;

        $this->assertTrue($user->isGlobalAdmin());
    }

    public function test_admin_role_name_with_whitespace_gets_global_access(): void
    {
        $user = new User();
        $user->role_id = 999;
        $user->setRelation('role', new Role(['role' => '  Admin  ']));

        $this->assertTrue($user->isGlobalAdmin());
    }

    public function test_non_admin_role_does_not_get_global_access(): void
    {
        $user = new User();
        $user->role_id = 999;
        $user->setRelation('role', new Role(['role' => 'Sales']));

        $this->assertFalse($user->isGlobalAdmin());
    }
}
