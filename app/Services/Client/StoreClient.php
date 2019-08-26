<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\Client;
use Vanguard\Services\BaseServiceInterface;
use Vanguard\Services\Client\StoreClientContact;
use Vanguard\Services\Brands\StoreBrand;
use DB;

class StoreClient implements BaseServiceInterface
{
    protected $client_details;
    protected $company_id;
    protected $user_id;

    public function __construct($client_details, $company_id, $user_id)
    {
        $this->client_details = $client_details;
        $this->company_id = $company_id;
        $this->user_id = $user_id;
    }

    public function run()
    {
        return DB::transaction(function () {
            $client = new Client();
            $client->name = $this->client_details['name'];
            $client->image_url =  $this->client_details['image_url'];
            $client->created_by =  $this->user_id;
            $client->company_id = $this->company_id;
            $client->street_address = $this->client_details['street_address'];
            $client->city = $this->client_details['city'];
            $client->state = $this->client_details['state'];
            $client->nationality = $this->client_details['nationality'];
            $client->save();
            $this->storeContact($client, $this->client_details['contact'][0]);

            $new_brand = new StoreBrand($this->client_details['brand_details'][0], $client->id , $this->user_id );
            $result =  $new_brand->run();
            return  $client;   

        });

             
    }



    public function storeContact($client, $client_contact_details)
    { 
       return $client->contacts()->create($client_contact_details);
    }



}
