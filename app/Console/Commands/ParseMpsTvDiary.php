<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;

use Vanguard\Libraries\MpsImporter\TvDiary;

class ParseMpsTvDiary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mps:parse-tv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan command for parsing tv mps data';

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
        try{
            $start = microtime(true);

            $tv_importer = new TvDiary();
            $tv_importer->import('s3://whatever');

            $time_elapsed_secs = microtime(true) - $start;

            $this->info('Command executed in ' . $time_elapsed_secs . ' seconds.');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
