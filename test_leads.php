<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lead;

// Clean up test leads
Lead::where('email', 'test.ds@example.com')->delete();

$request = Request::create('/leads', 'POST', [
    'name' => 'Test DS Buyer',
    'email' => 'test.ds@example.com',
    'phone' => '1234567890',
    'lead_type' => 'digital_signage',
    'message' => 'Testing DS lead'
]);

$response = app()->handle($request);
echo "Response 1 Status: " . $response->getStatusCode() . "\n";
echo "Response 1 Body: " . $response->getContent() . "\n";

// Duplicate submission
$request2 = Request::create('/leads', 'POST', [
    'name' => 'Test DS Buyer 2',
    'email' => 'test.ds@example.com',
    'phone' => '1234567890',
    'lead_type' => 'digital_signage',
    'message' => 'Testing duplicate'
]);
$response2 = app()->handle($request2);
echo "Response 2 Status: " . $response2->getStatusCode() . "\n";
echo "Response 2 Body: " . $response2->getContent() . "\n";

// Sales Partner
$request3 = Request::create('/leads', 'POST', [
    'name' => 'Test SP Partner',
    'email' => 'test.sp@example.com',
    'phone' => '0987654321',
    'lead_type' => 'sales_partner',
    'message' => 'Testing SP lead'
]);
$response3 = app()->handle($request3);
echo "Response 3 Status: " . $response3->getStatusCode() . "\n";
echo "Response 3 Body: " . $response3->getContent() . "\n";

// Check if they are in DB
$count = Lead::whereIn('email', ['test.ds@example.com', 'test.sp@example.com'])->count();
echo "Leads in DB: $count\n";

// Cleanup
Lead::whereIn('email', ['test.ds@example.com', 'test.sp@example.com'])->delete();
