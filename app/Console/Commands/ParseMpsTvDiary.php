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
            //example: s3://faya-global-tools/mps-data/january-tv-diary.tar.gz
            // $bucket = $this->ask('What bucket is the file located?');
            // $key = $this->ask('What is the key of the file in s3?');

            $bucket = 'faya-global-tools';
            $key = 'mps-data/diary.tar.gz';

            $start = microtime(true);

            $tv_importer = new TvDiary();
            $res = $tv_importer->import($bucket, $key);

            $time_elapsed_secs = microtime(true) - $start;

            $this->info("Created {$res[0]} profiles");
            $this->info("Created {$res[1]} activities");
            $this->info('Command executed in ' . $time_elapsed_secs . ' seconds.');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
