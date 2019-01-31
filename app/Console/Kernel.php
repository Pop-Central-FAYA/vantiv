<?php

namespace Vanguard\Console;

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
        '\Vanguard\Console\Commands\ValidateCampaigns',
        'Vanguard\Console\Commands\ComplianceMimicReport',
        'Vanguard\Console\Commands\CreateParentCompany',
        'Vanguard\Console\Commands\CreateCompany',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('ValidateCampaigns:validatecampaigns')->hourly();

        $schedule->command('ComplianceMimicReport:mimiccompliance')->daily("01:37");
    }


    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }

}
