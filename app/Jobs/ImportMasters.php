<?php

namespace App\Jobs;

use App\Http\Services\Ratelist\RatelistImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImportMasters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;
    public $backoff = 60;

    public function __construct(
        private readonly string $jobId,
        private readonly int $serviceId,
        private readonly string $url,
        private readonly ?int $limit
    ) {}

    public function handle(RatelistImportService $importService): void
    {
        Log::info('Starting import job', [
            'job_id' => $this->jobId,
            'service_id' => $this->serviceId,
            'url' => $this->url,
            'limit' => $this->limit
        ]);

        try {
            $detailUrls = $importService->getDetailLinks($this->url);
            Log::info('Extracted detail URLs', [
                'job_id' => $this->jobId,
                'count' => count($detailUrls)
            ]);

            Cache::store('redis')->put(
                "import_progress_{$this->jobId}",
                [
                    'status' => 'running',
                    'imported' => 0,
                    'skipped' => 0,
                    'processed' => 0,
                    'error' => null,
                    'total_urls' => count($detailUrls)
                ],
                now()->addHour()
            );

            $result = $importService->performImport(
                $this->serviceId,
                $this->url,
                $this->limit,
                function (array $context) use ($detailUrls) {
                    Log::info('Import progress update', [
                        'job_id' => $this->jobId,
                        'context' => $context,
                        'total_urls' => count($detailUrls)
                    ]);

                    Cache::store('redis')->put(
                        "import_progress_{$this->jobId}",
                        [
                            'status' => 'running',
                            'imported' => (int) ($context['imported'] ?? 0),
                            'skipped' => (int) ($context['skipped'] ?? 0),
                            'processed' => (int) ($context['processed'] ?? 0),
                            'error' => null,
                            'total_urls' => count($detailUrls)
                        ],
                        now()->addHour()
                    );
                }
            );

            Log::info('Import completed', [
                'job_id' => $this->jobId,
                'result' => $result,
                'total_urls' => count($detailUrls)
            ]);

            Cache::store('redis')->put(
                "import_progress_{$this->jobId}",
                [
                    'status' => 'completed',
                    'imported' => (int) $result['imported'],
                    'skipped' => (int) $result['skipped'],
                    'processed' => (int) ($result['imported'] + $result['skipped']),
                    'error' => null,
                    'total_urls' => count($detailUrls)
                ],
                now()->addHour()
            );

        } catch (\Exception $e) {
            Log::error('Import failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage()
            ]);

            Cache::store('redis')->put(
                "import_progress_{$this->jobId}",
                [
                    'status' => 'error',
                    'imported' => 0,
                    'skipped' => 0,
                    'processed' => 0,
                    'error' => $e->getMessage(),
                    'total_urls' => 0
                ],
                now()->addHour()
            );

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Import job failed', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage()
        ]);

        Cache::store('redis')->put(
            "import_progress_{$this->jobId}",
            [
                'status' => 'error',
                'imported' => 0,
                'skipped' => 0,
                'processed' => 0,
                'error' => $exception->getMessage(),
                'total_urls' => 0
            ],
            now()->addHour()
        );
    }
}
