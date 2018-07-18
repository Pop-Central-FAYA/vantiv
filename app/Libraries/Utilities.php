<?php

namespace Vanguard\Libraries;

use Hamcrest\Util;
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
        $campaign_details = Utilities::switch_db('api')->select("SELECT * from campaignDetails where campaign_id = '$id' GROUP BY campaign_id");
        $campaign_id = $campaign_details[0]->campaign_id;
        $brand_name = $campaign_details[0]->brand;
        $channel = $campaign_details[0]->channel;

        $location_ids = $campaign_details[0]->region;
        $target_id = $campaign_details[0]->target_audience;
        $location = Utilities::switch_db('api')->select("SELECT * FROM regions where id IN ($location_ids) ");
        $brand = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_name'");
        $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($channel) ");
        $payments = Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$campaign_id' ");
        $target_audiences = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id IN ($target_id)");
        $payment_id = $payments[0]->id;
        $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$id')");
        if(\Session::get('broadcaster_id')){
            $broadcaster_id = \Session::get('broadcaster_id');
            $payments = Utilities::switch_db('api')->select("SELECT amount as total from paymentDetails where payment_id = '$payment_id' and broadcaster = '$broadcaster_id'");
        }
        $user_id = $campaign_details[0]->user_id;
        $company_info = Utilities::switch_db('api')->select("SELECT * from walkIns where user_id = '$user_id'");
        $company_name = $company_info[0]->company_name ? $company_info[0]->company_name : '';
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
            #
        }else{
            $name = $user_advertiser[0]->firstname .' '.$user_advertiser[0]->lastname;
            $email = $user_advertiser[0]->email;
            $phone = $user_advertiser[0]->phone_number;
        }

        $campaign_det = [
            'campaign_id' => $campaign_details[0]->campaign_id,
            'campaign_name' => $campaign_details[0]->name,
            'product_name' => $campaign_details[0]->product,
            'brand' => $brand[0]->name,
            'industry' => $campaign_details[0]->Industry,
            'sub_industry' => $campaign_details[0]->sub_industry,
            'channel' => $channel,
            'start_date' => date('Y-m-d', strtotime($campaign_details[0]->start_date)),
            'end_date' => date('Y-m-d', strtotime($campaign_details[0]->stop_date)),
            'campaign_cost' => number_format($payments[0]->total, '2'),
            'walkIn_name' => $name,
            'company_name' => $company_name,
            'company_user_id' => $user_id,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'age' => $campaign_details[0]->min_age .' - '.$campaign_details[0]->max_age,
            'target_audience' => $target_audiences
        ];



        $files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$campaign_id'");
        if(\Session::get('broadcaster_id')){
            $broadcaster_id = \Session::get('broadcaster_id');
            $files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$campaign_id' AND broadcaster_id = '$broadcaster_id'");
        }
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
                'agency_broadcaster' => $file->broadcaster_id,
                'broadcaster_id' => $file->broadcaster_id,
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
                'file_name' => $file->file_name,
                'format' => $file->format ? $file->format : ''
            ];
        }

        $compliance_reports = [];
        $campaign_compliances = Utilities::switch_db('api')->select("SELECT * from compliances where campaign_id = '$id'");
        foreach ($campaign_compliances as $campaign_compliance){
            $media_type = Utilities::switch_db('api')->select("SELECT * FROM campaignChannels where id = '$campaign_compliance->channel'");
            $media_channel = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id = '$campaign_compliance->broadcaster_id'");
            $adslots = Utilities::switch_db('api')->select("SELECT * FROM adslots where id = '$campaign_compliance->adslot_id'");
            $compliance_reports[] = [
                'media_type' => $media_type[0]->channel,
                'media_channel' => $media_channel[0]->brand,
                'date' => date('M j, Y', strtotime($campaign_compliance->time_created)),
                'booked_spot' => $adslots[0]->from_to_time,
                'aired_spot' => $adslots[0]->from_to_time,
            ];
        }

        $uploaded_files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$id' GROUP BY file_name");

        return (['campaign_det' => $campaign_det, 'file_details' => $file_details, 'broadcasters' => $broadcasters, 'compliance_reports' => $compliance_reports, 'uploaded_files' => $uploaded_files]);

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

    public static function fetchTimeInCart($id, $broadcaster)
    {
        $times = [];
        $cart_check = \DB::select("SELECT SUM(time) as time_sum, adslot_id from carts WHERE user_id = '$id' GROUP BY adslot_id");
        foreach($cart_check as $q){
            $check_adslot_space = Utilities::switch_db('api')->select("SELECT * from adslots where id = '$q->adslot_id'");
            $time_left = (integer)$check_adslot_space[0]->time_difference - (integer)$check_adslot_space[0]->time_used;
            $broadcaster_username = Utilities::switch_db('api')->select("SELECT brand from broadcasters where id = '$broadcaster'");
            $times[] = [
                'initial_time_left' => $time_left,
                'time_bought' => $q->time_sum,
                'adslot_id' => $q->adslot_id,
                'broadcaster_name' => $broadcaster_username[0]->brand,
                'from_to_time' => $check_adslot_space[0]->from_to_time,
            ];
        }

        return $times;
    }

    public static function getMpoDetails($id)
    {
        $agency_id = \Session::get('agency_id');
        $mpo = Utilities::switch_db('api')->select("SELECT * from mpos where campaign_id = '$id'");
        $mpo_id = $mpo[0]->id;
        $mpo_details = Utilities::switch_db('api')->select("SELECT * FROM mpoDetails where mpo_id = '$mpo_id'");
        $all_details_mpos = [];
        $agency_det = Utilities::switch_db('api')->select("SELECT * from agents where id = '$agency_id'");
        $camp_det = Utilities::switch_db('api')->select("SELECT * from campaignDetails where campaign_id = '$id' GROUP BY campaign_id");
        $brand_id = $camp_det[0]->brand;
        $brands = Utilities::switch_db('api')->select("SELECT * FROM brands where id = '$brand_id'");
        $user_id = $camp_det[0]->user_id;
        $user_broad = Utilities::switch_db('api')->select("SELECT * from users where id = '$user_id' ");
        $user_agency = DB::select("SELECT * from users where id = '$user_id' ");
        $user_advertiser = Utilities::switch_db('api')->select("SELECT * from users where id = (SELECT user_id from advertisers WHERE id = '$user_id')");
        if($user_broad){
            $name = $user_broad[0]->firstname .' '.$user_broad[0]->lastname;
        }elseif($user_agency){
            $name = $user_agency[0]->first_name .' '.$user_agency[0]->last_name;
        }else{
            $name = $user_advertiser[0]->firstname .' '.$user_advertiser[0]->lastname;
        }

        $all_mpos = [];
        foreach ($mpo_details as $mpo_detail){
            $payments_det = Utilities::switch_db('api')->select("SELECT * from paymentDetails where payment_id = (SELECT id from payments where campaign_id = '$id') AND broadcaster = '$mpo_detail->broadcaster_id'");
            $broadcaster_details = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$mpo_detail->broadcaster_id'");
            $broadcaster_name = $broadcaster_details[0]->brand;
            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where broadcaster = '$mpo_detail->broadcaster_id' AND campaign_id = '$id'");
            $all_mpos[] = [
                'year' => date('Y', strtotime($mpo[0]->time_created)),
                'media' => $broadcaster_name,
                'spot' => $campaigns[0]->adslots,
                'total' => number_format($payments_det[0]->amount, 2)
            ];
        }

        $all_details_mpos = [
            'clients' => $name,
            'brand' => $brands[0]->name,
            'campaign' => $camp_det[0]->name,
            'date' => date('Y-m-d', strtotime($camp_det[0]->time_created)),
            'agency' => $agency_det[0]->brand,
            'invoice_number' => $mpo[0]->invoice_number,
            'mpo' => $all_mpos,
        ];

        return $all_details_mpos;
    }

    public static function invoiceDetails()
    {
        $all_invoices = [];
        $inv_files = [];
        $invoices = Utilities::switch_db('api')->select("SELECT * from invoices");
        foreach ($invoices as $invoice)
        {
            $invoice_details = Utilities::switch_db('api')->select("SELECT * from invoiceDetails where invoice_id = '$invoice->id'");

            $all_invoices[] = [
                'campaign_id' => $invoice->campaign_id,
                'invoice_number' => $invoice->invoice_number,
            ];
        }

        return $all_invoices;
    }

    public static function generateReference()
    {
        $date = strtotime(date("Y-m-d H:i:s")) * 4;
        $reference = (integer)round((mt_rand(100000, 999999999).$date) / 199999999999);
        return $reference;
    }

}
