<?php

namespace App\Http\Services;

use App\Models\Review;
use App\Models\Master;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Request OTP for review submission
     */
    public function requestReviewOtp(string $phone, int $masterId): array
    {
        try {
            // Check if master exists
            $master = Master::find($masterId);
            if (!$master) {
                return [
                    'success' => false,
                    'message' => 'Master not found'
                ];
            }

            // Generate and send OTP
            $this->otpService->generateAndSendOtp($phone, 'general', 6);

            return [
                'success' => true,
                'message' => 'OTP sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Error requesting review OTP: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send OTP'
            ];
        }
    }

        /**
     * Submit review without OTP verification (simple review)
     */
    public function submitReview(array $data, int $masterId): array
    {
        try {
            // Log input data for debugging
            Log::info('Review submission attempt', [
                'user_name' => $data['user_name'],
                'rating' => $data['rating'],
                'master_id' => $masterId
            ]);

            // Check if master exists
            $master = Master::find($masterId);
            if (!$master) {
                return [
                    'success' => false,
                    'message' => 'Master not found'
                ];
            }

            DB::beginTransaction();

            // Create a simple user record with just name (no phone verification)
            $user = \App\Models\User::create([
                'name' => $data['user_name'],
                'phone' => null, // No phone for simple reviews
            ]);

            // Create review
            $review = Review::create([
                'master_id' => $masterId,
                'user_id' => $user->id,
                'rating' => $data['rating'],
                'review' => $data['comment'],
            ]);

            // No need to update reviews_count - it's calculated dynamically

            DB::commit();

            return [
                'success' => true,
                'message' => 'Review submitted successfully',
                'review' => $review
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting review: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to submit review'
            ];
        }
    }



    /**
     * Get reviews for master
     */
    public function getMasterReviews(int $masterId, int $limit = 10): array
    {
        $reviews = Review::with('user')
            ->where('master_id', $masterId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return [
            'success' => true,
            'reviews' => $reviews,
            'total' => Review::where('master_id', $masterId)->count()
        ];
    }

    /**
     * Check if user can submit review (rate limiting)
     */
    public function canSubmitReview(string $phone, int $masterId): bool
    {
        // Check if user submitted review in last 24 hours
        $user = \App\Models\User::where('phone', $phone)->first();
        if (!$user) {
            return true; // New user can submit review
        }

        $recentReview = Review::where('user_id', $user->id)
            ->where('master_id', $masterId)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        return !$recentReview;
    }
}
