<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Libraries\Utilities;
use Mail;

class ComplianceMimicReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ComplianceMimicReport:mimiccompliance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command mimic the compliance report of an adserver';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
