<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Config app.locales: \n";
print_r(config('app.locales'));
echo "\n\n";

echo "Session locale: " . session('locale', 'not set') . "\n";
echo "App locale: " . app()->getLocale() . "\n";