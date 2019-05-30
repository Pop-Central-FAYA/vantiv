<?php

namespace Tests\Feature\Users;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Services\User\InviteUser;
use Vanguard\User;

class InviteUserStoreTest extends TestCase
{
    public function test_an_authenticated_user_can_visit_the_invite_user_page()
    {
        $result = $this->get('/user/invite');
        $result->assertRedirect('/login');
    }

    public function test_it_has_a_valid_email_when_inviting_a_user()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->assignRole($this->createDefaultRole()->id);
        $user->companies()->attach(factory(Company::class)->create()->id);
        //check if the user can view the profile page

        $this->actingAs($user)
            ->ajaxPost('/user/invite/store', ['email' => ['jfshadbwe'], 'roles' => ['hbnhb']])
            ->assertJson(['message' => 'jfshadbwe is not a valid email']);
    }

    public function test_it_validate_role_to_be_required()
    {
        //create a user
        $user = factory(User::class)->create();
        $user->assignRole($this->createDefaultRole()->id);
        $user->companies()->attach(factory(Company::class)->create()->id);
        //check if the user can view the profile page

        $this->actingAs($user)
            ->ajaxPost('/user/invite/store', ['email' => ['test@test.com'], 'roles' => []])
            ->assertJson(['message' => 'roles is required']);
    }

    public function test_it_can_process_invite_user()
    {
        //invite the user
        $company = factory(Company::class)->create();
        $email = 'test@test.com';
        $invite_user_service = new InviteUser($this->createDefaultRole()->id, $company->id, $email, 'web');
        $invite_user_service->createUnconfirmedUser();

        $this->assertDatabaseHas('users', [
            'email' => $email
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
