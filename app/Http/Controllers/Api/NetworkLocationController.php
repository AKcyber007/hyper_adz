<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationMapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NetworkLocationController extends Controller
{
    /**
     * The location map service.
     */
    protected LocationMapService $mapService;

    /**
     * Create a new controller instance.
     */
    public function __construct(LocationMapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Display a listing of advertising locations formatted for map rendering, with search and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category_id', 'status', 'city']);
        $locations = $this->mapService->getFormattedLocationsForMap($filters);

        return response()->json($locations);
    }
}
