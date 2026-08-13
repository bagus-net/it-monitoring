<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    /**
     * Jadwal pengecekan situs otomatis.
     * Pastikan cron server sudah dikonfigurasi untuk menjalankan `schedule:run`
     * setiap menit (lihat README.md).
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('monitor:check')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
