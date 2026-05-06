<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Generate monthly subscriptions on the 1st of each month at 00:05.
 * (Implementation-Rules §7 / System-Design §8)
 *
 * Manual trigger for backfills:
 *   php artisan subscriptions:generate --month=2025-05
 */
Schedule::command('subscriptions:generate')
    ->monthlyOn(1, '00:05')
    ->withoutOverlapping()
    ->runInBackground();
