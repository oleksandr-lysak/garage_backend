<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class SubmitReviewResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'success' => $this->resource['success'] ?? false,
            'message' => $this->resource['message'] ?? null,
            'review' => isset($this->resource['review']) ? new ReviewResource($this->resource['review']) : null,
        ];
    }
}
