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

    public function test_user_can_view_update_company_form()
    {
        $response = $this->get('/company');
        $response->assertSuccessful();
    }

    public function test_unauthenticated_user_cannot_access_company_update_route()
    {
        \Session::start();
        $response = $this->postJson(route('company.update'), [
            '_token' => csrf_token()
        ]);
        $response->assertStatus(401);

    }
}
