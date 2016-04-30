<?php

namespace App\Console;

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
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       /* $schedule->command('inspire')
                 ->hourly();*/
        // 每分钟运行一次 注意windows与linux的区别
        // windows下的cron（linux下方可）以及时间都是不可引用的
        $schedule->command('queue:work --daemon --sleep=60')->everyMinute();
    }
}
