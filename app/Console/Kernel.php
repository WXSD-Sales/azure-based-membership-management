<?php

namespace App\Console;

use App\Jobs\PerformCrossSync;
use App\Jobs\RefreshAzureToken;
use App\Jobs\RefreshWebexToken;
use App\Jobs\RetrieveAzureGroups;
use App\Jobs\RetrieveAzureUsers;
use App\Jobs\RetrieveWebexGroups;
use App\Jobs\RetrieveWebexUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new RefreshAzureToken())
            ->everyFifteenMinutes()
            ->withoutOverlapping();
        $schedule->job(new RefreshWebexToken())
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        $schedule->job(new RetrieveAzureUsers())
            ->daily()
            ->withoutOverlapping();
        $schedule->job(new RetrieveAzureGroups())
            ->daily()
            ->withoutOverlapping();

        $schedule->job(new RetrieveWebexUsers())
            ->daily()
            ->withoutOverlapping();
        $schedule->job(new RetrieveWebexGroups())
            ->daily()
            ->withoutOverlapping();

        $schedule->job(new PerformCrossSync())
            ->daily()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
