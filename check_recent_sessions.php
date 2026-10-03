<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the most recent sessions
$sessions = DB::table('sessions')->orderBy('last_activity', 'desc')->limit(5)->get();
foreach($sessions as $s) {
    echo "ID: {$s->id} | User: " . ($s->user_id ?? 'null') . " | Last: {$s->last_activity}" . PHP_EOL;
}