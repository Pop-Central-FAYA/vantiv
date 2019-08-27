<?php

namespace Tests\Feature\Dsp\Client;
use Tests\TestCase;


use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 * @todo add permission support
 * @todo add support for only people with access rights to update a client model
 */

class UpdateClient extends TestCase 
{
    protected $route_name = 'client.update';

    protected function setupClient($user)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
       factory(\Vanguard\Models\ClientContact::class)->create([
            'client_id' => $client->id
        ]);
        return $client->refresh();
    }
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.client']);
        return $user;
    }

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $id]), $data);
    }
    public function test_attempting_to_update_non_existent_client_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $client_id = uniqid();
        $response = $this->getResponse($user, $client_id, [ '_token' => csrf_token()]);
        $response->assertStatus(404);
    }

    public function test_invalid_data_is_validated_on_update()
    {
        $contacts= [
            'first_name' => '',
            'last_name' => '',
            'email' => "dino",
            'phone_number' => "",
            'is_primary' => true,
        ];
        $client_data = [
            '_token' => csrf_token(),
            'name' => '',
            'brand' => '',
            'image_url' =>  'quae-quae-error-cum-qui-ducimus',
            'street_address' => '',
            'city' => '',
            'state' => '',
            'nationality' => '',
            'contacts'=> $contacts,
        ];

        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);
        
        $response = $this->getResponse($user, $client->id, $client_data);

        $response->assertStatus(422);
    }

    public function test_403_returned_if_attempting_to_update_client_that_user_does_not_have_rights_to_update()
    {
        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);

        $another_user = $this->setupAuthUser();
        $another_client = $this->setupClient($another_user);

        $response = $this->getResponse($user, $another_client->id, ['_token' => csrf_token()]);

        $response->assertStatus(403);
    }

    public function test_user_without_update_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $client = $this->setupClient($user);
        
        $response = $this->getResponse($user, $client->id, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }
    

      /**
     * @dataProvider validUpdateDataProvider
     */
    public function test_client_and_contact_fields_are_updated_if_value_sent_in_request($client_data)
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);

        $client_array = Arr::dot($client->get()->first()->toArray());
        $response = $this->getResponse($user, $client->id, $client_data);

        $response->assertStatus(200);

        
        $actual_data = Arr::dot($client->with('contacts')->get()->first()->toArray());
        $expected_data = array_merge($client_array, Arr::dot($client_data));
       
        Arr::forget($expected_data, ['updated_at', 'contacts.0.updated_at']);
        $this->assertArraySubset($expected_data, $actual_data);
    }

    public static function validUpdateDataProvider()
    {
        return array(
            array(array('name' => 'Vinna')),
            array(array('street_address' => '21 akin ogunlewe road')),
            array(array('city' => 'Lokoja')),
            array(array('state' => 'Kogi')),
            array(array('nationality' => 'Nigeria')),
            //update client contacts
            array(array('contacts' => array(array('first_name' => 'Malaye')))),
            array(array('contacts' => array(array('email' => 'dino@example.org')))),
            array(array('contacts' => array(array('last_name' => 'Dino')))),
            array(array('contacts' => array(array('phone_number' => '+2341111111111')))),
            //multiple fields to update
            array(array('name' => 'Vinna', 'contacts' => array(array('phone_number' => '+2341111111111')))),
        );
    }
   
}