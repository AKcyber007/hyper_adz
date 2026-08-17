<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$campaign = \App\Models\Campaign::find(1);
if ($campaign) {
    $mockLinkId = 'mock_plink_' . uniqid();
    $campaign->zoho_payment_link_id = $mockLinkId;
    $campaign->zoho_payment_url = url('/mock-checkout/' . $mockLinkId);
    $campaign->save();
    echo "Updated Campaign 1 URL to local mock endpoint\n";
}
