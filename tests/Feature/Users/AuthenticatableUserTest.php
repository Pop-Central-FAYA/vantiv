<?php

namespace Tests\Feature\Users;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\CompanyType;
use Vanguard\Services\User\AuthenticatableUser;
use Vanguard\User;
use Vanguard\Libraries\Enum\CompanyTypeName;

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
        $authenticate = $this->login($user->email, 'helloThisIsAuthenticatable');
        $authenticate->assertRedirect(route('broadcaster.dashboard.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_torch_user_can_logout()
    {
        \Session::start();
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $authenticate = $this->login($user->email, 'helloThisIsAuthenticatable');
        $this->assertAuthenticatedAs($user);
        $authenticate = $this->logout();
        $authenticate->assertRedirect('/login');        
    }

    public function test_a_torch_user_with_wrong_credentials_get_proper_message()
    {
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $authenticate = $this->login($user->email, "wrongpassword");
        $authenticate->assertRedirect('/login');
        $this->followRedirects($authenticate)
        ->assertSee('email and or password invalid');
        $this->assertGuest();
    }

    public function test_an_authenticated_torch_user_cannot_view_the_login_page()
    {
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $response = $this->actingAs($user)->get('login');
        $response->assertRedirect('/');

    }

    public function test_non_torch_user_cannot_be_authenticated()
    {
        $password = 'testing';
        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);
        $authenticate = $this->login($user->email, $password);
        $authenticate->assertRedirect('/login');
        $this->followRedirects($authenticate)
        ->assertSee('email and or password invalid');
        $this->assertGuest();

    }

    public function createUser($faker)
    {
        //create company type
        $company_type = factory(CompanyType::class)->create([
            'name' => CompanyTypeName::BROADCASTER
        ]);
        //create company
        $company = \factory(Company::class)->create([
            'company_type_id' => $company_type->id
        ]);

        $authenticatable_user_service = new AuthenticatableUser($faker->firstName, $faker->lastName, $faker->unique()->email,
            null, $faker->phoneNumber, 'helloThisIsAuthenticatable', null, $company->id);
        return $authenticatable_user_service->createAuthenticatableUser();
    }

    private function logout()
    {
        return $this->get(route('auth.logout'));
    }

    public function login($email, $password)
    {
        \Session::start();
        return $this->post('/login', [
                    '_token' => csrf_token(),
                    'email' => $email,
                    'password' => $password,
                ]);
    }

}
