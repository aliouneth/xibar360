<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check container bindings
$bindings = $app->getBindings();
foreach($bindings as $abstract => $concrete) {
    if (str_contains($abstract, 'Guard') || str_contains($abstract, 'Auth')) {
        $type = is_string($concrete) ? 'string' : (is_object($concrete) ? get_class($concrete) : gettype($concrete));
        echo "$abstract => $type" . PHP_EOL;
    }
}

echo "\n--- Checking Guard binding specifically ---\n";
echo "Guard bound: " . ($app->bound(\Illuminate\Contracts\Auth\Guard::class) ? 'YES' : 'NO') . PHP_EOL;
if ($app->bound(\Illuminate\Contracts\Auth\Guard::class)) {
    $guard = $app->make(\Illuminate\Contracts\Auth\Guard::class);
    echo "Guard instance: " . get_class($guard) . PHP_EOL;
    echo "Guard ID: " . ($guard->id() ?? 'null') . PHP_EOL;
}