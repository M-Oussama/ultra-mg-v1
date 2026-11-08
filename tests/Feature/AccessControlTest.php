<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Cashbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_effective_abilities_merge_role_and_direct_permissions(): void
    {
        $role = Role::create(['role' => 'Owner View']);
        $rolePermission = Permission::create(['action' => 'list', 'subject' => 'sales']);
        $directPermission = Permission::create(['action' => 'list', 'subject' => 'benefits']);
        $role->permissions()->attach($rolePermission);

        $user = User::create([
            'name' => 'Restricted Owner',
            'email' => 'owner@example.test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);
        $user->permissions()->attach($directPermission);

        $user->load('role.permissions', 'permissions');
        $abilities = $user->effectivePermissions();

        $this->assertSame('Owner View', $user->role->role);
        $this->assertSame([$directPermission->id], $user->permissions->pluck('id')->all());
        $this->assertEqualsCanonicalizing(
            ['list_sales', 'list_benefits'],
            $abilities->map(fn (Permission $permission) => $permission->action . '_' . $permission->subject)->all()
        );
    }

    public function test_user_creation_persists_direct_permissions(): void
    {
        $adminRole = Role::create(['role' => 'admin']);
        $restrictedRole = Role::create(['role' => 'Restricted']);
        $permission = Permission::create(['action' => 'list', 'subject' => 'benefits']);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('secret123'),
            'role_id' => $adminRole->id,
        ]);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/users/store', [
            'name' => 'Direct Grant User',
            'email' => 'direct@example.test',
            'password' => 'secret123',
            'role_id' => $restrictedRole->id,
            'permissions' => [['id' => $permission->id]],
        ]);

        $response->assertOk();
        $user = User::where('email', 'direct@example.test')->firstOrFail();
        $this->assertTrue($user->permissions()->whereKey($permission->id)->exists());
        $this->assertTrue($user->hasPermission('list', 'benefits'));
    }

    public function test_department_assignments_scope_employee_visibility_for_any_role(): void
    {
        Role::create(['role' => 'admin']);
        $visibleDepartment = Department::create(['name' => 'Visible Department']);
        $hiddenDepartment = Department::create(['name' => 'Hidden Department']);
        $role = Role::create(['role' => 'Custom Viewer']);
        $permission = Permission::create(['action' => 'list', 'subject' => 'employees']);
        $role->permissions()->attach($permission);

        $user = User::create([
            'name' => 'Department Viewer',
            'email' => 'department-viewer@example.test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);
        $user->departments()->attach($visibleDepartment);

        Employee::create([
            'name' => 'Visible Employee',
            'department_id' => $visibleDepartment->id,
        ]);
        Employee::create([
            'name' => 'Hidden Employee',
            'department_id' => $hiddenDepartment->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/employees/list?perPage=50');

        $response->assertOk();
        $response->assertJsonFragment(['name' => 'Visible Employee']);
        $response->assertJsonMissing(['name' => 'Hidden Employee']);
        $this->assertTrue($user->hasAssignedDepartments());
        $this->assertTrue($user->canAccessDepartment($visibleDepartment->id));
        $this->assertFalse($user->canAccessDepartment($hiddenDepartment->id));
    }

    public function test_granular_cash_permissions_control_each_entry_action(): void
    {
        Role::create(['role' => 'admin']);
        $role = Role::create(['role' => 'Cash In Operator']);
        foreach ([
            ['list', 'cashbooks'],
            ['view', 'cashbooks'],
            ['list', 'transactions'],
            ['cash_in', 'cashbook_entries'],
            ['edit', 'cashbook_entries'],
            ['delete', 'cashbook_entries'],
            ['export', 'cashbook_entries'],
        ] as [$action, $subject]) {
            $permission = Permission::create(compact('action', 'subject'));
            $role->permissions()->attach($permission);
        }

        $user = User::create([
            'name' => 'Cash Operator',
            'email' => 'cash-operator@example.test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);
        $owner = User::create([
            'name' => 'Cashbook Owner',
            'email' => 'cashbook-owner@example.test',
            'password' => Hash::make('secret123'),
        ]);
        $cashbook = Cashbook::create([
            'name' => 'Operator Cashbook',
            'user_id' => $owner->id,
        ]);
        $cashbook->members()->attach($user->id, ['role' => 'viewer']);
        Sanctum::actingAs($user);

        $incomeResponse = $this->postJson("/api/cashbooks/{$cashbook->id}/transactions", [
            'type' => 'income',
            'amount' => 120,
            'transaction_date' => '2026-10-25',
        ]);
        $incomeResponse->assertCreated();
        $transactionId = $incomeResponse->json('transaction.id');

        $this->postJson("/api/cashbooks/{$cashbook->id}/transactions", [
            'type' => 'expense',
            'amount' => 20,
            'transaction_date' => '2026-10-25',
        ])->assertForbidden();

        $this->putJson("/api/transactions/{$transactionId}", [
            'amount' => 125,
        ])->assertOk()->assertJsonPath('transaction.amount', 125);

        $export = $this->get("/api/cashbooks/{$cashbook->id}/export-pdf");
        $export->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $export->headers->get('content-type'));

        $this->deleteJson("/api/transactions/{$transactionId}")->assertOk();
    }
}
