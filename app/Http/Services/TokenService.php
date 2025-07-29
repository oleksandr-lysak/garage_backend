<?php

namespace App\Http\Services;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    /**
     * Generate a short-lived JWT access token.
     */
    public function createAccessToken(User $user): string
    {
        // setTTL only affects the next token generation
        $ttlMinutes = Config::get('auth.access_token_ttl', 15);
        JWTAuth::factory()->setTTL($ttlMinutes);

        return JWTAuth::claims(['phone' => $user->phone])->fromUser($user);
    }

    /**
     * Generate and persist a refresh token.
     */
    public function createRefreshToken(User $user): RefreshToken
    {
        $tokenString = Str::random(64);

        // Check if refresh tokens should never expire
        $ttlDays = Config::get('auth.refresh_token_ttl_days', 30);
        $expiresAt = $ttlDays > 0 ? now()->addDays($ttlDays) : null;

        $refreshModel = $user->refreshTokens()->create([
            'token' => Hash::make($tokenString), // store hashed for security
            'expires_at' => $expiresAt,
        ]);

        // Expose plain token for API response
        $refreshModel->plain_token = $tokenString;

        return $refreshModel;
    }

    /**
     * Validate refresh token and return model if valid.
     */
    public function validateRefreshToken(string $plainToken): ?RefreshToken
    {
        $query = RefreshToken::where('revoked', false);

        // Check if refresh tokens have expiration
        $ttlDays = Config::get('auth.refresh_token_ttl_days', 30);
        if ($ttlDays > 0) {
            $query->where('expires_at', '>', now());
        } else {
            $query->whereNull('expires_at');
        }

        $refreshTokens = $query->get();

        foreach ($refreshTokens as $token) {
            if (Hash::check($plainToken, $token->token)) {
                return $token;
            }
        }

        return null;
    }

    /**
     * Revoke token instance.
     */
    public function revoke(RefreshToken $refreshToken): void
    {
        $refreshToken->update(['revoked' => true]);
    }

    /**
     * Revoke all tokens for a user.
     */
    public function revokeAllTokens(User $user): void
    {
        $user->refreshTokens()->update(['revoked' => true]);
    }

    /**
     * Clean up expired tokens.
     */
    public function cleanupExpiredTokens(): int
    {
        return RefreshToken::where('expires_at', '<', now())
            ->orWhere('revoked', true)
            ->delete();
    }

    /**
     * Get active tokens count for user.
     */
    public function getActiveTokensCount(User $user): int
    {
        return $user->refreshTokens()
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->count();
    }
}
