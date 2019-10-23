<?php

namespace Tests\Feature\Dsp\User;
use Tests\TestCase;

use Illuminate\Support\Arr;

/**
 * @todo test the actual error messages (add human readable error messages)
 */

class ListClient extends TestCase 
{
    protected $route_name = 'users.list';

    protected function setupUser($company)
    {
        $user = factory(\Vanguard\User::class)->create();
        $user->companies()->attach([$company]);
        return $user;
    }

    protected function setupUserWithPermissions()
    {
        $user = $this->setupAuthUser(null, ['view.user']);
        return $user;
    }

    protected function getResponse($user)
    {
        return $this->actingAs($user)->getJson(route($this->route_name));
    }

    public function test_user_without_view_permissions_cannot_access_route()
    {
        $user = $this->setupAuthUser();
        $response = $this->getResponse($user);
        $response->assertStatus(403);
    }

   
    public function test_user_list_with_all_users_retrievable_by_user()
    {
        $user = $this->setupUserWithPermissions();
        $invited_user_one = $this->setupUser($user->companies->first()->id);
        $response = $this->getResponse($user);
        $response->assertStatus(200);
        $expected = Arr::sort([$invited_user_one->id, $user->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }
     

    public function test_user_list_is_always_limited_to_users_that_belongs_to_users_company()
    {
        $user = $this->setupUserWithPermissions();
        $invited_user_one = $this->setupUser($user->companies->first()->id);

        $user_two = $this->setupUserWithPermissions();
        $invited_user_one_for_user_two = $this->setupUser($user_two->companies->first()->id);

        $response = $this->getResponse($user);
        $response->assertStatus(200);
        $expected = Arr::sort([$invited_user_one->id, $user->id]);
        $actual = Arr::pluck($response->json()['data'], 'id');
        $actual = Arr::sort($actual);
        $this->assertEquals(\array_values($expected), \array_values($actual));
    }

}