<?php

use Illuminate\Database\Seeder;

use \Vanguard\Models\FakeTimeBeltRevenue;
use \Vanguard\Models\TimeBelt;
use \Vanguard\Models\TimeBeltTransaction;
use \Vanguard\Models\SelectedAdslot;
use \Vanguard\Models\CampaignDetail;
use \Vanguard\Models\AdslotPrice;
use \Vanguard\Models\Adslot;

use \Carbon\Carbon;

/**
 * This seeder is to generate fake timebelt transactions
 * (THIS IS NOT TO BE RUN ON PRODUCTION AND SHOULD BE REMOVED AFTER A WHILE)
 * (THIS IS TO GENERATE TRANSACTIONS TO PULL REPORTS FROM ONLY)
 */
class FakeTimeBeltTransactionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     * select
     * a.from_to_time as time_belt,
     * cd.id as campaign_details_id,
     * sa.time_picked as duration, 
     * sa.file_name,
     * sa.file_url,
     * sa.adslot_amount as amount_paid,
     * sa.air_date as playout_date,
     * sa.status as approval_status 
     * from campaignDetails cd
     * join adslots a on a.id in (cd.adslots)
     * left join selected_adslots sa on sa.adslot = a.id
     * order by a.id;
     * @return void
     */
    // public function run()
    // {
    //     $existing = TimeBeltTransaction::all()->first();
    //     if ($existing !== null) {
    //         echo "You need to manually delete before this seeder can run";
    //         return;
    //     }

    //     $collection = DB::table('campaignDetails as cd')
    //         ->selectRaw('cd.start_date, cd.stop_date, a.id as adslot_id, cd.launched_on, a.from_to_time, cd.id as cd_id, sa.time_picked, sa.file_name, sa.file_url, sa.adslot_amount, sa.air_date, sa.status')
    //         ->join('adslots as a', function($query) {
    //             $query->whereRaw('a.id IN (cd.adslots)');
    //         })
    //         ->leftJoin('selected_adslots as sa', 'sa.adslot', '=', 'a.id')
    //         ->get();
        
    //     $adslot_prices = AdslotPrice::all();
    //     $current_date = Carbon::now();
        
    //     $collection->each(function($item, $key) use ($adslot_prices, $current_date) {
    //         $time_belt = $this->getTimeBelt($item);

    //         $model = new TimeBeltTransaction();
    //         $model->time_belt_id = $time_belt->id;
    //         $model->campaign_details_id = $item->cd_id;
    //         if ($item->status != null) {
    //             $model = $this->setupTransactionForCompleteData($item, $model);
    //         } else {
    //             $model = $this->setupTransactionForInCompleteData($item, $model, $adslot_prices, $current_date);
    //         }
    //         $model->save();
    //     });
    // }
    
    public function run()
    {
        $existing = TimeBeltTransaction::all()->first();
        if ($existing !== null) {
            echo "You need to manually delete before this seeder can run";
            return;
        }

        $adslot_prices = AdslotPrice::all();
        $current_date = Carbon::now();

        $campaign_details_list = CampaignDetail::all();
        echo "number of campaign details: {$campaign_details_list->count()}\n";
        $campaign_details_list->each(function($campaign_details) use ($adslot_prices, $current_date) {
            $adslots_id = explode(",", str_replace("'", "", $campaign_details->adslots_id));
            $adslot_list = SelectedAdslot::whereIn("adslot", $adslots_id)
                ->selectRaw("selected_adslots.*, adslots.from_to_time")
                ->join("adslots", "adslots.id", "=", "selected_adslots.adslot")
                ->where('campaign_id', $campaign_details->campaign_id)
                ->get();
            echo "found selected adslots: {$adslot_list->count()}\n";
            if ($adslot_list->count() > 0) {
                $res = $this->setupTransactionForCompleteData($adslot_list, $campaign_details);
                $current_num = $res;
            } else {
                echo "generating random transactions\n";
                $adslot_list = Adslot::whereIn('id', $adslots_id);
                $res = $this->setupTransactionForInCompleteData(
                    $adslot_list, 
                    $campaign_details, 
                    $adslot_prices, 
                    $current_date
                );
                $current_num = $res;
            }
        });
    }

    protected function setupTransactionForCompleteData($adslot_list, $campaign_details) {
        $current_num = 0;
        foreach ($adslot_list as $adslots) {
            $model = new TimeBeltTransaction();
            $time_belt = $this->getTimeBelt($adslots->from_to_time, $adslots->air_date, $campaign_details->launched_on);
            
            if ($time_belt) {
                $model->time_belt_id = $time_belt->id;
                $model->campaign_details_id = $campaign_details->id;
                $model->playout_date = $adslots->air_date;
                $model->duration = $adslots->time_picked;
                $model->file_name = $adslots->file_name;
                $model->file_url = $adslots->file_url;
                $model->amount_paid = $adslots->adslot_amount;
                $model->approval_status = $adslots->status;
                $model->save();

                $current_num += 1;
                echo "{$current_num}\n";
            } else {
                echo "timebelt non existent for {$adslots->from_to_time} and {$adslots->air_date} and {$campaign_details->launched_on}\n";
            }
            
        }
        return $current_num;
    }

    protected function setupTransactionForInCompleteData($campaign_details, $adslot, $adslot_prices, $current_date) {
        $current_num = 0;
        foreach ($adslot_list as $adslots) {
            $model = new TimeBeltTransaction();
            // get a random date from the campaign details start and end date
            $start_date = new Carbon($campaign_details->start_date);
            $end_date = new Carbon($campaign_details->stop_date);
            $days = $end_date->diffInDays($start_date);
            $start_date->addDays(rand(0, $days));

            $time_belt = $this->getTimeBelt($adslots->from_to_time, $start_date, $campaign_details->launched_on);
            
            if ($time_belt) {
                $model->time_belt_id = $time_belt->id;
                $model->campaign_details_id = $campaign_details->id;
                $model->playout_date = $start_date;

                $duration = $this->getRandomDuration();
                $price = $adslot_prices->where('adslot_id', $adslots->id)->first()["price_{$duration}"];
                $status = $this->getRandomTimeBeltStatus($start_date, $current_date);

                $model->amount_paid = $price;
                $model->duration = $duration;
                $model->approval_status = $status;

                $model->save();

                $current_num += 1;
                echo "{$current_num}\n";
            } else {
                echo "timebelt non existent for {$adslots->from_to_time} and {$start_date} and {$campaign_details->launched_on}\n";
            }
            
        }
        return $current_num;
    }

    protected function getTimeBelt($from_to_time, $air_date, $launched_on) {
        $times = explode("-",  $from_to_time);
        $start_time = trim($times[0]);
        $end_time = trim($times[1]);

        $date = new \DateTime($air_date);
        $day = strtolower($date->format('l'));

        return TimeBelt::where('station_id', $launched_on)
            ->whereBetween('start_time', [$start_time, $end_time])
            ->where('day', $day)
            ->first();
    }

    protected function getRandomDuration() {
        // get a random duration between [15, 30, 45, 60]
        $random_num = (int) rand(0, 3);
        switch ($random_num) {
            case 0:
                $duration = 15;
                break;
            case 1:
                $duration = 30;
                break;
            case 2:
                $duration = 45;
                break;
            default:
                $duration = 60;
                break;
        }
        return $duration;
    }

    protected function getRandomTimeBeltStatus($playout_date, $current_date) {
        // if the playout date is lesser than current date (then 75/25 percent chance it is approved)
        $status = "pending";
        if ($playout_date < $current_date) {
            $index = rand(0, 3);
            if ($index <= 2) {
                $status = 'approved';
            }
        }
        return $status;
    }

}

// php artisan db:seed --class=FakeTimeBeltTransactionSeeder