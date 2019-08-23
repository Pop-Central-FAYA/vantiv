<?php

namespace Tests\Feature\Dsp\Client;
use Tests\TestCase;


use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 */

class ListClient extends TestCase 
{
    protected $route_name = 'client.list';

    protected function setupClient($user, $name, $price)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'name'=> $name,
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
        $this->setupBrand($user->id, $client->id, $price);
        $this->setupBrand($user->id, $client->id, $price);
        return $client->refresh();
    }

    protected function setupBrand($user_id, $client_id, $price)
    {
        $brand = factory(\Vanguard\Models\Brand::class)->create([
            'created_by' => $user_id,
            'client_id' => $client_id
        ]);
        $this->setUpActiveCampaign($user_id, $brand->id, $price);
        $this->setUpActiveCampaign($user_id, $brand->id, $price);
        return $brand->refresh();
    }

    protected function setUpActiveCampaign($user_id,$brand_id, $price)
    {
        $campaign= factory(\Vanguard\Models\Campaign::class)->create([
            'brand_id' => $brand_id,
            "status" => "active",
            "budget" => $price
        ]);

       return $campaign->refresh();
    }

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.client']);
        return $user;
    }

    protected function getResponse($user, $query_params=[])
    {
        return $this->actingAs($user)->getJson(route($this->route_name, $query_params));
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(403);
    }
    public function test_empty_array_returned_if_no_client_exist_that_the_user_has_access_to()
    {
        $user = $this->setupUserWithPermissions();
        $response = $this->getResponse($user);
        $response->assertStatus(200)
                 ->assertExactJson(['data' => []]);
    }
    public function test_client_list_with_no_filter_params_returns_all_client_retrievable_by_user()
    {
        $user = $this->setupUserWithPermissions();
        $client_one = $this->setupClient($user, "ABC CAR", 3000);
        $client_two = $this->setupClient($user, "ABC CAR", 3000);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = Arr::sort([$client_one->id, $client_two->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }
    public function test_client_list_with_user_id_param_returns_relevant_list()
    {
        $user = $this->setupUserWithPermissions();
        $client_one = $this->setupClient($user, "ABC CAR", 3000);
        $client_two = $this->setupClient($user, "ABC CAR", 3000);
        //different company and user
        $different_user = $this->setupAuthUser($user->companies->first());
        $client_three = $this->setupClient($different_user, "ABC CAR", 3000);

        $query_params = ['created_by' => $different_user->id];
        $response = $this->getResponse($user, $query_params);

        $response->assertStatus(200);

        $expected = [$client_three->id];
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);

        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

    public function test_client_list_is_always_limited_to_client_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $client_one = $this->setupClient($user, "ABC CAR", 3000);
        $client_two = $this->setupClient($user, "ABC CAR", 3000);

        //different company and user
        $another_user = $this->setupAuthUser();
        $client_three = $this->setupClient($another_user, "ABC CAR", 3000);

        $response = $this->getResponse($user);
        $response->assertStatus(200);

        $expected = Arr::sort([$client_one->id, $client_two->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }
  
    public function test_client_list_values_are_always_accurate_and_correct()
    {
        $user = $this->setupUserWithPermissions();
        $client_one = $this->setupClient($user, "ABC CAR", 3000);
        $client_two = $this->setupClient($user, "ABC Motos", 2000);

        $response = $this->getResponse($user);
        $response->assertStatus(200);
        $expected_one =[
                "name" => "ABC CAR",
                "name" => "ABC Motos",
                "number_brands" => 2,
                "sum_active_campaign" => 4,
                "client_spendings" => 12000,
                "client_spendings" => 8000,
            ];
        $actual = $response->json()['data'];
        $response->assertJsonFragment($expected_one);
     
    }
}