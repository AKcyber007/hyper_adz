<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdvertiserProfile;
use Symfony\Component\HttpFoundation\Response;

class AdvertiserMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if user is authenticated via advertiser guard
        if (!Auth::guard('advertiser')->check()) {
            return redirect()->route('advertiser.login');
        }

        // 2. Fetch the linked active profile
        $user = Auth::guard('advertiser')->user();
        $profile = AdvertiserProfile::where('user_id', $user->id)->first();

        if (!$profile || $profile->status !== 'active') {
            Auth::guard('advertiser')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('advertiser.login')->with('error', 'Your advertiser account is not active. Please contact support.');
        }

        return $next($request);
    }
}
