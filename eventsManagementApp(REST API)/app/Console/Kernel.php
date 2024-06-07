<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:send-event-reminders')->daily();
        // ->everyMinute() then run: php artisan schedule:work or php artisan app:send-event-reminders - to run manualy 
        // cron command to configure it on server: * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
                                                        
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
