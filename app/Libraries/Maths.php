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

        $hourly = Utilities::switch_db('api')
            ->select("SELECT * from hourlyRanges ");

        for($i = 0; $i < count($hourly); $i++)
        {
            $rate[] = [
                'id' => uniqid(),
                'user_id' => '10zmij9sroa62',
                'broadcaster' => '10zmij9sroads',
                'hourly_range_id' => $hourly[$i]->id,
                'day' => 'nzru9ch893abec',
                'time_created' => $now,
                'time_modified' => $now,
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
                    $get_rate = Utilities::switch_db('api')->select("SELECT id from rateCards WHERE hourly_range_id = '$h_id'");
                    $insert[] = [
                        'id' => uniqid(),
                        'target_audience' => $target[0]->id,
                        'rate_card' => $get_rate[0]->id,
                        'day_parts' => $daypart[0]->id,
                        'region' => $region[0]->id,
                        'from_to_time' => $value->start. ' - ' .$value->stop,
                        'time_in_seconds' => 60,
                        'is_premium' => (integer) $is_p,
                        'premium_percent' => (integer) $pp,
                        'price' => (integer) $p60,
                        'min_age' => (integer) $m_age,
                        'max_age' => (integer) $max_a,
                        'time_created' => $now,
                        'time_modified' => $now,

                    ];
                    $insert[] = [
                        'id' => uniqid(),
                        'target_audience' => $target[0]->id,
                        'rate_card' => $get_rate[0]->id,
                        'day_parts' => $daypart[0]->id,
                        'region' => $region[0]->id,
                        'from_to_time' => $value->start. ' - ' .$value->stop,
                        'time_in_seconds' => 45,
                        'is_premium' => (integer) $is_p,
                        'premium_percent' => (integer) $pp,
                        'price' => (integer) $p45,
                        'min_age' => (integer) $m_age,
                        'max_age' => (integer) $max_a,
                        'time_created' => $now,
                        'time_modified' => $now,

                    ];
                    $insert[] = [
                        'id' => uniqid(),
                        'target_audience' => $target[0]->id,
                        'rate_card' => $get_rate[0]->id,
                        'day_parts' => $daypart[0]->id,
                        'region' => $region[0]->id,
                        'from_to_time' => $value->start. ' - ' .$value->stop,
                        'time_in_seconds' => 30,
                        'is_premium' => (integer) $is_p,
                        'premium_percent' => (integer) $pp,
                        'price' => (integer) $p30,
                        'min_age' => (integer) $m_age,
                        'max_age' => (integer) $max_a,
                        'time_created' => $now,
                        'time_modified' => $now,

                    ];
                    $insert[] = [
                        'id' => uniqid(),
                        'target_audience' => $target[0]->id,
                        'rate_card' => $get_rate[0]->id,
                        'day_parts' => $daypart[0]->id,
                        'region' => $region[0]->id,
                        'from_to_time' => $value->start. ' - ' .$value->stop,
                        'time_in_seconds' => 15,
                        'is_premium' => (integer) $is_p,
                        'premium_percent' => (integer) $pp,
                        'price' => (integer) $p15,
                        'min_age' => (integer) $m_age,
                        'max_age' => (integer) $max_a,
                        'time_created' => $now,
                        'time_modified' => $now,

                    ];

                }

                if(!empty($insert)){
                    $each_save = Utilities::switch_db('api')->table('adslots')->insert($insert);
                    if($each_save)
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
            $region = $reg[0]->id;
            $dayparts = $day_p[0]->id;
            $target = $targ[0]->id;
            $adslots = Utilities::switch_db('api')->select("SELECT * from adslots where target_audience = '$target' AND day_parts = '$dayparts' AND region = '$region' AND time_in_seconds = '$time' AND min_age <= '$min_age' AND max_age >= '$max_age'");
            $insert[] = [
                'id' => uniqid(),
                'user_id' => Session::get('user_id'),
                'walkins_id' => $user_id[0]->id,
                'broadcaster' => '10zmij9sroads',
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
                'time_created' => $now,
                'time_modified' => $now,
                'duration' => $value->duration,
                'adslots' => count($adslots),
                'start_date' => strtotime($value->creation_date),
                'stop_date' => strtotime($value->expiry_date)
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
                        'time_created' => $now,
                        'time_modified' => $now,
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