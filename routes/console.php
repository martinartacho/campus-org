<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
// use App\Console\Commands\ChangeDefaultPasswords;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::command('notifications:send-pending-push')->everyMinute();
Schedule::command('logs:clean-push')->dailyAt('02:00');


/*Artisan::starting(function ($artisan) {
    $artisan->resolveCommands([
        ChangeDefaultPasswords::class,
    ]);
});*/
