<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Session driver: ' . config('session.driver') . PHP_EOL;
echo 'Session save: ' . (config('session.save') ?? 'not set') . PHP_EOL;