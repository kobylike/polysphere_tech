<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('sitemap:generate')->dailyAt('02:00')->withoutOverlapping();

Schedule::command('queue:work --max-time=55 --sleep=2 --tries=3')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
