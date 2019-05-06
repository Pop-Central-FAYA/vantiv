<?php

namespace Tests\Feature\Users;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\User;

class InviteUserEditTest extends TestCase
{
    public function test_an_authenticated_user_can_visit_the_invite_user_page()
    {
        $result = $this->get('/user/invite');
        $result->assertRedirect('/login');
    }

    public function test_non_admin_users_get_forbidden_when_visiting_the_edit_page()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);

        $user2 = factory(User::class)->create();
        $user2->companies()->attach(factory(Company::class)->create()->id);
        //check if the user can view the profile page
        $this->actingAs($user)
            ->get('/user/edit/'.$user2->id)
            ->assertSee('Forbidden!');
    }

    public function test_admin_users_can_actually_visit_the_edit_page_and_see_its_content()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->assignRole(factory(Role::class)->create()->id);
        $user->companies()->attach(factory(Company::class)->create()->id);

        $user2 = factory(User::class)->create();
        $user2->companies()->attach(factory(Company::class)->create()->id);
        //check if the user can view the profile page
        $this->actingAs($user)
            ->get('/user/edit/'.$user2->id)
            ->assertSee($user2->email);
    }
}
