<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Services\TokenService;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TokenService;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_access_token_returns_string(): void
    {
        $user = User::factory()->create(['phone' => '+380501111111']);

        // Mock JWT facade chain
        JWTAuth::shouldReceive('factory')->once()->andReturnSelf();
        JWTAuth::shouldReceive('setTTL')->once();
        JWTAuth::shouldReceive('claims')->once()->with(['phone' => $user->phone])->andReturnSelf();
        JWTAuth::shouldReceive('fromUser')->once()->with($user)->andReturn('access-token-123');

        $token = $this->service->createAccessToken($user);

        $this->assertEquals('access-token-123', $token);
    }

    public function test_create_refresh_token_persists_and_returns_plain_token(): void
    {
        $user = User::factory()->create();

        $refresh = $this->service->createRefreshToken($user);

        $this->assertInstanceOf(RefreshToken::class, $refresh);
        $this->assertNotEmpty($refresh->plain_token);
        $this->assertDatabaseHas('refresh_tokens', [
            'id' => $refresh->id,
            'user_id' => $user->id,
        ]);
        // Stored token must be hashed, so shouldn't equal plain
        $this->assertTrue(Hash::check($refresh->plain_token, $refresh->token));
    }

    public function test_validate_refresh_token_returns_model_when_valid(): void
    {
        $user = User::factory()->create();
        $refresh = $this->service->createRefreshToken($user);

        $validated = $this->service->validateRefreshToken($refresh->plain_token);

        $this->assertNotNull($validated);
        $this->assertEquals($refresh->id, $validated->id);
    }

    public function test_validate_refresh_token_returns_null_when_revoked(): void
    {
        $user = User::factory()->create();
        $refresh = $this->service->createRefreshToken($user);

        // Use fresh model so it doesn't contain plain_token attribute
        $refreshFresh = $refresh->fresh();

        // Revoke token
        $this->service->revoke($refreshFresh);

        $validated = $this->service->validateRefreshToken($refresh->plain_token);

        $this->assertNull($validated);
    }
}
