<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected \App\Services\OtpService $otpService;

    public function __construct(\App\Services\OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validates email/password and Admin role, but does NOT log them in.
        $request->authenticate();

        try {
            // Request OTP
            $this->otpService->requestOtp(null, $request->email, 'Admin', $request->ip());

            // Store email in session for the verification step
            session(['admin_login_email' => $request->email]);

            return redirect()->route('admin.login.verify')->with('success', 'A verification code has been sent to your email.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the Admin OTP verification view.
     */
    public function showVerify(): View|RedirectResponse
    {
        $email = session('admin_login_email');
        if (!$email) {
            return redirect()->route('login');
        }
        return view('auth.admin-verify', compact('email'));
    }

    /**
     * Verify the Admin OTP.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|string|email',
            'otp_code' => 'required|numeric|digits:6',
        ]);

        $email = $request->email;

        try {
            // Verify OTP
            $this->otpService->verifyOtp(null, $email, 'Admin', $request->otp_code, $request->ip());

            // Find Admin user
            $user = \App\Models\User::where('email', $email)->first();
            if (!$user || $user->status !== 'active') {
                return redirect()->route('login')->with('error', 'Authentication failed.');
            }

            // Log them in
            Auth::guard('web')->login($user);

            // Clear transient session data
            session()->forget('admin_login_email');

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard', absolute: false));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
