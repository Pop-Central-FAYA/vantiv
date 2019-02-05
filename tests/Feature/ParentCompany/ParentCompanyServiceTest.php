<?php

namespace Tests\Feature\ParentCompany;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\ParentCompany;
use Vanguard\Services\ParentCompany\CreateParentCompany;

class ParentCompanyServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_parent_company_can_be_created()
    {
        $faker = Factory::create();
        $parent_company_service = new CreateParentCompany($faker->unique()->company);
        $parent_company = $parent_company_service->createParentCompany();
        $this->assertEquals($parent_company->name, ParentCompany::find($parent_company->id)->name);
    }
}
