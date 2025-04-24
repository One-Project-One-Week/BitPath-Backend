<?php

use App\Console\Commands\SendDailyReminderNotify;
use App\Models\User;
use App\Notifications\DailyReminderNotify;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command(SendDailyReminderNotify::class)->everyMinute();