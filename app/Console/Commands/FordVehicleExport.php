<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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

    function convertArrayToCsvString(array $fields): ?string
    {
        $f = fopen('php://memory', 'r+');
        if (fputcsv($f, $fields) === false) {
            return null;
        }
        rewind($f);
        $csv_line = stream_get_contents($f);
        return rtrim($csv_line);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->output->title('Starting export');

        $path = sprintf('%s.csv', now()->format('Y-m-d H:i:s'));
        $disk = Storage::disk('ford-export');

        $headers = [
            'Registration',
            'Car Title',
            'Price exc vat',
            'Vat on vehicle',
            'Image'
        ];

        $disk->put($path, $this->convertArrayToCsvString($headers));


        Vehicle::byMake('Ford')
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
            ->each(function ($item) use ($path, $disk) {
                $disk->append($path, $this->convertArrayToCsvString($item));
            });

        $this->output->success('Export successful');
        return 0;
    }
}
