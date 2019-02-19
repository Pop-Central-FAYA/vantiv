<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\Session;
use Vanguard\Models\SelectedAdslot;

Class Api
{

    public static function get_dayParts()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM dayParts");
    }


    public static function get_discountTypes()
    {
        $discount_types = [];
        $types =  Utilities::switch_db('reports')->select("SELECT * FROM discount_types");
        foreach ($types as $type){
            $key = $type->value;
            $discount_types[$key] = $type->id;
        }

        return $discount_types;

    }

    public static function get_discount_classes()
    {
        $discount_classes = [];
        $classes =  Utilities::switch_db('reports')->select("SELECT * FROM discount_classes");
        foreach ($classes as $class){
            $key = $class->class;
            $discount_classes[$key] = $class->id;
        }

        return $discount_classes;
    }

    public static function getOutstandingFiles($campaign_id, $broadcaster)
    {
        return SelectedAdslot::where('campaign_id', $campaign_id)
                    ->where('broadcaster_id', $broadcaster)
                    ->whereIn('status', array('pending', 'rejected'))
                    ->get();

    }


    public static function saveActivity($user_id, $description)
    {
        $ip = request()->ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $store_activity = [
            'description' => $description,
            'user_id' => $user_id,
            'ip_address' => $ip,
            'user_agent' => $user_agent
        ];

        return \DB::table('user_activity')->insert($store_activity);
    }

    public static function validateCampaign()
    {

        $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails WHERE campaign_status = 0 AND stop_date < current_date GROUP BY campaign_id");
        $adslot_arrays = [];
        foreach ($campaigns as $campaign){
                $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where id IN ($campaign->adslots_id) ORDER BY time_created DESC");
                foreach ($adslots as $adslot){
                    $files = SelectedAdslot::where([['adslot', $adslot->id], ['campaign_id', $campaign->campaign_id]])->get();
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

        return true;

    }

    public static function approvedCampaignFiles($campaign_id, $broadcaster_id)
    {
        $allFiles = SelectedAdslot::where([
           ['campaign_id', $campaign_id],
           ['broadcaster_id', $broadcaster_id]
        ])->get();

        $approvedFiles = SelectedAdslot::where([
           ['campaign_id', $campaign_id],
           ['status', 'approved'],
           ['broadcaster_id', $broadcaster_id]
        ])->get();

        return (['mpo_approval_status' => count($allFiles) === count($approvedFiles), 'check_file_for_updating_mpo' => count($allFiles) - count($approvedFiles)]);
    }


}
