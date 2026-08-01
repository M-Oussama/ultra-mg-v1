<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id',
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'unit_price',
        'total_price',
        'client_id',
        'sale_date',
        'package_type',
        'units_per_package',
        'package_quantity',
        'number_of_packages',
        'items_per_package',
        'price_active'
    ];
    protected $casts = [
        'price_active' => 'boolean',
        'quantity' => 'double',
        'price' => 'double',
        'total_price' => 'double',
        'units_per_package' => 'integer',
        'package_quantity' => 'integer',
        'number_of_packages' => 'integer',
        'items_per_package' => 'integer',
    ];
    protected $with = ['product'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    private function resolvedPackageType(): string
    {
        $type = trim((string) ($this->package_type ?? $this->product?->package_type ?? ''));

        return $type !== '' ? $type : '';
    }

    private function resolvedUnitsPerPackage(): int
    {
        return (int) ($this->units_per_package ?? $this->product?->units_per_package ?? 0);
    }

    public function hasPackaging(): bool
    {
        return $this->resolvedPackageType() !== '' && $this->resolvedUnitsPerPackage() > 0;
    }

    public function packageQuantity(): int
    {
        if (!$this->hasPackaging()) {
            return 0;
        }

        if (!is_null($this->package_quantity)) {
            return (int) $this->package_quantity;
        }

        return (int) floor(((float) $this->quantity) / $this->resolvedUnitsPerPackage());
    }

    public function packageRemainder(): int
    {
        if (!$this->hasPackaging()) {
            return 0;
        }

        return (int) $this->quantity % $this->resolvedUnitsPerPackage();
    }

    public function packagingLabel(): string
    {
        if (!$this->hasPackaging()) {
            return '';
        }

        $type = $this->resolvedPackageType();
        $type = $type !== '' ? $type : 'package';
        $packageSize = $this->resolvedUnitsPerPackage();
        $packages = $this->packageQuantity();
        $remainder = $this->packageRemainder();

        if ($remainder > 0) {
            return $packages . ' ' . $type . ' (' . $packageSize . ') + ' . $remainder . ' pieces';
        }

        return $packages . ' ' . $type . ' (' . $packageSize . ')';
    }
}


