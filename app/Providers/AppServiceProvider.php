<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\RoleRepositoryInterface::class,
            \App\Repositories\Eloquent\RoleRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PermissionRepositoryInterface::class,
            \App\Repositories\Eloquent\PermissionRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LocationRepositoryInterface::class,
            \App\Repositories\Eloquent\LocationRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\ScreenRepositoryInterface::class,
            \App\Repositories\Eloquent\ScreenRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LeadRepositoryInterface::class,
            \App\Repositories\Eloquent\LeadRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\AdvertiserRepositoryInterface::class,
            \App\Repositories\Eloquent\AdvertiserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LocationPartnerRepositoryInterface::class,
            \App\Repositories\Eloquent\LocationPartnerRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\OtpRepositoryInterface::class,
            \App\Repositories\Eloquent\OtpRepository::class
        );
        $this->app->bind(
            \App\Services\Otp\OtpProviderInterface::class,
            function ($app) {
                $provider = env('OTP_PROVIDER', 'email');
                if ($provider === 'msg91') {
                    return new \App\Services\Otp\Msg91OtpProvider();
                }
                return new \App\Services\Otp\EmailOtpProvider();
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        try {
            \Illuminate\Support\Facades\View::composer('*', function ($view) {
                $settings = \Illuminate\Support\Facades\Cache::remember('website_settings', 3600, function () {
                    return \App\Models\WebsiteSetting::first();
                });
                $branding = \Illuminate\Support\Facades\Cache::remember('website_branding', 3600, function () {
                    return \App\Models\WebsiteBranding::first();
                });
                $socialLinks = \Illuminate\Support\Facades\Cache::remember('website_social_links', 3600, function () {
                    return \App\Models\WebsiteSocialLink::where('status', true)->get();
                });

                $view->with([
                    'globalSettings' => $settings,
                    'globalBranding' => $branding,
                    'globalSocialLinks' => $socialLinks
                ]);
            });
        } catch (\Exception $e) {
            // Do nothing if tables are missing
        }
    }
}
