<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Utilities;

class CronjobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCmpliance()
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

    }

    public function validateCampaign()
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
