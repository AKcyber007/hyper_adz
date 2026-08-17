<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NetworkLocationController;
// Public / Authenticated Network Routes
Route::get('/network/locations', [NetworkLocationController::class, 'index']);
Route::get('/network/locations/{id}/reviews', [\App\Http\Controllers\Api\LocationInteractionController::class, 'getReviews']);

Route::middleware('auth')->group(function () {
    Route::post('/network/locations/{id}/reviews', [\App\Http\Controllers\Api\LocationInteractionController::class, 'storeReview']);
    Route::post('/network/locations/{id}/favorite', [\App\Http\Controllers\Api\LocationInteractionController::class, 'toggleFavorite']);
});


// Protected Partner JSON APIs
Route::middleware('auth:location_partner')->prefix('partner')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Api\PartnerDashboardController::class, 'index']);
    Route::get('/locations', [\App\Http\Controllers\Api\PartnerDashboardController::class, 'locations']);
    Route::get('/screens', [\App\Http\Controllers\Api\PartnerDashboardController::class, 'screens']);
});

// Protected Advertiser JSON APIs
Route::middleware('auth:advertiser')->prefix('advertiser')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Api\AdvertiserDashboardController::class, 'index']);
});

// Zoho Payments Webhook
Route::post('/webhooks/zoho/payments', [\App\Http\Controllers\Webhook\ZohoPaymentsWebhookController::class, 'handle']);
