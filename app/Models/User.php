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
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['role'];

    public function role() {
        return $this->belongsTo(Role::class);
    }
}
