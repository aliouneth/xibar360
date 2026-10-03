<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

$routes = Route::getRoutes();
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'lang')) {
        $methods = implode('|', $route->methods());
        $name = $route->action['name'] ?? 'N/A';
        echo "Route: {$methods} {$route->uri()} -> {$name}\n";
    }
}