<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Services\Google\GoogleImportService;

class ImportGooglePlacesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Disable timeout for this job.
     * A value of 0 means the job can run indefinitely.
     */
    public int $timeout = 0;

    private int $serviceId;

    private ?int $limit;

    /**
     * Create a new job instance.
     */
    public function __construct(int $serviceId, ?int $limit = null)
    {
        $this->serviceId = $serviceId;
        $this->limit     = $limit;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleImportService $importService): void
    {
        $importService->performImport($this->serviceId, $this->limit);
    }
}
