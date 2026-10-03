<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test login manually
$user = App\Models\User::where('email', 'admin@example.com')->first();
echo "User: {$user->name} (ID: {$user->id})" . PHP_EOL;

// Simulate login
Auth::login($user);
echo "After Auth::login - User ID: " . (Auth::id() ?? 'null') . PHP_EOL;
echo "Session ID: " . session()->getId() . PHP_EOL;

// Save session manually
session()->save();
echo "After session()->save()" . PHP_EOL;

// Check session in DB
$sessionId = session()->getId();
$session = DB::table('sessions')->where('id', $sessionId)->first();
if ($session) {
    echo "Session in DB - User ID: " . ($session->user_id ?? 'null') . PHP_EOL;
    echo "Session payload: " . substr($session->payload, 0, 200) . "..." . PHP_EOL;
} else {
    echo "Session not found in DB!" . PHP_EOL;
}