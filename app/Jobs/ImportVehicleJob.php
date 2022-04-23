<?php

namespace App\Jobs;

use App\Exceptions\InvalidVehicleDataException;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImportVehicleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    private array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->mapData();
    }

    private function validate()
    {
        $validator = Validator::make($this->data, [
            'reg' => ['required'],
            'price' => ['numeric', 'min:1'],
            'images' => ['array', 'min:3']
        ]);

        if ($validator->fails()) {
            throw new InvalidVehicleDataException($this->data, $validator->errors()->all());
        }
    }

    private function mapData(): void
    {
        $row = $this->data;
        $price = (string) Str::of($row['price_inc_vat'])->replace(',', '');
        $images = collect(explode(',', $row['images']))
            ->reject(fn ($item) => !$item)
            ->toArray();
        $row['price'] = (float)$price;
        $row['images'] = $images;
        if ($row['date_on_forecourt'] === '0000-00-00') {
            $row['date_on_forecourt'] = null;
        }

        $this->data = $row;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(VehicleService $vehicleService)
    {
        $this->validate();


        $row = $this->data;


        $colour = $vehicleService->findOrNewColour($row['colour']);
        $type = $vehicleService->findOrNewType($row['vehicle_type']);
        $make = $vehicleService->findOrNewMake($row['make']);

        $images = $row['images'];

        $isActive = true;
        $carbonDateOnForecourt = Carbon::parse($row['date_on_forecourt']);

        if ($row['date_on_forecourt'] || $carbonDateOnForecourt->isFuture()) {
            $isActive = false;
        }


        $vehicle = new Vehicle([
            'registration' => $row['reg'],
            'range' => $row['range'],
            'model' => $row['model'],
            'derivative' => $row['derivative'],
            'price' => $row['price'],
            'mileage' => $row['mileage'],
            'date_on_forecourt' => $row['date_on_forecourt'],
            'type_id' => $type->id,
            'colour_id' => $colour->id,
            'make_id' => $make->id,
            'is_active' => $isActive
        ]);

        $vehicle->save();


        foreach ($images as $image) {
            $vehicle->images()->create([
                'url' => $image
            ]);
        }

        return $vehicle;
    }
}
