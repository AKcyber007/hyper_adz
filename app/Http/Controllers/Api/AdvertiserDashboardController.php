<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvertiserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertiserDashboardController extends Controller
{
    /**
     * Get the active advertiser profile.
     */
    protected function getAdvertiserProfile(): AdvertiserProfile
    {
        return AdvertiserProfile::where('user_id', Auth::id())->firstOrFail();
    }

    /**
     * Dashboard statistics overview.
     */
    public function index(): JsonResponse
    {
        $profile = $this->getAdvertiserProfile();

        $stats = [
            'total_campaigns'     => 0,
            'active_campaigns'    => 0,
            'pending_campaigns'   => 0,
            'completed_campaigns' => 0,
        ];

        return response()->json([
            'success'    => true,
            'advertiser' => [
                'code' => $profile->advertiser_code,
                'company_name' => $profile->company_name,
            ],
            'stats'      => $stats
        ]);
    }
}
