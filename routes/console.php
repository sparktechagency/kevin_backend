<?php

use App\Console\Commands\UpdateDreamStatus;
use App\Console\Commands\UpdateSubscriptionStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(UpdateSubscriptionStatus::class)->everyMinute();
Schedule::command(UpdateDreamStatus::class)->everyMinute();
