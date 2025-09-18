<?php

namespace App\Console\Commands;

use App\Http\Services\Ratelist\RatelistImportService;
use Illuminate\Console\Command;

class ImportRatelist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masters:import-ratelist {service_id : Service ID (0 for auto-detect)} {url : RateList rating URL} {--limit= : Optional limit of masters to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import masters from RateList rating page (e.g., https://ratelist.top/l/kyiv/rating-435). Shows progress.';

    /**
     * Execute the console command.
     */
    public function handle(RatelistImportService $importService): int
    {
        $serviceId = (int) $this->argument('service_id');
        $url = (string) $this->argument('url');
        $limitOpt = $this->option('limit');
        $limit = is_null($limitOpt) || $limitOpt === '' ? null : (int) $limitOpt;

        $this->info('Starting import from RateList...');
        $this->line('Service ID: ' . $serviceId . '; URL: ' . $url . '; Limit: ' . ($limit ?? 'no limit'));

        $processed = 0;
        $bar = $this->output->createProgressBar($limit ?? 500);
        $bar->setBarWidth(50);
        $bar->start();

        $result = $importService->performImport($serviceId, $url, $limit, function (array $context) use (&$processed, $bar, $limit) {
            $processed = $context['processed'] ?? $processed + 1;
            if ($limit) {
                $bar->setMaxSteps($limit);
                $bar->advance();
            } else {
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Imported: ' . $result['imported'] . '; Skipped: ' . $result['skipped']);

        return Command::SUCCESS;
    }
}
