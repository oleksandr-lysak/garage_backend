<?php

namespace App\Http\Services\Google;

use App\Jobs\ImportGooglePlacesJob;

class GoogleImportService
{
    public function queueImport(int $serviceId, ?int $limit = null): void
    {
        ImportGooglePlacesJob::dispatch($serviceId, $limit);
    }
}
