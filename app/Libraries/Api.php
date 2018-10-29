<?php

namespace Vanguard\Libraries;

use Illuminate\Support\Facades\Session;
use Vanguard\Models\SelectedAdslot;

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
        $discount_types = [];
        $types =  Utilities::switch_db('reports')->select("SELECT * FROM discount_types");
        foreach ($types as $type){
            $key = $type->value;
            $discount_types[$key] = $type->id;
        }

        return $discount_types;

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
        $discount_classes = [];
        $classes =  Utilities::switch_db('reports')->select("SELECT * FROM discount_classes");
        foreach ($classes as $class){
            $key = $class->class;
            $discount_classes[$key] = $class->id;
        }

        return $discount_classes;
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

    public static function getPriceDiscount($type, $broadcaster_id)
    {
        return Utilities::switch_db('api')->select("SELECT * from discounts where broadcaster = '$broadcaster_id' and discount_type = '$type' AND status = '1'");
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
        $files = SelectedAdslot::where('campaign_id', $campaign_id)->get();

        return $files;
    }

    public static function getOutstandingFiles($campaign_id, $broadcaster)
    {
        return SelectedAdslot::where('campaign_id', $campaign_id)
                    ->where('broadcaster_id', $broadcaster)
                    ->whereIn('status', array('pending', 'rejected'))
                    ->get();

    }

    public static function getRejectedFiles($campaign_id, $broadcaster)
    {
        return SelectedAdslot::where([['campaign_id', $campaign_id], ['status', 'rejected'], ['broadcaster_id', $broadcaster]])->get();
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

    public static function countFiles($advertiser_id)
    {
        $files = SelectedAdslot::where('agency_id', $advertiser_id)->get();
        return count($files);
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

    public static function addFile($id)
    {

        ignore_user_abort(true);

        set_time_limit(0);

        $broadcaster = Session::get('broadcaster_id');

        $check_file = SelectedAdslot::where('file_code', $id)->first();

        if($check_file->airbox_status === 1){
            return response()->json(['file_code' => $id], 200);
        }

        $adslot_id = $check_file->adslot;

        $campaign_id = $check_file->campaign_id;

        $campsign_details = Utilities::switch_db('api')->select("SELECT * from campaignDetails where campaign_id = '$campaign_id' and broadcaster = '$broadcaster'");

        $adslot_day_details = Utilities::switch_db('api')->select("SELECT * FROM days where id = (SELECT `day` from rateCards where id = (SELECT rate_card from adslots where id = '$adslot_id'))");

        $adslot_day = $adslot_day_details[0]->day;

        $end_date = date('Y-m-d', strtotime($campsign_details[0]->stop_date));

        $start_date = date('Y-m-d', strtotime($campsign_details[0]->start_date));

        $url = $check_file->file_url;

        try {
            $explode = explode('.', $url);
            $extension = end($explode);

            $destination_file = "/media/ridwan/RIDWAN/Files/".$check_file->file_code.".".$extension;

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

            $destination_xml = "/media/ridwan/RIDWAN/Playlists/".$check_file->file_code.".xml";
            $xml = '<?xml version="1.0" encoding="utf-8"?>
                <PBPlaylist id="'.$check_file->file_code.'"><ITEM id="'.$check_file->file_code.'" 
                type="video_clip" file="H:/Files/'.$check_file->file_code.".".$extension.'" outp="'.$check_file->time_picked.'"
                 duration="'.$check_file->time_picked.'" isdynamicmedia="true" title="Test"/></PBPlaylist>';

            file_put_contents($destination_xml, $xml);

            $check_file->airbox_status = 1;
            $check_file->save();
            return $id;
        }catch(\Exception $e){
            return response()->json(['error' => 'Error Occured'], 500);
        }

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