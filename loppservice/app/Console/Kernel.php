<?php

namespace App\Console;

use App\Console\Commands\CountryUpdate;
use App\Console\Commands\PingEbrevetEventApp;
use App\Console\Commands\RemoveIncompletedRegistrations;
use App\Console\Commands\ExampleEveryTwoMinutes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(CountryUpdate::class)
            ->daily();
        $schedule->command(RemoveIncompletedRegistrations::class)
            ->daily();
        $schedule->command(PingEbrevetEventApp::class)
            ->hourly();
      /*   $schedule->command(ExampleEveryTwoMinutes::class)
            ->everyTwoMinutes(); */

//        $schedule->call(function () {
//
//
//
//
//        })->everyTwoMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
