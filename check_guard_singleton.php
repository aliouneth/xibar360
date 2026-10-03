<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if Guard binding is a singleton
$binding = $app->getBindings()[\Illuminate\Contracts\Auth\Guard::class] ?? null;
echo "Guard binding type: " . gettype($binding) . PHP_EOL;
if (is_array($binding)) {
    echo "Binding keys: " . implode(', ', array_keys($binding)) . PHP_EOL;
    echo "Shared: " . ($binding['shared'] ?? 'not set') . PHP_EOL;
}

// Try making it twice
$guard1 = $app->make(\Illuminate\Contracts\Auth\Guard::class);
$guard2 = $app->make(\Illuminate\Contracts\Auth\Guard::class);
echo "Same instance: " . ($guard1 === $guard2 ? 'YES' : 'NO') . PHP_EOL;
echo "Guard1 ID: " . ($guard1->id() ?? 'null') . PHP_EOL;
echo "Guard2 ID: " . ($guard2->id() ?? 'null') . PHP_EOL;

// Now login and check
$user = App\Models\User::where('email', 'admin@example.com')->first();
Auth::login($user);
echo "\nAfter Auth::login:" . PHP_EOL;
echo "Auth::id(): " . (Auth::id() ?? 'null') . PHP_EOL;
echo "Guard1 ID: " . ($guard1->id() ?? 'null') . PHP_EOL;
echo "Guard2 ID: " . ($guard2->id() ?? 'null') . PHP_EOL;

$guard3 = $app->make(\Illuminate\Contracts\Auth\Guard::class);
echo "Guard3 (new) ID: " . ($guard3->id() ?? 'null') . PHP_EOL;