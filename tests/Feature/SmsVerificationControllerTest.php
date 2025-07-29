<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_code_returns_message(): void
    {
        $this->mock(SmsService::class, function ($mock) {
            $mock->shouldReceive('generateAndSendCode')->once()->with('+380501000000')->andReturn('111111');
        });

        $response = $this->postJson('/api/auth/send-code', [
            'phone' => '+380501000000',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'The code was sent successfully, code: 111111']);
    }
}
