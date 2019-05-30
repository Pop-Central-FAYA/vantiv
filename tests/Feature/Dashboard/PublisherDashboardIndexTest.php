<?php

namespace Tests\Feature\Dashboard;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tests\Traits\PermissionsTrait;
use Vanguard\Models\Company;
use Vanguard\User;

class PublisherDashboardIndexTest extends TestCase
{
    use PermissionsTrait;
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->get('/broadcaster');
        $result->assertRedirect('/login');
    }

    public function test_a_user_with_role_of_scheduler_get_redirected_to_inventory_when_logged_in()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);
        $this->actingAs($user)
            ->get('/broadcaster')
            ->assertRedirect(route('broadcaster.inventory_management'));
    }

    public function test_a_user_with_role_of_admin_get_redirected_to_inventory_when_logged_in()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole($this->createDefaultRole()->id);
        $this->actingAs($user)
            ->get('/broadcaster')
            ->assertRedirect(route('broadcaster.inventory_management'));
    }

    public function createDefaultRole()
    {
        $role = factory(Role::class)->create([
            'name' => 'ssp.admin',
            'guard_name' => 'web'
        ]);
        $role->syncPermissions($this->permissionData());
        return $role;
    }

}
