<?php

namespace Tests\Feature\Dsp\Company;

use Tests\TestCase;

/**
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */
class UpdateCompanyTest extends TestCase
{
    protected $route_name = 'company.update';
    protected function getResponse($user, $company_id, $data)
    {
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $company_id]), $data);
    }

    protected function setupCompany($parent_company_id)
    {
        $company = factory(\Vanguard\Models\Company::class)->create([
            'parent_company_id' => $parent_company_id
        ]);
        return $company->refresh();
    }

     public function test_attempting_to_update_non_existent_company_returns_404()
    {
        $company_id = uniqid();
        \Session::start();
        $data = [
            '_token' => csrf_token(),
            'address' => 'address',
            'image_url' => "addres"
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $company_id, $data);
        $response->assertStatus(404);
    }

    public function test_invalid_company_data_is_validated_on_update()
    {
        $company_id = uniqid();
        \Session::start();
        $data = [
            '_token' => csrf_token(),
            'address' => '',
            'image_url' => ""
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $company_id, $data);
        $response->assertStatus(422);
    }

    public function test_authentication_user_can_update_company_with_route()
    {
        \Session::start();
        $parent_company_id = uniqid();
        $company = $this->setupCompany($parent_company_id);
        $data = [
            '_token' => csrf_token(),
            'address' => '21 akin',
            'logo' => "https://laravel.com"
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $company->id, $data);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'logo' => "https://laravel.com"
         ]);
    }
}
