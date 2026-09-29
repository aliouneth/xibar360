<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ad;

$ads = Ad::all();
foreach ($ads as $ad) {
    echo "ID: {$ad->id} | Zone: {$ad->zone} | Title: {$ad->title} | Active: " . ($ad->is_active ? 'yes' : 'no') . PHP_EOL;
}