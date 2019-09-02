<?php

namespace Tests\Feature\Dsp\AdVendor;

use Vanguard\Models\AdVendor as AdVendorModel;
use Vanguard\Models\Publisher;

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

    /*************************
     * Permission Tests
     *************************/

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

        $vendor_data['publishers'] = [];
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

        $vendor = AdVendorModel::findOrFail($data['id']);
        $this->assertEquals($data['created_by'], $user->id);
        $this->assertEquals(($data['company_id']), $user->companies->first()->id);

        $contact = $vendor->contacts[0];
        $this->assertEquals(($data['created_by']), $user->id);
        
    }

    public function test_invalid_data_for_vendor_returns_error_messages()
    {
        $vendor_data = ['contacts' => [[]]];
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $vendor_data);
        $response->assertStatus(422);

        $keys = ['name', 'street_address', 'city', 'state', 'country',
                'contacts.0.first_name', 'contacts.0.last_name',
                'contacts.0.email', 'contacts.0.phone_number'];
        $response->assertJsonValidationErrors($keys);
    }

    public function test_ad_vendor_successfully_created_with_publisher_association()
    {
        $pub_one = factory(Publisher::class)->create();
        $pub_two = factory(Publisher::class)->create();
        
        $publishers = [
            ['id' => $pub_one->id, 'name' => $pub_one->name],
            ['id' => $pub_two->id, 'name' => $pub_two->name]
        ];
        $vendor_data = [
            'name' => 'AIT Broker', 'street_address' => '21 akin ogunlewe road',
            'city' => 'Lagos', 'state' => 'Lagos', 'country' => 'Nigeria',
            'contacts' => [[
                'first_name' => 'Adedotun', 'last_name' => 'Oshiomole',
                'email' => 'aoshiomole@example.org', 'phone_number' => '+2341111111111'
            ]], 
            'publishers' => $publishers
        ];

        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $vendor_data);

        $response->assertStatus(201);
        $response->assertJson(['data' => ['publishers' => $publishers]]);
    }

    public function test_empty_publisher_array_throws_validation_exception_on_create()
    {
        $vendor_data = ['publishers' => []];

        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $vendor_data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['publishers']);
    }

    public function test_nonexistent_publisher_throws_validation_exception_on_create()
    {
        $random_pub_id = uniqid();
        $pub_one = factory(Publisher::class)->create();

        $publishers = [
            ['id' => $pub_one->id, 'name' => $pub_one->name],
            ['id' => $random_pub_id, 'name' => 'random nonexistent pub']
        ];
        $vendor_data = ['publishers' => $publishers];

        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user, $vendor_data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['publishers.1.id']);
    }

    public function test_validation_error_is_thrown_if_ad_vendor_with_same_name_already_exists()
    {
        $user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($user);

        $vendor_data = [
            'name' => $vendor->name, 'street_address' => '21 akin ogunlewe road',
            'city' => 'Lagos', 'state' => 'Lagos', 'country' => 'Nigeria',
            'contacts' => [[
                'first_name' => 'Adedotun', 'last_name' => 'Oshiomole',
                'email' => 'aoshiomole@example.org', 'phone_number' => '+2341111111111'
            ]]
        ];

        $response = $this->getResponse($user, $vendor_data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_successful_creation_despite_existence_of_identical_vendor_name_with_another_company()
    {
        $first_user = $this->setupUserWithPermissions();
        $vendor = $this->setupAdVendor($first_user);
        
        $user = $this->setupUserWithPermissions();

        $vendor_data = [
            'name' => $vendor->name, 'street_address' => '21 akin ogunlewe road',
            'city' => 'Lagos', 'state' => 'Lagos', 'country' => 'Nigeria',
            'contacts' => [[
                'first_name' => 'Adedotun', 'last_name' => 'Oshiomole',
                'email' => 'aoshiomole@example.org', 'phone_number' => '+2341111111111'
            ]]
        ];

        $response = $this->getResponse($user, $vendor_data);
        $response->assertStatus(201);
    }
}