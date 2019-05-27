<?php

namespace Tests\Feature\Dashboard;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\Publisher;
use Vanguard\User;

class InventoryManagementTest extends TestCase
{
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->get('/inventory-management/dashboard');
        $result->assertRedirect('/login');
    }

    public function test_it_get_inventory_management_data()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);

        $publisher = factory(Publisher::class)->create([
                        'company_id' => $company_id = $user->companies->first()->id
                    ]);

        $response = $this->actingAs($user)
                        ->get('/inventory-management/dashboard');
        $response->assertSee($publisher->type);
    }

    public function test_a_user_with_multiple_company_is_able_to_select_company()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);
        $company_2 = factory(Company::class)->create();
        $user->companies()->attach($company_2->id);
        factory(Publisher::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        factory(Publisher::class)->create([
            'company_id' => $company_2->id
        ]);

        $response = $this->actingAs($user)
            ->get('/inventory-management/dashboard');
        $response->assertSee($company_2->name);
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
            'name' => 'view.campaign',
            'guard_name' => 'ssp'
        ]);

        factory(Permission::class)->create([
            'name' => 'view.inventory',
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
