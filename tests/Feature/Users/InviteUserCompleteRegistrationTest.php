<?php

namespace Tests\Feature\Users;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vanguard\User;

class InviteUserCompleteRegistrationTest extends TestCase
{
    public function test_an_authenticated_user_can_visit_the_invite_user_page()
    {
        $result = $this->get('/user/invite');
        $result->assertRedirect('/login');
    }

    public function test_user_with_expired_link_get_throws_the_right_status_code()
    {
        //create a user
        $user = factory(User::class)->create();
        $this->get(\URL::temporarySignedRoute('user.complete_registration', now()->subHours(1),
            ['id'=> $user->id]))
            ->assertStatus(403);
    }

    public function test_user_with_the_right_link_can_visit_the_complete_registration_page()
    {
        //create a user
        $user = factory(User::class)->create();
        $this->get(\URL::temporarySignedRoute('user.complete_registration', now()->addHour(1),
            ['id'=> $user->id]))
            ->assertSee($user->email);
    }
}
