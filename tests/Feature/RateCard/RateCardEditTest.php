<?php

namespace Tests\Feature\RateCard;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\User;

class RateCardEditTest extends TestCase
{
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->get('/rate-card-management/edit/1');
        $result->assertRedirect('/login');
    }

    public function test_user_with_wrong_role_get_redirected()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $this->actingAs($user)
            ->get('/rate-card-management/edit/4')
            ->assertSeeText('Forbidden!');
    }

    public function test_it_throws_error_if_rate_card_is_not_found()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);
        $this->actingAs($user)
            ->get('/rate-card-management/edit/4')
            ->assertSessionHas('error', 'Could not fetch your rate card details');
    }

    public function test_it_has_the_base_checkbox_checked_if_the_rate_card_is_base()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);
        $rate_card = factory(Ratecard::class)->create([
            'company_id' => $user->companies->first()->id,
            'is_base' => true
        ]);
        $this->actingAs($user)
            ->get('/rate-card-management/edit/'.$rate_card->id)
            ->assertSee('checked');
    }

    public function test_it_has_the_base_checkbox_disabled_if_the_rate_card_is_base()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);
        $rate_card = factory(Ratecard::class)->create([
            'company_id' => $user->companies->first()->id,
            'is_base' => true
        ]);
        $this->actingAs($user)
            ->get('/rate-card-management/edit/'.$rate_card->id)
            ->assertSee('disabled');
    }

    public function createDefaultRole()
    {
        $role = factory(Role::class)->create([
            'name' => 'admin',
            'guard_name' => 'ssp'
        ]);
        $role->syncPermissions($this->permissionData());
        return $role;
    }

    public function permissionData()
    {
        factory(Permission::class)->create([
            'name' => 'view.rate_card',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'update.rate_card',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'create.inventory',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'update.inventory',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.inventory',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.campaign',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.profile',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.user',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.rate_card',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.discount',
            'guard_name' => 'ssp'
        ]);

        return Permission::where('guard_name', 'ssp')->get();
    }

}
