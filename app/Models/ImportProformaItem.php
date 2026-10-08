<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportProformaItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'import_proforma_id',
        'product_id',
        'product_name',
        'reference',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'double',
        'total_price' => 'double',
    ];

    public function proforma()
    {
        return $this->belongsTo(ImportProforma::class, 'import_proforma_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
