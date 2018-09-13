<?php

namespace Vanguard\Libraries;

use Hamcrest\Util;
use Illuminate\Support\Facades\Session;
use Ixudra\Curl\Facades\Curl;
use Vanguard\ApiLog;
use Vanguard\Http\Requests\Request;

Class Api
{
    public static function get_hourly_ranges()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM hourlyRanges");
    }

    public static function get_dayParts()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM dayParts");
    }

    public function get_all_region()
    {
        Utilities::switch_db('api')->select("SELECT * from regions");
    }

    public static function get_discountTypes()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM discount_types");
    }

    public static function get_agencies()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM agents");
    }

    public static function get_agent_user($user_id)
    {
        $data = Utilities::switch_db('reports')->select("SELECT id, firstname, lastname FROM users WHERE id = '$user_id'");

        return $data;
    }

    public static function get_discount_classes()
    {
        return Utilities::switch_db('reports')->select("SELECT * FROM discount_classes");
    }

    public static function get_brands($broadcaster_id){
        return Utilities::switch_db('reports')->select("SELECT id, name FROM brands where broadcaster_agency = '$broadcaster_id'");
    }

    public static function session_id(){
        $id = date('Y-m-d H:i:s').mt_rand(1000000000000,999999999999999);
        return $id;
    }

    /**
     * Discounts
     */

    public static function get_agency_discounts($type, $broadcaster_id)
    {
        return Utilities::switch_db('reports')->select("SELECT d.*, CONCAT(u.firstname,' ',u.lastname) as name FROM discounts as d
                                                          INNER JOIN agents as a ON d.discount_type_value = a.id
                                                          INNER JOIN users as u ON u.id = a.user_id
                                                          WHERE d.broadcaster = '$broadcaster_id' AND d.discount_type = '$type' AND d.status = '1'");
    }

    public static function get_brand_discounts($type, $broadcaster_id){
        return Utilities::switch_db('api')->select("SELECT d.*, b.name as name from discounts as d INNER JOIN brands as b ON b.id = d.discount_type_value
                                                        WHERE d.broadcaster = '$broadcaster_id' AND d.discount_type = '$type' AND d.status = '1'");
    }

    public static function get_time_discounts($type, $broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT d.*, h.time_range as hourly_range from discounts as d INNER JOIN hourlyRanges as h on h.id = d.discount_type_value
                                                        WHERE d.broadcaster = '$broadcaster_id' AND d.discount_type = '$type' AND d.status = '1'");
    }

    public static function get_dayparts_discount($type, $broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT d.*, dp.day_parts as day_part from discounts as d INNER JOIN dayParts as dp ON dp.id = d.discount_type_value
                                                        WHERE d.broadcaster = '$broadcaster_id' AND d.discount_type = '$type' AND d.status = '1'");
    }

    public static function getPriceAndSurchargeDiscount($type, $broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from discounts where broadcaster = '$broadcaster_id' and discount_type = '$type'");
    }


    public static function fetchCampaign($campaign_id)
    {
        $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaignDetails WHERE campaign_id = '$campaign_id'");

        return $campaign;

    }

    public static function brand($campaign_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from brands where id = (SELECT brand from campaignDetails where campaign_id = '$campaign_id' GROUP BY campaign_id)");
    }

    public static function fetchPayment($campaign_id, $broadcaster_id)
    {
        $payment = Utilities::switch_db('reports')->select("SELECT * FROM paymentDetails WHERE payment_id = (SELECT id from payments where campaign_id = '$campaign_id') and broadcaster = '$broadcaster_id'");

        return $payment;
    }

    public static function getMpoByType($type)
    {
        $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE status = '$type'");

        return $mpos;
    }

    public static function getChannelName($channel_id)
    {
//        dd($channel_id);
        $channel = Utilities::switch_db('reports')->select("SELECT * FROM campaignChannels WHERE id IN ($channel_id) ");

        return $channel;
    }

    public static function getCampaignFiles($campaign_id)
    {
        $files = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id'");

        return $files;
    }

    public static function getOutstandingFiles($campaign_id, $broadcaster)
    {
        $files = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' AND is_file_accepted <> 1 AND broadcaster_id = '$broadcaster'");

        return $files;
    }

    public static function countClients($agency_id)
    {
        $client = Utilities::switch_db('api')->select("SELECT * from walkIns where agency_id = '$agency_id'");
        return count($client);
    }

    public static function countCampaigns($agency_id)
    {
        $client_campaigns = Utilities::switch_db('api')->select("SELECT * from campaignDetails where agency = '$agency_id' GROUP BY campaign_id");
        return count($client_campaigns);
    }

    public static function countInvoices($agency_id)
    {
        $client_invoice = Utilities::switch_db('api')->select("SELECT * from invoiceDetails where agency_id = '$agency_id' GROUP BY invoice_id");
        return count($client_invoice);
    }

    public static function countBrands($agency_id)
    {
        $brands = Utilities::switch_db('api')->select("SELECT * from brands where broadcaster_agency = '$agency_id'");
        return count($brands);
    }

    public static function countApproved($agency_id)
    {
        $count_approval = Utilities::switch_db('api')->select("SELECT * from invoiceDetails where agency_id = '$agency_id' AND status = 1 GROUP BY invoice_id");
        return count($count_approval);
    }

    public static function countUnapproved($agency_id)
    {
        $count_unapproval = Utilities::switch_db('api')->select("SELECT * from invoiceDetails where agency_id = '$agency_id' AND status = 0 GROUP BY invoice_id");
        return count($count_unapproval);
    }

    public static function allInvoiceAdvertiserorAgency($agency_id)
    {
        $all_invoices = Utilities::switch_db('reports')->select("SELECT SUM(actual_amount_paid) as total, invoice_number, SUM(refunded_amount) as refunded, status, invoice_id FROM invoiceDetails WHERE  agency_id = '$agency_id' GROUP BY invoice_id ORDER BY time_created DESC LIMIT 5");
        $invoice_campaign_details = [];

        foreach ($all_invoices as $invoice) {

            $inv_cam = Utilities::switch_db('api')->select("SELECT * from invoices where id = '$invoice->invoice_id'");
            $campaign_id = $inv_cam[0]->campaign_id;
            $campaign = Utilities::switch_db('reports')->select("SELECT * FROM campaignDetails WHERE campaign_id = '$campaign_id'");
            $brand_id = $campaign[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");

            $invoice_campaign_details[] = [
                'invoice_number' => $invoice->invoice_number,
                'actual_amount_paid' => $invoice->total,
                'refunded_amount' => $invoice->refunded,
                'status' => $invoice->status,
                'campaign_brand' => $brand_name[0]->name,
                'campaign_name' => $campaign[0]->name
            ];
        }
        return $invoice_campaign_details;
    }

    public static function saveActivity($user_id, $description, $ip, $user_agent)
    {
        $store_activity = [
            'description' => $description,
            'user_id' => $user_id,
            'ip_address' => $ip,
            'user_agent' => $user_agent
        ];

        return \DB::table('user_activity')->insert($store_activity);
    }

    public static function countFiles($advertiser_id)
    {
        $files = Utilities::switch_db('api')->select("SELECT * FROM files where agency_id = '$advertiser_id'");
        return count($files);
    }

    public static function validateCampaign()
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

        return true;

    }

    public static function addFile($id)
    {

        ignore_user_abort(true);

        set_time_limit(0);

        $broadcaster = Session::get('broadcaster_id');

        $check_file = Utilities::switch_db('api')->select("SELECT * from files where file_code = '$id'");

        if($check_file[0]->airbox_status === 1){
            return response()->json(['file_code' => $id], 200);
        }

        $adslot_id = $check_file[0]->adslot;

        $campaign_id = $check_file[0]->campaign_id;

        $campsign_details = Utilities::switch_db('api')->select("SELECT * from campaignDetails where campaign_id = '$campaign_id' and broadcaster = '$broadcaster'");

        $adslot_day_details = Utilities::switch_db('api')->select("SELECT * FROM days where id = (SELECT `day` from rateCards where id = (SELECT rate_card from adslots where id = '$adslot_id'))");

        $adslot_day = $adslot_day_details[0]->day;

        $end_date = date('Y-m-d', strtotime($campsign_details[0]->stop_date));

        $start_date = date('Y-m-d', strtotime($campsign_details[0]->start_date));

        $url = decrypt($check_file[0]->file_url);

        $explode = explode('.', $url);
        $extension = end($explode);

        $destination_file = "/media/ridwan/RIDWAN/Files/".$check_file[0]->file_code.".".$extension;

        $ci = curl_init();

        $fp = fopen($destination_file, "x+"); // Destination location
        curl_setopt_array( $ci, array(
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 3600,
            CURLOPT_FILE => $fp
        ));
        $contents = curl_exec($ci); // Returns '1' if successful
        curl_close($ci);
        fclose($fp);

        $destination_xml = "/media/ridwan/RIDWAN/Playlists/".$check_file[0]->file_code.".xml";
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <PBPlaylist id="'.$check_file[0]->file_code.'"><ITEM id="'.$check_file[0]->file_code.'" type="video_clip" file="H:/Files/'.$check_file[0]->file_code.".".$extension.'" outp="'.$check_file[0]->time_picked.'" duration="'.$check_file[0]->time_picked.'" isdynamicmedia="true" title="Test"/></PBPlaylist>';

        file_put_contents($destination_xml, $xml);

        $update_file = Utilities::switch_db('api')->update("UPDATE files set airbox_status = 1 WHERE file_code = '$id'");

        if($update_file){
            return $id;
        }else{
            return response()->json(['error' => 'Error Occured'], 500);
        }

    }

    public static function approvedCampaignFiles($campaign_id, $broadcaster_id)
    {
        $allFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' and broadcaster_id = '$broadcaster_id'");

        $approvedFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' AND is_file_accepted = 1 and broadcaster_id = '$broadcaster_id'");

        return count($allFiles) === count($approvedFiles);
    }

    public static function checkFilesForUpdatingMpos($campaign_id, $broadcaster_id)
    {
        $allFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' and broadcaster_id = '$broadcaster_id'");
        $approvedFiles = Utilities::switch_db('reports')->select("SELECT * FROM files WHERE campaign_id = '$campaign_id' AND broadcaster_id = '$broadcaster_id' AND is_file_accepted = 1");

        return count($allFiles) - count($approvedFiles);
    }


}