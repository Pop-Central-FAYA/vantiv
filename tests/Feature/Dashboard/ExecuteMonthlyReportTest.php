<?php

namespace Tests\Feature\Dashboard;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\User;

class ExecuteMonthlyReportTest extends TestCase
{
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->json('GET','/campaign-management/reports');
        $result->assertRedirect('login');
    }

    public function test_it_required_a_media_type()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $response = $this->actingAs($user)
                        ->json('GET','/campaign-management/reports?media_type=');
        $response->assertJsonValidationErrors('media_type');
    }

    public function test_it_required_a_report_type()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $response = $this->actingAs($user)
            ->json('GET','/campaign-management/reports?report_type=');
        $response->assertJsonValidationErrors('media_type');
    }

    public function test_it_requires_year()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $response = $this->actingAs($user)
            ->json('GET','/campaign-management/reports?year=');
        $response->assertJsonValidationErrors('year');
    }

    public function test_it_can_filter_when_supplied_filter_request()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->json('GET','/campaign-management/reports?_token=whqE2ZXj3YYQEhlznV43Nltn5arwJzieDlQrIB3s&media_type=tv&report_type=spots_sold&year=2019')
            ->assertJson([
                'status' => 'success'
            ]);
    }

}
