<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Request OTP for review submission
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'master_id' => 'required|integer|exists:masters,id'
        ]);

        $result = $this->reviewService->requestReviewOtp(
            $request->phone,
            $request->master_id
        );

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json($result, 400);
    }

    /**
     * Submit review without OTP verification (simple review)
     */
    public function submit(Request $request): JsonResponse
    {
        // Log incoming data for debugging
        \Log::info('Review submission request data:', $request->all());

        $request->validate([
            'user_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'master_id' => 'required|integer|exists:masters,id'
        ]);

        $result = $this->reviewService->submitReview($request->all(), $request->master_id);

        if ($result['success']) {
            return response()->json($result, 201);
        }

        return response()->json($result, 400);
    }

    /**
     * Get reviews for master
     */
    public function getMasterReviews(Request $request, int $masterId): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $result = $this->reviewService->getMasterReviews($masterId, $limit);

        return response()->json($result, 200);
    }
}
