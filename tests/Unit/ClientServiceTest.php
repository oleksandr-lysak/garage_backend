<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Services\ClientService;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientService(new Client);
    }

    public function test_create_or_update_creates_new_client(): void
    {
        $data = [
            'phone' => '+380639999999',
            'name' => 'Alice',
        ];

        $client = $this->service->createOrUpdate($data);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'phone' => '+380639999999',
            'name' => 'Alice',
        ]);
    }

    public function test_create_or_update_updates_existing_client(): void
    {
        $client = Client::create([
            'phone' => '+380501212121',
            'name' => 'Old',
        ]);

        $data = [
            'phone' => '+380501212121',
            'name' => 'New',
        ];

        $updated = $this->service->createOrUpdate($data);

        $this->assertEquals($client->id, $updated->id);
        $this->assertEquals('New', $updated->name);
    }
}
