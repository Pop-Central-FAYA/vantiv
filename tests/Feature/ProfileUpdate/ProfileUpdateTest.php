<?php

namespace Tests\Feature\ProfileUpdate;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Services\User\CreateUser;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Services\User\UserDetails;

class ProfileUpdateTest extends TestCase
{
    public function test_if_user_cannot_visit_the_profile_route_when_not_authenticated()
    {
        $result = $this->get('/user/profile');
        $result->assertRedirect('/login');
    }

    public function test_if_an_authenticated_user_can_view_the_profile_page()
    {
        //create a user
        $faker = Factory::create();
        $user = $this->createUser($faker);
        //check if the user can view the profile page
        $this->actingAs($user)
             ->get('/user/profile')
             ->assertSee('Profile Management');

    }

    public function test_user_can_update_information()
    {
        $faker = Factory::create();
        //create user
        $user = $this->createUser($faker);
        //log user in
        $this->actingAs($user);
        //update the user
        $update_user_service = new UpdateUser($user->id, $faker->name, $faker->name, $faker->phoneNumber,
            null, null, null, null, null);
        $update_user = $update_user_service->updateUser();
        //get the user details
        $user_details = $this->getUserDetails($user->id);
        //check if the user information was updated
        $this->assertEquals($user_details->firstname, $update_user->firstname);

    }

    public function test_user_avatar_was_updated()
    {
        $faker = Factory::create();
        //create user
        $created_user = $this->createUser($faker);
        //authenticate user
        $this->actingAs($created_user);
        //updating the url of the image
        $update_user_service = new UpdateUser($created_user->id, null, null,
            null, null, $faker->url, null, null, null);
        $update_avatar = $update_user_service->updateAvatar();
        $user_details = $this->getUserDetails($created_user->id);
        $this->assertEquals($update_avatar->avatar, $user_details->avatar);

    }

    public function test_user_can_update_password()
    {
        $new_password = 'this_is_new';
        $faker = Factory::create();
        $created_user = $this->createUser($faker);
        $update_user_service = new UpdateUser($created_user->id, null, null, null, null,
            null, $new_password, null, null);
        $update_user_service->updatePassword();
        $user_details = $this->getUserDetails($created_user->id);
        $this->assertTrue(\Hash::check($new_password, $user_details->password));
    }

    public function createUser($faker)
    {
        $create_user_service = new CreateUser($faker->name, $faker->name, $faker->email, null,
            $faker->phoneNumber, 'testUserPassword', null);
        $user = $create_user_service->createUser();
        $company = \factory(Company::class)->create();
        $user->companies()->sync($company->id);
        return $user;
    }

    public function getUserDetails($user_id)
    {
        $user_details_service = new UserDetails($user_id);
        return $user_details_service->getUserDetails();
    }
}
