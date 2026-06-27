<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Attributes as OA;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[OA\Schema(schema: "ICertifyClient",
    required: ["id", "name", "surname", "email", "address", "NRC", "NIF", "NART", "NIS", "created_at", "updated_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: "1"),
        new OA\Property(property: "name", type: "string", example: "jon"),
        new OA\Property(property: "surname", type: "string", example: "jon"),
        new OA\Property(property: "profession", type: "string", example: "Trader"),
        new OA\Property(property: "email", type: "string", example: "doe@gmail.com"),
        new OA\Property(property: "address", type: "string", example: "address"),
        new OA\Property(property: "NRC", type: "string", example: "NRC"),
        new OA\Property(property: "is_cnrc_active", type: "boolean", example: true),
        new OA\Property(property: "NIF", type: "string", example: "NIF"),
        new OA\Property(property: "is_nif_active", type: "boolean", example: true),
        new OA\Property(property: "is_sub_certify", type: "boolean", example: false),
        new OA\Property(property: "NART", type: "string", example: "NART"),
        new OA\Property(property: "NIS", type: "string", example: "NIS"),
        new OA\Property(property: "pdf_file", type: "string", example: "https://example.com/storage/1/certify-client.pdf"),
        new OA\Property(property: "nif_file", type: "string", example: "https://example.com/storage/2/nif.jpg"),
        new OA\Property(property: "cnrc_file", type: "string", example: "https://example.com/storage/3/cnrc.jpg"),
        new OA\Property(property: "has_pdf_file", type: "boolean", example: true),
        new OA\Property(property: "has_nif_file", type: "boolean", example: true),
        new OA\Property(property: "has_cnrc_file", type: "boolean", example: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2023-08-13"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2023-08-13"),
    ]
)]

class CertifyClient extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'certify_clients';

    protected $with = ['city'];

    protected $hidden = ['media'];

    protected $casts = [
        'is_cnrc_active' => 'boolean',
        'is_nif_active' => 'boolean',
        'is_sub_certify' => 'boolean',
    ];

    protected $appends = [
        'pdf_file',
        'nif_file',
        'cnrc_file',
        'has_pdf_file',
        'has_nif_file',
        'has_cnrc_file',
    ];

    protected $fillable = [
        'name',
        'surname',
        'profession',
        'address',
        'phone',
        'NRC',
        'is_cnrc_active',
        'NIF',
        'is_nif_active',
        'is_sub_certify',
        'NIS',
        'NART',
        'email',
        'city_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function invoices()
    {
        return $this->hasMany(CertifyInvoices::class, 'client_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdf_file')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);

        $this->addMediaCollection('nif_file')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/bmp',
                'image/tiff',
                'image/heic',
                'image/heif',
            ]);

        $this->addMediaCollection('cnrc_file')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/bmp',
                'image/tiff',
                'image/heic',
                'image/heif',
            ]);
    }

    public function getPdfFileAttribute(): ?string
    {
        $media = $this->getFirstMedia('pdf_file');

        return $media ? $media->getFullUrl() : null;
    }

    public function getNifFileAttribute(): ?string
    {
        if (! $this->is_nif_active) {
            return null;
        }

        $media = $this->getFirstMedia('nif_file');

        return $media ? $media->getFullUrl() : null;
    }

    public function getCnrcFileAttribute(): ?string
    {
        if (! $this->is_cnrc_active) {
            return null;
        }

        $media = $this->getFirstMedia('cnrc_file');

        return $media ? $media->getFullUrl() : null;
    }

    public function getHasPdfFileAttribute(): bool
    {
        return $this->getFirstMedia('pdf_file') !== null;
    }

    public function getHasNifFileAttribute(): bool
    {
        return $this->is_nif_active && $this->getFirstMedia('nif_file') !== null;
    }

    public function getHasCnrcFileAttribute(): bool
    {
        return $this->is_cnrc_active && $this->getFirstMedia('cnrc_file') !== null;
    }
}
