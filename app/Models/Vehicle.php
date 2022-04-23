<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration',
        'range',
        'model',
        'derivative',
        'price',
        'mileage',
        'date_on_forecourt',
        'type_id',
        'colour_id',
        'make_id',
        'is_active'
    ];

    protected $casts = [
        'price' => 'float'
    ];

    protected $appends = [
        'price_ex_vat', 'vat_price'
    ];

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function colour(): HasOne
    {
        return $this->hasOne(VehicleColour::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class);
    }

    public function getPriceExVatAttribute(): float
    {
        return round($this->price / 1.2, 2, PHP_ROUND_HALF_DOWN);
    }

    public function getVatPriceAttribute(): float
    {
        return $this->price - $this->getPriceExVatAttribute();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->whereHas('type', function ($q) use ($type) {
            $q->where('name', $type);
        });
    }

    public function scopeByColour($query, string $colour)
    {
        return $query->whereHas('colour', function ($q) use ($colour) {
            $q->where('name', $colour);
        });
    }

    public function scopeByMake($query, string $make)
    {
        return $query->whereHas('make', function ($q) use ($make) {
            $q->where('name', $make);
        });
    }
}
