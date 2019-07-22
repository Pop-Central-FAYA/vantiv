<?php

namespace Tests\Feature\Users;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\CompanyType;
use Vanguard\Services\User\AuthenticatableUser;
use Vanguard\User;
use Vanguard\Libraries\Enum\CompanyTypeName;

class UserFlowTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_view_a_login_form()
    {
        $response = $this->get('login');
        $response->assertSuccessful();
        $response->assertViewIs('auth.dsp.login');
    }
    
    public function test_authenticated_user_can_be_created()
    {
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $this->assertInstanceOf(User::class, $user);
    }

    public function test_user_can_login_to_vantage()
    {
       \Session::start();

        $faker = Factory::create();
        $user = $this->createUser($faker);
         $this->assertInstanceOf(User::class, $user);
         $authenticate = $this->login($user->email, "helloThisIsAuthenticatable");
         $authenticate->assertRedirect('/');
         $this->assertAuthenticatedAs($user);

    }


    public function test_user_can_log_out_of_vantage()
    {
       \Session::start();

        $faker = Factory::create();
        $user = $this->createUser($faker);
         $this->assertInstanceOf(User::class, $user);
         $authenticate = $this->login($user->email, "helloThisIsAuthenticatable");
        $this->assertAuthenticatedAs($user);
        $authenticate = $this->logout();
         $authenticate->assertRedirect('/login');
         

    }

    public function test_user_cannot_view_a_login_form_when_authenticated()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('login');
        $response->assertRedirect('/');
    }

    public function createUser($faker)
    {
        //create company type
        $company_type = factory(CompanyType::class)->create([
            'name' => CompanyTypeName::AGENCY
        ]);
        //create company
        $company = \factory(Company::class)->create([
            'company_type_id' => $company_type->id
        ]);

        $authenticatable_user_service = new AuthenticatableUser($faker->firstName, $faker->lastName, $faker->unique()->email,
            null, $faker->phoneNumber, 'helloThisIsAuthenticatable', null, $company->id);
        return $authenticatable_user_service->createAuthenticatableUser();
    }


    public function login($email, $password)
    {
        \Session::start();
        return $this->post(route('post.login'), [
                    '_token' => \Session::token(),
                    'email' => $email,
                    'password' => $password,
                ]);
    }
    public function logout()
    {
        return $this->get(route('auth.logout'));
    }
    
    public function test_user_cannot_login_with_incorrect_password()
    {       
        $faker = Factory::create();
        $user = $this->createUser($faker);
         $this->assertInstanceOf(User::class, $user);
         $authenticate = $this->login($user->email, "wrongpassword");
        $authenticate->assertRedirect('/login');
        $this->followRedirects($authenticate)
        ->assertSee('email and or password invalid');
        $this->assertGuest();
    }


   
}
