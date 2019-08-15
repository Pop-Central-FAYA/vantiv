<?php

namespace Tests\Feature\Dsp\Client;
use Tests\TestCase;
use Faker\Factory;

use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 * @todo add permission support
 * @todo add support for only people with access rights to update a client model
 * @todo need to add support to make sure that updating the name of a field does not violate the unique reqs
 */

class DeleteClient extends TestCase 
{
    protected $route_name = 'client.destroy';
   
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
        return $this->actingAs($user)->deleteJson(route($this->route_name, ['id' => $id]), $data);
    }
    public function test_attempting_to_delete_non_existent_client_returns_404()
    {
        $user = $this->setupUserWithPermissions();
        $client_id = uniqid();
        $response = $this->getResponse($user, $client_id, [ '_token' => csrf_token()]);
        $response->assertStatus(404);
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

    public function test_authentication_user_can_delete_client_with_route()
    {
        \Session::start();
        $user = $this->setupUserWithPermissions();
        $client = $this->setupClient($user);

        $response = $this->getResponse($user, $client->id, [ '_token' => csrf_token()]);
        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => "Client deleted successfully",
         ]);
    }

}