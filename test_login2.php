<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate the controller flow
$user = App\Models\User::where('email', 'admin@example.com')->first();
echo "User: {$user->name} (ID: {$user->id})" . PHP_EOL;

// Login
Auth::login($user);
echo "After Auth::login - auth()->id(): " . (auth()->id() ?? 'null') . PHP_EOL;
echo "Guard ID: " . (auth()->guard()->id() ?? 'null') . PHP_EOL;

// Get session
$session = $app['session'];
echo "Session ID before save: " . $session->getId() . PHP_EOL;

// Save session
$session->save();
echo "After session()->save()" . PHP_EOL;

// Check session in DB
$sessionId = $session->getId();
$dbSession = DB::table('sessions')->where('id', $sessionId)->first();
if ($dbSession) {
    echo "Session in DB - User ID: " . ($dbSession->user_id ?? 'null') . PHP_EOL;
} else {
    echo "Session not found in DB!" . PHP_EOL;
}