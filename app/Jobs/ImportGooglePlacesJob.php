<?php

namespace App\Jobs;

use App\Helpers\PhoneHelper;
use App\Helpers\AutomotiveServiceClassifier;
use App\Http\Services\Google\GooglePlacesService;
use App\Http\Services\Master\MasterService;
use App\Http\Services\ClientService;
use App\Http\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportGooglePlacesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Disable timeout for this job.
     * A value of 0 means the job can run indefinitely.
     */
    public int $timeout = 0;

    private int $serviceId;

    private ?int $limit;

    /**
     * Create a new job instance.
     */
    public function __construct(int $serviceId, ?int $limit = null)
    {
        $this->serviceId = $serviceId;
        $this->limit     = $limit;
    }

    /**
     * Execute the job.
     */
    public function handle(
        GooglePlacesService $gps,
        MasterService $masterService,
        ClientService $clientService
    ): void {

        $telegramService = new TelegramService();
        $telegramService->send('Start Importing Google Places... \n Time: ' . now()->format('Y-m-d H:i:s'));
        $imported = 0;
        $skipped  = 0;
        $limit    = $this->limit ?? 0;

        // --- Generate grid coordinates for Kyiv ---
        $gridLatStart = 50.30;
        $gridLatEnd   = 50.55;
        $gridLngStart = 30.30;
        $gridLngEnd   = 30.80;

        $latStep = 0.025; // ≈ 2.7 km
        $lngStep = 0.04;  // ≈ 2.8 km

        for ($lat = $gridLatStart; $lat <= $gridLatEnd; $lat += $latStep) {
            for ($lng = $gridLngStart; $lng <= $gridLngEnd; $lng += $lngStep) {
                $token = null; // reset pagination for each coordinate

                do {
                    // Get service type name for Google Places API
                    $serviceType = null;
                    if ($this->serviceId > 0) {
                        $service = \App\Models\Service::find($this->serviceId);
                        $serviceType = $service ? $service->name : null;
                    }

                    $data    = $gps->fetch($token, $lat, $lng, 2500, $serviceType);
                    $results = $data['results'] ?? [];
                    $token   = $data['next_page_token'] ?? null;

                    foreach ($results as $place) {
                        try {
                            // Fetch detailed info to get phone number
                            $details = $gps->details($place['place_id']);
                            $phone   = $details['formatted_phone_number'] ?? $details['international_phone_number'] ?? null;

                            // Skip places without phone number
                            if (empty($phone)) {
                                $skipped++;
                                continue;
                            }

                            // Accept only mobile phone numbers
                            if (! PhoneHelper::isMobile($phone)) {
                                $skipped++;
                                continue;
                            }

                            $detectedServiceId = $this->serviceId == 0
                                ? AutomotiveServiceClassifier::guessServiceId($place['name'] ?? '', $details['types'] ?? [])
                                : $this->serviceId;

                            if (! $detectedServiceId) {
                                $skipped++;
                                continue;
                            }

                            $dto = [
                                'name'        => $place['name'] ?? 'No name',
                                'phone'       => $phone,
                                'address'     => $details['formatted_address'] ?? $place['vicinity'] ?? null,
                                'description' => 'Imported from Google Places',
                                'coordinates' => [
                                    'lat' => $place['geometry']['location']['lat'] ?? null,
                                    'lng' => $place['geometry']['location']['lng'] ?? null,
                                ],
                                'main_photo'  => isset($place['photos'][0]['photo_reference'])
                                    ? $gps->photoUrl($place['photos'][0]['photo_reference'])
                                    : null,
                                'reviews'          => [], // Google NearbySearch does not return reviews; left empty
                                'place_id'         => $place['place_id'],
                                'rating_google'    => $place['rating'] ?? null,
                            ];

                            DB::beginTransaction();
                            $masterService->importFromExternal($detectedServiceId, $dto, $clientService);
                            DB::commit();
                            $imported++;

                            // Stop if we reached requested limit
                            if ($limit > 0 && $imported >= $limit) {
                                // Finish processing immediately
                                $token = null; // prevent further paging
                                break 2; // exit both foreach and do-while
                            }
                        } catch (\Throwable $e) {
                            DB::rollBack();
                            $skipped++;
                        }
                    }

                    if ($token) {
                        sleep(2); // per Google requirement between paginated requests
                    }

                } while ($token && ($limit === 0 || $imported < $limit));

                // Break outer loops if limit reached
                if ($limit > 0 && $imported >= $limit) {
                    break 2;
                }
            }
        }

        $telegramService->send('Imported ' . $imported . ' places, skipped ' . $skipped . ' places \n Time: ' . now()->format('Y-m-d H:i:s'));
    }
}
