<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ad;

foreach (Ad::where('title', 'Date Check Ad')->get() as $ad) {
    echo "#{$ad->id} [{$ad->zone}] start=".var_export($ad->start_date?->toDateString(), true)
        ." end=".var_export($ad->end_date?->toDateString(), true)
        ." active=".var_export($ad->is_active, true)
        ." image=".var_export($ad->image, true)."\n";
}
