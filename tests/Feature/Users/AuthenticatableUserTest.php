<?php

namespace Tests\Feature\Users;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\CompanyType;
use Vanguard\Services\User\AuthenticatableUser;
use Vanguard\User;

class AuthenticatableUserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_authenticated_user_can_be_created()
    {
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_user_can_login_in()
    {
        \Session::start();

        $faker = Factory::create();
        $user = $this->createUser($faker);
        $authenticate = $this->login($user->email);
        $authenticate->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

    }

    public function createUser($faker)
    {
        //create company type
        $company_type = factory(CompanyType::class)->create();
        //create company
        $company = \factory(Company::class)->create([
            'company_type_id' => $company_type->id
        ]);

        $authenticatable_user_service = new AuthenticatableUser($faker->firstName, $faker->lastName, $faker->unique()->email,
            null, $faker->phoneNumber, 'helloThisIsAuthenticatable', null, $company->id);
        return $authenticatable_user_service->createAuthenticatableUser();
    }

    public function login($email)
    {
        \Session::start();
        return $this->post(route('post.login'), [
                    '_token' => \Session::token(),
                    'email' => $email,
                    'password' => 'helloThisIsAuthenticatable',
                ]);
    }

}
