<?php

namespace Tests\Feature\Dsp\Brand;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Support\Arr;


class UpdateBrand extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $route_name = 'brand.update';
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.client']);
        return $user;
    }
    
    protected function getData()
    {
        return [
            '_token' => csrf_token(),
            'name' => "Ayo NIG LMT",
            'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus',
        ];
    }
    protected function setupBrand($user_id)
    {
        $brand = factory(\Vanguard\Models\Brand::class)->create([
            'created_by' => $user_id
        ]);
        return $brand->refresh();
    }

    protected function setupClient($user)
    {
        $client = factory(\Vanguard\Models\Client::class)->create([
            'company_id' => $user->companies->first(),
            'created_by' => $user->id
        ]);
      $brand = factory(\Vanguard\Models\Brand::class)->create([
            'created_by' => $user->id,
            'client_id' => $client->id,
        ]);
        return $brand->refresh();
    }

    protected function getResponse($user, $id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $id]), $data);
    }
   

    public function test_invalid_brand_data_is_validated_on_update()
    {
        \Session::start();
        $data = [
            '_token' => csrf_token(),
            'client_id' => '',
            'name' => '',
            'image_url' => '',
        ];
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupBrand($user->id);
        $response = $this->getResponse($user, $brand->id, $data);
        $response->assertStatus(422);
    }
    /**
     * @dataProvider validUpdateDataProvider
     */
      public function test_band_fields_are_updated_if_value_sent_in_request($brand_data)
    {
       
        \Session::start();
        $brand_data['_token'] = csrf_token();
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupClient($user);

        $brand_array = Arr::dot($brand->get()->first()->toArray());
        $response = $this->getResponse($user, $brand->id, $brand_data);

        $response->assertStatus(200);
        $actual_data = Arr::dot($brand->get()->first()->toArray());
        $expected_data = array_merge($brand_array, Arr::dot($brand_data));
        Arr::forget($expected_data, ['updated_at', '_token']);
        $this->assertArraySubset($expected_data, $actual_data);

    }
    public static function validUpdateDataProvider()
    {
        return array(
            array(array('name' => 'My Brand',  'image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus')),
            array(array('name' => 'My Brand')),
            array(array('image_url' => 'https://www.turcotte.com/quae-quae-error-cum-qui-ducimus'))
        );
    }

    public function test_403_returned_if_attempting_to_update_brand_that_user_does_not_have_rights_to()
    {
        $user = $this->setupUserWithPermissions();
        $brand = $this->setupClient($user);

        $another_user = $this->setupAuthUser();
        $another_brand = $this->setupClient($another_user);
        $response = $this->getResponse($user, $another_brand->id, ['_token' => csrf_token(), 'client_id' => uniqid()]);

        $response->assertStatus(403);
    }
    public function test_attempting_to_update_non_existent_brand_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $brand_id = uniqid();
        $response = $this->getResponse($user, $brand_id, ['_token' => csrf_token(), 'client_id' => uniqid()]);
        $response->assertStatus(404);
    }
    public function test_403_returned_if_attempting_to_update_brand_without_update_permmision()
    {
        $user =  $this->setupAuthUser();
        $brand = $this->setupClient($user);
        $response = $this->getResponse($user, $brand->id, ['_token' => csrf_token(), 'client_id' => uniqid()]);

        $response->assertStatus(403);
    }
}
