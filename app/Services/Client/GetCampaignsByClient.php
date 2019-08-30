<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\Campaign;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Models\Brand;
use Illuminate\Support\Arr;
use Vanguard\Services\Campaign\ReformatCampaignList;


class GetCampaignsByClient implements BaseServiceInterface
{
    protected $client_id;
 

    public function __construct($client_id)
    {
        $this->client_id = $client_id;
      
    }

    public function run()
    {
        $brand_list = Brand::where('client_id', '=', $this->client_id)->get();
        return $this->getCampaigns($brand_list);
    }


    /**
    *get campaigns list
     */
    public function getCampaigns($brand_list)
    {
        $brand_id = Arr::pluck($brand_list, 'id');
        $campaigns = Campaign::whereIn('brand_id', $brand_id)->get();
        $format_campaign = new ReformatCampaignList($campaigns);
        $formated_campaign =$format_campaign->run();
        return collect($formated_campaign);
    }



}
