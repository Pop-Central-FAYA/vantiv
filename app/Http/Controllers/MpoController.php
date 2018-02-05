<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;

class MpoController extends Controller
{
    public function index()
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE broadcaster_id = '$broadcaster_id' OR agency_broadcaster = '$broadcaster_id'");

        $mpo_data = [];

        foreach ($mpos as $mpo) {

            $campaign_details = Api::fetchCampaign($mpo->campaign_id);
            $payment_details = Api::fetchPayment($mpo->campaign_id);

            if (count($campaign_details) === 0) {
                $product = 0;
                $name = 0;
                $time = 0;
            } else {
                $product = $campaign_details[0]->product;
                $name = $campaign_details[0]->name;
                $time = date('Y-m-d', strtotime($campaign_details[0]->time_created));
            }

            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            $mpo_data[] = [
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'amount' => $amount,
                'name' => $name,
                'time_created' => $time,
            ];
        }

        return view('mpos.index', compact('mpo_data'));
    }

    public function pending_mpos()
    {
        $broadcaster_id = \Session::get('broadcaster_id');

        $pending_mpos = $mpos = Utilities::switch_db('reports')->select("SELECT * FROM mpos WHERE is_mpo_accepted = 0 AND (broadcaster_id = '$broadcaster_id' OR agency_broadcaster = '$broadcaster_id')");

        $mpo_data = [];

        foreach ($pending_mpos as $mpo) {

            $campaign_details = Api::fetchCampaign($mpo->campaign_id);
            $payment_details = Api::fetchPayment($mpo->campaign_id);

            $brand_id = $campaign_details[0]->brand;
            $brand_name = Utilities::switch_db('api')->select("SELECT name from brands where id = '$brand_id'");

            if (count($campaign_details) === 0) {
                $product = 0;
                $brand = 0;
                $name = 0;
                $time = 0;
                $start_date = 0;
                $start_end = 0;
                $channel = 0;
            } else {
                $product = $campaign_details[0]->product;
                $brand = $campaign_details[0]->brand;
                $name = $campaign_details[0]->name;
                $time = date('Y-m-d', strtotime($campaign_details[0]->time_created));
                $start_date = $campaign_details[0]->start_date;
                $start_end = $campaign_details[0]->stop_date;
                $channel = Api::getChannelName($campaign_details[0]->channel)[0]->channel;
            }

            if (count($payment_details) === 0) {
                $amount = 0;
            } else {
                $amount = $payment_details[0]->amount;
            }

            if (Api::getCampaignFiles($mpo->campaign_id) === 0) {
                $files = 0;
            } else {
                $files = Api::getCampaignFiles($mpo->campaign_id);
            }

            $mpo_data[] = [
                'id' => $mpo->id,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $product,
                'brand' => $brand_name[0]->name,
                'campaign_name' => $name,
                'channel' => $channel,
                'time_created' => $time,
                'start_date' => $start_date,
                'stop_date' => $start_end,
                'files' => $files,
                'amount' => $amount
            ];
        }

        return view('mpos.pending-mpos', compact('mpo_data'));
    }

    public function update_file($is_file_accepted, $broadcaster_id, $file_code, $campaign_id)
    {
        if(request()->ajax()){

            $update_file = json_decode(Api::update_fileStatus($is_file_accepted, $broadcaster_id, $file_code, $campaign_id));

            return response()->json(['is_file_accepted' => $update_file]);

        } else {
            return;
        }
    }
}