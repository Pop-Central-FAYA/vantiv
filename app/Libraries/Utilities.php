<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\DB;
use Vanguard\Models\BrandClient;
use Vanguard\Models\BroadcasterPlayout;
use Vanguard\Models\PreselectedAdslot;
use Vanguard\Models\SelectedAdslot;
use Vanguard\Models\Upload;

class Utilities {

    protected $campaign_dates;

    public function __construct(CampaignDate $campaignDate)
    {
        $this->campaign_dates = $campaignDate;
    }

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
            $campaign_details = Utilities::switch_db('api')->select("SELECT c_d.campaign_id,c_d.status, c_d.min_age, 
                                                                          c_d.max_age, c_d.name AS campaign_name,
                                                                          c_d.user_id, c_d.agency, c_d.product, c_d.Industry, 
                                                                          c_d.sub_industry, c_d.broadcaster, c_d.start_date,
                                                                          c_d.stop_date, b.name AS brand, c_d.channel, c_d.target_audience,
                                                                          c_d.region, b.name, p.total, p.id AS payment_id 
                                                                          FROM campaignDetails AS c_d 
                                                                          INNER JOIN brands AS b ON b.id = c_d.brand
                                                                          INNER JOIN payments AS p ON p.campaign_id = c_d.campaign_id 
                                                                          WHERE c_d.campaign_id = '$id' AND c_d.broadcaster = '$broadcaster_id'");
        }else if($agency_id){
            $campaign_details = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.status, c_d.min_age, 
                                                                          c_d.max_age, c_d.name as campaign_name, c_d.user_id,
                                                                          c_d.agency, c_d.product, c_d.Industry, c_d.sub_industry, 
                                                                          c_d.start_date, c_d.stop_date, b.name AS brand,
                                                                          c_d.channel, c_d.target_audience, c_d.region, b.name,
                                                                          p.total, p.id AS payment_id 
                                                                          FROM campaignDetails AS c_d 
                                                                          INNER JOIN brands AS b ON b.id = c_d.brand
                                                                          INNER JOIN payments AS p ON p.campaign_id = c_d.campaign_id 
                                                                          WHERE  c_d.campaign_id = '$id' GROUP BY c_d.campaign_id");
        }
        $campaign_id = $campaign_details[0]->campaign_id;
        $channel = $campaign_details[0]->channel;
        $location_ids = $campaign_details[0]->region;
        $target_id = $campaign_details[0]->target_audience;
        $location = Utilities::switch_db('api')->select("SELECT * FROM regions where id IN ($location_ids) ");
        if($broadcaster_id){
            $broadcaster_campaign_id = $campaign_details[0]->broadcaster;
            $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels 
                                                                WHERE id 
                                                                IN (SELECT channel_id from broadcasters where id = '$broadcaster_campaign_id') 
                                                                ");

        }else if($agency_id){
            $channel = Utilities::switch_db('api')->select("SELECT * from campaignChannels where id IN ($channel) ");
        }
        $target_audiences = Utilities::switch_db('api')->select("SELECT * from targetAudiences where id IN ($target_id)");
        if($broadcaster_id){
            $broadcaster_campaign_id = $campaign_details[0]->broadcaster;
            $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters where id = '$broadcaster_campaign_id'");
        }else if($agency_id){
            $broadcasters = Utilities::switch_db('api')->select("SELECT * FROM broadcasters 
                                                                      WHERE id 
                                                                      IN (SELECT broadcaster from campaignDetails where campaign_id = '$id')
                                                                      ");
        }
        $payment_id = $campaign_details[0]->payment_id;
        $user_id = $campaign_details[0]->user_id;
        if($broadcaster_id){
            $campaign_details_broad = Utilities::switch_db('api')->select("SELECT amount AS total FROM paymentDetails 
                                                                                WHERE payment_id = '$payment_id' 
                                                                                AND broadcaster = '$broadcaster_id'");
        }

        $company_info = Utilities::switch_db('api')->select("SELECT * FROM walkIns WHERE user_id = '$user_id'");
        $company_name = $company_info[0]->company_name ? $company_info[0]->company_name : '';
        $user_broad = Utilities::switch_db('api')->select("SELECT * FROM users WHERE id = '$user_id' ");
        $user_agency = DB::select("SELECT * FROM users WHERE id = '$user_id' ");
        if($user_broad){
            $name = $user_broad[0]->firstname .' '.$user_broad[0]->lastname;
            $email = $user_broad[0]->email;
            $phone = $user_broad[0]->phone_number;
        }elseif($user_agency){
            $name = $user_agency[0]->first_name .' '.$user_agency[0]->last_name;
            $email = $user_agency[0]->email;
            $phone = $user_agency[0]->phone;
            #
        }

        $campaign_det = [
            'campaign_id' => $campaign_details[0]->campaign_id,
            'campaign_name' => $campaign_details[0]->campaign_name,
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
            'target_audience' => $target_audiences,
            'status' => $campaign_details[0]->status,
            'agency_id' => $campaign_details[0]->agency
        ];

        $files = Utilities::switch_db('api')->select("SELECT f.id, f.user_id, f.broadcaster_id, f.file_url, f.time_picked, f.status,
                                                                f.file_name, f.format, a.from_to_time, a.min_age, a.max_age,
                                                                d_p.day_parts, t.audience, r.region, h.time_range, d.day, b.brand from selected_adslots as f,
                                                                dayParts as d_p, adslots as a, targetAudiences as t, regions as r, days as d,
                                                                hourlyRanges as h, rateCards as r_c, broadcasters as b where f.broadcaster_id = b.id and
                                                                f.adslot = a.id and a.day_parts = d_p.id and a.target_audience = t.id and
                                                                a.region = r.id and a.rate_card = r_c.id and h.id = r_c.hourly_range_id and
                                                                r_c.day = d.id and a.broadcaster = b.id and campaign_id = '$campaign_id'");

        if($broadcaster_id){
            $files = Utilities::switch_db('api')->select("SELECT f.id, f.user_id, f.broadcaster_id, f.file_url, f.time_picked, f.status,
                                                              f.file_name, f.format, a.from_to_time, a.min_age, a.max_age, d_p.day_parts,
                                                              t.audience, r.region, h.time_range, d.day, b.brand from selected_adslots as f, dayParts as d_p, adslots as a,
                                                              targetAudiences as t, regions as r, days as d, hourlyRanges as h, rateCards as r_c, broadcasters as b
                                                              where f.broadcaster_id = b.id and f.adslot = a.id and a.day_parts = d_p.id and a.target_audience = t.id
                                                               and a.region = r.id and a.rate_card = r_c.id and h.id = r_c.hourly_range_id and r_c.day = d.id and
                                                               a.broadcaster = b.id and campaign_id = '$campaign_id' and f.broadcaster_id = '$broadcaster_id'");
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
                'file' => $file->file_url,
                'slot_time' => $file->time_picked.' seconds',
                'file_status' => $file->status,
                'file_name' => $file->file_name,
                'format' => $file->format ? $file->format : '',
            ];
        }

        //query builder to get compliance
        $compliances = Utilities::getComplianceLog($broadcaster_id, $campaign_details[0]->campaign_id);

        if($broadcaster_id){
            $uploaded_files = SelectedAdslot::where([['campaign_id', $campaign_id], ['broadcaster_id', $broadcaster_id]])->get();
        }else if($agency_id){
            $uploaded_files = SelectedAdslot::where('campaign_id', $campaign_id)->get();

        }

        return (['campaign_det' => $campaign_det, 'file_details' => $file_details, 'broadcasters' => $broadcasters, 'compliance_reports' => $compliances, 'uploaded_files' => $uploaded_files]);

    }

    public static function getComplianceLog($broadcaster_id, $campaign_id)
    {

        return Utilities::switch_db('api')->table('broadcaster_playouts')
            ->join('broadcasters', 'broadcaster_playouts.broadcaster_id', '=', 'broadcasters.id')
            ->join('selected_adslots', 'broadcaster_playouts.selected_adslot_id', '=', 'selected_adslots.id')
            ->join('mpos', 'broadcaster_playouts.mpo_detail_id', '=', 'mpos.id')
            ->select('broadcasters.id AS broadcaster_id','broadcasters.brand AS broadcaster_station',
                'selected_adslots.file_name AS asset_name','selected_adslots.time_picked AS duration',
                'broadcaster_playouts.status AS compliance_status', 'broadcaster_playouts.played_at AS played_date',
                'mpos.campaign_id AS campaign_id', 'selected_adslots.air_date AS schedule_date',
                'broadcaster_playouts.air_between AS schedule_spot')
            ->when($broadcaster_id, function($query) use ($broadcaster_id) {
                return $query->where('broadcaster_playouts.broadcaster_id', $broadcaster_id);
            })
            ->where('mpos.campaign_id', $campaign_id)
            ->get();

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
        $cart_check = Utilities::switch_db('api')->select("SELECT SUM(time) as time_sum, adslot_id from preselected_adslots WHERE user_id = '$id' GROUP BY adslot_id");
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
        $api_db = Utilities::switch_db('api');
        $local_db = Utilities::switch_db('local');

        $walkins = $api_db->select("SELECT * from walkIns where id = '$client_id'");
        $user_id = $walkins[0]->user_id;
        if($request->company_logo){
            $walkins_update_logo = $api_db->update("UPDATE walkIns set company_logo = '$request->company_logo' where id = '$client_id'");
        }

        try {
            $walkins_update = $api_db->update("UPDATE walkIns set location = '$request->address', company_name = '$request->company_name' where id = '$client_id'");
        }catch (\Exception $e) {
            $api_db->rollback();
            return 'error';
        }

        try {
            $api_user_update = $api_db->update("UPDATE users set firstname = '$request->first_name', lastname = '$request->last_name',
                                                                      phone_number = '$request->phone' where id = '$user_id'");
        }catch (\Exception $e){
            $api_db->rollback();
            return 'error';
        }

        try {
            $local_db_update = $local_db->update("UPDATE users set first_name = '$request->first_name', last_name = '$request->last_name', phone = '$request->phone' where email = '$request->email'");
        }catch (\Exception $e) {
            $local_db->rollback();
            return 'error';
        }

        $api_db->commit();
        $local_db->commit();
        return 'success';

    }

    public static function getClientCampaignData($user_id, $broadcaster_id)
    {
        $campaigns = [];
        $all_campaign = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.name, c_d.product, c_d.start_date, c_d.stop_date, c_d.adslots, c.campaign_reference, p.total,
                                                                b.name as brands from campaignDetails as c_d, campaigns as c, payments as p, brands as b WHERE c_d.user_id = '$user_id' AND
                                                                p.campaign_id = c_d.campaign_id AND c_d.campaign_id = c.id AND b.id = c_d.brand AND c_d.adslots > 0 and
                                                                c_d.broadcaster = '$broadcaster_id' ORDER BY c_d.time_created DESC");
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
        $walkin_brands = Utilities::getBrandsForWalkins($id);
        $brands = [];
        foreach ($walkin_brands as $walkin_brand){
            $campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where brand = '$walkin_brand->id' and walkins_id = '$walkin_brand->client_walkins_id'
                                                                and broadcaster = '$broadcaster_id'");
            $last_count_campaign = count($campaigns) - 1;
            $pay = Utilities::switch_db('api')->select("SELECT SUM(total) as total from payments where campaign_id IN (SELECT campaign_id from campaignDetails where
                                                            brand = '$walkin_brand->id' and walkins_id = '$walkin_brand->client_walkins_id' and broadcaster = '$broadcaster_id')");
            $brands[] = [
                'id' => $walkin_brand->id,
                'client_id' => $walkin_brand->client_walkins_id,
                'brand' => $walkin_brand->name,
                'date' => $walkin_brand->created_at,
                'count_brand' => count($walkin_brands),
                'campaigns' => count($campaigns),
                'image_url' => $walkin_brand->image_url,
                'last_campaign' => $campaigns ? $campaigns[$last_count_campaign]->name : 'none',
                'total' => number_format($pay[0]->total,2),
                'industry_id' => $walkin_brand->industry_code,
                'sub_industry_id' => $walkin_brand->sub_industry_code,
            ];
        }

        return $brands;
    }

    public static function spreadDateInCampaign($start_date, $end_date)
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

        return $dates;
    }

    public static function numberOfAdslotOccurrence($adslot_id, $start_date, $end_date)
    {
        $adslot_array = [];
        $dates = Utilities::spreadDateInCampaign($start_date, $end_date);
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

    public static function getBrandsForWalkins($client_id)
    {
        $brand_list = Utilities::switch_db('api')->select("SELECT b.*, b_c.media_buyer_id as agency_broadcaster, b_c.client_id as client_walkins_id
                                                        FROM brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id where b_c.client_id = '$client_id'");
        return $brand_list;
    }

    public static function getBrands($media_buyer_id)
    {
        return Utilities::switch_db('api')->select("SELECT b.*, b_c.media_buyer_id as broadcaster_agency_id, b_c.client_id as client_walkins_id
                                                        FROM brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id where b_c.media_buyer_id = '$media_buyer_id'");
    }

    public static function singleBrand($id, $client_id)
    {
        return Utilities::switch_db('api')->select("SELECT b.*, b_c.media_buyer_id as broadcaster_agency_id, b_c.client_id as client_walkins_id
                                                  FROM brand_client as b_c INNER JOIN brands as b ON b.id = b_c.brand_id where b.id = '$id' AND b_c.client_id = '$client_id'");
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
        $industries = Utilities::switch_db('api')->select("SELECT * FROM sectors ORDER BY `name` ASC");
        $subindustries = Utilities::switch_db('api')->select("SELECT * FROM subSectors ORDER BY `name` ASC");
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
        $adslots =  Utilities::switch_db('api')->select("SELECT a.*,r.day as day_id, IF(a.id = p_p.adslot_id,p_p.price_60,p.price_60) as price_60, IF(a.id = p_p.adslot_id,p_p.price_45,p.price_45) as price_45,
                                                                         IF(a.id = p_p.adslot_id,p_p.price_30,p.price_30) as price_30, IF(a.id = p_p.adslot_id,p_p.price_15,p.price_15) as price_15 from adslots as a
                                                                         JOIN adslotPrices as p ON a.id = p.adslot_id
                                                                         JOIN rateCards as r ON a.rate_card = r.id
                                                                         LEFT JOIN adslotPercentages as p_p ON a.id = p_p.adslot_id
                                                                         where a.min_age >= $step1->min_age AND a.max_age <= $step1->max_age AND a.target_audience IN ($target_audience) AND a.day_parts IN ($day_parts)
                                                                         AND a.region IN ($region) AND a.is_available = 0 AND a.channels = '$channel' and a.broadcaster = '$broadcaster_id'");

        return $adslots;

    }


    public function getCampaignDatatables($all_campaigns)
    {
        $campaigns = [];
        foreach ($all_campaigns as $all_campaign)
        {
            $start_date = strtotime($all_campaign->start_date);
            $stop_date = strtotime($all_campaign->stop_date);

            $campaigns[] = [
                'id' => $all_campaign->campaign_reference,
                'campaign_id' => $all_campaign->campaign_id,
                'name' => $all_campaign->name,
                'brand' => ucfirst($all_campaign->brand_name),
                'product' => $all_campaign->product,
                'date_created' => date('M j, Y', strtotime($all_campaign->time_created)),
                'start_date' => date('M j, Y', $start_date),
                'end_date' => date('Y-m-d', $stop_date),
                'adslots' => count((explode(',', $all_campaign->adslots_id))),
                'budget' => number_format($all_campaign->total, 2),
                'status' => $all_campaign->status
            ];
        }

        return $campaigns;

    }

    public static function getCampaignDatatablesforCampaignOnHold($all_campaigns)
    {
        $campaigns = [];
        foreach ($all_campaigns as $all_campaign)
        {
            $start_date = strtotime($all_campaign->start_date);
            $stop_date = strtotime($all_campaign->stop_date);

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
                'status' => $all_campaign->status,
                'full_name' => $all_campaign->full_name ? $all_campaign->full_name : '',
                'email' => $all_campaign->email ? $all_campaign->email : '',
                'user_id' => $all_campaign->user_id ? $all_campaign->user_id : '',
                'phone_number' => $all_campaign->phone_number ? $all_campaign->phone_number : '',
                'payment_id' => $all_campaign->payment_id,
                'total' => (integer)$all_campaign->total
            ];
        }

        return $campaigns;

    }

    public static function deleteCartsUploadsfilePosition($user_id, $broadcaster_id, $agency_id)
    {
        $del_uplaods = Upload::where('user_id', $user_id)->delete();
        $del_file_position = Utilities::switch_db('api')->delete("DELETE FROM adslot_filePositions where select_status = 0");
        if($broadcaster_id){
            $del_cart = PreselectedAdslot::where([
                ['user_id', $user_id],
                ['broadcaster_id', $broadcaster_id]
            ])->delete();
        }else{
            $del_cart = PreselectedAdslot::where([
                ['user_id', $user_id],
                ['agency_id', $broadcaster_id]
            ])->delete();
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

    public function checkCampaignInformationSessionActiveness($campaign_general_information)
    {
        if(!$campaign_general_information){
            return 'data_lost';
        }
    }

    public static function getRateCardIdBetweenStartAndEndDates($start_date, $end_date)
    {
        $day_id = [];
        $ratecards_array = [];
        $campaign_dates = Utilities::spreadDateInCampaign($start_date, $end_date);
        foreach ($campaign_dates as $campaign_date){
            $date_name = date('l', strtotime($campaign_date));
            $date_id = Utilities::switch_db('api')->select("SELECT * from days where `day` = '$date_name'");
            $day_id[] = $date_id[0]->id;
        }
        $day_id_imploded = "'".implode("','" ,$day_id)."'";

        $ratecards =  Utilities::switch_db('api')->select("SELECT id from rateCards where day IN ($day_id_imploded) ");

        foreach ($ratecards as $ratecard){
            $ratecards_array[] = $ratecard->id;
        }

        return $ratecards_array;

    }

    public static function adslotFilter($step1, $broadcaster_id, $agency_id)
    {
        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";

        $ratecards = Utilities::getRateCardIdBetweenStartAndEndDates($step1->start_date, $step1->end_date);

        $ratecards_imploded = "'".implode("','", $ratecards)."'";

        if($agency_id){
            $ads_broad = [];
            $channel = "'".implode("','", $step1->channel)."'";
            $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots
                                                        where min_age >= $step1->min_age AND max_age <= $step1->max_age AND target_audience IN
                                                        ($target_audience) AND day_parts IN ($day_parts) AND region IN ($region) AND rate_card IN
                                                        ($ratecards_imploded) AND is_available = 0 AND channels IN ($channel) group by broadcaster");
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
            $adslots = Utilities::switch_db('api')->select("SELECT broadcaster, COUNT(broadcaster) as all_slots FROM adslots where min_age >= $step1->min_age AND
                                                                max_age <= $step1->max_age AND target_audience IN ($target_audience) AND day_parts IN ($day_parts) AND
                                                                region IN ($region) AND rate_card IN ($ratecards_imploded) AND is_available = 0 AND channels = '$channel'
                                                                and broadcaster = '$broadcaster_id'");
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
        $user_id = request()->user_id;

        if(round((integer)request()->duration) > (integer)request()->time_picked){
            return 'error';
        }

        $check_file = Upload::where('user_id', $user_id)->get();
        if(count($check_file) > 4){
            return 'error_number';
        }
        $file_url = request()->file_url;
        $time = request()->time_picked;
        $channel = request()->channel;
        $format = request()->file_format;

        if (request()->file_url) {

            $check_file = Upload::where([
                ['time', $time],
                ['channel', $channel],
                ['user_id', $user_id]
            ])->first();

            if($check_file){
                return 'error_check_image';
            }

            Upload::create([
                'user_id' => $user_id,
                'time' => $time,
                'file_url' => $file_url,
                'file_name' => $time.'_'.$format.'_'.request()->file_name,
                'channel' => $channel,
                'format' => $format
            ]);

            return 'success';
        }
    }

    public function getRateCards($step1, $broadcaster_id, $start_date, $end_date)
    {

        $day_parts = "'".implode("','" ,$step1->dayparts)."'";
        $region = "'".implode("','", $step1->region)."'";
        $target_audience = "'".implode("','", $step1->target_audience)."'";
        $all_adslots = Utilities::getAllAvailableSlots($step1, $broadcaster_id);
        $adslots_inventory = Utilities::fecthAllAdslotsForBroadcaster($broadcaster_id);
        $first_week_days = $this->campaign_dates->getFirstWeek($start_date, $end_date);
        $campaign_dates_in_first_week = $this->campaign_dates->getStartAndEndDateForFirstWeek($first_week_days);
        $ratecards = Utilities::getRateCardIdBetweenStartAndEndDates($campaign_dates_in_first_week['start_date_of_the_week'],
                                                                        $campaign_dates_in_first_week['end_date_of_the_week']);
        $all_adslots_for_broadcaster = Utilities::fecthAllAdslotsForBroadcaster($broadcaster_id);
        $array_of_filtered_and_unfiltered = Utilities::differentiateArray($all_adslots_for_broadcaster, $all_adslots);
        $ratecards_imploded = "'".implode("','", $ratecards)."'";

        $ratecards = Utilities::switch_db('api')->select("SELECT d.day, r.day as day_id, r.id FROM rateCards as r 
                                                                JOIN days as d ON d.id = r.day
                                                                AND r.id IN (SELECT rate_card FROM adslots where min_age >= $step1->min_age
                                                                AND max_age <= $step1->max_age
                                                                AND target_audience IN ($target_audience)
                                                                AND day_parts IN ($day_parts) AND region IN ($region) AND rate_card IN ($ratecards_imploded)
                                                                AND is_available = 0 AND broadcaster = '$broadcaster_id') GROUP BY r.day");
        foreach ($ratecards as $ratecard){
            $adslots = Utilities::filterAdslots(json_decode(json_encode($all_adslots), true), $ratecard->day_id);
            $rate_card[] = [
                'id' => $ratecard->id,
                'day' => $ratecard->day,
                'day_id' => $ratecard->day_id,
                'adslot' => $adslots,
                'array_filtered_unfiltered' => $array_of_filtered_and_unfiltered,
                'start_date' => $campaign_dates_in_first_week['start_date_of_the_week'],
                'end_date' => $campaign_dates_in_first_week['end_date_of_the_week'],
                'actual_date' => $first_week_days[$ratecard->day]
            ];
        }
        return ['rate_card' => $rate_card, 'adslot' => $all_adslots];
    }

    public static function differentiateArray($big_array, $small_array)
    {
        $new_unfiltered_array = [];
        $new_filtered_array = [];
        foreach ($big_array as $unfiltered){
            $new_unfiltered_array[$unfiltered->adslot_id][] = $unfiltered;
        }

        foreach ($small_array as $filtered_array){
            $new_filtered_array[$filtered_array->id][] = $filtered_array;
        }

        $new_array = [];
        foreach ($new_unfiltered_array as $key => $value){
            if(array_key_exists($key, $new_filtered_array)){
                $new_array[] = $new_filtered_array[$key];
            }else{
                $new_array[] = $value;
            }

        }

        return $new_array;
    }

    public static function fecthAllAdslotsForBroadcaster($broadcaster)
    {
        $broadcasters_adslots = Utilities::switch_db('api')->select("SELECT a.from_to_time, a.id as adslot_id, r.day as day_id from adslots as a 
                                                              INNER JOIN rateCards as r ON r.id = a.rate_card
                                                              where a.broadcaster = '$broadcaster'");
        return $broadcasters_adslots;
    }

    public static function filterAdslots($adslots, $day)
    {
        $matches = array();
        foreach($adslots as $adslot){
            if($adslot['day_id'] === $day)
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
        $file_url = $request->file;
        $time = $request->time;
        $hourly_range = $request->range;
        $user_id = $request->walkins;
        $adslot_id = $request->adslot_id;
        $position = $request->position;
        $file_name = $request->file_name;
        $broadcaster = $request->broadcaster;
        $file_format = $request->file_format;
        $air_date = $request->air_date;

        //check if the fileposition is picked
        $check_pos = Utilities::switch_db('api')->select("SELECT * from adslot_filePositions where broadcaster_id = '$broadcaster' AND adslot_id = '$adslot_id'
                                                              AND filePosition_id = '$position'");
        if(count($check_pos) === 1){
            return 'file_error';
        }

        if((int)$request->position != '') {
            $id = uniqid();
            $insert_file = Utilities::switch_db('api')->insert("INSERT into adslot_filePositions (id, adslot_id,filePosition_id, status, select_status, broadcaster_id)
                                                                    VALUES ('$id', '$adslot_id', '$position', 1, 0, '$broadcaster')");
        }


        if($broadcaster_id){
            $check_for_occurence_in_cart = PreselectedAdslot::where([
                ['adslot_id', $adslot_id],
                ['user_id', $user_id],
                ['broadcaster_id', $broadcaster],
                ['filePosition_id', $position],
                ['filePosition_id', '<>', '']
            ])->get();

            if(count($check_for_occurence_in_cart) === 1){
                return 'error';
            }

            //check if the budget has not been reached
            $check_cart = Utilities::switch_db('api')->select("SELECT SUM(total_price) as total from preselected_adslots where user_id = '$user_id' AND broadcaster_id = '$broadcaster'");
            $new_total = (integer)$new_price + $check_cart[0]->total;
            if((integer)$new_total > (integer)$first->campaign_budget){
                return 'budget_exceed_error';
            }

            $insert = PreselectedAdslot::create([
                'user_id' => $user_id,
                'broadcaster_id' => $broadcaster,
                'price' => $price,
                'file_url' => $file_url,
                'from_to_time' => $hourly_range,
                'time' => $time,
                'adslot_id' => $adslot_id,
                'percentage' => $percentage,
                'total_price' => $new_price,
                'filePosition_id' => $position,
                'file_name' => $file_name,
                'format' => $file_format,
                'air_date' => $air_date
            ]);
        }else{
            $check_for_occurence_in_cart = PreselectedAdslot::where([
                ['adslot_id', $adslot_id],
                ['user_id', $user_id],
                ['agency_id', $agency_id],
                ['filePosition_id', $position],
                ['filePosition_id', '<>', '']
            ])->get();

            if(count($check_for_occurence_in_cart) === 1){
                return 'error';
            }

            //check if the budget has not been reached
            $check_cart = Utilities::switch_db('api')->select("SELECT SUM(total_price) as total from preselected_adslots where user_id = '$user_id' AND broadcaster_id = '$broadcaster'");
            $new_total = (integer)$new_price + $check_cart[0]->total;
            if((integer)$new_total > (integer)$first->campaign_budget){
                return 'budget_exceed_error';
            }

            $insert = PreselectedAdslot::create([
               'user_id' => $user_id,
               'broadcaster_id' => $broadcaster,
               'price' => $price,
               'file_url' => $file_url,
               'from_to_time' => $hourly_range,
               'time' => $time,
               'adslot_id' => $adslot_id,
               'percentage' => $percentage,
               'total_price' => $new_price,
               'filePosition_id' => $position,
               'agency_id' => $agency_id,
               'file_name' => $file_name,
               'format' => $file_format,
                'air_date' => $air_date
            ]);

        }

        return $insert;

    }

    public static function getCheckout($id, $first, $agency_id, $broadcaster_id)
    {
        $preselected_adslots_array = [];
        $day_parts = implode("','" ,$first->dayparts);
        $region = implode("','", $first->region);
        $target_audience = implode(",", $first->target_audience);
        $brands = Utilities::switch_db('api')->select("SELECT name from brands where id = '$first->brand'");
        $day_partss = Utilities::switch_db('api')->select("SELECT day_parts from dayParts where id IN ('$day_parts') ");
        $targets = Utilities::switch_db('api')->select("SELECT audience from targetAudiences where id IN ('$target_audience')");
        $regions = Utilities::switch_db('api')->select("SELECT region from regions where id IN ('$region') ");
        $user = Utilities::switch_db('api')->select("SELECT * from users where id = '$id' ");
        if($agency_id){
            $calc = Utilities::switch_db('api')->select("SELECT SUM(total_price) as total_price FROM preselected_adslots WHERE user_id = '$id' and agency_id = '$agency_id'");
            $query_preselected_adslots = Utilities::switch_db('api')->select("SELECT c.id, c.from_to_time, c.time, c.price, c.percentage, c.total_price,c.air_date, f.position, b.brand, b.image_url FROM preselected_adslots as c
                                                                  LEFT JOIN api_db.filePositions as f ON c.filePosition_id = f.id LEFT JOIN api_db.broadcasters as b ON b.id = c.broadcaster_id
                                                                  WHERE c.user_id = '$id' AND c.agency_id = '$agency_id'");
        }else{
            $calc = Utilities::switch_db('api')->select("SELECT SUM(total_price) as total_price FROM preselected_adslots WHERE user_id = '$id' and broadcaster_id = '$broadcaster_id'");
            $query_preselected_adslots = Utilities::switch_db('api')->select("SELECT c.id, c.from_to_time, c.time, c.price, c.percentage, c.total_price,c.air_date, f.position, b.brand, b.image_url FROM preselected_adslots as c
                                                                    LEFT JOIN api_db.filePositions as f ON c.filePosition_id = f.id LEFT JOIN api_db.broadcasters as b ON b.id = c.broadcaster_id
                                                                    WHERE c.user_id = '$id' AND c.broadcaster_id = '$broadcaster_id'");
        }
        foreach ($query_preselected_adslots as $query_preselected_adslot){
            $preselected_adslots_array[] = [
                                'id' => $query_preselected_adslot->id,
                                'from_to_time' => $query_preselected_adslot->from_to_time,
                                'time' => $query_preselected_adslot->time,
                                'price' => $query_preselected_adslot->price,
                                'percentage' => $query_preselected_adslot->percentage,
                                'position' => $query_preselected_adslot->position === null ? 'No Position' : $query_preselected_adslot->position,
                                'total_price' => $query_preselected_adslot->total_price,
                                'broadcaster_logo' => $query_preselected_adslot->image_url,
                                'broadcaster_brand' => $query_preselected_adslot->brand,
                                'air_date' => $query_preselected_adslot->air_date
                            ];
        }

        return (['calc' => $calc, 'brands' => $brands, 'preselected_adslot_arrays' => $preselected_adslots_array, 'day_parts' => $day_partss, 'targets' => $targets, 'regions' => $regions, 'user' => $user]);

    }

    public static function checkRatecardExistence($broadcaster_id, $hourly_range_id, $day_id)
    {
        $check_rate_card = Utilities::switch_db('api')->select("SELECT id from rateCards where broadcaster = '$broadcaster_id' AND day = '$day_id' AND hourly_range_id = '$hourly_range_id'");
        return $check_rate_card;
    }

    public static function getProfileDetails($api_user, $local_user,$api_agent)
    {
        $user_details = [
            'first_name' => $api_user[0]->firstname,
            'last_name' => $api_user[0]->lastname,
            'phone' => $api_user[0]->phone_number,
            'email' => $api_user[0]->email,
            'address' => $local_user[0]->address,
            'location' => $api_agent[0]->location,
            'nationality' => $api_agent[0]->nationality,
            'username' => $local_user[0]->username,
            'image' => $api_agent[0]->image_url ? $api_agent[0]->image_url : ''
        ];

        return $user_details;
    }

    public static function clientscampaigns($campaigns)
    {
        $user_camp = [];
        foreach ($campaigns as $campaign){
            $user_camp[] = [
                'product' => $campaign->product,
                'num_of_slot' => $campaign->adslots,
                'payment' => $campaign->total,
                'date' => $campaign->time_created
            ];
        }

        return $user_camp;
    }

    public static function clientGraph($campaigns)
    {
        $all_campaign_total_graph = [];
        $all_campaign_date_graph = [];

        foreach ($campaigns as $all_camp){
            $all_campaign_total_graph[] = $all_camp->total;
            $all_campaign_date_graph[] = date('Y-m-d', strtotime($all_camp->time_created));
        }


        $campaign_payment = json_encode($all_campaign_total_graph);
        $campaign_date = json_encode($all_campaign_date_graph);

        return (['campaign_payment' => $campaign_payment, 'campaign_date' => $campaign_date]);
    }


    public static function campaignDetailsInformations($first, $campaign_id, $id, $now, $ads, $group_data, $agency_id, $walkin_id, $broadcaster_id, $broadcaster_details, $preselected_adslots)
    {
        return [
            'id' => uniqid(),
            'campaign_id' => $campaign_id,
            'user_id' => $id,
            'channel' => $agency_id ? "'". implode("','" ,$first->channel) . "'" : "'". $broadcaster_details[0]->channel_id . "'",
            'brand' => $first->brand,
            'start_date' => date('Y-m-d', strtotime($first->start_date)),
            'stop_date' => date('Y-m-d', strtotime($first->end_date)),
            'name' => $first->campaign_name,
            'product' => $first->product,
            'day_parts' => "'". implode("','" ,$first->dayparts) . "'",
            'target_audience' => "'". implode("','" ,$first->target_audience) . "'",
            'region' => "'". implode("','" ,$first->region) . "'",
            'min_age' => (integer)$first->min_age,
            'max_age' => (integer)$first->max_age,
            'industry' => $first->industry,
            'adslots' => $agency_id ? $group_data->total_slot : count($preselected_adslots),
            'walkins_id' => $walkin_id[0]->id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'adslots_id' => "'". implode("','" ,$ads) . "'",
            'agency' => $agency_id ? $agency_id : '',
            'agency_broadcaster' => $agency_id ? $group_data->broadcaster_id : '',
            'broadcaster' => $agency_id ? $group_data->broadcaster_id : $broadcaster_id,
            'sub_industry' => $first->sub_industry,
            'status' => 'on_hold'
        ];
    }

    public static function campaignInformation($campaign_id, $campaign_reference, $now)
    {
        return [
            'id' => $campaign_id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'campaign_reference' => $campaign_reference
        ];
    }

    public static function campaignFileInformation($camp_id, $preselected_adslot, $id, $now, $agency_id, $broadcaster_id)
    {
        return [
            'id' => uniqid(),
            'campaign_id' => $camp_id[0]->id,
            'file_name' => $preselected_adslot->file_name,
            'file_url' => $preselected_adslot->file_url,
            'adslot' => $preselected_adslot->adslot_id,
            'user_id' => $id,
            'file_code' => Utilities::generateReference(),
            'created_at' => date('Y-m-d H:i:s', $now),
            'updated_at' => date('Y-m-d H:i:s', $now),
            'agency_id' => $agency_id,
            'agency_broadcaster' => $preselected_adslot->broadcaster_id,
            'time_picked' => $preselected_adslot->time,
            'broadcaster_id' => $agency_id ? $preselected_adslot->broadcaster_id : $broadcaster_id,
            'public_id' => '',
            'format' => $preselected_adslot->format,
            'status' => 'pending',
            'air_date' => $preselected_adslot->air_date
        ];
    }

    public static function campaignPaymentInformation($pay_id, $camp_id, $request, $now, $first)
    {
        return [
            'id' => $pay_id,
            'campaign_id' => $camp_id[0]->id,
            'campaign_reference' => $camp_id[0]->campaign_reference,
            'total' => $request->total,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'campaign_budget' => $first->campaign_budget
        ];
    }

    public static function campaignPaymentDetailsInformation($pay_id, $request, $group_data, $walkin_id, $now, $agency_id, $first, $calc, $broadcaster_id)
    {
        return [
            'id' => uniqid(),
            'payment_id' => $pay_id,
            'payment_method' => '',
            'amount' => $agency_id ? (integer) $group_data->total : (integer) $calc[0]->total_price,
            'walkins_id' => $walkin_id[0]->id,
            'time_created' => date('Y-m-d H:i:s', $now),
            'time_modified' => date('Y-m-d H:i:s', $now),
            'agency_id' => $agency_id ? $agency_id : '',
            'agency_broadcaster' => $agency_id ? $group_data->broadcaster_id : '',
            'broadcaster' => $agency_id ? $group_data->broadcaster_id : $broadcaster_id,
            'campaign_budget' => $first->campaign_budget
        ];
    }

    public static function campaignInvoiceInformation($invoice_id, $camp_id, $invoice_number, $payment_id)
    {
        return [
            'id' => $invoice_id,
            'campaign_id' => $camp_id[0]->id,
            'campaign_reference' => $camp_id[0]->campaign_reference,
            'invoice_number' => $invoice_number,
            'payment_id' => $payment_id[0]->id,
        ];
    }

    public static function campaignInvoiceDetailsInformation($invoice_id, $id, $invoice_number, $group_data, $walkin_id, $agency_id, $broadcaster_id, $calc)
    {
        return [
            'id' => uniqid(),
            'invoice_id' => $invoice_id,
            'user_id' => $id,
            'invoice_number' => $invoice_number,
            'actual_amount_paid' => $agency_id ? (integer)$group_data->total : (integer) $calc[0]->total_price,
            'refunded_amount' => 0,
            'walkins_id' => $walkin_id[0]->id,
            'agency_id' => $agency_id ? $agency_id : '',
            'agency_broadcaster' => $agency_id ? $group_data->broadcaster_id : '',
            'broadcaster_id' => $agency_id ? $group_data->broadcaster_id : $broadcaster_id,
        ];
    }

    public static function campaignMpoInformation($mpo_id, $camp_id, $invoice_number)
    {
        return [
            'id' => $mpo_id,
            'campaign_id' => $camp_id[0]->id,
            'campaign_reference' => $camp_id[0]->campaign_reference,
            'invoice_number' => $invoice_number,
        ];
    }

    public static function campaignMpoDetailsInformation($mpo_id, $agency_id, $group_data, $broadcaster_id)
    {
        return [
            'id' => uniqid(),
            'mpo_id' => $mpo_id,
            'discount' => 0,
            'agency_id' => $agency_id ? $agency_id : '',
            'agency_broadcaster' => $agency_id ? $group_data->broadcaster_id : '',
            'broadcaster_id' => $agency_id ? $group_data->broadcaster_id : $broadcaster_id,
        ];

    }

    public static function insertIntoUsersLocalDb($request)
    {
        return DB::table('users')->insert([
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt('password'),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'country_id' => $request->country_id,
            'status' => 'Inactive',
        ]);
    }

    public static function insertRolesInLocalDb($user_id)
    {
        return DB::table('role_user')->insert([
            'user_id' => $user_id,
            'role_id' => 5
        ]);
    }

    public static function insertIntoUsersApiDB($request, $role_id)
    {
        return Utilities::switch_db('api')->table('users')->insert([
            'id' => uniqid(),
            'role_id' => $role_id,
            'email' => $request->email,
            'token' => '',
            'password' => bcrypt('password'),
            'firstname' => $request->first_name,
            'lastname' => $request->last_name,
            'phone_number' => $request->phone,
            'user_type' => 4,
            'status' => 1
        ]);
    }

    public static function insertIntoWalkinsApiDB($client_id, $user_id, $broadcaster_id, $request, $company_image, $agency_id)
    {
        return Utilities::switch_db('api')->table('walkIns')->insert([
            'id' => $client_id,
            'user_id' => $user_id,
            'broadcaster_id' => $agency_id ? $request->broadcaster_id : $broadcaster_id,
            'client_type_id' => $request->client_type_id,
            'location' => $request->address,
            'agency_id' => $agency_id ? $agency_id : '',
            'nationality' => 566,
            'company_name' => $request->company_name,
            'company_logo' => $company_image
        ]);
    }

    public static function storeBrands($brand, $request, $unique, $image_url, $brand_slug)
    {
        $brand->id = $unique;
        $brand->name = $request->brand_name;
        $brand->image_url = $image_url;
        $brand->industry_code = $request->industry;
        $brand->sub_industry_code = $request->sub_industry;
        $brand->slug = $brand_slug;
        return $brand->save();

    }

    public static function storeBrandClient($brand_id, $broadcaster_agency_id, $client_id)
    {
        if(\Session::get('agency_id')){
            $media_buyer = 'Agency';
        }else{
            $media_buyer = 'Broadcaster';
        }
        BrandClient::create([
           'brand_id' => $brand_id,
           'media_buyer' => $media_buyer,
           'media_buyer_id' => $broadcaster_agency_id,
           'client_id' => $client_id,
        ]);
    }

    public static function checkIfCampaignStartDateHasReached($campaign_id, $broadcaster_id, $agency_id)
    {
        $today = date('Y-m-d');
        if($broadcaster_id){
            $campaign_start_date = Utilities::switch_db('api')->select("SELECT start_date from campaignDetails
                                                                        where campaign_id = '$campaign_id'
                                                                        AND broadcaster = '$broadcaster_id'");
        }else{
            $campaign_start_date = Utilities::switch_db('api')->select("SELECT start_date from campaignDetails
                                                                        where campaign_id = '$campaign_id'
                                                                        AND agency = '$agency_id' GROUP BY campaign_id");
        }

        if($today > $campaign_start_date[0]->start_date){
            return 'error';
        }
    }

    public static function transactionHistory($agency_id, $amount, $current_balance, $previous_balance)
    {
        return  [
            'id' => uniqid(),
            'user_id' => $agency_id,
            'amount' => $amount,
            'prev_balance' => $previous_balance,
            'current_balance' => $current_balance,
            'status' => 1,

        ];
    }

    public function getStartAndEndDateWithTheWeek($start_date, $end_date)
    {
        $campaign_date_by_weeks = $this->campaign_dates->groupCampaignDateByWeek($start_date, $end_date);
        $campaign_dates_by_week_with_start_end_date = [];
        foreach ($campaign_date_by_weeks as $campaign_date_by_week){
            $start_and_end_date_of_the_week = $this->campaign_dates->getStartAndEndDateForFirstWeek($campaign_date_by_week);
            $campaign_dates_by_week_with_start_end_date[] = [
                'date_by_week' => $campaign_date_by_week,
                'start_date' => $start_and_end_date_of_the_week['start_date_of_the_week'],
                'end_date' => $start_and_end_date_of_the_week['end_date_of_the_week'],
            ];
        }

        return $campaign_dates_by_week_with_start_end_date;
    }

}
