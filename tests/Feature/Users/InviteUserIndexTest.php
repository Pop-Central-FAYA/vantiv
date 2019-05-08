<?php

namespace Tests\Feature\Users;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\User;

class InviteUserIndexTest extends TestCase
{
    public function test_an_authenticated_user_can_visit_the_invite_user_page()
    {
        $result = $this->get('/user/invite');
        $result->assertRedirect('/login');
    }

    public function test_authenticated_user_with_the_wrong_role_get_access_denied()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        //check if the user can view the profile page
        $this->actingAs($user)
            ->get('/user/invite')
            ->assertSee('Forbidden!');
    }

    public function test_authenticated_user_that_have_the_right_role_can_view_the_invite_user_page()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->assignRole(factory(Role::class)->create()->id);
        $user->companies()->attach(factory(Company::class)->create()->id);
        //check if the user can view the profile page
        $this->actingAs($user)
            ->get('/user/invite')
            ->assertSee('Invite User(s)');
    }

    public function test_a_user_with_multiple_companies_can_assign_company()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->assignRole(factory(Role::class)->create()->id);
        $user->companies()->attach(factory(Company::class, 3)->create());
        $this->actingAs($user)
            ->get('/user/invite')
            ->assertSee($user->companies->first()->id);
    }
}
