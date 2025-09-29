<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterReviewsResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'success' => $this->resource['success'] ?? false,
            'reviews' => ReviewResource::collection($this->resource['reviews'] ?? [])
                ->collection
                ->toArray($request),
            'total' => $this->resource['total'] ?? 0,
        ];
    }
}
