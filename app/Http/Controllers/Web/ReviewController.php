<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetMasterReviewsRequest;
use App\Http\Requests\RequestReviewOtpRequest;
use App\Http\Requests\SubmitReviewRequest;
use App\Http\Resources\Web\MasterReviewsResponse;
use App\Http\Resources\Web\RequestOtpResponse;
use App\Http\Resources\Web\SubmitReviewResponse;
use App\Http\Services\ReviewService;
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
     *
     * @param RequestReviewOtpRequest $request
     * @return JsonResponse
     */
    public function requestOtp(RequestReviewOtpRequest $request): JsonResponse
    {
        $result = $this->reviewService->requestReviewOtp(
            $request->validated()['phone'],
            $request->validated()['master_id']
        );

        return (new RequestOtpResponse($result))->response()->setStatusCode($result['success'] ? 200 : 400);
    }

    /**
     * Submit review without OTP verification (simple review)
     *
     * @param SubmitReviewRequest $request
     * @return JsonResponse
     */
    public function submit(SubmitReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->reviewService->submitReview($validated, $validated['master_id']);

        return (new SubmitReviewResponse($result))->response()->setStatusCode($result['success'] ? 201 : 400);
    }

    /**
     * Get reviews for master
     *
     * @param GetMasterReviewsRequest $request
     * @param int $masterId
     * @return JsonResponse
     */
    public function getMasterReviews(GetMasterReviewsRequest $request, int $masterId): JsonResponse
    {
        $limit = $request->validated()['limit'] ?? 10;

        $result = $this->reviewService->getMasterReviews($masterId, $limit);

        return (new MasterReviewsResponse($result))->response()->setStatusCode(200);
    }
}
