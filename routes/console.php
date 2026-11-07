<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Daily Statutory Background Sweeps (48h challan, 72h reviewer nudges, 24h appeals)
Schedule::command('hirely:run-statutory-sweeps')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground();

