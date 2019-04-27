<?php

use Illuminate\Database\Seeder;

use \Vanguard\Models\FakeTimeBeltRevenue;
use \Vanguard\Models\TimeBelt;
use \Vanguard\Models\TimeBeltTransaction;
use \Vanguard\Models\SelectedAdslot;
use \Vanguard\Models\CampaignDetail;

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
     * join selected_adslots sa on sa.adslot = a.id
     * order by a.id;
     * @return void
     */
    public function run()
    {
        $existing = TimeBeltTransaction::all()->first();
        if ($existing !== null) {
            echo "You need to manually delete before this seeder can run";
            return;
        }

        $collection = DB::table('campaignDetails as cd')
            ->selectRaw('cd.launched_on, a.from_to_time, cd.id as cd_id, sa.time_picked, sa.file_name, sa.file_url, sa.adslot_amount, sa.air_date, sa.status')
            ->join('adslots as a', function($query) {
                $query->whereRaw('a.id IN (cd.adslots)');
            })
            ->join('selected_adslots as sa', 'sa.adslot', '=', 'a.id')
            ->get();
        
        $collection->each(function($item, $key) {
            $time_belt = $this->getTimeBelt($item);

            $model = new TimeBeltTransaction();
            $model->time_belt_id = $time_belt->id;
            $model->playout_date = $item->air_date;
            $model->duration = $item->time_picked;
            $model->file_name = $item->file_name;
            $model->file_url = $item->file_url;
            $model->campaign_details_id = $item->cd_id;
            $model->amount_paid = $item->adslot_amount;
            $model->approval_status = $item->status;
            $model->save();
        });
    }

    protected function getTimeBelt($item) {
        $times = explode("-",  $item->from_to_time);
        $start_time = trim($times[0]);
        $end_time = trim($times[1]);

        $date = new \DateTime($item->air_date);
        $day = strtolower($date->format('l'));

        return TimeBelt::where('station_id', $item->launched_on)
            ->whereBetween('start_time', [$start_time, $end_time])
            ->where('day', $day)
            ->first();
    }

}

// php artisan db:seed --class=FakeTimeBeltTransactionSeeder