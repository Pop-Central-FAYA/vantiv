<?php

namespace Vanguard\Console\Commands;

use Illuminate\Console\Command;
use Vanguard\Libraries\Utilities;

class ValidateCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ValidateCampaigns:validatecampaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validating if a campaign is expired and relieve the adslots off the time space consumed';

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
        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE campaign_status = 0 AND stop_date < current_date GROUP BY campaign_id");
        $adslot_arrays = [];
        foreach ($campaigns as $campaign){
            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where id IN ($campaign->adslots_id) ORDER BY time_created DESC");
            foreach ($adslots as $adslot){
                $files = Utilities::switch_db('api')->select("SELECT * from files where adslot = '$adslot->id' AND campaign_id = '$campaign->campaign_id'");
                $adslot_arrays[] = [
                    'file' => $files,
                ];
            }

        }

        $flatten_arrays = Utilities::array_flatten($adslot_arrays);

        foreach ($flatten_arrays as $flatten_array){
            $adslots = Utilities::switch_db('api')->select("SELECT * FROM adslots where id = '$flatten_array->adslot'");
            $time_picked = (integer)$flatten_array->time_picked;
            $new_time_used = (integer)$adslots[0]->time_used - $time_picked;
            $update_adslot = Utilities::switch_db('api')->update("UPDATE adslots set time_used = '$new_time_used' where id = '$flatten_array->adslot'");
        }

        foreach ($campaigns as $campaign){
            $update_campaign = Utilities::switch_db('api')->update("UPDATE campaignDetails set campaign_status = 1 where campaign_id = '$campaign->campaign_id' AND stop_date < current_date ");
        }
    }
}
