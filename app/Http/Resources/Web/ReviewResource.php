<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->review,
            'rating' => $this->rating,
            'master_id' => $this->master_id,
        ];
    }
}
