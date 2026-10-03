<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

$route = Route::getRoutes()->getByName('lang.switch');
echo "Route name: " . ($route->getName() ?? 'N/A') . "\n";
echo "Route URI: " . ($route->uri() ?? 'N/A') . "\n";
echo "Route methods: " . implode('|', $route->methods()) . "\n";

// Check if the route exists
$route2 = \Illuminate\Support\Facades\Route::getRoutes()->getByName('lang.switch');
if ($route2) {
    echo "Route found: " . $route2->getName() . "\n";
} else {
    echo "Route NOT found by name 'lang.switch'\n";
}

// Check if the route exists by URI
$routes = \Illuminate\Support\Facades\Route::getRoutes();
foreach ($routes as $r) {
    if ($r->uri() === 'lang/{locale}') {
        echo "Found route: " . $r->getName() . " - " . $r->uri() . "\n";
        echo "Action: " . json_encode($r->getAction()) . "\n";
    }
}