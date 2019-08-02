<?php

namespace Vanguard\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Faker\Factory;
use Vanguard\Services\Client\StoreClient;
use Vanguard\Services\Client\StoreClientContact;
use Illuminate\Support\Facades\Auth;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;


class ClientController extends Controller
{
    use CompanyIdTrait;
    public function action()
    {
        $user = Auth::user();
        $faker = Factory::create();

        $data = (object)[
            'id' => $faker->word,
            'name' => $faker->word,
            'brand' => $faker->word,
            'image_url' => $faker->url,
            'status' => $faker->word,
            'street_address' => $faker->word,
            'city' => $faker->word,
            'state' => $faker->word,
            'nationality' => $faker->word,
            'client_contact'=> [
                'first_name' => $faker->word,
                'last_name' => $faker->word,
                'email' => $faker->word,
                'phone_number' => $faker->word,
                'is_primary' => true,
            ],
            'brand_details'=> (object)[
                'name' => $faker->word,
                'image_url' => $faker->url,
                'status' => "active",
            ]
        ];
     
         $new_client = new StoreClient($data, $this->companyId(), $user);
         $client = $new_client->run(); 



    

         return "GG";


    }
    
}
