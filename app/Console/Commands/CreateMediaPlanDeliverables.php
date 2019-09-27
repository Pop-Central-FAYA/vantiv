<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;

use Vanguard\Models\MediaPlan;
use Vanguard\Services\Ratings\StoreMediaPlanDeliverables;

/**
 * This class grabs all the media plans and regenerates the deliverables for them
 * Deliverables being cpm, cpp, gross_impressions etc
 * This is so that we can use the new deliverables feature
 * @todo Possibly remove this after pushing the deliverables feature, or make it more fully features
 */
class CreateMediaPlanDeliverables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media-plan:create-deliverables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan command for creating media plan deliverables';

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
            $collection = MediaPlan::all();
            $num_successful = 0;
            $start = microtime(true);
            foreach ($collection as $media_plan) {
                echo("Processing media plan {$media_plan->id}" . PHP_EOL);
                $service = new StoreMediaPlanDeliverables($media_plan);
                $service->run();
                $num_successful += 1;
            }
            $time_elapsed_secs = microtime(true) - $start;

            $this->info("Processed {$num_successful} media plans");
            $this->info('Command executed in ' . $time_elapsed_secs . ' seconds.');
        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
