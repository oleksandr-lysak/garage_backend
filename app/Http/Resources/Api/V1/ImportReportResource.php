<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ImportReportResource Class
 *
 * Presents statistics about an import operation.
 */
class ImportReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'imported' => (int) ($this['imported'] ?? 0),
            'skipped'  => (int) ($this['skipped'] ?? 0),
        ];
    }
}
