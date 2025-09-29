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
            'created_at' => $this->created_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                ];
            }),
            // backward compatibility if UI checks user_name
            'user_name' => $this->whenLoaded('user', fn () => $this->user->name),
        ];
    }
}
