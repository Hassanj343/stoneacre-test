<?php

namespace App\Services;

use App\Models\VehicleColour;
use App\Models\VehicleMake;
use App\Models\VehicleType;

class VehicleService
{
    public function findOrNewColour(string $name)
    {
        return VehicleColour::firstOrCreate([
            'name' => $name,
        ]);
    }

    public function findOrNewType(string $name)
    {
        return VehicleType::firstOrCreate([
            'name' => $name,
        ]);
    }

    public function findOrNewMake(string $name)
    {
        return VehicleMake::firstOrCreate([
            'name' => $name,
        ]);
    }
}
