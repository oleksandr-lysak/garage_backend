<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Services\Master\MasterSearchService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MasterSearchServiceQueryTest extends TestCase
{
    public function test_get_masters_on_distance_returns_array(): void
    {
        // mock DB::select to avoid raw sql execution
        DB::shouldReceive('select')->once()->andReturn([]);

        $service = new MasterSearchService;

        $result = $service->getMastersOnDistance(50.0, 30.0, 5.0, [], 10000, 1);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
