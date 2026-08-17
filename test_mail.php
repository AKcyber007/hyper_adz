<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::to('akil.sp2004@gmail.com')
        ->send(new \App\Mail\LoginOtpMail('123456'));
    echo "Test email sent successfully!\n";
} catch (\Exception $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";
}
