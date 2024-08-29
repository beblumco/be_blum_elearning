<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $maxAge =now()->subMinutes(60)->setTimezone('UTC'); // Una hora atrás
            $files = Storage::files('public/qr_codes');
            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp(Storage::lastModified($file), 'UTC');
                if ($fileTime <= $maxAge) {
                    Storage::delete($file);
                }
            }
        })->everyMinute(); // Ejecutar cada hora

        $schedule->command('token:refresh')->cron('0 6 * * *');
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
