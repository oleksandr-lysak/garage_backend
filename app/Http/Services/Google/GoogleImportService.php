<?php

namespace App\Http\Services\Google;

use App\Helpers\AutomotiveServiceClassifier;
use App\Helpers\PhoneHelper;
use App\Http\Services\ClientService;
use App\Http\Services\Master\MasterService;
use App\Http\Services\TelegramService;
use Illuminate\Support\Facades\DB;
use App\Jobs\ImportGooglePlacesJob;

class GoogleImportService
{
    private GooglePlacesService $googlePlacesService;

    private MasterService $masterService;

    private ClientService $clientService;

    private TelegramService $telegramService;

    public function __construct(
        GooglePlacesService $googlePlacesService,
        MasterService $masterService,
        ClientService $clientService,
        TelegramService $telegramService
    ) {
        $this->googlePlacesService = $googlePlacesService;
        $this->masterService       = $masterService;
        $this->clientService       = $clientService;
        $this->telegramService     = $telegramService;
    }

    public function queueImport(int $serviceId, ?int $limit = null): void
    {
        ImportGooglePlacesJob::dispatch($serviceId, $limit);
    }

    /**
     * Run Google Places import synchronously.
     *
     * @param int $serviceId service id to import for; 0 means auto-detect per place
     * @param int|null $limit max number of masters to import; null or 0 means no limit
     * @param callable|null $onProgress optional callback invoked on each processed place
     *                                  signature: function(array $context): void
     *                                  where $context has keys: imported, skipped, processed
     * @return array{imported:int, skipped:int}
     */
    public function performImport(int $serviceId, ?int $limit = null, ?callable $onProgress = null): array
    {
        $this->telegramService->send('Start Importing Google Places... \n Time: ' . now()->format('Y-m-d H:i:s'));

        $imported = 0;
        $skipped  = 0;
        $max      = $limit ?? 0;

        // Define bounding boxes for Kyiv and suburbs
        $areas = [
            // Kyiv core
            ['name' => 'Kyiv', 'latStart' => 50.20, 'latEnd' => 50.60, 'lngStart' => 30.20, 'lngEnd' => 30.90],
            // Irpin / Bucha / Hostomel
            ['name' => 'Irpin-Bucha-Hostomel', 'latStart' => 50.43, 'latEnd' => 50.62, 'lngStart' => 30.10, 'lngEnd' => 30.35],
            // Vyshhorod
            ['name' => 'Vyshhorod', 'latStart' => 50.50, 'latEnd' => 50.65, 'lngStart' => 30.40, 'lngEnd' => 30.65],
            // Brovary
            ['name' => 'Brovary', 'latStart' => 50.45, 'latEnd' => 50.58, 'lngStart' => 30.70, 'lngEnd' => 31.05],
            // Boryspil
            ['name' => 'Boryspil', 'latStart' => 50.28, 'latEnd' => 50.40, 'lngStart' => 30.86, 'lngEnd' => 31.20],
            // Vyshneve / Boyarka
            ['name' => 'Vyshneve-Boyarka', 'latStart' => 50.30, 'latEnd' => 50.42, 'lngStart' => 30.15, 'lngEnd' => 30.40],
        ];

        $latStep = 0.025; // ≈ 2.7 km
        $lngStep = 0.04;  // ≈ 2.8 km

        foreach ($areas as $area) {
            for ($lat = $area['latStart']; $lat <= $area['latEnd']; $lat += $latStep) {
                for ($lng = $area['lngStart']; $lng <= $area['lngEnd']; $lng += $lngStep) {
                    $token = null; // reset pagination for each coordinate

                    do {
                        // Get service type name for Google Places API
                        $serviceType = null;
                        if ($serviceId > 0) {
                            $service = \App\Models\Service::find($serviceId);
                            $serviceType = $service ? $service->name : null;
                        }

                        $data    = $this->googlePlacesService->fetch($token, $lat, $lng, 2500, $serviceType);
                        $results = $data['results'] ?? [];
                        $token   = $data['next_page_token'] ?? null;

                        foreach ($results as $place) {
                            try {
                                // Fetch detailed info to get phone number
                                $detailsUk = $this->googlePlacesService->details($place['place_id'], 'uk');
                                $detailsEn = [];
                                if (empty($detailsUk['editorial_summary']['overview'])) {
                                    $detailsEn = $this->googlePlacesService->details($place['place_id'], 'en');
                                }
                                $details = array_filter(array_replace_recursive($detailsUk ?? [], $detailsEn ?? []));
                                $phone   = $details['formatted_phone_number'] ?? $details['international_phone_number'] ?? null;

                                // Skip places without phone number
                                if (empty($phone)) {
                                    $skipped++;
                                    if ($onProgress) {
                                        $onProgress(['imported' => $imported, 'skipped' => $skipped, 'processed' => $imported + $skipped]);
                                    }
                                    continue;
                                }

                                // Accept only mobile phone numbers
                                if (! PhoneHelper::isMobile($phone)) {
                                    $skipped++;
                                    if ($onProgress) {
                                        $onProgress(['imported' => $imported, 'skipped' => $skipped, 'processed' => $imported + $skipped]);
                                    }
                                    continue;
                                }

                                $detectedServiceId = $serviceId == 0
                                    ? AutomotiveServiceClassifier::guessServiceId($place['name'] ?? '', $details['types'] ?? [])
                                    : $serviceId;

                                if (! $detectedServiceId) {
                                    $skipped++;
                                    if ($onProgress) {
                                        $onProgress(['imported' => $imported, 'skipped' => $skipped, 'processed' => $imported + $skipped]);
                                    }
                                    continue;
                                }

                                $description = $details['editorial_summary']['overview']
                                    ?? ($detailsEn['editorial_summary']['overview'] ?? null)
                                    ?? null;
                                if (! $description) {
                                    $name = $place['name'] ?? '';
                                    $addr = $details['formatted_address'] ?? $place['vicinity'] ?? '';
                                    $description = trim($name . ' — ' . $addr);
                                }

                                $dto = [
                                    'name'        => $place['name'] ?? 'No name',
                                    'phone'       => $phone,
                                    'address'     => $details['formatted_address'] ?? $place['vicinity'] ?? null,
                                    'description' => $description,
                                    'coordinates' => [
                                        'lat' => $place['geometry']['location']['lat'] ?? null,
                                        'lng' => $place['geometry']['location']['lng'] ?? null,
                                    ],
                                    'main_photo'  => isset($place['photos'][0]['photo_reference'])
                                        ? $this->googlePlacesService->photoUrl($place['photos'][0]['photo_reference'])
                                        : null,
                                    'reviews'          => [], // Google NearbySearch does not return reviews; left empty
                                    'place_id'         => $place['place_id'],
                                    'rating_google'    => $place['rating'] ?? null,
                                ];

                                DB::beginTransaction();
                                $this->masterService->importFromExternal($detectedServiceId, $dto, $this->clientService);
                                DB::commit();
                                $imported++;

                                if ($onProgress) {
                                    $onProgress(['imported' => $imported, 'skipped' => $skipped, 'processed' => $imported + $skipped]);
                                }

                                // Stop if we reached requested limit
                                if ($max > 0 && $imported >= $max) {
                                    // Finish processing immediately
                                    $token = null; // prevent further paging
                                    break 3; // exit foreach areas, for lng and do-while
                                }
                            } catch (\Throwable $e) {
                                DB::rollBack();
                                $skipped++;
                                if ($onProgress) {
                                    $onProgress(['imported' => $imported, 'skipped' => $skipped, 'processed' => $imported + $skipped]);
                                }
                            }
                        }

                        if ($token) {
                            sleep(2); // per Google requirement between paginated requests
                        }

                    } while ($token && ($max === 0 || $imported < $max));

                    // Break outer loops if limit reached
                    if ($max > 0 && $imported >= $max) {
                        break 2;
                    }
                }
            }

            // If limit reached after finishing an area
            if ($max > 0 && $imported >= $max) {
                break;
            }
        }

        $this->telegramService->send('Imported ' . $imported . ' places, skipped ' . $skipped . ' places \n Time: ' . now()->format('Y-m-d H:i:s'));

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
