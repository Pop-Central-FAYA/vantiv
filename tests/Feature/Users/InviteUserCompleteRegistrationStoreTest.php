<?php

namespace Tests\Feature\Users;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tests\Traits\PermissionsTrait;
use Vanguard\Models\Company;
use Vanguard\Services\User\InviteUser;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class InviteUserCompleteRegistrationStoreTest extends TestCase
{
    use PermissionsTrait;
    public function test_an_authenticated_user_can_visit_the_invite_user_page()
    {
        $result = $this->get('/user/invite');
        $result->assertRedirect('/login');
    }


    public function test_it_requires_first_name_when_completing_account()
    {
        $user = factory(User::class)->create();
        $this->ajaxPost('/user/complete-account/store/'.$user->id)
            ->assertJson(['message' => 'firstname is required']);
    }

    public function test_it_requires_lastname_name_when_completing_account()
    {
        $user = factory(User::class)->create([
            
        ]);
        $this->ajaxPost('/user/complete-account/store/'.$user->id, [
            'firstname' => 'Ridwan'
        ])
            ->assertJson(['message' => 'lastname is required']);
    }

    public function test_it_requires_password_when_completing_account()
    {
        $user = factory(User::class)->create();
        $this->ajaxPost('/user/complete-account/store/'.$user->id, [
            'firstname' => 'Ridwan',
            'lastname' => 'Busari'
        ])
            ->assertJson(['message' => 'password is required']);
    }

    public function test_it_requires_a_password_with_min_length_of_six_when_completing_account()
    {
        $user = factory(User::class)->create();
        $this->ajaxPost('/user/complete-account/store/'.$user->id, [
            'firstname' => 'Ridwan',
            'lastname' => 'Busari',
            'password' => 'hhdd'
        ])
            ->assertJson(['message' => 'The password must be at least 6 characters.']);
    }

    public function test_it_requires_re_password_with_min_length_of_six_when_completing_account()
    {
        $user = factory(User::class)->create();
        $this->ajaxPost('/user/complete-account/store/'.$user->id, [
            'firstname' => 'Ridwan',
            'lastname' => 'Busari',
            'password' => 'hhdd7z',
        ])
            ->assertJson(['message' => 're password is required']);
    }

    public function test_the_re_password_is_same_with_password()
    {
        $user = factory(User::class)->create();
        $this->ajaxPost('/user/complete-account/store/'.$user->id, [
            'firstname' => 'Ridwan',
            'lastname' => 'Busari',
            'password' => 'hhdd7z',
            're_password' => 'jkghfcf'
        ])
            ->assertJson(['message' => 'The re password and password must match.']);
    }

    public function test_it_update_user_after_account_completion_process_from_the_service()
    {
        //invite the user
        $company = factory(Company::class)->create();
        $email = 'test@test.com';
        $invite_user_service = new InviteUser($this->createDefaultRole()->id, $company->id, $email, 'web');
        $user = $invite_user_service->createUnconfirmedUser();

        //update the user
        $update_user_service = new UpdateUser($user->id,'Test', 'Test', '', '',
            '', 'password', '', UserStatus::ACTIVE);
        $update_user_service->updateUser();
        $update_user_service->updatePassword();
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'status' => UserStatus::ACTIVE,
            'firstname' => 'Test'
        ]);
    }

    public function test_it_can_process_the_update_from_the_form()
    {
        $user = factory(User::class)->create();
        $this->ajaxPost('/user/complete-account/store/'.$user->id, [
            'firstname' => 'Ridwan',
            'lastname' => 'Busari',
            'password' => 'hhdd7z',
            're_password' => 'hhdd7z'
        ])
            ->assertJson([
                'status' => 'success'
            ]);
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
