<?php

namespace App\Http\Controllers;

use App\Models\AdvertiserProfile;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdvertiserAuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('advertiser')->check()) {
            return redirect()->route('advertiser.dashboard');
        }
        return view('auth.advertiser-login');
    }

    /**
     * Handle incoming phone number and request OTP.
     */
    public function requestOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => 'required|string|max:50',
        ]);

        $phone = trim($request->phone);
        $cleanPhone = User::normalizePhone($phone);

        $profile = AdvertiserProfile::where('phone', $cleanPhone)->first();

        // Validate Profile Active status
        if (!$profile || $profile->status !== 'active') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Your phone number is not registered or active. Please contact support or submit an enquiry.');
        }

        try {
            $this->otpService->requestOtp($cleanPhone, $profile->email, 'advertiser', $request->ip());
            
            // Store phone in session (clean format)
            session(['auth_login_phone' => $cleanPhone]);

            return redirect()->route('advertiser.login.verify')
                ->with('success', "A login code has been sent to the email associated with {$phone}.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show OTP verification input form.
     */
    public function showVerify(): View|RedirectResponse
    {
        $phone = session('auth_login_phone');
        if (!$phone) {
            return redirect()->route('advertiser.login');
        }
        return view('auth.advertiser-verify', compact('phone'));
    }

    /**
     * Validate verification OTP code and authorize session.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'phone'    => 'required|string',
            'otp_code' => 'required|numeric|digits:6',
        ]);

        $phone = trim($request->phone);
        $cleanPhone = User::normalizePhone($phone);
        $user = User::where('phone', $cleanPhone)->first();

        if (!$user) {
            // Self-heal: if profile exists but no user is linked yet
            $profile = AdvertiserProfile::where('phone', $cleanPhone)->first();
            if ($profile) {
                $user = User::where('email', $profile->email)->first();
                if (!$user) {
                    $user = User::create([
                        'name'   => $profile->company_name ?: $profile->contact_person,
                        'email'  => $profile->email,
                        'phone'  => $cleanPhone,
                        'status' => 'active',
                    ]);
                } else {
                    if (empty($user->phone)) {
                        $user->update(['phone' => $cleanPhone]);
                    }
                }
                $profile->update(['user_id' => $user->id]);
            }
        }

        if ($user) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
            if (!$user->hasRole('advertiser')) {
                $user->assignRole('advertiser');
            }
        }

        if (!$user || $user->status !== 'active') {
            return redirect()->route('advertiser.login')
                ->with('error', 'Authentication failed. Account is ' . ($user ? $user->status : 'inactive') . '.');
        }

        $actualRole = \Spatie\Permission\Models\Role::where('name', 'advertiser')->first()?->name ?? 'advertiser';
        if (!$user->hasRole($actualRole)) {
            return redirect()->route('advertiser.login')
                ->with('error', 'Authentication failed. Unauthorized role.');
        }

        // Fetch associated profile
        $profile = $user->advertiserProfile;
        if (!$profile || $profile->status !== 'active') {
            return redirect()->route('advertiser.login')
                ->with('error', 'Authentication failed. Advertiser profile is inactive.');
        }

        try {
            $this->otpService->verifyOtp($cleanPhone, null, 'advertiser', $request->otp_code, $request->ip());

            // Perform guard login
            Auth::guard('advertiser')->login($user);

            // Clear transient login phone from session
            session()->forget('auth_login_phone');

            // Update profile and user analytics
            $profile->increment('login_count');
            $profile->update([
                'last_login_at' => now()
            ]);

            $user->update([
                'last_login_at' => now()
            ]);

            // 5. Log activity
            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'login_success',
                'entity_type' => AdvertiserProfile::class,
                'entity_id'   => $profile->id,
                'description' => "Advertiser logged in successfully: {$profile->advertiser_code}",
            ]);

            return redirect()->route('advertiser.dashboard')
                ->with('success', 'Logged in successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display Advertiser Dashboard workspace.
     */
    public function dashboard(): View
    {
        $user = Auth::guard('advertiser')->user();
        $profile = AdvertiserProfile::where('user_id', $user->id)->first();

        return view('advertiser.dashboard', compact('profile'));
    }

    /**
     * Terminate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::guard('advertiser')->user();
        if ($user) {
            $profile = AdvertiserProfile::where('user_id', $user->id)->first();
            
            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'logout',
                'entity_type' => AdvertiserProfile::class,
                'entity_id'   => $profile ? $profile->id : null,
                'description' => "Advertiser logged out: " . ($profile ? $profile->advertiser_code : $user->email),
            ]);
        }

        Auth::guard('advertiser')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('advertiser.login')
            ->with('success', 'Logged out successfully.');
    }
}
