<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    echo "Email: " . $user->email . "\n";
} else {
    echo "No users found.\n";
}
