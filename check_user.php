<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('phone', '9994206375')->orWhere('phone', '+919994206375')->first();
if ($user) {
    echo "User found: {$user->id}\n";
    echo "Roles: " . json_encode($user->roles->pluck('name')) . "\n";
    echo "Partner profile: " . ($user->partnerProfile()->exists() ? 'yes' : 'no') . "\n";
    echo "Advertiser profile: " . ($user->advertiserProfile()->exists() ? 'yes' : 'no') . "\n";
} else {
    echo "User not found\n";
}
