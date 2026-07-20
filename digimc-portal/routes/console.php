<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('emails:weekly-news')->weeklyOn(1, '09:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('send:daily-news')->dailyAt('9:00');
Schedule::command('payments:sync-statuses')->everyThirtyMinutes();
