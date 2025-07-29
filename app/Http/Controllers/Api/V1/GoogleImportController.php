<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportGooglePlacesRequest;
use App\Http\Resources\Api\V1\JobQueuedResource;
use App\Http\Services\Google\GoogleImportService;

class GoogleImportController extends Controller
{
    /**
     * Queue Google Places import job.
     */
    public function import(
        int $service_id,
        ImportGooglePlacesRequest $request,
        GoogleImportService $importService
    ): JobQueuedResource {
        $limit = $request->input('limit');

        // Queue the import job via service
        $importService->queueImport($service_id, $limit);

        // Return response indicating job has been queued
        return new JobQueuedResource(['message' => 'Import job has been queued']);
    }
}
