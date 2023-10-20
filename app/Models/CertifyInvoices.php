<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: "ICertifyInvoice",
    required: ["id", "client_id", "amount", "date", "fac_id", "payment_type", "created_at", "updated_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: "1"),
        new OA\Property(property: "client_id", type: "integer", example: "1"),
        new OA\Property(property: "amount", type: "number", example: "123"),
        new OA\Property(property: "date", type: "string", format: "date-time", example: "2023-08-13"),
        new OA\Property(property: "fac_id", type: "integer", example: "1"),
        new OA\Property(property: "payment_type", type: "integer", example: "1"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2023-08-13"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2023-08-13"),
    ]
)]

class CertifyInvoices extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'amount',
        'date',
        'payment_type',
        'fac_id'
    ];

    protected $with = [
      'client'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
