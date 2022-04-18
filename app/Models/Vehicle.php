<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration',
        'make',
        'range',
        'model',
        'derivative',
        'price',
        'mileage',
        'date_on_forecourt',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImages::class);
    }

    public function colour(): HasOne
    {
        return $this->hasOne(VehicleColour::class);
    }

    public function type(): HasOne
    {
        return $this->hasOne(VehicleType::class);
    }
}
