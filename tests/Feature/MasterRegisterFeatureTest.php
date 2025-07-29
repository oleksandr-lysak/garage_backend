<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Services\Master\MasterService;
use App\Http\Services\SmsService;
use App\Http\Services\UserService;
use App\Models\Master;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MasterRegisterFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_master_register_success(): void
    {
        // Mock SmsService verifyCode returns true
        $this->mock(SmsService::class, function ($mock) {
            $mock->shouldReceive('verifyCode')->once()->andReturn(true);
        });

        // Create a fake master returned by MasterService
        $fakeMaster = Master::forceCreate([
            'id' => 100,
            'name' => 'Tester',
            'contact_phone' => '+380501234567',
            'service_id' => 1,
            'longitude' => 30,
            'latitude' => 50,
            'description' => 'desc',
            'photo' => 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==',
        ]);

        $this->mock(MasterService::class, function ($mock) use ($fakeMaster) {
            $mock->shouldReceive('createOrUpdate')->once()->andReturn($fakeMaster);
        });

        $this->mock(UserService::class, function ($mock) {
            $mock->shouldReceive('createOrUpdateFromMaster')->once()->andReturnUsing(function ($master) {
                return \App\Models\User::forceCreate([
                    'id' => 77,
                    'phone' => $master->contact_phone,
                    'name' => $master->name,
                    'password' => bcrypt('pass'),
                ]);
            });
        });

        JWTAuth::shouldReceive('claims')->once()->andReturnSelf();
        JWTAuth::shouldReceive('fromUser')->once()->andReturn('jwt_master');

        $payload = [
            'phone' => '+380501234567',
            'sms_code' => '123456',
            'name' => 'Tester',
            'description' => 'desc',
            'address' => 'Addr',
            'latitude' => 50,
            'longitude' => 30,
            'photo' => 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==',
            'service_id' => 1,
        ];

        $response = $this->postJson('/api/auth/master-register', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'master',
                'user',
                'token',
            ]);
    }
}
