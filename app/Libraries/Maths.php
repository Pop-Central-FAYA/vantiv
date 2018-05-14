<?php

namespace Vanguard\Libraries;

use Maatwebsite;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;
use Session;

class Maths {

    public static function populateAdslot()
    {
        $now = strtotime(Carbon::now('Africa/Lagos'));

        $broadcaster = Session::get('broadcaster_id');

        $user_id = Utilities::switch_db('api')->select("SELECT user_id from broadcasters where id = '$broadcaster' LIMIT 1");

        $hourly = Utilities::switch_db('api')
            ->select("SELECT * from hourlyRanges ");

        for($i = 0; $i < count($hourly); $i++)
        {
            $rate[] = [
                'id' => uniqid(),
                'user_id' => $user_id[0]->user_id,
                'broadcaster' => Session::get('broadcaster_id'),
                'hourly_range_id' => $hourly[$i]->id,
                'day' => 'nzrm6h098amedb',
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
            ];
        }

        if(!empty($rate)){
            $each_save = Utilities::switch_db('api')->table('rateCards')->insert($rate);
            if($each_save)
            {
                $file = storage_path('excels') . '/nta.xlsx';
                $data = Excel::load($file, function($reader) {
                })->get();

                $insert = [];
                $rate = [];
                $count = count($data);
                $s=0;
                $f=0;
                $price = [];
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');

                foreach ($data as $key => $value) {
                    $pp = Utilities::clean_num($value->premium_percent);
                    $p60 = Utilities::clean_num($value->p60secs);
                    $p45 = Utilities::clean_num($value->p45secs);
                    $p30 = Utilities::clean_num($value->p30secs);
                    $p15 = Utilities::clean_num($value->p15secs);
                    $m_age = Utilities::clean_num($value->min_age);
                    $max_a = Utilities::clean_num($value->max_age);
                    $is_p = Utilities::clean_num($value->premium_yn);
                    $target = Utilities::switch_db('api')
                        ->select(
                            "SELECT id from targetAudiences WHERE audience = '$value->target_audience'");
                    $daypart = Utilities::switch_db('api')
                        ->select("SELECT id from dayParts WHERE day_parts = '$value->daypart'");

                    $region = Utilities::switch_db('api')
                        ->select("SELECT id from regions WHERE region = '$value->region'");

                    $gethourly = Utilities::switch_db('api')
                        ->select("SELECT * from hourlyRanges WHERE time_range = '$value->hourly_range'");

                    $h_id = $gethourly[0]->id;

                    $adslot_id = uniqid();

                    $get_rate = Utilities::switch_db('api')->select("SELECT id from rateCards WHERE hourly_range_id = '$h_id'");

                    $insert[] = [
                        'id' => $adslot_id,
                        'rate_card' => $get_rate[0]->id,
                        'target_audience' => $target[0]->id,
                        'day_parts' => $daypart[0]->id,
                        'region' => $region[0]->id,
                        'from_to_time' => $value->start. ' - ' .$value->stop,
                        'min_age' => (integer) $m_age,
                        'max_age' => (integer) $max_a,
                        'time_created' => date('Y-m-d H:i:s', $now),
                        'time_modified' => date('Y-m-d H:i:s', $now),
                        'broadcaster' => $broadcaster,
                        'is_available' => 0,
                        'time_difference' => (strtotime($value->stop)) - (strtotime($value->start)),
                        'time_used' => 0,
                    ];

                    $price[] = [
                        'id' => uniqid(),
                        'adslot_id' => $adslot_id,
                        'price_60' => $p60,
                        'price_45' => $p45,
                        'price_30' => $p30,
                        'price_15' => $p15,
                        'time_created' => date('Y-m-d H:i:s', $now),
                        'time_modified' => date('Y-m-d H:i:s', $now),
                    ];

                }

                if(!empty($insert) && !empty($price)){
                    $each_save = Utilities::switch_db('api')->table('adslots')->insert($insert);
                    $each_save_price = Utilities::switch_db('api')->table('adslotPrices')->insert($price);
                    if($each_save && $each_save_price)
                    {
                        return "SUCCESS";
                    }else{
                        return "FAIL";
                    }
                    $s++;
                }
            }else{
                return "FAIL";
            }
        }

    }

    public static function populateCampaign()
    {
        $file = storage_path('excels') . '/Campaign.xlsx';
        $data = Excel::load($file, function($reader) {
        })->get();

        $now = strtotime(Carbon::now('Africa/Lagos'));

        $insert = [];
        $payment = [];
        $count = count($data);
        $s=0;
        $f=0;
        $ads_id = [];

        foreach ($data as $key => $value)
        {
            $min_age = (integer) Utilities::clean_num($value->minimum_age);
            $max_age = (integer) Utilities::clean_num($value->maximum_age);
            $duration = explode(' ', $value->duration);
            $time = $duration[0];
            $day_p = Utilities::switch_db('api')->select("SELECT id from dayParts where day_parts = '$value->day_part'");
            $reg = Utilities::switch_db('api')->select("SELECT id from regions where region = '$value->region'");
            $targ = Utilities::switch_db('api')->select("SELECT id from targetAudiences where audience = '$value->audience'");
            $chanel = Utilities::switch_db('api')->select("SELECT id from campaignChannels WHERE channel = '$value->channel'");
            $user_id = Utilities::switch_db('api')->select("SELECT id from users WHERE firstname = '$value->first_name' AND lastname = '$value->last_name'");
            $u_id = $user_id[0]->id;
            $walkins_id = Utilities::switch_db('api')->select("SELECT id from walkIns WHERE user_id = '$u_id'");
            $region = $reg[0]->id;
            $dayparts = $day_p[0]->id;
            $target = $targ[0]->id;
            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where target_audience = '$target' AND day_parts = '$dayparts' AND region = '$region' AND time_in_seconds = '$time' AND min_age <= '$min_age' AND max_age >= '$max_age'");
            foreach ($adslots as $ad)
            {
                $ads_id[] = $ad->id;
            }
            $insert[] = [
                'id' => uniqid(),
                'user_id' => Session::get('user_id'),
                'walkins_id' => $walkins_id[0]->id,
                'broadcaster' => Session::get('broadcaster_id'),
                'brand' => $value->brand,
                'name' => $value->campaign_name,
                'product' => $value->product_name,
                'channel' => $chanel[0]->id,
                'day_parts' => $dayparts,
                'target_audience' => $target,
                'region' => $region,
                'industry' => $value->industry,
                'min_age' => $min_age,
                'max_age' => $max_age,
                'time_created' => date('Y-m-d H:i:s', $now),
                'time_modified' => date('Y-m-d H:i:s', $now),
                'duration' => $value->duration,
                'adslots' => count($adslots),
                'adslots_id' => implode(',', $ads_id),
                'start_date' => $value->creation_date,
                'stop_date' => $value->expiry_date
            ];

        }
        if(!empty($insert)){
            $each_save = Utilities::switch_db('api')->table('campaigns')->insert($insert);
            if($each_save)
            {
                echo "SUCCESS";
                $campaign = Utilities::switch_db('api')->select("SELECT * from campaigns where adslots != ''");
                for($i = 0; $i < count($campaign); $i++)
                {
                    $target = $campaign[$i]->target_audience;
                    $dayparts = $campaign[$i]->day_parts;
                    $region = $campaign[$i]->region;
                    $duration = explode(' ', $campaign[$i]->duration);
                    $time = $duration[0];
                    $min_age = $campaign[$i]->min_age;
                    $max_age = $campaign[$i]->max_age;
                    $walkins_id = $campaign[$i]->walkins_id;
                    $adslots = Utilities::switch_db('api')->select("SELECT SUM(price) as total_price from adslots where target_audience = '$target' AND day_parts = '$dayparts' AND region = '$region' AND time_in_seconds = '$time' AND min_age <= '$min_age' AND max_age >= '$max_age'");
                    $payment[] = [
                        'id' => uniqid(),
                        'campaign_id' => $campaign[$i]->id,
                        'payment_method' => 'cash',
                        'payment_status' => 1,
                        'amount' => $adslots[0]->total_price,
                        'time_created' => date('Y-m-d H:i:s', $now),
                        'time_modified' => date('Y-m-d H:i:s', $now),
                        'broadcaster' => Session::get('broadcaster_id'),
                        'walkins_id' => $walkins_id,
                    ];
                }

                if(!empty($payment)){
                    $each_save = Utilities::switch_db('api')->table('payments')->insert($payment);
                    if($each_save)
                    {
                        return "SUCCESS";
                    }else{
                        return "FAIL";
                    }
                }
            }else{
                return "FAIL";
            }
        }


    }

    public static function populateIndustry()
    {
        $now = strtotime(Carbon::now('Africa/Lagos'));
        $file = storage_path('excels') . '/industry.xlsx';
        $data = Excel::load($file, function($reader) {
        })->get();

        $insert = [];

        foreach ($data as $key => $value)
        {
            $insert[] = [
                'id' => uniqid(),
                'name' => $value->industry,
                'sector_code' => uniqid(),
                'time_created' => $now,
                'time_modified' => $now,
                'status' => 1,
            ];
        }

        if(!empty($insert)) {
            $each_save = Utilities::switch_db('api')->table('sectors')->insert($insert);
            if($each_save)
            {
                return "SUCCESS";
            }else{
                return "FAILURE";
            }
        }

    }

    public static function getFilters()
    {
        return [
            'chunk'
        ];
    }


}