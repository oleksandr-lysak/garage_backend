<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Providers\TelescopeServiceProvider;
use Tests\TestCase;

class TelescopeProviderTest extends TestCase
{
    public function test_register_provider(): void
    {
        app()->register(TelescopeServiceProvider::class);
        $this->assertTrue(true);
    }
}
