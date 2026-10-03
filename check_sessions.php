<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Sessions count: ' . DB::table('sessions')->count() . PHP_EOL;
$session = DB::table('sessions')->first();
if ($session) {
    echo 'Session ID: ' . $session->id . PHP_EOL;
    echo 'User ID: ' . ($session->user_id ?? 'null') . PHP_EOL;
}