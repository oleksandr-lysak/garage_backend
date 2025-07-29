<?php

namespace App\Console\Commands;

use App\Http\Services\TokenService;
use Illuminate\Console\Command;

class CleanupExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and revoked refresh tokens';

    /**
     * Execute the console command.
     */
    public function handle(TokenService $tokenService): int
    {
        $deletedCount = $tokenService->cleanupExpiredTokens();

        $this->info("Cleaned up {$deletedCount} expired/revoked tokens.");

        return Command::SUCCESS;
    }
}
