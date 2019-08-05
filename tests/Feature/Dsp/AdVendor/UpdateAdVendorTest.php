<?php

namespace Tests\Feature\Dsp\AdVendor;

use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 * @todo add permission support
 * @todo add support for only people with access rights to update a model
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */
class UpdateAdVendorTest extends AdVendorTestCase
{
    protected $route_name = 'ad-vendor.update';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.ad_vendor']);
        return $user;
    }

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $id]), $data);
    }

    public function test_unauthenticated_user_cannot_access_ad_vendor_update_route()
    {
        $vendor_id = uniqid();
        $response = $this->patchJson(route($this->route_name, ['id' => $vendor_id]), []);
        $response->assertStatus(401);
    }

    public function test_attempting_to_update_non_existent_vendor_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $vendor_id = uniqid();
        $response = $this->getResponse($user, $vendor_id, []);
        $response->assertStatus(404);
    }

    /**
     * @dataProvider validUpdateDataProvider
     */
    public function test_ad_vendor_and_contact_fields_are_updated_if_value_sent_in_request($vendor_data)
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);
        $vendor_array = Arr::dot($vendor->with('contacts')->get()->first()->toArray());
        
        $response = $this->getResponse($user, $vendor->id, $vendor_data);

        $response->assertStatus(200);
        
        $actual_data = Arr::dot($vendor->with('contacts')->get()->first()->toArray());
        
        $expected_data = array_merge($vendor_array, Arr::dot($vendor_data));
        Arr::forget($expected_data, ['updated_at', 'contacts.0.updated_at']);

        $this->assertArraySubset($expected_data, $actual_data);
    }

    public static function validUpdateDataProvider()
    {
        return array(
            array(array('name' => 'AIT Broker')),
            array(array('street_address' => '21 akin ogunlewe road')),
            array(array('city' => 'Lagos')),
            array(array('state' => 'Lagos')),
            array(array('country' => 'Nigeria')),
            //update contacts
            array(array('contacts' => array(array('first_name' => 'Adedotun')))),
            array(array('contacts' => array(array('email' => 'aoshiomole@example.org')))),
            array(array('contacts' => array(array('last_name' => 'Oshiomole')))),
            array(array('contacts' => array(array('phone_number' => '+2341111111111')))),
            //multiple fields to update
            array(array('name' => 'AIT Broker', 'contacts' => array(array('phone_number' => '+2341111111111')))),
        );
    }

    public function test_invalid_data_is_validated_on_update()
    {
        $vendor_data = [
            'name' => '',
            'street_address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'contacts' => [
                'first_name' => '',
                'last_name' => '',
                'email' => 'aoshiomole',
                'phone_number' => ''
            ]
        ];

        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);
        
        $response = $this->getResponse($user, $vendor->id, $vendor_data);

        $response->assertStatus(422);
    }

    public function test_403_returned_if_attempting_to_update_vendor_that_user_does_not_have_rights_to()
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);

        $another_user = $this->setupAuthUser();
        $another_vendor = $this->setupAdVendor($another_user);

        $response = $this->getResponse($user, $another_vendor->id, []);

        $response->assertStatus(403);
    }

    public function test_user_without_update_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $vendor = $this->setupAdVendor($user);
        
        $response = $this->getResponse($user, $vendor->id, []);
        $response->assertStatus(403);
    }
}