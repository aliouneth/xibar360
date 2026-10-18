<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ad;

foreach (['header_left', 'header_center', 'header_right'] as $zone) {
    Ad::create(['title' => "HT $zone", 'zone' => $zone, 'image' => 'ads/m6RO8UntXfxzY2eHuO0tqg0HUiWbjtqpsM2UIKpV.jpg', 'is_active' => true]);
}

$request = Illuminate\Http\Request::create('/', 'GET');
$content = $app->handle($request)->getContent();
foreach (['header_left', 'header_center', 'header_right'] as $zone) {
    echo "$zone occurrences: ".substr_count($content, 'data-ad-zone="'.$zone.'"')."\n";
}
echo "header_ad grid occurrences: ".substr_count($content, 'grid grid-cols-1 md:grid-cols-3')."\n";

Ad::where('title', 'like', 'HT %')->delete();
echo "cleaned up\n";
