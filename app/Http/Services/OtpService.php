<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    /**
     * Generate and send OTP code via SMS
     */
    public function generateAndSendOtp(string $phone, string $purpose = 'general', int $length = 6): string
    {
        // Use existing SmsService to generate and send SMS
        $smsService = new SmsService();
        $code = $smsService->generateAndSendCode($phone, $length);

        // Cache the same code with purpose prefix for OTP verification
        $cacheKey = "otp_{$purpose}_{$phone}";
        Cache::put($cacheKey, $code, now()->addMinutes(5));

        return $code;
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $phone, string $inputCode, string $purpose = 'general'): bool
    {
        $cacheKey = "otp_{$purpose}_{$phone}";
        $cachedCode = Cache::get($cacheKey);

        // Log for debugging
        \Log::info('OTP verification attempt', [
            'phone' => $phone,
            'purpose' => $purpose,
            'cache_key' => $cacheKey,
            'cached_code' => $cachedCode,
            'input_code' => $inputCode,
            'match' => $cachedCode == $inputCode
        ]);

        if ($cachedCode == $inputCode) {
            // Remove code after successful verification
            Cache::forget($cacheKey);
            return true;
        }

        return false;
    }

    /**
     * Check if OTP exists for phone
     */
    public function hasOtp(string $phone, string $purpose = 'general'): bool
    {
        $cacheKey = "otp_{$purpose}_{$phone}";
        return Cache::has($cacheKey);
    }

    /**
     * Get remaining time for OTP
     */
    public function getOtpRemainingTime(string $phone, string $purpose = 'general'): int
    {
        $cacheKey = "otp_{$purpose}_{$phone}";
        $ttl = Cache::getTimeToLive($cacheKey);

        return $ttl ? max(0, $ttl) : 0;
    }
}
