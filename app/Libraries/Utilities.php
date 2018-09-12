<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\DB;
use JD\Cloudder\Facades\Cloudder;

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

    }

    public static function removeSpace($string)
    {
        return str_replace(' ','',$string);
    }

    public static function campaignDetails($id, $broadcaster_id, $agency_id)
    {
        $file_details = [];
        if($broadcaster_id){
            $campaign_details = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.min_age, c_d.max_age, c_d.name, c_d.user_id, c_d.agency, c_d.product, c_d.Industry, c_d.sub_industry, c_d.broadcaster, c_d.start_date, c_d.stop_date, b.name as brand, c_d.channel, c_d.target_audience, c_d.region, b.name, p.total, p.id as payment_id from campaignDetails as c_d, brands as b, payments as p where p.campaign_id = c_d.campaign_id and c_d.brand = b.id  and c_d.campaign_id = '$id' and c_d.broadcaster = '$broadcaster_id'");
        }else if($agency_id){
            $campaign_details = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.min_age, c_d.max_age, c_d.name, c_d.user_id, c_d.agency, c_d.product, c_d.Industry, c_d.sub_industry, c_d.start_date, c_d.stop_date, b.name as brand, c_d.channel, c_d.target_audience, c_d.region, b.name, p.total, p.id as payment_id from campaignDetails as c_d, brands as b, payments as p where p.campaign_id = c_d.campaign_id and c_d.brand = b.id  and c_d.campaign_id = '$id' GROUP BY c_d.campaign_id");
        }
        $campaign_id = $campaign_details[0]->campaign_id;
        $channel = $campaign_details[0]->channel;
        $location_ids = $campaign_details[0]->region;
        $target_id = $campaign_details[0]->target_audience;
        $location = Utilities::switch_db('api')->select("SELECT * FROM regions where id IN ($location_ids) ");
        if($broadcaster_id){
            $broadcaster_campaign_id = $campaign_details[0]->broadcaster;
            $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN (SELECT channel_id from broadcasters where id = '$broadcaster_campaign_id') ");

        }else if($agency_id){
            $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($channel) ");
        }
        $target_audiences = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id IN ($target_id)");
        if($broadcaster_id){
            $broadcaster_campaign_id = $campaign_details[0]->broadcaster;
            $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id = '$broadcaster_campaign_id'");
        }else if($agency_id){
            $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id IN (SELECT broadcaster from campaignDetails where campaign_id = '$id')");
        }
        $payment_id = $campaign_details[0]->payment_id;
        $user_id = $campaign_details[0]->user_id;
        if($broadcaster_id){
            $campaign_details_broad = Utilities::switch_db('api')->select("SELECT amount as total from paymentDetails where payment_id = '$payment_id' and broadcaster = '$broadcaster_id'");
        }

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
            'campaign_cost' => number_format($broadcaster_id ? $campaign_details_broad[0]->total : $campaign_details[0]->total, '2'),
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

        if($broadcaster_id){
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
        if($broadcaster_id){
            $campaign_compliances = Utilities::switch_db('api')->select("SELECT c.time_created, c_c.channel, b.brand, a.from_to_time from compliances as c, campaignChannels as c_c, adslots as a, broadcasters as b where
                                                                         c_c.id = c.channel and b.id = '$broadcaster_id' and c.broadcaster_id = '$broadcaster_id' and a.id = c.adslot_id and campaign_id = '$id'");
        }else if($agency_id){
            $campaign_compliances = Utilities::switch_db('api')->select("SELECT c.time_created, c_c.channel, b.brand, a.from_to_time from compliances as c, campaignChannels as c_c, adslots as a, broadcasters as b where
                                                                         c_c.id = c.channel and b.id = c.broadcaster_id and a.id = c.adslot_id and campaign_id = '$id'");
        }


        foreach ($campaign_compliances as $campaign_compliance){
            $compliance_reports[] = [
                'media_type' => $campaign_compliance->channel,
                'media_channel' => $campaign_compliance->brand,
                'date' => date('M j, Y', strtotime($campaign_compliance->time_created)),
                'booked_spot' => $campaign_compliance->from_to_time,
                'aired_spot' => $campaign_compliance->from_to_time,
            ];
        }

        if($broadcaster_id){
            $uploaded_files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$id' and broadcaster_id = '$broadcaster_id' GROUP BY file_name");
        }else if($agency_id){
            $uploaded_files = Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$id' GROUP BY file_name");
        }

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

    public static function getMpoDetails($id, $agency_id)
    {
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
        $walkins_update_logo = '';
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

        if($api_user_update || $walkins_update || $local_db_update ){
            return "success";
        }else{
            return "error";
        }
    }

    public static function getClientCampaignData($user_id, $broadcaster_id)
    {
        $campaigns = [];
        $all_campaign = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.name, c_d.product, c_d.start_date, c_d.stop_date, c_d.adslots, c.campaign_reference, p.total, b.name as brands from campaignDetails as c_d, campaigns as c, payments as p, brands as b WHERE c_d.user_id = '$user_id' AND p.campaign_id = c_d.campaign_id AND c_d.campaign_id = c.id AND b.id = c_d.brand AND c_d.adslots > 0 and c_d.broadcaster = '$broadcaster_id' ORDER BY c_d.time_created DESC");
        foreach ($all_campaign as $cam)
        {
            $today = date("Y-m-d");
            if(strtotime($today) > strtotime($cam->start_date) && strtotime($today) > strtotime($cam->stop_date)){
                $status = 'expired';
            }elseif (strtotime($today) >= strtotime($cam->start_date) && strtotime($today) <= strtotime($cam->stop_date)){
                $status = 'active';
            }else{
                $now = strtotime($today);
                $your_date = strtotime($cam->start_date);
                $datediff = $your_date - $now;
                $new_day =  round($datediff / (60 * 60 * 24));
                $status = 'pending';
            }
            $campaigns[] = [
                'id' => $cam->campaign_reference,
                'camp_id' => $cam->campaign_id,
                'name' => $cam->name,
                'brand' => $cam->brands,
                'product' => $cam->product,
                'start_date' => date('Y-m-d', strtotime($cam->start_date)),
                'end_date' => date('Y-m-d', strtotime($cam->stop_date)),
                'adslots' => $cam->adslots,
                'budget' => number_format($cam->total, 2),
                'compliance' => '0%',
                'status' => $status
            ];
        }

        return $campaigns;
    }

    public static function getClientsBrands($id, $broadcaster_id)
    {
        $brs = Utilities::switch_db('api')->select("SELECT * from brands where walkin_id = '$id'");
        $brands = [];
        foreach ($brs as $br){
            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where brand = '$br->id' and broadcaster = '$broadcaster_id'");
            $last_count_campaign = count($campaigns) - 1;
            $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where brand = '$br->id' and broadcaster = '$broadcaster_id')");
            $brands[] = [
                'id' => $br->id,
                'brand' => $br->name,
                'date' => $br->time_created,
                'count_brand' => count($brs),
                'campaigns' => count($campaigns),
                'image_url' => $br->image_url,
                'last_campaign' => $campaigns ? $campaigns[$last_count_campaign]->name : 'none',
                'total' => number_format($pay[0]->total,2),
                'industry_id' => $br->industry_id,
                'sub_industry_id' => $br->sub_industry_id,
            ];
        }
        if(count($brands) === 0){
            return 'error';
        }

        return $brands;
    }

    public static function numberOfAdslotOccurrence($adslot_id, $start_date, $end_date)
    {
        $format = 'Y-m-d';
        $start  = new \DateTime($start_date);
        $end    = new \DateTime($end_date);
        $invert = $start > $end;

        $dates = array();
        $dates[] = $start->format($format);
        while ($start != $end) {
            $start->modify(($invert ? '-' : '+') . '1 day');
            $dates[] = $start->format($format);
        }
        $adslot_array = [];

        foreach ($dates as $index => $date){
            $date_name = date('l', strtotime($date));
            $date_id = Utilities::switch_db('api')->select("SELECT * from days where `day` = '$date_name'");
            $day_id = $date_id[0]->id;
            $adslots = Utilities::switch_db('api')->select("SELECT a.id, r.day from adslots as a, rateCards as r where a.id = '$adslot_id' and r.id = a.rate_card");
            if($adslots[0]->day === $day_id){
                $adslot_array[] = [
                    'adslot' => $adslots
                ];
            }
        }

        return count($adslot_array);

    }

    public static function getChannels()
    {
        return Utilities::switch_db('api')->select("SELECT * from campaignChannels");
    }

    public static function getClients($agency_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from walkIns where agency_id = '$agency_id'");
    }

    public static function getWalkIns($broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from walkIns where broadcaster_id = '$broadcaster_id'");
    }

    public static function getBrandsForWalkins($walkin_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from brands WHERE walkin_id = '$walkin_id'");
    }

    public static function getBroadcasterDetails($broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$broadcaster_id'");
    }

    public static function getWalkInsDetails($id)
    {
        return Utilities::switch_db('api')->select("SELECT * from walkIns where id = '$id'");
    }

    public static function getPreloadedData()
    {
        $targetAudiences = Utilities::switch_db('api')->select("SELECT * from targetAudiences");
        $regions = Utilities::switch_db('api')->select("SELECT * from regions");
        $day_parts = Utilities::switch_db('api')->select("SELECT * from dayParts");
        $industries = Utilities::switch_db('api')->select("SELECT * from sectors");
        $subindustries = Utilities::switch_db('api')->select("select * from subSectors");
        $channels = Utilities::switch_db('api')->select("SELECT * from campaignChannels");
        $days = Utilities::switch_db('api')->select("SELECT * from days");
        $hourly_ranges = Utilities::switch_db('api')->select("SELECT * from hourlyRanges");

        return (['hourly_ranges' => $hourly_ranges, 'days' => $days, 'regions' => $regions, 'target_audience' => $targetAudiences, 'day_parts' => $day_parts, 'industries' => $industries, 'subindustries' => $subindustries, 'channels' => $channels]);
    }

    public static function getAllAvailableSlots($step1, $broadcaster_id)
    {
        $broadcaster_details = Utilities::getBroadcasterDetails($broadcaster_id);
        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";
        $channel = $broadcaster_details[0]->channel_id;
        $adslots =  Utilities::switch_db('api')->select("SELECT a.*, IF(a.id = p_p.adslot_id,p_p.price_60,p.price_60) as price_60, IF(a.id = p_p.adslot_id,p_p.price_45,p.price_45) as price_45,
                                                                         IF(a.id = p_p.adslot_id,p_p.price_30,p.price_30) as price_30, IF(a.id = p_p.adslot_id,p_p.price_15,p.price_15) as price_15 from adslots as a
                                                                         JOIN adslotPrices as p ON a.id = p.adslot_id
                                                                          LEFT JOIN adslotPercentages as p_p ON a.id = p_p.adslot_id
                                                                          where a.min_age >= $step1->min_age AND a.max_age <= $step1->max_age AND a.target_audience IN ($target_audience) AND a.day_parts IN ($day_parts)
                                                                           AND a.region IN ($region) AND a.is_available = 0 AND a.channels = '$channel' and a.broadcaster = '$broadcaster_id'");
        return $adslots;

    }

    public static function getCampaignDatatables($all_campaigns)
    {
        $campaigns = [];
        $today = strtotime(date("Y-m-d"));
        foreach ($all_campaigns as $all_campaign)
        {
            $start_date = strtotime($all_campaign->start_date);
            $stop_date = strtotime($all_campaign->stop_date);
            if($today > $stop_date){
                $status = 'Finished';
            }elseif ($today >= $start_date && $today <= $all_campaign->stop_date){
                $status = 'Active';
            }else{
                $status = 'Pending';
            }
            $campaigns[] = [
                'id' => $all_campaign->campaign_reference,
                'campaign_id' => $all_campaign->campaign_id,
                'name' => $all_campaign->name,
                'brand' => ucfirst($all_campaign->brand_name),
                'product' => $all_campaign->product,
                'date_created' => date('M j, Y', strtotime($all_campaign->time_created)),
                'start_date' => date('Y-m-d', $start_date),
                'end_date' => date('Y-m-d', $stop_date),
                'adslots' => count((explode(',', $all_campaign->adslots_id))),
                'budget' => number_format($all_campaign->total, 2),
                'status' => $status
            ];
        }

        return $campaigns;

    }

    public static function deleteCartsUploadsfilePosition($user_id, $broadcaster_id, $agency_id)
    {
        $del_uplaods = \DB::delete("DELETE FROM uploads WHERE user_id = '$user_id'");
        $del_file_position = Utilities::switch_db('api')->delete("DELETE FROM adslot_filePositions where select_status = 0");
        if($broadcaster_id){
            $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$user_id' AND broadcaster_id = '$broadcaster_id'");
        }else{
            $del_cart = \DB::delete("DELETE FROM carts WHERE user_id = '$user_id' AND agency_id = '$agency_id'");
        }

        return;
    }

    public static function sessionizedRequest($request, $broadcaster_id, $agency_id)
    {
        if($request->min_age < 0 || $request->max_age < 0){
            return 'error_negative';
        }

        if($request->min_age > $request->max_age){
            return 'error_age';
        }

        $walkin = Utilities::getWalkInsDetails($request->client);
        $user_id = $walkin[0]->user_id;

        Utilities::deleteCartsUploadsfilePosition($user_id, $broadcaster_id, $agency_id);

        if (strtotime($request->end_date) < strtotime($request->start_date)) {
            return 'error_date';
        }

        $step1_req = ((object) $request->all());
        session(['first_step' => $step1_req]);

        return $user_id;
    }

    public static function checkRequestSession($request_session)
    {
        if(!$request_session){
            return 'data_lost';
        }
    }

    public static function adslotFilter($step1, $broadcaster_id, $agency_id)
    {
        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";
        if($agency_id){
            $ads_broad = [];
            $channel = "'".implode("','", $step1->channel)."'";
            $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND channels IN ($channel) group by broadcaster");
            foreach ($adslots as $adslot)
            {
                $broad = Utilities::switch_db('api')->select("SELECT * from broadcasters where id = '$adslot->broadcaster'");
                $ads_broad[] = [
                    'broadcaster' => $adslot->broadcaster,
                    'count_adslot' => $adslot->all_slots,
                    'boradcaster_brand' => $broad[0]->brand,
                    'logo' => $broad[0]->image_url,
                ];
            }
        }else{
            $broadcaster_details = Utilities::getBroadcasterDetails($broadcaster_id);
            $channel = $broadcaster_details[0]->channel_id;
            $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND is_available = 0 AND channels = '$channel' and broadcaster = '$broadcaster_id'");
            $ads_broad = [
                'broadcaster' => $adslots[0]->broadcaster,
                'count_adslot' => $adslots[0]->all_slots,
                'bradcaster_brand' => $broadcaster_details[0]->brand,
                'logo' => $broadcaster_details[0]->image_url
            ];
        }

        return $ads_broad;
    }

    public static function uploadMedia()
    {
        $id = request()->user_id;
        if(round((integer)request()->duration) > (integer)request()->time_picked){
            return response()->json(['error' => 'error']);
        }

        $check_file = \DB::select("SELECT * from uploads where user_id = '$id'");
        if(count($check_file) > 4){
            return response()->json(['error_number' => 'error_number']);
        }
        $image_url = encrypt(request()->image_url);
        $time = request()->time_picked;
        $channel = request()->channel;
        $format = request()->file_format;

        if (request()->image_url) {

            $check_image = \DB::select("SELECT * from uploads where time = '$time' AND channel = '$channel' AND user_id = '$id'");

            if(count($check_image) === 1){
                return response()->json(['error_check_image' => 'error_check_image']);
            }

            $insert_upload = \DB::table('uploads')->insert([
                'user_id' => $id,
                'time' => $time,
                'uploads' => $image_url,
                'file_name' => $time.'_'.$format.'_'.request()->file_name,
                'file_code' => request()->public_id,
                'channel' => $channel,
                'format' => $format
            ]);

            return 'success';

        }
    }

    public static function getRateCards($step1, $broadcaster_id)
    {

        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";
        $all_adslots = Utilities::getAllAvailableSlots($step1, $broadcaster_id);
        $ratecards = Utilities::switch_db('api')->select("SELECT d.day, h.time_range, r.id FROM rateCards as r JOIN days as d ON d.id = r.day 
                                                                JOIN hourlyRanges as h ON h.id = r.hourly_range_id where r.broadcaster = '$broadcaster_id'
                                                                AND r.id IN (SELECT rate_card FROM adslots where min_age >= $step1->min_age
                                                                AND max_age <= $step1->max_age
                                                                AND target_audience IN ($target_audience)
                                                                AND day_parts IN ($day_parts) AND region IN ($region)
                                                                AND is_available = 0 AND broadcaster = '$broadcaster_id')");

        foreach ($ratecards as $ratecard){
            $adslots = Utilities::filterAdslots(json_decode(json_encode($all_adslots), true), $ratecard->id);;
            $rate_card[] = [
                'id' => $ratecard->id,
                'hourly_range' => $ratecard->time_range,
                'day' => $ratecard->day,
                'adslot' => $adslots,
            ];
        }
        return ['rate_card' => $rate_card, 'adslot' => $all_adslots];
    }

    public static function filterAdslots($adslots, $rate_card)
    {
        $matches = array();
        foreach($adslots as $adslot){
            if($adslot['rate_card'] === $rate_card)
                $matches[] = (object)$adslot;
        }

        return $matches;

    }

    public static function getPositionPriceAndPercent($request)
    {
        if((int)$request->position != ''){
            $get_percentage = Utilities::switch_db('api')->select("SELECT percentage from filePositions where id = '$request->position'");
            $percentage = $get_percentage[0]->percentage;
            $percentage_price = (($percentage / 100) * (int)$request->price);
            $new_price = $percentage_price + (int)$request->price;
        }else{
            $new_price = (int)$request->price;
            $percentage = 0;
        }
        return (['percentage' => $percentage, 'new_price' => $new_price]);
    }

    public static function storeCart($request, $first, $agency_id, $broadcaster_id)
    {
        $percentage_new_price = Utilities::getPositionPriceAndPercent($request);
        $new_price = $percentage_new_price['new_price'];
        $percentage = $percentage_new_price['percentage'];

        $price = $request->price;
        $file = $request->file;
        $time = $request->time;
        $hourly_range = $request->range;
        $user = $request->walkins;
        $adslot_id = $request->adslot_id;
        $position = $request->position;
        $file_name = $request->file_name;
        $public_id = $request->file_code;
        $broadcaster = $request->broadcaster;
        $file_format = $request->file_format;
        $ip = \Request::ip();
        $start_date = date('Y-m-d', strtotime($first->start_date));
        $end_date = date('Y-m-d', strtotime($first->end_date));
        $number_of_occurrence = Utilities::numberOfAdslotOccurrence($adslot_id, $start_date, $end_date);

        $new_price = $new_price * $number_of_occurrence;

        //check if the fileposition is picked
        $check_pos = Utilities::switch_db('api')->select("SELECT * from adslot_filePositions where broadcaster_id = '$broadcaster' AND adslot_id = '$adslot_id' AND filePosition_id = '$position'");
        if(count($check_pos) === 1){
            return 'file_error';
        }

        if((int)$request->position != '') {
            $id = uniqid();
            $insert_file = Utilities::switch_db('api')->insert("INSERT into adslot_filePositions (id, adslot_id,filePosition_id, status, select_status, broadcaster_id) VALUES ('$id', '$adslot_id', '$position', 1, 0, '$broadcaster')");
        }


        if($broadcaster_id){
            $check = \DB::select("SELECT * from carts where adslot_id = '$adslot_id' and user_id = '$user' AND broadcaster_id = '$broadcaster' and filePosition_id = '$position' and filePosition_id != ''");
            if(count($check) === 1){
                return 'error';
            }

            //check if the budget has not been reached
            $check_cart = \DB::select("SELECT SUM(total_price) as total from carts where user_id = '$user' AND broadcaster_id = '$broadcaster'");
            $new_total = (integer)$new_price + $check_cart[0]->total;
            if((integer)$new_total > (integer)$first->campaign_budget){
                return 'budget_exceed_error';
            }


            $insert = \DB::insert("INSERT INTO carts (user_id, broadcaster_id, price, ip_address, file, from_to_time, `time`, adslot_id, percentage, total_price, filePosition_id, status, file_name, public_id, format) VALUES ('$user','$broadcaster','$price','$ip','$file','$hourly_range','$time','$adslot_id', '$percentage', '$new_price', '$position', 1, '$file_name', '$public_id', '$file_format')");
        }else{
            $check = \DB::select("SELECT * from carts where adslot_id = '$adslot_id' and user_id = '$user' AND agency_id = '$agency_id' and filePosition_id = '$position' and filePosition_id != ''");
            if(count($check) === 1){
                return 'error';
            }

            //check if the budget has not been reached
            $check_cart = \DB::select("SELECT SUM(total_price) as total from carts where user_id = '$user' AND agency_id = '$agency_id'");
            $new_total = (integer)$new_price + $check_cart[0]->total;
            if((integer)$new_total > (integer)$first->campaign_budget){
                return 'budget_exceed_error';
            }


            $insert = \DB::insert("INSERT INTO carts (user_id, broadcaster_id, price, ip_address, file, from_to_time, `time`, adslot_id, percentage, total_price, filePosition_id, status, agency_id, file_name, public_id, format) VALUES ('$user','$broadcaster','$price','$ip','$file','$hourly_range','$time','$adslot_id', '$percentage', '$new_price', '$position', 1, '$agency_id', '$file_name', '$public_id', '$file_format')");
        }

        return $insert;

    }

    public static function getCheckout($id, $first, $agency_id, $broadcaster_id)
    {
        $query = [];
        $day_parts = implode("','" ,$first->dayparts);
        $region = implode("','", $first->region);
        $target_audience = implode(",", $first->target_audience);
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ('$day_parts') ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id IN ('$target_audience')");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ('$region') ");
        $user = Utilities::switch_db('api')->select("SELECT * from users where id = '$id' ");
        if($agency_id){
            $calc = \DB::select("SELECT SUM(total_price) as total_price FROM carts WHERE user_id = '$id' and agency_id = '$agency_id'");
            $query_carts = \DB::select("SELECT c.id, c.from_to_time, c.time, c.price, c.percentage, c.total_price, f.position, b.brand, b.image_url FROM carts as c LEFT JOIN api_db.filePositions as f ON c.filePosition_id = f.id LEFT JOIN api_db.broadcasters as b ON b.id = c.broadcaster_id WHERE c.user_id = '$id' AND c.agency_id = '$agency_id'");
        }else{
            $calc = \DB::select("SELECT SUM(total_price) as total_price FROM carts WHERE user_id = '$id' and broadcaster_id = '$broadcaster_id'");
            $query_carts = \DB::select("SELECT c.id, c.from_to_time, c.time, c.price, c.percentage, c.total_price, f.position, b.brand, b.image_url FROM carts as c LEFT JOIN api_db.filePositions as f ON c.filePosition_id = f.id LEFT JOIN api_db.broadcasters as b ON b.id = c.broadcaster_id WHERE c.user_id = '$id' AND c.broadcaster_id = '$broadcaster_id'");
        }
        foreach ($query_carts as $query_cart){
            $query[] = [
                'id' => $query_cart->id,
                'from_to_time' => $query_cart->from_to_time,
                'time' => $query_cart->time,
                'price' => $query_cart->price,
                'percentage' => $query_cart->percentage,
                'position' => $query_cart->position === null ? 'No Position' : $query_cart->position,
                'total_price' => $query_cart->total_price,
                'broadcaster_logo' => $query_cart->image_url,
                'broadcaster_brand' => $query_cart->brand,
            ];
        }

        return (['calc' => $calc, 'brands' => $brands, 'queries' => $query, 'day_parts' => $day_partss, 'targets' => $targets, 'regions' => $regions, 'user' => $user]);

    }

    public static function checkRatecardExistence($broadcaster_id, $hourly_range_id, $day_id)
    {
        $check_rate_card = Utilities::switch_db('api')->select("SELECT id from rateCards where broadcaster = '$broadcaster_id' AND day = '$day_id' AND hourly_range_id = '$hourly_range_id'");
        return $check_rate_card;
    }


}
