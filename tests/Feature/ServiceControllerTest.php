<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Master;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_services(): void
    {
        // create 3 services
        for ($i = 1; $i <= 3; $i++) {
            Service::forceCreate(['name' => 'Srv '.$i]);
        }

        $response = $this->getJson('/api/services');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_get_service_returns_single_resource(): void
    {
        $service = Service::forceCreate(['name' => 'Hair']);

        $response = $this->getJson('/api/services/'.$service->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $service->id,
                ],
            ]);
    }

    public function test_get_services_for_master_returns_related(): void
    {
        // create services and master relationship
        $serviceA = Service::forceCreate(['name' => 'A']);
        $serviceB = Service::forceCreate(['name' => 'B']);
        $master = Master::factory()->make([
            'service_id' => $serviceA->id,
            'latitude' => 50,
            'longitude' => 30,
            'description' => 'desc',
            'photo' => 'data:image/png;base64,'.base64_encode('x'),
        ]);
        $master->save();
        $master->services()->attach([$serviceA->id, $serviceB->id]);

        $response = $this->getJson('/api/services/get-for-master/'.$master->id);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
