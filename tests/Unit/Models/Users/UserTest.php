<?php

namespace Tests\Unit\Models\Users;

use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\CompanyType;
use Vanguard\User;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_user_can_have_many_companies()
    {
        $user = factory(User::class)->create(
            ['id' => uniqid()]
        );
        $company_id = ['jhgfdcb', 'hjezrhd'];
        $user->companies()->attach($company_id);
        $this->assertDatabaseHas('company_user', [
            'user_id' => $user->id,
            'company_id' => $company_id[0]
        ]);
    }

    public function test_user_can_fetch_company_type()
    {
        //cerate a companytype
        $company_type = factory(CompanyType::class)->create();

        //create a user and a company
        $company = factory(Company::class)->create([
            'company_type_id' => $company_type->id
        ]);

        //create user
        $user = factory(User::class)->create([
            'id' => uniqid(),
            'country_id' => ''
        ]);

        //attach company to user
        $user->companies()->attach($company->id);

        $this->assertEquals($company_type->name, $user->company_type);
    }
}
