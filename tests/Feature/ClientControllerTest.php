<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Services\ClientService;
use App\Http\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_client_returns_token_and_user(): void
    {
        // Mock UserService and ClientService
        $this->mock(UserService::class, function ($mock) {
            $mock->shouldReceive('createOrUpdateForClient')->once()->andReturnUsing(function ($data) {
                return \App\Models\User::forceCreate([
                    'id' => 55,
                    'phone' => $data['phone'],
                    'name' => $data['name'],
                    'password' => bcrypt('pass'),
                ]);
            });
            $mock->shouldReceive('findUserByPhone');
        });

        $this->mock(ClientService::class, function ($mock) {
            $mock->shouldReceive('createOrUpdate')->once();
        });

        JWTAuth::shouldReceive('claims')->once()->andReturnSelf();
        JWTAuth::shouldReceive('fromUser')->once()->andReturn('jwt_token');

        $response = $this->postJson('/api/auth/client-register', [
            'phone' => '+380501234567',
            'name' => 'Client',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'token' => 'jwt_token',
                'user' => [
                    'id' => 55,
                    'phone' => '+380501234567',
                ],
            ]);
    }

    public function test_register_client_validation_error(): void
    {
        $response = $this->postJson('/api/auth/client-register', [
            // missing name field
            'phone' => '+380501234567',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['name']]);
    }
}
