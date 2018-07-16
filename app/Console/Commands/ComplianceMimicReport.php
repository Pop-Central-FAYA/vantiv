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
        $today = date('Y-m-d');
        $file_details = [];
        $all_files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id IN (SELECT campaign_id from campaignDetails where start_date > '$today' AND stop_date > '$today' GROUP BY campaign_id)");
        foreach ($all_files as $all_file){
            $adslot_price = Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$all_file->adslot'");
            if(!$adslot_price){
                $adslot_price = Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$all_file->adslot'");
            }
            if($adslot_price[0]->price_60 === $all_file->time_picked){
                $price = $adslot_price[0]->price_60;
            }elseif($adslot_price[0]->price_45 === $all_file->time_picked){
                $price = $adslot_price[0]->price_45;
            }elseif($adslot_price[0]->price_30 === $all_file->time_picked){
                $price = $adslot_price[0]->price_30;
            }else{
                $price = $adslot_price[0]->price_15;
            }

            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where id = '$all_file->adslot'");
            $ratecard_id = $adslots[0]->rate_card;
            $rate_card = Utilities::switch_db('api')->select("SELECT * from rateCards where id = '$ratecard_id'");

            $surge_percent = Utilities::switch_db('api')->select("SELECT * from filePositions where id = (SELECT filePosition_id from adslot_filePositions where adslot_id = '$all_file->adslot' AND broadcaster_id = '$all_file->broadcaster_id')");
            if($surge_percent){
                $new_price = ($price + (($surge_percent[0]->percentage / 100) * $price));
            }else{
                $new_price = $price;
            }

            $file_details[] = [
                'price' => $new_price,
                'adslot_id' => $all_file->adslot,
                'campaign_id' => $all_file->campaign_id,
                'broadcaster_id' => $all_file->broadcaster_id,
                'media_type' => $adslots[0]->channels,
                'day' => $rate_card[0]->day,
                'hourly_range' => $rate_card[0]->hourly_range_id,
                'region' => $adslots[0]->region,
                'day_parts' => $adslots[0]->day_parts
            ];
        }

        //checking if the day the campaing file is supposed to be aired
        $record_compliance = [];
        foreach ($file_details as $file_detail){
            $today_day_letter = date('l');
            $day_id = $file_detail['day'];
            $slot_day = Utilities::switch_db('api')->select("SELECT * FROM days where id = '$day_id'");
            if($today_day_letter === $slot_day[0]->day){
                $record_compliance[] = [
                    'id' => uniqid(),
                    'campaign_id' => $file_detail['campaign_id'],
                    'adslot_id' => $file_detail['adslot_id'],
                    'amount_spent' => $file_detail['price'],
                    'broadcaster_id' => $file_detail['broadcaster_id'],
                    'channel' => $file_detail['media_type']
                ];

            }
        }

        //insert compliance
        $insert_compliance = Utilities::switch_db('api')->table('compliances')->insert($record_compliance);

        return "Success";

    }
}
