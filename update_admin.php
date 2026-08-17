<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\User::role('Admin')->first();

if (!$admin) {
    echo "Admin user not found using role. Trying by email...\n";
    $admin = \App\Models\User::where('email', 'admin@hyperadz.in')->first();
}

if ($admin) {
    $admin->email = 'akil.1bussiness@gmail.com';
    $admin->password = \Illuminate\Support\Facades\Hash::make('Akil@123');
    $admin->save();
    echo "Admin credentials updated successfully!\n";
} else {
    echo "Admin user could not be found to update.\n";
}
