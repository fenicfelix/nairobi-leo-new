<?php

namespace App\Console;

use App\Console\Commands\ClearLogs;
use App\Console\Commands\PublishPost;
use App\Console\Commands\PullVideos;
use App\Console\Commands\ResetBreakingNews;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        PublishPost::class,
        PullVideos::class,
        ResetBreakingNews::class,
        ClearLogs::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(ResetBreakingNews::class)->everyMinute();
        $schedule->command(PublishPost::class)->everyMinute();
        $schedule->command(ClearLogs::class)->weekly();
        if(env('HAS_TV_SECTION', false)) $schedule->command(PullVideos::class)->everyTenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        $this->load(__DIR__ . '/Commands/PublishPost');
        $this->load(__DIR__ . '/Commands/PullVideos');
        $this->load(__DIR__ . '/Commands/ResetBreakingNews');
        $this->load(__DIR__ . '/Commands/ClearLogs');

        require base_path('routes/console.php');
    }
}
