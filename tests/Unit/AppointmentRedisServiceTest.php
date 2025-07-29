<?php

namespace Tests\Unit;

use App\Http\Services\Appointment\AppointmentRedisService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;

class AppointmentRedisServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_mark_as_busy_stores_interval_and_clears_expired()
    {
        $service = new AppointmentRedisService;

        $start = Carbon::now();
        $end = $start->copy()->addMinutes(30);

        // Expect clearExpiredIntervals (two zremrangebyscore calls)
        Redis::shouldReceive('zremrangebyscore')->twice()->andReturn(0);

        Redis::shouldReceive('zadd')
            ->once()
            ->andReturn(1);

        $service->markAsBusy(1, $start, $end);
        $this->assertTrue(true); // if no exception, expectations are met
    }

    public function test_is_master_available_at_returns_true_when_inside_free_interval()
    {
        $service = new AppointmentRedisService;

        $check = Carbon::now();
        $start = $check->copy()->subMinutes(10)->timestamp;
        $end = $check->copy()->addMinutes(10)->timestamp;

        // Redis returns list json,score pairs
        Redis::shouldReceive('zrangebyscore')
            ->once()
            ->andReturn([
                json_encode(['start' => $start, 'end' => $end]),
                $start,
            ]);

        $this->assertTrue($service->isMasterAvailableAt(2, $check));
    }
}
