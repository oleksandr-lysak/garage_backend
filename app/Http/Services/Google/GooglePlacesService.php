<?php

namespace App\Http\Services\Google;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    private const ENDPOINT_NEARBY = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json';
    private const ENDPOINT_DETAILS = 'https://maps.googleapis.com/maps/api/place/details/json';

    /**
     * Fetch automotive service places in Kyiv.
     *
     * @param string|null $pageToken token for next page
     * @param float $lat latitude of the location
     * @param float $lng longitude of the location
     * @param int $radius radius in metres
     * @param string|null $serviceType specific service type to search for
     * @return array{results: array<int,array>, next_page_token?: string}
     */
    public function fetch(string $pageToken = null, float $lat = 50.4501, float $lng = 30.5234, int $radius = 2500, ?string $serviceType = null): array
    {
        $params = [
            'key'       => config('services.google_places.key'),
            'location'  => $lat.','.$lng,
            'radius'    => $radius, // metres; default +-2.5 km
            'language'  => 'uk',
        ];

        // Set appropriate type based on service
        if ($serviceType) {
            $params['type'] = $this->getGooglePlacesType($serviceType);
        } else {
            // Default to car_repair for general automotive services
            $params['type'] = 'car_repair';
        }

        if ($pageToken) {
            $params['pagetoken'] = $pageToken;
        }

        $response = Http::retry(3, 200)->get(self::ENDPOINT_NEARBY, $params);
        $response->throw();

        return $response->json();
    }

    /**
     * Get Google Places API type based on our service type.
     */
    private function getGooglePlacesType(?string $serviceType): string
    {
        return match ($serviceType) {
            'tire_service', 'tire_balancing', 'tire_alignment' => 'car_repair',
            'car_service', 'car_repair', 'engine_repair', 'transmission_repair',
            'electrical_repair', 'diagnostics', 'oil_change' => 'car_repair',
            'car_glass' => 'car_repair',
            'car_audio', 'car_alarm' => 'car_repair',
            'car_painting', 'car_body_repair' => 'car_repair',
            'car_air_conditioning' => 'car_repair',
            default => 'car_repair',
        };
    }

    /**
     * Fetch detailed information for a place (needed to get phone number).
     *
     * @param string $placeId
     * @return array<string, mixed>
     */
    public function details(string $placeId): array
    {
        $params = [
            'key'      => config('services.google_places.key'),
            'place_id' => $placeId,
            // Request only fields we really need to minimise quota consumption
            'fields'   => 'formatted_phone_number,international_phone_number,formatted_address,geometry,name,website,types',
            'language' => 'uk',
        ];

        $response = Http::retry(3, 200)->get(self::ENDPOINT_DETAILS, $params);
        $response->throw();

        return $response->json('result') ?? [];
    }

    /**
     * Build a public photo URL for a given photo reference.
     */
    public function photoUrl(string $photoReference, int $maxWidth = 400): string
    {
        return sprintf('https://maps.googleapis.com/maps/api/place/photo?maxwidth=%d&photo_reference=%s&key=%s',
            $maxWidth,
            $photoReference,
            config('services.google_places.key')
        );
    }
}
