<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestOtpResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'success' => $this->resource['success'] ?? false,
            'message' => $this->resource['message'] ?? null,
        ];
    }
}
