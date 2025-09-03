<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\MasterResource;
use App\Models\Master;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MasterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('MastersList', [
            'seoConfig' => [
                'type' => config('app.masters_type', 'auto_mechanics'),
                'city' => config('app.city', 'Київ'),
                'region' => config('app.region', 'Київська область'),
            ],
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $master = Master::where('slug', $slug)->firstOrFail();
        $master->load([
            'reviews',
            'services',
        ]);

        return Inertia::render('Master', [
            'master' => new MasterResource($master),
        ]);
    }

    public function fetchMasters(Request $request)
    {
        $query = Master::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('services', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('id', $request->get('service_id'));
            });
        }

        if ($request->filled('min_rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->get('min_rating')]);
            });
        }

        if ($request->filled('available')) {
            $query->where('available', $request->get('available') === 'true');
        }

        if ($request->filled('min_age')) {
            $query->where('age', '>=', $request->get('min_age'));
        }

        if ($request->filled('max_age')) {
            $query->where('age', '<=', $request->get('max_age'));
        }

        if ($request->filled('min_price')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('price', '>=', $request->get('min_price'));
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('price', '<=', $request->get('max_price'));
            });
        }

        if ($request->filled('selected_services') && is_array($request->get('selected_services'))) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->whereIn('id', $request->get('selected_services'));
            });
        }

        // Apply sorting - use proper rating calculation
        $sortBy = $request->get('sort_by', 'rating');
        switch ($sortBy) {
            case 'rating':
                // Join with reviews to get average rating for sorting
                $query->leftJoin('reviews', 'masters.id', '=', 'reviews.master_id')
                      ->select('masters.*', \DB::raw('COALESCE(AVG(reviews.rating), masters.rating_google, 0) as avg_rating'))
                      ->groupBy('masters.id')
                      ->orderBy('avg_rating', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'age':
                $query->orderBy('age', 'asc');
                break;
            default:
                // Default sorting by rating
                $query->leftJoin('reviews', 'masters.id', '=', 'reviews.master_id')
                      ->select('masters.*', \DB::raw('COALESCE(AVG(reviews.rating), masters.rating_google, 0) as avg_rating'))
                      ->groupBy('masters.id')
                      ->orderBy('avg_rating', 'desc');
        }

        // Load relationships for performance
        $query->with(['services', 'reviews']);

        // Paginate results
        $perPage = $request->get('per_page', 20);
        $masters = $query->paginate($perPage);

        return response()->json([
            'masters' => [
                'data' => MasterResource::collection($masters),
                'current_page' => $masters->currentPage(),
                'last_page' => $masters->lastPage(),
                'total' => $masters->total(),
                'prev_page_url' => $masters->previousPageUrl(),
                'next_page_url' => $masters->nextPageUrl(),
            ],
        ]);
    }

    public function getFilters()
    {
        return response()->json([
            'services' => \App\Models\Service::all(),
            'cities' => Master::distinct()->pluck('address')->filter()->values(),
        ]);
    }
}
