<?php

namespace Vanguard\Http\Controllers\Ssp;

use Session;
use Vanguard\Libraries\Utilities;
use Vanguard\Http\Controllers\BrandsController as MainBrandController;
use Illuminate\Support\Facades\DB;

class BrandsController extends MainBrandController
{
    public function getLayout()
    {
        $this->brand_view = 'broadcaster_module.brands.index';
    }

    public function getBrandCampaigns()
    {
        return DB::select("SELECT * from campaignDetails 
                                WHERE brand = '$this->brand_id' 
                                AND walkins_id = '$this->client_id'");
    }

    public function getBrandTotalSpent()
    {
        return DB::select("SELECT SUM(total) AS total 
                            FROM payments 
                            WHERE campaign_id 
                            IN (SELECT campaign_id 
                                    FROM campaignDetails 
                                    WHERE brand = '$this->brand_id' 
                                    AND walkins_id = '$this->client_id'
                                )
                            ");
    }

    public function getBrandDetails($id, $client_id)
    {
        $broadcaster_id = Session::get('broadcaster_id');
        $campaigns = [];
        //get client details
        $client = Utilities::switch_db('reports')->select("SELECT * FROM walkIns WHERE id = '$client_id'");
        $user_id = $client[0]->user_id;
        $user_details = Utilities::switch_db('api')->select("SELECT * FROM users where id = '$user_id'");

        $this_brand = Utilities::switch_db('api')->select("SELECT * FROM brands where id = '$id'");
        $all_campaigns = Utilities::switch_db('api')->select("SELECT c_d.campaign_id, c_d.status, c_d.name, b.name as brand_name, p.total, c_d.product, c_d.time_created, c_d.start_date, 
                                                                c_d.stop_date, c_d.adslots, c.campaign_reference FROM campaignDetails as c_d
                                                                INNER JOIN campaigns as c ON c.id = c_d.campaign_id 
                                                                INNER JOIN brands as b ON c_d.brand = b.id
                                                                INNER JOIN payments as p ON p.campaign_id = c_d.campaign_id 
                                                                where c_d.brand = '$id' and b.id = '$id' and c_d.broadcaster = '$broadcaster_id' and c_d.walkins_id = '$client_id'");

        foreach ($all_campaigns as $campaign)
        {
            $mpo = Utilities::switch_db('api')->select("SELECT * FROM mpoDetails where mpo_id = (SELECT id from mpos where campaign_id = '$campaign->campaign_id') LIMIT 1");
            $campaigns[] = [
                'id' => $campaign->campaign_reference,
                'camp_id' => $campaign->campaign_id,
                'name' => $campaign->name,
                'brand' => $campaign->brand_name,
                'product' => $campaign->product,
                'date_created' => date('Y/m/d',strtotime($campaign->time_created)),
                'start_date' => date('Y-m-d', strtotime($campaign->start_date)),
                'end_date' => date('Y-m-d', strtotime($campaign->stop_date)),
                'adslots' => $campaign->adslots,
                'budget' => number_format($campaign->total, 2),
                'compliance' => '0%',
                'status' => ucfirst($campaign->status),
                'mpo_status' => $mpo[0]->is_mpo_accepted
            ];
        }

        return view('broadcaster_module.brands.details', compact('this_brand', 'campaigns', 'user_details', 'client_id', 'client'));
    }
}
