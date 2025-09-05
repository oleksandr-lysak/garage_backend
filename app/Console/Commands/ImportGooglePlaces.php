<?php

namespace App\Console\Commands;

use App\Http\Services\Google\GoogleImportService;
use Illuminate\Console\Command;

class ImportGooglePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masters:import-google {service_id : Service ID (0 for auto-detect)} {--limit= : Optional limit of masters to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import masters from Google Places for a service (0 for auto-detect). Shows progress.';

    /**
     * Execute the console command.
     */
    public function handle(GoogleImportService $importService): int
    {
        $serviceId = (int) $this->argument('service_id');
        $limitOpt  = $this->option('limit');
        $limit     = is_null($limitOpt) || $limitOpt === '' ? null : (int) $limitOpt;

        $this->info('Starting import from Google Places...');
        $this->line('Service ID: ' . $serviceId . '; Limit: ' . ($limit ?? 'no limit'));

        $processed = 0;
        $bar = $this->output->createProgressBar($limit ?? 1000);
        $bar->setBarWidth(50);
        $bar->start();

        $result = $importService->performImport($serviceId, $limit, function (array $context) use (&$processed, $bar, $limit) {
            $processed = $context['processed'] ?? $processed + 1;
            // Advance progress bar up to limit (if set) otherwise cap visually
            if ($limit) {
                $bar->setMaxSteps($limit);
                $bar->advance();
            } else {
                // For unknown total, just advance; Laravel handles overflow fine
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Imported: ' . $result['imported'] . '; Skipped: ' . $result['skipped']);

        return Command::SUCCESS;
    }
}
