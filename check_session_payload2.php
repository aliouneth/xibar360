<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the most recent session with payload
$sessions = DB::table('sessions')->orderBy('last_activity', 'desc')->limit(3)->get();
foreach($sessions as $s) {
    echo "ID: {$s->id} | User: " . ($s->user_id ?? 'null') . " | Last: {$s->last_activity}" . PHP_EOL;
    echo "  Payload (first 500 chars): " . substr($s->payload, 0, 500) . PHP_EOL;
    echo PHP_EOL;
}