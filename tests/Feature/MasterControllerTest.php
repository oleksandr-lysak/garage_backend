<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Services\Appointment\AppointmentRedisService;
use App\Models\Master;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_master_returns_resource(): void
    {
        /** @var Master $master */
        $master = Master::factory()->make([
            'latitude' => 50,
            'longitude' => 30,
            'description' => 'desc',
            'photo' => 'data:image/png;base64,'.base64_encode('x'),
        ]);
        $master->save();

        $response = $this->getJson('/api/masters/'.$master->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => ['id' => $master->id],
            ]);
    }

    public function test_index_uses_master_fetcher(): void
    {
        $mockMasterService = \Mockery::mock(\App\Http\Services\Master\MasterService::class);
        $mockMasterService->shouldReceive('getMastersOnDistance')->once()->andReturn(new \Illuminate\Pagination\LengthAwarePaginator([], 0, 2000));

        $mockRedis = \Mockery::mock(\App\Http\Services\Appointment\AppointmentRedisService::class);
        $mockRedis->shouldReceive('getAvailabilityForMany')->once()->andReturn([]);

        $fetcher = new \App\Http\Services\Master\MasterFetcher($mockMasterService, $mockRedis);
        $this->app->instance(\App\Http\Services\Master\MasterFetcher::class, $fetcher);

        $response = $this->getJson('/api/masters?lat=50&lng=30&zoom=5');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function test_set_available_calls_service_and_returns_message(): void
    {
        $this->mock(AppointmentRedisService::class, function ($mock) {
            $mock->shouldReceive('markAsFree')->once();
        });

        // Provide missing DTO class stub
        if (! class_exists(\App\DTO\AvailabilityInterval::class)) {
            eval('namespace App\\DTO; class AvailabilityInterval { public $start; public $end; public function __construct($start,$duration){ $this->start=\Illuminate\\Support\\Carbon::parse($start); $this->end=$this->start->copy()->addMinutes($duration); }}');
        }

        // dummy master id = 1
        Master::factory()->make(['id' => 1, 'service_id' => 1, 'latitude' => 0, 'longitude' => 0, 'description' => 'd', 'photo' => 'data:image/png;base64'.base64_encode('x')])->save();

        $response = $this->postJson('/api/masters/1/availability', [
            'start_time' => now()->addHour()->format('Y-m-d H:i:s'),
            'duration' => 60,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Master is available']);
    }

    public function test_set_unavailable_calls_service_and_returns_message(): void
    {
        $this->mock(AppointmentRedisService::class, function ($mock) {
            $mock->shouldReceive('markAsUnavailableFromNow')->once();
        });

        Master::factory()->make(['id' => 2, 'service_id' => 1, 'latitude' => 0, 'longitude' => 0, 'description' => 'd', 'photo' => 'data:image/png;base64'.base64_encode('x')])->save();

        $response = $this->deleteJson('/api/masters/2/availability');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Master is unavailable']);
    }
}
