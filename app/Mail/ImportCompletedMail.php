<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImportCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $totalImports;
    public int $successImports;
    public int $failedImports;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $totalImports, int $successImports, int $failedImports)
    {
        $this->totalImports = $totalImports;
        $this->successImports = $successImports;
        $this->failedImports = $failedImports;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.vehicle-import-completed');
    }
}
