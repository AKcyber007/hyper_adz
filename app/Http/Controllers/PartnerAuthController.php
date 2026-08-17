<?php

namespace App\Http\Controllers;

use App\Models\LocationPartnerProfile;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PartnerAuthController extends Controller
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
        if (Auth::guard('location_partner')->check()) {
            return redirect()->route('partner.dashboard');
        }
        return view('auth.partner-login');
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

        $profile = LocationPartnerProfile::where('phone', $cleanPhone)->first();

        // Validate Profile Active status
        if (!$profile || $profile->status !== 'active') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Your phone number is not registered or active. Please contact support or submit an enquiry.');
        }

        try {
            $this->otpService->requestOtp($cleanPhone, $profile->email, 'location_partner', $request->ip());
            
            // Store phone and remember preference in session (clean format)
            session([
                'auth_login_phone' => $cleanPhone,
                'auth_login_remember' => $request->has('remember')
            ]);

            return redirect()->route('partner.login.verify')
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
            return redirect()->route('partner.login');
        }
        return view('auth.partner-verify', compact('phone'));
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
            $profile = LocationPartnerProfile::where('phone', $cleanPhone)->first();
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
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'location_partner', 'guard_name' => 'web']);
            if (!$user->hasRole('location_partner')) {
                $user->assignRole('location_partner');
            }
        }

        if (!$user || $user->status !== 'active') {
            return redirect()->route('partner.login')
                ->with('error', 'Authentication failed. Account is ' . ($user ? $user->status : 'inactive') . '.');
        }

        $actualRole = \Spatie\Permission\Models\Role::where('name', 'location_partner')->first()?->name ?? 'location_partner';
        if (!$user->hasRole($actualRole)) {
            return redirect()->route('partner.login')
                ->with('error', 'Authentication failed. Unauthorized role.');
        }

        // Fetch associated profile
        $profile = $user->partnerProfile;
        if (!$profile || $profile->status !== 'active') {
            return redirect()->route('partner.login')
                ->with('error', 'Authentication failed. Partner profile is inactive.');
        }

        try {
            $this->otpService->verifyOtp($cleanPhone, null, 'location_partner', $request->otp_code, $request->ip());

            // Retrieve remember me preference
            $remember = session('auth_login_remember', false);

            // Perform guard login
            Auth::guard('location_partner')->login($user, $remember);

            // Clear transient login session data
            session()->forget(['auth_login_phone', 'auth_login_remember']);

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
                'entity_type' => LocationPartnerProfile::class,
                'entity_id'   => $profile->id,
                'description' => "Location Partner logged in successfully: {$profile->partner_code}",
            ]);

            return redirect()->route('partner.dashboard')
                ->with('success', 'Logged in successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display Partner Dashboard workspace.
     */
    public function dashboard(): View
    {
        $user = Auth::guard('location_partner')->user();
        $profile = LocationPartnerProfile::with('locations.screens')->where('user_id', $user->id)->first();

        return view('partner.dashboard', compact('profile'));
    }

    /**
     * Terminate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::guard('location_partner')->user();
        if ($user) {
            $profile = LocationPartnerProfile::where('user_id', $user->id)->first();
            
            ActivityLog::create([
                'user_id'     => $user->id,
                'action'      => 'logout',
                'entity_type' => LocationPartnerProfile::class,
                'entity_id'   => $profile ? $profile->id : null,
                'description' => "Location Partner logged out: " . ($profile ? $profile->partner_code : $user->email),
            ]);
        }

        Auth::guard('location_partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('partner.login')
            ->with('success', 'Logged out successfully.');
    }
}
