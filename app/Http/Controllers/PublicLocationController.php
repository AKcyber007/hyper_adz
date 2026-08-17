<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\LocationRepositoryInterface;
use Illuminate\View\View;

class PublicLocationController extends Controller
{
    protected LocationRepositoryInterface $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * Display details of a specific location.
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $location = $this->locationRepository->findBySlug($slug);
        
        if (!$location) {
            abort(404);
        }

        // Load screens with type and images
        $location->load(['screens.type', 'screens.images']);

        return view('locations.detail', compact('location'));
    }
}
