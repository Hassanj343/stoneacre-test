<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Console\Command;

class FordVehicleExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stoneacre:export-ford';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports Ford vehicle data to ftp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function getCsvFile()
    {
        $name = sprintf('%s.csv', now()->format('Y-m-d H:i:s'));
        $this->output->info(sprintf('Writing csv file to: %s', $name));
        return fopen(storage_path($name), 'w');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->output->title('Starting export');

        $csv = $this->getCsvFile();

        $headers = [
            'Registration',
            'Car Title',
            'Price exc vat',
            'Vat on vehicle',
            'Image'
        ];

        fputcsv($csv, $headers);

        $data = Vehicle::byMake('Ford')
            ->with('make', 'images')
            ->get()
            ->map(function ($item) {
                return [
                    'reg' => $item->registration,
                    'title' => sprintf(
                        "%s %s %s",
                        $item->make->name,
                        $item->model,
                        $item->derivative
                    ),
                    'price_ex_vat' => $item->price_ex_vat,
                    'vat_amount' => $item->vat_price,
                    'image' => $item->images->first()->url,
                ];
            })

            ->each(function ($item) use ($csv) {
                fputcsv($csv, $item);
            });

        // push to ftp

        $this->output->success('Export successful');
        return 0;
    }
}
