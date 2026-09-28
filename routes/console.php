<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Import new articles from the configured RSS/Atom sources at 05:00, 07:00,
// 11:00 and 17:00 GMT, every day.
//
// The timezone is pinned to UTC rather than left to config/app.php so these
// times stay GMT even if the app timezone is changed later (GMT and UTC share
// the same offset, so this is also safe for Africa/Dakar). withoutOverlapping()
// keeps a slow run from stacking on top of the next slot, and the 30 minute
// expiry is comfortably longer than a full 17 source import.
Schedule::command('news:scan')
    ->cron('0 5,7,11,17 * * *')
    ->timezone('UTC')
    ->withoutOverlapping(30)
    ->name('news-scan')
    ->description('Import new articles from the configured RSS/Atom sources');
