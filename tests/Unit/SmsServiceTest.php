<?php

namespace Tests\Unit;

use App\Http\Services\SmsService;
use Daaner\TurboSMS\Facades\TurboSMS; // might need alias path; actual class name maybe Daaner\TurboSMS\Facades\TurboSMS
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class SmsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_and_send_code_puts_cache_and_calls_sms()
    {
        Cache::shouldReceive('put')->once()->withArgs(function ($key, $value, $ttl) {
            return str_contains($key, 'sms_code_');
        });

        TurboSMS::shouldReceive('sendMessages')->once();

        $service = new SmsService;
        $code = $service->generateAndSendCode('+380501234567');

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function test_verify_code_returns_true_when_matches_cache()
    {
        Cache::shouldReceive('get')->once()->andReturn('123456');

        $service = new SmsService;
        $this->assertTrue($service->verifyCode('+380501234567', '123456'));
    }
}
