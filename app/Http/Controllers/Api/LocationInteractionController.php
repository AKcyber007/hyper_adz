<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\LocationFavorite;
use App\Models\LocationReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LocationInteractionController extends Controller
{
    /**
     * Submit a review for a location.
     */
    public function storeReview(Request $request, $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        $location = Location::where('status', Location::STATUS_ACTIVE)->findOrFail($id);

        $review = LocationReview::create([
            'location_id' => $location->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review->load('user:id,name'),
            'new_average' => $location->average_rating,
            'new_count' => $location->reviews_count,
        ]);
    }

    /**
     * Get reviews for a location.
     */
    public function getReviews($id): JsonResponse
    {
        $location = Location::where('status', Location::STATUS_ACTIVE)->findOrFail($id);

        $reviews = $location->reviews()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'reviews' => $reviews,
            'average' => $location->average_rating,
            'count' => $location->reviews_count,
        ]);
    }

    /**
     * Toggle a location favorite.
     */
    public function toggleFavorite($id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $location = Location::where('status', Location::STATUS_ACTIVE)->findOrFail($id);
        $userId = Auth::id();

        $favorite = LocationFavorite::where('location_id', $location->id)
            ->where('user_id', $userId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites', 'is_favorited' => false]);
        } else {
            LocationFavorite::create([
                'location_id' => $location->id,
                'user_id' => $userId,
            ]);
            return response()->json(['message' => 'Added to favorites', 'is_favorited' => true]);
        }
    }
}
