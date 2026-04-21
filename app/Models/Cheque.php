<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ICheque",
    required: ["id", "cheque_date", "cheque_number", "client_id", "created_at", "updated_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "cheque_date", type: "string", format: "date", example: "2024-02-12"),
        new OA\Property(property: "cheque_number", type: "string", example: "CHQ-123456"),
        new OA\Property(property: "client_id", type: "integer", example: 1),
        new OA\Property(property: "file_path", type: "string", nullable: true, example: "cheques/scans/cheque_1.pdf"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-02-12T10:00:00Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2024-02-12T10:00:00Z"),
    ]
)]
class Cheque extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cheque_date',
        'cheque_number',
        'client_id',
        'amount',
        'file_path',
        'banque',
    ];

    public function client()
    {
        return $this->belongsTo(CertifyClient::class, 'client_id');
    }
}
