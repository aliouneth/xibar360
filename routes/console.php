<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Import new articles from the configured RSS/Atom sources every two hours,
// on the hour (00:00, 02:00, 04:00 ... 22:00), every day.
//
// The timezone is pinned to GMT rather than left to config/app.php so the run
// times stay on GMT even if the app timezone is changed later. withoutOverlapping()
// keeps a slow run from stacking on top of the next slot, and the 30 minute expiry
// is comfortably longer than a full 17 source import.
Schedule::command('news:scan')
    ->cron('0 */2 * * *')
    ->timezone('GMT')
    ->withoutOverlapping(30)
    ->name('news-scan')
    ->description('Import new articles from the configured RSS/Atom sources');
