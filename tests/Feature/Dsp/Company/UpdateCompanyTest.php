<?php

namespace Tests\Feature\Dsp\Company;

use Tests\TestCase;

/**
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */
class UpdateCompanyTest extends TestCase
{
    protected $route_name = 'company.update';
    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['update.company']);
        return $user;
    }

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

    public function test_403_returned_if_attempting_to_update_compnay_that_user_does_not_have_rights_to()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $company = $this->setupCompany(uniqid());
        
        $another_user = $this->setupAuthUser();
        $another_company = $this->setupCompany(uniqid());

        $response = $this->getResponse($user, $another_company->id, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }


    public function test_user_without_update_permissions_cannot_access_company_update_route()
    {
        \Session::start();
        $user = $this->setupAuthUser();
        $parent_company_id = uniqid();
        $company = $this->setupCompany($parent_company_id);
        $response = $this->getResponse($user, $company->id, ['_token' => csrf_token()]);
        $response->assertStatus(403);
    }
}
