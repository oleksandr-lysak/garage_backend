<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class AdminMasterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photoUrl = null;
        if ($this->photo) {
            $photoUrl = Str::startsWith($this->photo, ['http://', 'https://'])
                ? (string) $this->photo
                : url('storage/'.$this->photo);
        }

        $usesSystem = (int) $this->user_id !== 1;
        $lastLoginAt = $usesSystem && $this->user ? $this->user->last_login_at : null;

        // Average rating from withAvg('reviews', 'rating') alias
        $avgRating = $this->reviews_avg_rating !== null
            ? round((float) $this->reviews_avg_rating, 1)
            : null;

        return [
            'id' => (int) $this->id,
            'user_id' => $this->user_id,
            'uses_system' => $usesSystem,
            'last_login_at' => $lastLoginAt,
            'reviews_avg_rating' => $avgRating,
            'name' => (string) $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'age' => (int) $this->age,
            'phone' => $this->phone,
            'available' => (bool) $this->available,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'reviews_count' => (int) $this->reviews_count,
            'main_photo' => $photoUrl,
            'service_id' => $this->service_id,
            'tariff_id' => $this->tariff_id,
            'slug' => (string) $this->slug,
            'services' => $this->whenLoaded('services', function () {
                return $this->services->map(fn ($s) => [
                    'id' => (int) $s->id,
                    'name' => (string) $s->name,
                ]);
            }),
        ];
    }
}
