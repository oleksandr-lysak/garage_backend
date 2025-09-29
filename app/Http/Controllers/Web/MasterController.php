<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchMastersRequest;
use App\Http\Resources\Web\MasterListResponse;
use App\Http\Resources\Web\MasterResource;
use App\Http\Services\Master\MasterListService;
use App\Models\Master;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MasterController extends Controller
{
    /**
     * Display the master list page
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('MastersList', [
            'seoConfig' => [
                'type' => config('seo.type', 'auto_mechanics'),
                'city' => config('seo.city', 'Київ'),
                'region' => config('seo.region', 'Київська область'),
            ],
        ]);
    }

    /**
     * Display the master page
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function show(Request $request, string $slug)
    {
        $master = Master::where('slug', $slug)
            ->withAvg('reviews', 'rating')
            ->firstOrFail();
        $master->load([
            'reviews.user',
            'services',
        ]);

        return Inertia::render('Master', [
            'master' => new MasterResource($master),
        ]);
    }

    /**
     * Fetch the masters
     *
     * @param FetchMastersRequest $request
     * @param MasterListService $service
     * @return JsonResponse
     */
    public function fetchMasters(FetchMastersRequest $request, MasterListService $service)
    {
        $masters = $service->list($request->validated());

        return (new MasterListResponse($masters))->response();
    }

    /**
     * Get the filters
     *
     * @return JsonResponse
     */
    public function getFilters()
    {
        return response()->json([
            'services' => \App\Models\Service::all(),
            'cities' => Master::distinct()->pluck('address')->filter()->values(),
        ]);
    }
}
