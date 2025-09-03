<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MasterResource Class
 *
 * This class extends the JsonResource class provided by Laravel.
 * It is used to transform your resource into an array that can be returned as a JSON response.
 *
 * @property mixed $reviews
 * @property int $id
 * @property string $name
 * @property float $latitude
 * @property float $longitude
 * @property string $description
 * @property string $address
 * @property int $age
 * @property string $phone
 * @property mixed $services
 * @property string $photo
 * @property float $distance
 * @property int $main_service_id
 * @property bool $approved
 * @property int $reviews_count
 * @property float $rating
 * @property int $service_id
 * @property int $tariff_id
 * @property string $slug
 * @property string|null $tariff
 */
class MasterResource extends JsonResource
{
    protected array $availabilityMap;

    public function __construct($resource, array $availabilityMap = [])
    {
        parent::__construct($resource);
        $this->availabilityMap = $availabilityMap;
    }

    /**
     * Transform the resource into an array.
     *
     * This method is used to transform the `Master` model into an array that can be returned as a JSON response.
     * It calculates the average rating of the master based on the reviews and returns an array containing the master's details.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'description' => $this->description,
            'address' => $this->getFormattedAddress($this->address),
            'age' => (int) $this->age,
            'phone' => $this->phone,
            'reviews_count' => (int) $this->reviews_count,
            'rating' => (float) round($this->rating, 1),
            'main_photo' => (string) 'storage/'.$this->photo,
            'distance' => (float) round($this->distance, 3),
            'main_service_id' => (int) $this->service_id,
            'available' => array_key_exists($this->id, $this->availabilityMap)
                ? (bool) $this->availabilityMap[$this->id]
                : false,
            'approved' => isset($this->approved)
                ? (bool) $this->approved
                : (bool) ($this->user_id ?? 0),
            'tariff' => is_object($this->tariff) ? (string) $this->tariff->name : (string) $this->tariff,
            'tariff_id' => (int) $this->tariff_id,
            'slug' => (string) $this->slug,
        ];
    }

    private function getFormattedAddress($address)
    {
        // try {
        //     return json_decode($address)->results[0]->formatted_address ?? '';
        // } catch (\Exception $e) {
        return $address;
        // }
    }
}
