<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Http\Request;
use Vanguard\Libraries\Api;
use Vanguard\Libraries\Utilities;

class MpoController extends Controller
{
    public function index()
    {
        // $mpos = json_decode(Api::get_broadcaster_mpo())->data;

        $mpos = Utilities::switch_db('api')->select("SELECT * FROM mpos");

        $mpo_data = [];

        foreach ($mpos as $mpo) {

            $campaign_details = Api::fetchCampaign($mpo->campaign_id);
            $payment_details = Api::fetchPayment($mpo->campaign_id);

            $mpo_data[] = [
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $campaign_details[0]->product,
                'amount' => $payment_details,
                'amount' => $payment_details[0]->amount,
                'name' => $campaign_details[0]->name,
                'time_created' => $campaign_details[0]->time_created,
            ];
        }

        return view('mpos.index', compact('mpo_data'));
    }

    public function pending_mpos()
    {
        // $pending_mpos = json_decode(Api::get_mpo_by_type(2))->data;

        $pending_mpos = Api::getMpoByType(2);

        $mpo_data = [];

        foreach ($pending_mpos as $mpo) {

            $campaign_details = Api::fetchCampaign($mpo->campaign_id);
            $payment_details = Api::fetchPayment($mpo->campaign_id);

            $mpo_data[] = [
                'id' => $mpo->id,
                'is_mpo_accepted' => $mpo->is_mpo_accepted,
                'product' => $campaign_details[0]->product,
                'brand' => $campaign_details[0]->brand,
                'campaign_name' => $campaign_details[0]->name,
                'channel' => Api::getChannelName($campaign_details[0]->channel)[0]->channel,
                'time_created' => $campaign_details[0]->time_created,
                'start_date' => $campaign_details[0]->start_date,
                'stop_date' => $campaign_details[0]->stop_date,
                'files' => Api::getCampaignFiles($mpo->campaign_id),
                'amount' => $payment_details[0]->amount
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
