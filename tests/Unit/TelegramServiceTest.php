<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Services\TelegramService;
use Mockery;
use Tests\TestCase;

class TelegramServiceTest extends TestCase
{
    public function test_send_short_message_calls_telegram_once(): void
    {
        $msgMock = Mockery::mock('alias:NotificationChannels\\Telegram\\TelegramMessage');
        $msgMock->shouldReceive('create')->once()->andReturnSelf();
        $msgMock->shouldReceive('token')->once()->andReturnSelf();
        $msgMock->shouldReceive('to')->once()->andReturnSelf();
        $msgMock->shouldReceive('content')->once()->with('Hello')->andReturnSelf();
        $msgMock->shouldReceive('send')->once();

        $service = new TelegramService;
        $service->send('Hello');
    }

    public function test_send_long_message_splits_into_chunks(): void
    {
        $long = str_repeat('A', 5000); // >4096
        $expectedChunks = 2;

        $msgMock = Mockery::mock('alias:NotificationChannels\\Telegram\\TelegramMessage');
        $msgMock->shouldReceive('create')->times($expectedChunks)->andReturnSelf();
        $msgMock->shouldReceive('token')->times($expectedChunks)->andReturnSelf();
        $msgMock->shouldReceive('to')->times($expectedChunks)->andReturnSelf();
        // content called with chunk strings; allow any string
        $msgMock->shouldReceive('content')->times($expectedChunks)->andReturnSelf();
        $msgMock->shouldReceive('send')->times($expectedChunks);

        $service = new TelegramService;
        $service->send($long);
    }
}
