<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role'
    ];


    const ADMIN = 1 ;


    protected $with = ['permissions'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');

    }

    // Accessor to get remaining permissions
    public function getRemainingPermissionsAttribute()
    {
        // Get all permissions
        $allPermissions = Permission::all();

        // Get current permissions associated with the role
        $currentPermissions = $this->permissions;

        // Calculate remaining permissions
        return $allPermissions->diff($currentPermissions);
    }

    // Specify that the remaining permissions should be included in the JSON output
    protected $appends = ['remaining_permissions'];
}
