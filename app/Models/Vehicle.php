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

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function colour(): HasOne
    {
        return $this->hasOne(VehicleColour::class);
    }

    public function type(): HasOne
    {
        return $this->hasOne(VehicleType::class);
    }

    public function make(): HasOne
    {
        return $this->hasOne(VehicleMake::class);
    }
}
