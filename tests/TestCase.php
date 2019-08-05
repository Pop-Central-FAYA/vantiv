<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    public function setUp()
    {
        // first include all the normal setUp operations
        parent::setUp();

        // now re-register all the roles and permissions
        $this->seedPermissions();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();

        //     //this function will let all unhandled exceptions bubble up
        //     $this->withoutExceptionHandling();
    }

    protected function ajaxPost($uri, array $data = [])
    {
        \Session::start();
        return $this->post($uri, array_merge($data, ['_token' => \Session::token()]));
    }

    protected function setupAuthUser($company=null, $permissions=[]) 
    {   
        if ($company == null) {
            $parent_company = factory(\Vanguard\Models\ParentCompany::class)->create();
            $company = factory(\Vanguard\Models\Company::class)->create([
                'parent_company_id' => $parent_company->id
            ]);
        }
        $user = factory(\Vanguard\User::class)->create();
        $user->companies()->attach([$company->id]);

        if (count($permissions) > 0) {
            $user->givePermissionTo($permissions);

        }
        return $user;
    }

    protected function seedPermissions()
    {
        $this->seed('AgencyRoleSeeder');
    }
    
}
