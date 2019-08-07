<?php

namespace Tests\Feature\Dsp\AdVendor;

class CreateAdVendorTest extends AdVendorTestCase
{

    protected $route_name = 'ad-vendor.create';

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['create.ad_vendor']);
        return $user;
    }

    protected function getResponse($user, $data)
    {
        return $this->actingAs($user)->postJson(route($this->route_name), $data);
    }

    /**
     * @todo verify that the tiemstamp fields are saved as utc
     */
    public function test_ad_vendor_successfully_created()
    {
        $contact = [
            'first_name' => 'Adedotun',
            'last_name' => 'Oshiomole',
            'email' => 'aoshiomole@example.org',
            'phone_number' => '+2341111111111'
        ];
        $vendor_data = [
            'name' => 'AIT Broker',
            'street_address' => '21 akin ogunlewe road',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'contacts' => [$contact]
        ];

        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $vendor_data);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => $vendor_data,
        ]);
    }

    public function test_creation_of_ad_vendor_injects_user_variables()
    {
        $contact = [
            'first_name' => 'Adedotun',
            'last_name' => 'Oshiomole',
            'email' => 'aoshiomole@example.org',
            'phone_number' => '+2341111111111'
        ];
        $vendor_data = [
            'name' => 'AIT Broker',
            'street_address' => '21 akin ogunlewe road',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'contacts' => [$contact]
        ];

        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $vendor_data);

        $response->assertStatus(201);
        $data = $response->json()['data'];

        $vendor = \Vanguard\Models\AdVendor::findOrFail($data['id']);
        $this->assertEquals($data['created_by'], $user->id);
        $this->assertEquals(($data['company_id']), $user->companies->first()->id);

        $contact = $vendor->contacts[0];
        $this->assertEquals(($data['created_by']), $user->id);
        
    }

    /**
     * @todo fix validation tests for nested fields
     * @todo possibly get proper error messages
     */
    public function test_invalid_data_for_vendor_returns_error_messages()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, []);
        $response->assertStatus(422);

        $keys = ['name', 'street_address', 'city', 'state', 'country'];
        $response->assertJsonValidationErrors($keys);

        // $keys = ['contacts.*.first_name', 'contacts.*.last_name', 'contacts.*.email', 'contacts.*.phone_number'];
        // $response->assertJsonValidationErrors($keys);
    }
    
    public function test_unauthenticated_user_cannot_access_ad_vendor_creation_route()
    {
        $response = $this->postJson(route($this->route_name), []);
        $response->assertStatus(401);
    }

    public function test_user_without_create_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, []);
        $response->assertStatus(403);
    }

}