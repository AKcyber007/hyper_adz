<?php

use Illuminate\Support\Facades\Route;

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

$pages = [
    '/'                  => ['home',    'home'],
    '/about'             => ['about',   'about'],
    '/services'          => ['services','services'],
    '/network'           => ['network', 'network'],
    '/why-hyper-adz'     => ['why',     'why'],
    '/contact'           => ['contact', 'contact'],
    '/become-a-partner'  => ['partner', 'partner'],
    '/privacy-policy'    => ['privacy', 'policies.privacy'],
    '/terms-conditions'  => ['terms',   'policies.terms'],
    '/refund-policy'     => ['refund',  'policies.refund'],
    '/cookie-policy'     => ['cookie',  'policies.cookie'],
];

foreach ($pages as $uri => [$name, $view]) {
    Route::view($uri, $view)->name($name);
}
