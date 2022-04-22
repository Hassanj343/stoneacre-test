<?php

namespace App\Console\Commands;

use App\Jobs\ImportVehicleJob;
use App\Services\VehicleService;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Throwable;
use App\Jobs\VehicleImportCompleted;
use Illuminate\Support\Facades\Log;

class ImportVehicles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stoneacre:import-vehicles {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports vehicle data from csv';

    private VehicleService $vehicleService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
        parent::__construct();
    }



    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->output->title('Starting import');
        $file = $this->argument('file');

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function (Batch $b) {
                VehicleImportCompleted::dispatch($b->toArray());
                Log::debug('Import completed');
            })->dispatch();


        if (($handle = fopen($file, "r")) !== false) {
            $headers = collect(fgetcsv($handle, 1000, ","))
                ->map(fn ($item) => Str::of($item)->lower()->snake())
                ->toArray();
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                $data = array_combine($headers, $row);

                $batch->add([
                    new ImportVehicleJob($data)
                ]);
            }
            fclose($handle);
        }

        $this->output->success('Import successful');
    }
}
