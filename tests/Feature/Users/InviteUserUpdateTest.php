<?php

namespace Tests\Feature\Users;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Services\User\UpdateUserService;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class InviteUserUpdateTest extends TestCase
{
    public function test_an_authenticated_user_can_visit_the_invite_user_page()
    {
        $result = $this->get('/user/invite');
        $result->assertRedirect('/login');
    }

    public function test_it_requires_role_while_updating_user()
    {
        $user = factory(User::class)->create();
        $user->assignRole($this->createDefaultRole()->id);
        $user->companies()->attach(factory(Company::class)->create()->id);

        $user2 = factory(User::class)->create();
        $user2->companies()->attach(factory(Company::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/user/update/'.$user2->id)
            ->assertJson(['message' => 'roles is required']);
    }

    public function test_it_update_user_from_the_service()
    {
        $role = $this->createDefaultRole();

        $company = factory(Company::class)->create([
            'name' => 'Simple Analytics'
        ]);

        $user2 = factory(User::class)->create();
        $user2->assignRole($role->id);
        $user2->companies()->attach(factory(Company::class)->create()->id);

        $update_user_service = new UpdateUserService($role->id, $company->id, $user2->id, 'web');
        $update_user = $update_user_service->updateUser();
        $this->assertEquals($role->name, $update_user->getRoleNames()->first());
        $this->assertEquals($company->name, $update_user->companies->first()->name);
    }

    public function test_it_can_update_status()
    {
        $user = factory(User::class)->create();
        $user->assignRole($this->createDefaultRole()->id);
        $user->companies()->attach(factory(Company::class)->create()->id);

        $user2 = factory(User::class)->create([
            'status' => UserStatus::INACTIVE
        ]);
        $user2->companies()->attach(factory(Company::class)->create()->id);
        $this->actingAs($user)
            ->get('/user/status/update?user_id='.$user2->id.'&status='.UserStatus::ACTIVE)
            ->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'status' => UserStatus::ACTIVE
        ]);
    }

    public function createDefaultRole()
    {
        $role = factory(Role::class)->create([
            'name' => 'ssp.admin',
            'guard_name' => 'web'
        ]);
        $role->syncPermissions(factory(Permission::class)->create([
            'name' => 'update.user',
            'guard_name' => 'web'
        ]));
        return $role;
    }

}
