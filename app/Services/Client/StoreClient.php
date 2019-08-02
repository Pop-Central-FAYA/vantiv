<?php

namespace Vanguard\Services\Client;

use Vanguard\Models\Client;
use Vanguard\Services\IService;
use Vanguard\Services\Client\StoreClientContact;
use Vanguard\Services\Brands\StoreBrand;
use Illuminate\Support\Facades\Validator;

class StoreClient implements IService
{
    protected $client_details;
    protected $company_id;
    protected $user;

    public function __construct($client_details, $company_id, $user)
    {
        $this->client_details = $client_details;
        $this->company_id = $company_id;
        $this->user = $user;
    }

    public function run()
    {
       $validate = $this->validateCredentials($this->client_details);
        if(!$validate->fails()){
            $client = new Client();
            $client->name = $this->client_details->name;
            $client->brand = $this->client_details->brand;
            $client->image_url =  $this->client_details->image_url;
            $client->status = $this->client_details->status;
            $client->created_by =  $this->user->id;
            $client->company_id = $this->company_id;
            $client->street_address = $this->client_details->street_address;
            $client->city = $this->client_details->city;
            $client->state = $this->client_details->state;
            $client->nationality = $this->client_details->nationality;
            $client->save();

            $new_client_contact = new StoreClientContact($client,  $this->client_details->client_contact );
            $result =  $new_client_contact->run();

            $new_brand = new StoreBrand($this->client_details->brand_details , $client->id , $this->user );
            $result =  $new_brand->run();
            return true;

        }else{
            return $validate->messages();
        }
    }

    public function validateCredentials($request){
        $validator = Validator::make(
            $request->all(),
            array(
                'name' => 'required',
                'image_url' => 'required',
                'street_address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'nationality' => 'required',
                'client_contact.first_name' => 'required',
                'client_contact.last_name' => 'required',
                'client_contact.email' => 'required',
                'client_contact.phone_number' => 'required',
                'client_contact.is_primary' => 'required',
                'brand_details.name' => 'required',
                'brand_details.image_url' => 'required',
                'brand_details.status' => 'required',
            )
        );
         
        return $validator;
       
        
    }
}
