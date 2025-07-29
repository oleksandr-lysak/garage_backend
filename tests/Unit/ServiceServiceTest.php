<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Services\ServiceService;
use App\Models\Master;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ServiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ServiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ServiceService;
    }

    public function test_get_services_paginates(): void
    {
        for ($i = 0; $i < 150; $i++) {
            Service::forceCreate(['name' => 'Service '.$i]);
        }
        $request = Request::create('/api/services', 'GET', ['page' => 2]);

        $result = $this->service->getServices($request);

        $this->assertCount(50, $result->items()); // 100 per page, so 50 remain
        $this->assertEquals(2, $result->currentPage());
    }

    public function test_get_services_for_master_returns_related(): void
    {
        $services = collect();
        for ($i = 0; $i < 3; $i++) {
            $services->push(Service::forceCreate(['name' => 'Srv '.$i]));
        }
        /** @var Master $master */
        $master = Master::factory()->make([
            'service_id' => $services[0]->id,
            'latitude' => 50.0,
            'longitude' => 30.0,
            'description' => 'desc',
            'photo' => 'data:image/png;base64'.base64_encode('x'),
        ]);
        $master->save();
        $master->services()->attach($services);

        $result = $this->service->getServicesForMaster($master->id);

        $this->assertCount(3, $result);
        $this->assertEqualsCanonicalizing($services->pluck('id')->all(), $result->pluck('id')->all());
    }
}
