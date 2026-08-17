<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$otp = \App\Models\OtpVerification::where('phone', '9994206375')->latest()->first();
if ($otp) {
    $otp->otp_code = \Illuminate\Support\Facades\Hash::make('123456');
    $otp->save();
    echo "OTP updated to 123456\n";
} else {
    echo "No OTP found for this number.\n";
}
