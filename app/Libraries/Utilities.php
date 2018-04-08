<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Utilities {

    public static function switch_db($db)
    {
        switch ($db){
            case 'local':
                return DB::connection('mysql');
                break;
            case 'api_2':
                return DB::connection('mysql-2');
                break;
            case 'api':
                return DB::connection('api_db');
                break;
            case 'reports':
                return DB::connection('api_db');
                break;
            default;
                return null;
                break;
        }
    }

    public static function clean_num( $num ){
        $number  = $num;
        $trim = rtrim($number, '.');
        return $trim;
    }

    public static function formatString($string)
    {
        $string = strtolower($string);
        return str_replace('-', ' ', $string); // Replaces all spaces with hyphens.
//        return preg_replace('/[^A-Za-z]/', ' ', $string); // Removes special chars.
    }

    public static function campaignDetails($id)
    {
        $file_details = [];
        $campaign_details = Utilities::switch_db('api')->select("SELECT * from campaigns where id = '$id'");
        $brand_name = $campaign_details[0]->brand;
        $channel = $campaign_details[0]->channel;
        $brand = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_name'");
        $channel_name = Utilities::switch_db('api')->select("SELECT channel from campaignChannels where id = '$channel'");
        $payments = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$id'");
        $user_id = $campaign_details[0]->user_id;
        $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = '$user_id' ");
        $user_agency = DB::select("SELECT * from users where id = '$user_id' ");
        $user_advertiser = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from advertisers WHERE id = '$user_id')");
        if($user_broad){
            $name = $user_broad[0]->firstname .' '.$user_broad[0]->lastname;
            $email = $user_broad[0]->email;
            $phone = $user_broad[0]->phone_number;
        }elseif($user_agency){
            $name = $user_agency[0]->first_name .' '.$user_agency[0]->last_name;
            $email = $user_agency[0]->email;
            $phone = $user_agency[0]->phone;
        }else{
            $name = $user_advertiser[0]->firstname .' '.$user_advertiser[0]->lastname;
            $email = $user_advertiser[0]->email;
            $phone = $user_advertiser[0]->phone_number;
        }

        $campaign_det = [
            'campaign_name' => $campaign_details[0]->name,
            'product_name' => $campaign_details[0]->product,
            'brand' => $brand[0]->name,
            'channel' => $channel_name[0]->channel,
            'start_date' => date('Y-m-d', strtotime($campaign_details[0]->start_date)),
            'end_date' => date('Y-m-d', strtotime($campaign_details[0]->stop_date)),
            'campaign_cost' => number_format($payments[0]->amount, '2'),
            'walkIn_name' => $name,
            'email' => $email,
            'phone' => $phone,
        ];



        $files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$id'");
        foreach ($files as $file){
            $adslot_details = Utilities::switch_db('api')->select("SELECT * from adslots where id = '$file->adslot'");
            $day_part_id = $adslot_details[0]->day_parts;
            $day_parts = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id = '$day_part_id'");
            $target_audience_id = $adslot_details[0]->target_audience;
            $target_audience = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id = '$target_audience_id'");
            $region_id = $adslot_details[0]->region;
            $region = Utilities::switch_db('api')->select("SELECT * from regions where id = '$region_id'");
            $rate_card_id = $adslot_details[0]->rate_card;
            $rate_card_details = Utilities::switch_db('api')->select("SELECT * from rateCards where id = '$rate_card_id'");
            $hourly_range_id = $rate_card_details[0]->hourly_range_id;
            $hourly_range = Utilities::switch_db('api')->select("SELECT * from hourlyRanges where id = '$hourly_range_id'");
            $day_id = $rate_card_details[0]->day;
            $day = Utilities::switch_db('api')->select("SELECT * from days where id = '$day_id'");
            $broad_id = $adslot_details[0]->broadcaster;
            $broadcaster_info = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broad_id'");
            $file_details[] = [
                'file_id' => $file->id,
                'user_id' => $file->user_id,
                'agency_id' => $campaign_details[0]->agency,
                'agency_broadcaster' => $campaign_details[0]->agency_broadcaster,
                'broadcaster_id' => $campaign_details[0]->broadcaster,
                'from_to_time' => $adslot_details[0]->from_to_time,
                'day_part' => $day_parts[0]->day_parts,
                'target_audience' => $target_audience[0]->audience,
                'region' => $region[0]->region,
                'minimum_age' => $adslot_details[0]->min_age,
                'maximum_age' => $adslot_details[0]->max_age,
                'hourly_range' => $hourly_range[0]->time_range,
                'day' => $day[0]->day,
                'broadcast_station' => $broadcaster_info[0]->brand,
                'file' => decrypt($file->file_url),
                'slot_time' => $file->time_picked.' seconds',
                'file_status' => $file->is_file_accepted,
                'rejection_reason' => $file->rejection_reason,
            ];
        }

        return (['campaign_det' => $campaign_det, 'file_details' => $file_details]);

    }

    public static function array_flatten($array) {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, array_flatten($value));
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public static function checkForActivation($user_id)
    {
        $user = DB::select("SELECT status from users where id = '$user_id'");
        return $user[0]->status;
    }




}
