<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the most recent session with payload
$sessions = DB::table('sessions')->orderBy('last_activity', 'desc')->limit(3)->get();
foreach($sessions as $s) {
    echo "ID: {$s->id} | User: " . ($s->user_id ?? 'null') . " | Last: {$s->last_activity}" . PHP_EOL;
    $payload = json_decode($s->payload, true);
    if ($payload) {
        echo "  Payload keys: " . implode(', ', array_keys($payload)) . PHP_EOL;
        if (isset($payload['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'])) {
            echo "  login_web: " . $payload['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'] . PHP_EOL;
        }
    }
    echo PHP_EOL;
}