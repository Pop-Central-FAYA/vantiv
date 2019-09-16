<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\Campaign;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Models\Client;


class GetClientDetails implements BaseServiceInterface
{
    protected $validated;
 

    public function __construct($validated)
    {
        $this->validated = $validated;
      
    }

    public function run()
    {
        $client_list = Client::with('contacts', 'brands')->orderBy('created_at','desc')->filter($this->validated)->get();
        return $this->getClientDetails($client_list);
    }


    /**
     * @todo get list of client
     * @todo get summation of total spendings
     * @todo get date created
     * @todo get active campaigns
     */
    public function getClientDetails($client_list)
    {
        $item_clients = [];
        foreach ($client_list as $client) 
        {
            $brands = 0;
            $sum_active_campaign = 0;
            $client_spendings = 0;
            foreach ($client->brands as $brand) 
            {
                $brands++;
                $sum_active_campaign += $this->getActiveCampaign($brand->id);
                $client_spendings += $this->getBrandSpendings($brand->id);
            }
            $item_client = array(
                'id' => $client->id,
                'image_url' => $client->image_url,
                'name'=> $client->name, 
                'city'=> $client->city, 
                'state'=> $client->state, 
                'street_address'=> $client->street_address, 
                'number_brands' => $brands, 
                'sum_active_campaign' => $sum_active_campaign,
                'client_spendings' => $client_spendings,  
                'created_at' => $client->created_at, 
                'contacts' => $client->contacts, 
            );
            array_push($item_clients, $item_client);
        }
        return collect($item_clients);
        
    }

    public function getActiveCampaign($brand_id)
    {
        $campaigns = Campaign::where([['brand_id', '=', $brand_id], ['status', '=', 'active']])->get()->count();
        return $campaigns;
    }
    
    public function getBrandSpendings($brand_id)
    {
        $brand_spendings = 0;
        $campaigns = Campaign::where('brand_id', '=', $brand_id)->get();
        foreach ($campaigns as $campaign) 
        {
              $brand_spendings += $campaign->budget;
        }
        return $brand_spendings;
    }
}
