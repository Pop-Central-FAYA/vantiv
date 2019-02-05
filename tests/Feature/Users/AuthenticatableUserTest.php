<?php

namespace Tests\Feature\Users;

use Faker\Factory;
use Tests\TestCase;
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
        $companies_id = [
            'hjfgcsd64jh', 'hgdvg6474'
        ];
        $authenticatable_user_service = new AuthenticatableUser($faker->firstName, $faker->lastName, $faker->unique()->email,
            null, $faker->phoneNumber, 'helloThisIsAuthenticatable', null, $companies_id);
        $user = $authenticatable_user_service->createAuthenticatableUser();

        $this->assertInstanceOf(User::class, $user);
    }
}
