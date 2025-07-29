<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Services\SmsService;
use App\Http\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_otp_sends_sms_and_returns_200(): void
    {
        // Mock SmsService generateAndSendCode
        $this->mock(SmsService::class, function ($mock) {
            $mock->shouldReceive('generateAndSendCode')->once()->with('+380501234567');
        });

        $response = $this->postJson('/api/auth/request-otp', [
            'phone' => '+380501234567',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'needs_registration',
            ]);
    }

    public function test_verify_otp_returns_400_on_wrong_code(): void
    {
        // SmsService verifyCode returns false
        $this->mock(SmsService::class, function ($mock) {
            $mock->shouldReceive('verifyCode')->once()->andReturn(false);
        });

        $response = $this->postJson('/api/auth/verify-otp', [
            'phone' => '+380501234567',
            'sms_code' => '123456',
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Wrong code']);
    }

    public function test_verify_otp_returns_tokens_on_success(): void
    {
        // Mock SmsService verifyCode true
        $this->mock(SmsService::class, function ($mock) {
            $mock->shouldReceive('verifyCode')->once()->andReturn(true);
        });

        // Mock TokenService to skip JWT generation
        $this->mock(TokenService::class, function ($mock) {
            $mock->shouldReceive('createAccessToken')->once()->andReturn('access');
            $mock->shouldReceive('createRefreshToken')->once()->andReturnUsing(function ($user) {
                $model = new \App\Models\RefreshToken([
                    'token' => 'hashed',
                    'expires_at' => now()->addDays(30),
                ]);
                $model->plain_token = 'refresh';

                return $model;
            });
            $mock->shouldReceive('revoke')->andReturnNull();
        });

        $response = $this->postJson('/api/auth/verify-otp', [
            'phone' => '+380501234567',
            'sms_code' => '111111',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'access_token',
                'refresh_token',
                'expires_in',
            ]);
    }
}
