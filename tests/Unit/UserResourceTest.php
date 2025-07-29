<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_expected_fields(): void
    {
        $user = User::forceCreate([
            'id' => 99,
            'name' => 'Test',
            'phone' => '+380501234567',
            'password' => bcrypt('pass'),
        ]);

        $resource = (new UserResource($user))->toArray(new Request);

        $this->assertArrayHasKey('id', $resource);
        $this->assertArrayHasKey('phone', $resource);
    }
}
