<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendTrialEmails;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| Run trial email automation every day at 09:00
| Cron on server: * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
*/

Schedule::job(new SendTrialEmails)->dailyAt('09:00')
    ->name('send-trial-emails')
    ->withoutOverlapping()
    ->onOneServer();
