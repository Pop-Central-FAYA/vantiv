<?php

namespace Vanguard\Libraries;

use Hamcrest\Util;
use Illuminate\Support\Facades\DB;
use JD\Cloudder\Facades\Cloudder;
use Session;
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
//        $campaign_details = Utilities::switch_db('api')->select("SELECT * from campaignDetails where campaign_id = '$id' GROUP BY campaign_id");
        $campaign_details = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.min_age, c_d.max_age, c_d.name, c_d.user_id, c_d.agency, c_d.product, c_d.Industry, c_d.sub_industry, c_d.start_date, c_d.stop_date, b.name as brand, c_d.channel, c_d.target_audience, c_d.region, b.name, p.total, p.id as payment_id from campaignDetails as c_d, brands as b, payments as p where p.campaign_id = c_d.campaign_id and c_d.brand = b.id  and c_d.campaign_id = '$id' GROUP BY c_d.campaign_id");

        $campaign_id = $campaign_details[0]->campaign_id;
        $channel = $campaign_details[0]->channel;
        $location_ids = $campaign_details[0]->region;
        $target_id = $campaign_details[0]->target_audience;
        $location = Utilities::switch_db('api')->select("SELECT * FROM regions where id IN ($location_ids) ");
        $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($channel) ");
        $target_audiences = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id IN ($target_id)");
        $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$id')");
        $payment_id = $campaign_details[0]->payment_id;
        if(\Session::get('broadcaster_id')){
            $broadcaster_id = \Session::get('broadcaster_id');
            $campaign_details = Utilities::switch_db('api')->select("SELECT amount as total from paymentDetails where payment_id = '$payment_id' and broadcaster = '$broadcaster_id'");
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
            'brand' => $campaign_details[0]->brand,
            'industry' => $campaign_details[0]->Industry,
            'sub_industry' => $campaign_details[0]->sub_industry,
            'channel' => $channel,
            'start_date' => date('Y-m-d', strtotime($campaign_details[0]->start_date)),
            'end_date' => date('Y-m-d', strtotime($campaign_details[0]->stop_date)),
            'campaign_cost' => number_format($campaign_details[0]->total, '2'),
            'walkIn_name' => $name,
            'company_name' => $company_name,
            'company_user_id' => $user_id,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'age' => $campaign_details[0]->min_age .' - '.$campaign_details[0]->max_age,
            'target_audience' => $target_audiences
        ];

        $files = Utilities::switch_db('api')->select("SELECT f.id, f.user_id, f.broadcaster_id, f.file_url, f.time_picked, f.is_file_accepted, f.rejection_reason, f.file_name, f.format, a.from_to_time, a.min_age, a.max_age, d_p.day_parts, t.audience, r.region, h.time_range, d.day, b.brand from files as f, dayParts as d_p, adslots as a, targetAudiences as t, regions as r, days as d, hourlyRanges as h, rateCards as r_c, broadcasters as b where f.broadcaster_id = b.id and 
                                                          f.adslot = a.id and a.day_parts = d_p.id and a.target_audience = t.id and a.region = r.id and a.rate_card = r_c.id and h.id = r_c.hourly_range_id and r_c.day = d.id and a.broadcaster = b.id and campaign_id = '$campaign_id'");

        if(\Session::get('broadcaster_id')){
            $broadcaster_id = \Session::get('broadcaster_id');
            $files = Utilities::switch_db('api')->select("SELECT f.id, f.user_id, f.broadcaster_id, f.file_url, f.time_picked, f.is_file_accepted, f.rejection_reason, f.file_name, f.format, a.from_to_time, a.min_age, a.max_age, d_p.day_parts, t.audience, r.region, h.time_range, d.day, b.brand from files as f, dayParts as d_p, adslots as a, targetAudiences as t, regions as r, days as d, hourlyRanges as h, rateCards as r_c, broadcasters as b where f.broadcaster_id = b.id and 
                                                          f.adslot = a.id and a.day_parts = d_p.id and a.target_audience = t.id and a.region = r.id and a.rate_card = r_c.id and h.id = r_c.hourly_range_id and r_c.day = d.id and a.broadcaster = b.id and campaign_id = '$campaign_id' and f.broadcaster_id = '$broadcaster_id'");
        }
        foreach ($files as $file){
            $file_details[] = [
                'file_id' => $file->id,
                'user_id' => $file->user_id,
                'agency_id' => $campaign_details[0]->agency,
                'agency_broadcaster' => $file->broadcaster_id,
                'broadcaster_id' => $file->broadcaster_id,
                'from_to_time' => $file->from_to_time,
                'day_part' => $file->day_parts,
                'target_audience' => $file->audience,
                'region' => $file->region,
                'minimum_age' => $file->min_age,
                'maximum_age' => $file->max_age,
                'hourly_range' => $file->time_range,
                'day' => $file->day,
                'broadcast_station' => $file->brand,
                'file' => decrypt($file->file_url),
                'slot_time' => $file->time_picked.' seconds',
                'file_status' => $file->is_file_accepted,
                'rejection_reason' => $file->rejection_reason,
                'file_name' => $file->file_name,
                'format' => $file->format ? $file->format : ''
            ];
        }

        $compliance_reports = [];
        $campaign_compliances = Utilities::switch_db('api')->select("SELECT c.time_created, c_c.channel, b.brand, a.from_to_time from compliances as c, campaignChannels as c_c, adslots as a, broadcasters as b where
                                                                         c_c.id = c.channel and b.id = c.broadcaster_id and a.id = c.adslot_id and campaign_id = '$id'");

        foreach ($campaign_compliances as $campaign_compliance){
            $compliance_reports[] = [
                'media_type' => $campaign_compliance->channel,
                'media_channel' => $campaign_compliance->brand,
                'date' => date('M j, Y', strtotime($campaign_compliance->time_created)),
                'booked_spot' => $campaign_compliance->from_to_time,
                'aired_spot' => $campaign_compliance->from_to_time,
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

    public static function updateClients($request, $client_id)
    {
        $walkins = Utilities::switch_db('api')->select("SELECT * from walkIns where id = '$client_id'");
        $user_id = $walkins[0]->user_id;

        if($request->hasFile('company_logo')){
            $image = $request->company_logo;
            $filename = $request->file('company_logo')->getRealPath();
            Cloudder::upload($filename, Cloudder::getPublicId());
            $clouder = Cloudder::getResult();
            $image_url = encrypt($clouder['url']);
            $walkins_update_logo = Utilities::switch_db('api')->update("UPDATE walkIns set company_logo = '$image_url' where id = '$client_id'");
        }

        $walkins_update = Utilities::switch_db('api')->update("UPDATE walkIns set location = '$request->address', company_name = '$request->company_name' where id = '$client_id'");

        $api_user_update = Utilities::switch_db('api')->update("UPDATE users set firstname = '$request->first_name', lastname = '$request->last_name', phone_number = '$request->phone' where id = '$user_id'");

        $local_db_update = DB::update("UPDATE users set first_name = '$request->first_name', last_name = '$request->last_name', phone = '$request->phone' where email = '$request->email'");

        if($api_user_update || $walkins_update || $local_db_update || $walkins_update_logo){
            return "success";
        }else{
            return "error";
        }
    }

}
