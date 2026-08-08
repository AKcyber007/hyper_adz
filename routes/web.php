<?php

use Illuminate\Support\Facades\Route;

$pages = [
    '/' => ['home', 'home'],
    '/about' => ['about', 'about'],
    '/services' => ['services', 'services'],
    '/network' => ['network', 'network'],
    '/why-hyper-adz' => ['why', 'why'],
    '/contact' => ['contact', 'contact'],
    '/become-a-partner' => ['partner', 'partner'],
    '/privacy-policy' => ['privacy', 'policies.privacy'],
    '/terms-conditions' => ['terms', 'policies.terms'],
    '/refund-policy' => ['refund', 'policies.refund'],
    '/cookie-policy' => ['cookie', 'policies.cookie'],
];

foreach ($pages as $uri => [$name, $view]) {
    Route::view($uri, $view)->name($name);
}
