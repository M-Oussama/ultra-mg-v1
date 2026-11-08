<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const NEW_PERMISSIONS = [
        'cash_in' => ['action' => 'cash_in', 'subject' => 'cashbook_entries'],
        'cash_out' => ['action' => 'cash_out', 'subject' => 'cashbook_entries'],
        'edit' => ['action' => 'edit', 'subject' => 'cashbook_entries'],
        'delete' => ['action' => 'delete', 'subject' => 'cashbook_entries'],
        'export' => ['action' => 'export', 'subject' => 'cashbook_entries'],
    ];

    public function up(): void
    {
        $newIds = [];
        foreach (self::NEW_PERMISSIONS as $key => $attributes) {
            $newIds[$key] = Permission::firstOrCreate($attributes)->id;
        }

        $legacyMappings = [
            ['legacy' => ['action' => 'add', 'subject' => 'transactions'], 'new' => ['cash_in', 'cash_out']],
            ['legacy' => ['action' => 'edit', 'subject' => 'transactions'], 'new' => ['edit']],
            ['legacy' => ['action' => 'delete', 'subject' => 'transactions'], 'new' => ['delete']],
            ['legacy' => ['action' => 'download', 'subject' => 'cashbooks'], 'new' => ['export']],
        ];

        foreach ($legacyMappings as $mapping) {
            $legacyId = Permission::query()->where($mapping['legacy'])->value('id');
            if (!$legacyId) {
                continue;
            }

            $roleIds = DB::table('role_has_permissions')
                ->where('permission_id', $legacyId)
                ->pluck('role_id');
            foreach ($roleIds as $roleId) {
                foreach ($mapping['new'] as $newKey) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'role_id' => $roleId,
                        'permission_id' => $newIds[$newKey],
                    ], ['updated_at' => now(), 'created_at' => now()]);
                }
            }

            if (Schema::hasTable('user_has_permissions')) {
                $userIds = DB::table('user_has_permissions')
                    ->where('permission_id', $legacyId)
                    ->pluck('user_id');
                foreach ($userIds as $userId) {
                    foreach ($mapping['new'] as $newKey) {
                        DB::table('user_has_permissions')->updateOrInsert([
                            'user_id' => $userId,
                            'permission_id' => $newIds[$newKey],
                        ], ['updated_at' => now(), 'created_at' => now()]);
                    }
                }
            }
        }

        $adminRole = Role::query()->whereRaw('LOWER(role) = ?', ['admin'])->first();
        if ($adminRole) {
            foreach ($newIds as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                ], ['updated_at' => now(), 'created_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        $permissionIds = Permission::query()
            ->where('subject', 'cashbook_entries')
            ->whereIn('action', ['cash_in', 'cash_out', 'edit', 'delete', 'export'])
            ->pluck('id');

        DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        if (Schema::hasTable('user_has_permissions')) {
            DB::table('user_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
        }
        Permission::query()->whereIn('id', $permissionIds)->delete();
    }
};
