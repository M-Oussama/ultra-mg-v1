<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "IUser",
    required: ["id", "name", "email", "password", "created_at", "updated_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: "1"),
        new OA\Property(property: "name", type: "string", example: "jon"),
        new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        new OA\Property(property: "password", type: "string", example: "password"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2023-08-13"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2023-08-13"),
    ]
)]

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'organization_id',
        'fcm_token',
        'fcm_platform',
        'fcm_device_name',
        'fcm_token_updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fcm_token_updated_at' => 'datetime',
    ];

    protected $with = ['role', 'departments'];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function cashbooks()
    {
        return $this->hasMany(Cashbook::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    /**
     * Check if user is an administrator or director (Global Access).
     */
    public function isGlobalAdmin(): bool
    {
        // Emergency Fallback: Ensure the primary admin always has access
        if ($this->email === 'admin@gmail.com') return true;
        if ((int) ($this->role_id ?? 0) === Role::ADMIN) return true;

        if (!$this->role) return false;
        $role = strtolower(trim($this->role->role));
        return in_array($role, ['admin', 'super-admin', 'super admin', 'director']);
    }

    /**
     * Check if user is a department manager (Departmental Access).
     */
    public function isDepartmentManager(): bool
    {
        if (!$this->role) return false;
        return str_contains(strtolower($this->role->role), 'manager');
    }

    /**
     * Check if user is a salesperson (Individual Access).
     */
    public function isSalesperson(): bool
    {
        if (!$this->role) return false;
        return str_contains(strtolower($this->role->role), 'sale');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $action, string $subject): bool
    {
        if ($this->isGlobalAdmin()) return true;
        if (!$this->role) return false;

        return $this->role->permissions()
            ->where('action', $action)
            ->where('subject', $subject)
            ->exists();
    }
}
