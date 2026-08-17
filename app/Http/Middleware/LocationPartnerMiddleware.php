<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LocationPartnerProfile;
use Symfony\Component\HttpFoundation\Response;

class LocationPartnerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is authenticated via location_partner guard
        if (!Auth::guard('location_partner')->check()) {
            return redirect()->route('partner.login');
        }

        // 2. Fetch the linked active profile
        $user = Auth::guard('location_partner')->user();
        $profile = LocationPartnerProfile::where('user_id', $user->id)->first();

        if (!$profile || $profile->status !== 'active') {
            Auth::guard('location_partner')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('partner.login')->with('error', 'Your location partner account is not active. Please contact support.');
        }

        return $next($request);
    }
}
