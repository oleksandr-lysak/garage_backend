<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class JobQueuedResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status'  => 'queued',
            'message' => $this->resource['message'] ?? 'The import job has been queued successfully.'
        ];
    }
}
