<?php

namespace Tests\Unit\Models\ParentCompany;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vanguard\Models\Company;
use Vanguard\Models\ParentCompany;

class ParentCompanyTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_a_parent_company_can_have_companies()
    {
        $parent_company = factory(ParentCompany::class)->create();
        $companies = factory(Company::class)->create([
            'parent_company_id' => $parent_company->id
        ]);
        $this->assertInstanceOf(Company::class, $parent_company->companies->first());
    }

    public function test_parent_company_can_have_many_companies()
    {
        $parent_company = factory(ParentCompany::class)->create();

        $companies = factory(Company::class, 3)->create([
            'parent_company_id' => $parent_company->id
        ]);

        $this->assertEquals(count($companies), $parent_company->companies()->count());
    }
}
