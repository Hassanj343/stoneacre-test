<?php

namespace App\Jobs;

use App\Mail\ImportCompletedMail;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class VehicleImportCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $batch;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to('admin@example.com')
            ->send(
                new ImportCompletedMail(
                    $this->batch['totalJobs'],
                    $this->batch['processedJobs'],
                    $this->batch['failedJobs']
                )
            );
    }
}
