<?php

namespace Tests\Feature\Company;

use Tests\TestCase;

/**
 * @todo test the actual error messages (add human readable error messages)
 * @todo add permission support
 * @todo add support for only people with access rights to update a model
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */
class UpdateCompanyTest extends TestCase
{
    protected $route_name = 'company.update';
//this is for the index route
    public function test_user_can_view_update_company_form()
    {
        \Session::start();
        $user = $this->setupAuthUser();
        $response = $this->get('/company');
        $response->assertSuccessful();
    }

    protected function getResponse($user, $company_id, $data)
    {
       
        return $this->actingAs($user)->patchJson(route($this->route_name, ['id' => $company_id]), $data);
    }

    public function test_authentication_user_can_update_company_with_route()
    {
        $company_id = uniqid();
        \Session::start();
        $data = [
            '_token' => csrf_token(),
            'address' => '21 akin ogunlewe road',
            'image_url' => "hhdhdhdhdh"
        ];
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user, $company_id, $data);
        $response->assertStatus(200);
    }

    public function test_invalid_data_is_validated_on_update()
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

}
