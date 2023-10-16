<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "IClient",
    required: ["id", "name", "surname", "email", "address", "NRC", "NIF", "NART", "NIS", "created_at", "updated_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: "1"),
        new OA\Property(property: "name", type: "string", example: "jon"),
        new OA\Property(property: "surname", type: "string", example: "jon"),
        new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        new OA\Property(property: "address", type: "string", example: "address"),
        new OA\Property(property: "NRC", type: "string", example: "NRC"),
        new OA\Property(property: "NIF", type: "string", example: "NIF"),
        new OA\Property(property: "NART", type: "string", example: "NART"),
        new OA\Property(property: "NIS", type: "string", example: "NIS"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2023-08-13"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2023-08-13"),
    ]
)]


class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'address',
        'phone',
        'NRC',
        'NIF',
        'NIS',
        'NART',
        'email',
    ];
}
