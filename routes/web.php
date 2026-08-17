<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingsController;

// --- Debug routes (temporary) ---
Route::get('/test', function () {
    return 'Laravel is working';
});

Route::get('/debug', function () {
    return [
        'app_env'        => env('APP_ENV'),
        'app_debug'      => env('APP_DEBUG'),
        'app_key_exists' => !empty(env('APP_KEY')),
        'db_connection'  => env('DB_CONNECTION'),
    ];
});
// --- End debug routes ---

// --- Mock Zoho Payment Route for Local Dev ---
Route::get('/mock-checkout/{payment_link_id}', function ($payment_link_id) {
    $campaign = \App\Models\Campaign::where('zoho_payment_link_id', $payment_link_id)->firstOrFail();
    
    $campaign->update([
        'status' => 'Scheduled',
        'payment_status' => 'Paid',
        'zoho_payment_id' => 'mock_txn_' . uniqid(),
        'payment_paid_at' => now(),
    ]);

    $admin = \App\Models\User::role('Admin')->first();
    if ($admin) {
        $admin->notify(new \App\Notifications\CampaignPaidNotification($campaign));
    }

    return redirect()->route('advertiser.dashboard')->with('success', 'Mock payment successful! The campaign is now marked as Paid.');
});
// ----------------------------------------------

// Public marketing routes
// /become-a-partner now redirects to the Contact page (partner CTA removed per business requirements)
$pages = [
    '/about'             => ['about',   'about'],
    '/services'          => ['services','services'],
    '/why-hyper-adz'     => ['why',     'why'],
    '/contact'           => ['contact', 'contact'],
    '/enquiry'           => ['enquiry', 'enquiry'],
];

foreach ($pages as $uri => [$name, $view]) {
    Route::view($uri, $view)->name($name);
}

// Public Policy Pages (Dynamic)
Route::get('/privacy-policy', [\App\Http\Controllers\PublicPolicyController::class, 'show'])->defaults('type', 'privacy')->name('privacy');
Route::get('/terms-conditions', [\App\Http\Controllers\PublicPolicyController::class, 'show'])->defaults('type', 'terms')->name('terms');
Route::get('/refund-policy', [\App\Http\Controllers\PublicPolicyController::class, 'show'])->defaults('type', 'refund')->name('refund');
Route::get('/cookie-policy', [\App\Http\Controllers\PublicPolicyController::class, 'show'])->defaults('type', 'cookie')->name('cookie');

// Dynamic Homepage with Blogs
Route::get('/', function () {
    $latestBlogs = \App\Models\Blog::published()->latestFirst()->limit(3)->get();
    return view('home', compact('latestBlogs'));
})->name('home');

// Redirect become-a-partner to contact page (standalone partner page removed per requirements)
Route::redirect('/become-a-partner', '/contact')->name('partner');

Route::get('/network', function () {
    $categories = \App\Models\LocationCategory::where('status', 'active')->get();
    $cities = \App\Models\Location::where('status', 'active')->distinct()->pluck('city')->filter()->toArray();
    return view('network', compact('categories', 'cities'));
})->name('network');

// Public FAQ Page
Route::get('/faqs', [\App\Http\Controllers\FaqController::class, 'index'])->name('faqs');

// Public Blog Pages
Route::get('/blog', [\App\Http\Controllers\PublicBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\PublicBlogController::class, 'show'])->name('blog.show');

// Protected Admin Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/map-settings', [\App\Http\Controllers\Admin\MapSettingsController::class, 'index'])->name('map-settings');

    // Location Management
    Route::middleware('permission:manage-locations')->group(function () {
        Route::get('/locations/map', [\App\Http\Controllers\Admin\LocationController::class, 'map'])->name('locations.map');
        Route::post('/locations/map/store', [\App\Http\Controllers\Admin\LocationController::class, 'storeFromMap'])->name('locations.map.store');
        Route::post('/locations/map/{id}/update', [\App\Http\Controllers\Admin\LocationController::class, 'updateFromMap'])->name('locations.map.update');
        Route::get('/locations/categories', [\App\Http\Controllers\Admin\LocationController::class, 'categories'])->name('locations.categories');
        Route::get('/locations/update-requests', [\App\Http\Controllers\Admin\LocationController::class, 'updateRequests'])->name('locations.update-requests');
        Route::post('/locations/update-requests/{id}/approve', [\App\Http\Controllers\Admin\LocationController::class, 'approveRequest'])->name('locations.update-requests.approve');
        Route::post('/locations/update-requests/{id}/reject', [\App\Http\Controllers\Admin\LocationController::class, 'rejectRequest'])->name('locations.update-requests.reject');
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
        
        // Location Reviews
        Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{id}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Screen Management (DEPRECATED per business requirements - location is the advertising unit)
    // Routes kept only as redirects to locations so direct URL hits don't 404
    Route::middleware('permission:manage-screens')->group(function () {
        Route::redirect('/screens',           '/admin/locations')->name('screens.index');
        Route::redirect('/screens/create',    '/admin/locations')->name('screens.create');
        Route::redirect('/screens/dashboard', '/admin/locations')->name('screens.dashboard');
        Route::redirect('/screens/activity-logs', '/admin/locations')->name('screens.activity');
        // Catch-all pattern for /screens/{id} and /screens/{id}/edit
        Route::get('/screens/{id}',       fn() => redirect('/admin/locations'))->name('screens.show');
        Route::get('/screens/{id}/edit',  fn() => redirect('/admin/locations'))->name('screens.edit');
        Route::post('/screens',           fn() => redirect('/admin/locations'))->name('screens.store');
        Route::put('/screens/{id}',       fn() => redirect('/admin/locations'))->name('screens.update');
        Route::delete('/screens/{id}',    fn() => redirect('/admin/locations'))->name('screens.destroy');
    });

    // Lead Management (Dashboard redirects to Leads list)
    Route::middleware('permission:manage-leads')->group(function () {
        Route::redirect('/leads/dashboard', '/admin/leads')->name('leads.dashboard');
        Route::post('/leads/{id}/assign', [\App\Http\Controllers\Admin\LeadController::class, 'assignSelf'])->name('leads.assign');
        Route::put('/leads/{id}/status', [\App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('leads.updateStatus');
        Route::post('/leads/{id}/remarks', [\App\Http\Controllers\Admin\LeadController::class, 'addRemarks'])->name('leads.remarks');
        Route::post('/leads/{id}/approve', [\App\Http\Controllers\Admin\LeadController::class, 'approve'])->name('leads.approve');
        Route::post('/leads/{id}/reject', [\App\Http\Controllers\Admin\LeadController::class, 'reject'])->name('leads.reject');
        Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class);
    });

    // Advertiser Management
    Route::middleware('permission:manage-advertisers')->group(function () {
        Route::redirect('/advertisers/dashboard', '/admin/advertisers')->name('advertisers.dashboard');
        Route::post('/advertisers/convert/{lead_id}', [\App\Http\Controllers\Admin\AdvertiserController::class, 'convertLead'])->name('advertisers.convert');
        Route::put('/advertisers/{id}/status', [\App\Http\Controllers\Admin\AdvertiserController::class, 'updateStatus'])->name('advertisers.updateStatus');
        Route::get('/advertisers/{id}/campaigns', [\App\Http\Controllers\Admin\AdvertiserController::class, 'campaigns'])->name('advertisers.campaigns');
        Route::resource('advertisers', \App\Http\Controllers\Admin\AdvertiserController::class);
        // Industries kept as a backend-only route (no longer in main nav) so advertiser create/edit still works
        Route::resource('industries', \App\Http\Controllers\Admin\IndustryController::class);
    });

    // Location Partner Management (Dashboard redirects to Partner list)
    Route::middleware('permission:manage-location-partners')->group(function () {
        Route::redirect('/location-partners/dashboard', '/admin/location-partners')->name('location-partners.dashboard');
        Route::post('/location-partners/convert/{lead_id}', [\App\Http\Controllers\Admin\LocationPartnerController::class, 'convertLead'])->name('location-partners.convert');
        Route::put('/location-partners/{id}/status', [\App\Http\Controllers\Admin\LocationPartnerController::class, 'updateStatus'])->name('location-partners.updateStatus');
        Route::post('/location-partners/{id}/locations', [\App\Http\Controllers\Admin\LocationPartnerController::class, 'assignLocations'])->name('location-partners.locations.assign');
        Route::delete('/location-partners/locations/{location_id}', [\App\Http\Controllers\Admin\LocationPartnerController::class, 'removeLocation'])->name('location-partners.locations.remove');
        Route::resource('location-partners', \App\Http\Controllers\Admin\LocationPartnerController::class);
    });

    // Advertising Management
    Route::middleware('permission:manage-campaigns')->group(function () {
        Route::get('/advertising/requests', [\App\Http\Controllers\Admin\CampaignController::class, 'index'])->name('advertising.requests');
        Route::get('/advertising/requests/{id}', [\App\Http\Controllers\Admin\CampaignController::class, 'show'])->name('advertising.requests.show');
        Route::post('/advertising/requests/{id}/approve', [\App\Http\Controllers\Admin\CampaignController::class, 'approve'])->name('advertising.requests.approve');
        Route::post('/advertising/requests/{id}/reverse-approval', [\App\Http\Controllers\Admin\CampaignController::class, 'reverseApproval'])->name('advertising.requests.reverse-approval');
        Route::post('/advertising/requests/{id}/reject', [\App\Http\Controllers\Admin\CampaignController::class, 'reject'])->name('advertising.requests.reject');
        Route::put('/advertising/requests/{id}/confirm-payment', [\App\Http\Controllers\Admin\CampaignController::class, 'confirmPayment'])->name('advertising.requests.confirm-payment');
        Route::post('/advertising/requests/{id}/upload-report', [\App\Http\Controllers\Admin\CampaignController::class, 'uploadReport'])->name('advertising.requests.upload-report');

        Route::get('/advertising/creatives', function () {
            return view('admin.coming-soon', ['title' => 'Creative Approvals']);
        })->name('advertising.creatives');
        Route::get('/advertising/active', function () {
            return view('admin.coming-soon', ['title' => 'Active Campaigns']);
        })->name('advertising.active');
        Route::get('/advertising/reports', function () {
            return view('admin.coming-soon', ['title' => 'Reports & Invoices']);
        })->name('advertising.reports');
    });

    // FAQ Management (CMS)
    Route::middleware('permission:manage-faqs')->group(function () {
        Route::resource('faq-categories', \App\Http\Controllers\Admin\FaqCategoryController::class)->except(['show', 'create', 'edit']);
        Route::post('/faq-categories/reorder', [\App\Http\Controllers\Admin\FaqCategoryController::class, 'reorder'])->name('faq-categories.reorder');
        Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class)->except(['show']);
        Route::patch('/faqs/{id}/toggle-status', [\App\Http\Controllers\Admin\FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');
        Route::post('/faqs/reorder', [\App\Http\Controllers\Admin\FaqController::class, 'reorder'])->name('faqs.reorder');
    });

    // Blog Management (CMS)
    Route::middleware('permission:manage-blogs')->group(function () {
        Route::resource('blogs', \App\Http\Controllers\Admin\BlogController::class)->except(['show']);
        Route::patch('/blogs/{id}/toggle-status', [\App\Http\Controllers\Admin\BlogController::class, 'toggleStatus'])->name('blogs.toggle-status');
        Route::patch('/blogs/{id}/toggle-feature', [\App\Http\Controllers\Admin\BlogController::class, 'toggleFeature'])->name('blogs.toggle-feature');
        Route::post('/blogs/upload-image', [\App\Http\Controllers\Admin\BlogController::class, 'uploadImage'])->name('blogs.upload-image');
    });

    // Website Management (CMS)
    Route::middleware('permission:manage-website')->name('website.')->prefix('website')->group(function () {
        Route::get('/content', [\App\Http\Controllers\Admin\Website\ContentController::class, 'index'])->name('content.index');
        Route::put('/content', [\App\Http\Controllers\Admin\Website\ContentController::class, 'update'])->name('content.update');
        
        Route::get('/branding', [\App\Http\Controllers\Admin\Website\BrandingController::class, 'index'])->name('branding.index');
        Route::post('/branding', [\App\Http\Controllers\Admin\Website\BrandingController::class, 'update'])->name('branding.update');
        
        Route::resource('social-links', \App\Http\Controllers\Admin\Website\SocialMediaController::class)->except(['show']);
        Route::resource('policies', \App\Http\Controllers\Admin\Website\PolicyController::class)->except(['show', 'create', 'store', 'destroy']);
        
        // Partner Brands Management
        Route::get('/partner-brands', [\App\Http\Controllers\Admin\Website\PartnerBrandController::class, 'index'])->name('partner-brands.index');
        Route::post('/partner-brands', [\App\Http\Controllers\Admin\Website\PartnerBrandController::class, 'store'])->name('partner-brands.store');
        Route::delete('/partner-brands/{filename}', [\App\Http\Controllers\Admin\Website\PartnerBrandController::class, 'destroy'])->name('partner-brands.destroy');
    });
});

// Public Location Details Route
Route::get('/locations/{slug}', [\App\Http\Controllers\PublicLocationController::class, 'show'])->name('locations.details');

// Public Lead Submission Route
Route::post('/leads', [\App\Http\Controllers\PublicLeadController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('leads.store');

// Advertiser Auth & Protected Routes
Route::prefix('advertiser')->name('advertiser.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AdvertiserAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AdvertiserAuthController::class, 'requestOtp'])->name('login.post');
    Route::get('/login/verify', [\App\Http\Controllers\AdvertiserAuthController::class, 'showVerify'])->name('login.verify');
    Route::post('/login/verify', [\App\Http\Controllers\AdvertiserAuthController::class, 'verifyOtp'])->name('login.verify.post');
    Route::post('/logout', [\App\Http\Controllers\AdvertiserAuthController::class, 'logout'])->name('logout');

    // Protected Workspace
    Route::middleware('advertiser')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Advertiser\PortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/requests', [\App\Http\Controllers\Advertiser\PortalController::class, 'myRequests'])->name('my-requests');
        Route::get('/requests/create', [\App\Http\Controllers\Advertiser\PortalController::class, 'createRequest'])->name('my-requests.create');
        Route::post('/requests', [\App\Http\Controllers\Advertiser\PortalController::class, 'storeRequest'])->name('my-requests.store');
        Route::get('/requests/{id}', [\App\Http\Controllers\Advertiser\PortalController::class, 'showRequest'])->name('my-requests.show');
        Route::get('/requests/{id}/edit', [\App\Http\Controllers\Advertiser\PortalController::class, 'editRequest'])->name('my-requests.edit');
        Route::post('/requests/{id}/update', [\App\Http\Controllers\Advertiser\PortalController::class, 'updateRequest'])->name('my-requests.update');
        Route::post('/requests/{id}/resubmit', [\App\Http\Controllers\Advertiser\PortalController::class, 'resubmitRequest'])->name('my-requests.resubmit');
        Route::get('/map', [\App\Http\Controllers\Advertiser\PortalController::class, 'map'])->name('map');
        
        Route::get('/requests/{id}/download-report', [\App\Http\Controllers\Advertiser\PortalController::class, 'downloadReport'])->name('my-requests.report.download');
        Route::redirect('/reports', '/advertiser/requests')->name('reports');
        Route::redirect('/notifications', '/advertiser/dashboard');
        Route::get('/profile', [\App\Http\Controllers\Advertiser\PortalController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [\App\Http\Controllers\Advertiser\PortalController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Advertiser\PortalController::class, 'updateProfile'])->name('profile.update');

        // Compat aliases to prevent test breakage
        Route::redirect('/campaigns', '/advertiser/requests')->name('campaigns');
        Route::redirect('/bookings', '/advertiser/requests')->name('bookings');
    });
});

// Location Partner Auth & Protected Routes
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\PartnerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\PartnerAuthController::class, 'requestOtp'])->name('login.post');
    Route::get('/login/verify', [\App\Http\Controllers\PartnerAuthController::class, 'showVerify'])->name('login.verify');
    Route::post('/login/verify', [\App\Http\Controllers\PartnerAuthController::class, 'verifyOtp'])->name('login.verify.post');
    Route::post('/logout', [\App\Http\Controllers\PartnerAuthController::class, 'logout'])->name('logout');

    // Protected Workspace
    Route::middleware('location_partner')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Partner\PortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/location-requests', [\App\Http\Controllers\Partner\PortalController::class, 'locationRequests'])->name('location-requests');
        Route::delete('/location-requests/{id}', [\App\Http\Controllers\Partner\PortalController::class, 'cancelLocationRequest'])->name('location-requests.cancel');
        Route::get('/map', [\App\Http\Controllers\Partner\PortalController::class, 'map'])->name('map');
        Route::get('/campaigns', [\App\Http\Controllers\Partner\PortalController::class, 'campaignActivity'])->name('campaigns');
        Route::get('/notifications', [\App\Http\Controllers\Partner\PortalController::class, 'notifications'])->name('notifications');
        Route::get('/profile', [\App\Http\Controllers\Partner\PortalController::class, 'profile'])->name('profile');
        Route::get('/profile/edit', [\App\Http\Controllers\Partner\PortalController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Partner\PortalController::class, 'updateProfile'])->name('profile.update');

        // Location & Screen resources (screens resource kept to prevent test breakage)
        Route::resource('locations', \App\Http\Controllers\Partner\LocationController::class);

        // Screen routes DEPRECATED — partner no longer manages screens directly
        // Kept as redirects to prevent 404 on any stale bookmarks
        Route::get('/screens',          fn() => redirect()->route('partner.locations.index'))->name('partner.screens.index');
        Route::get('/screens/create',   fn() => redirect()->route('partner.locations.index'))->name('partner.screens.create');
        Route::get('/screens/{id}',     fn() => redirect()->route('partner.locations.index'))->name('partner.screens.show');
        Route::get('/screens/{id}/edit',fn() => redirect()->route('partner.locations.index'))->name('partner.screens.edit');
        Route::post('/screens',         fn() => redirect()->route('partner.locations.index'))->name('partner.screens.store');
        Route::put('/screens/{id}',     fn() => redirect()->route('partner.locations.index'))->name('partner.screens.update');
        Route::delete('/screens/{id}',  fn() => redirect()->route('partner.locations.index'))->name('partner.screens.destroy');

        // Compat aliases
        Route::redirect('/approvals', '/partner/location-requests')->name('approvals');
    });
});

require __DIR__.'/auth.php';

// Route Redirect Handlers for multi-guard authenticated users
Route::get('/dashboard', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (Auth::guard('advertiser')->check()) {
        return redirect()->route('advertiser.dashboard');
    }
    if (Auth::guard('location_partner')->check()) {
        return redirect()->route('partner.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth:web,advertiser,location_partner'])->name('dashboard');

Route::get('/home', function () {
    return redirect()->route('home');
})->name('home_redirect');

