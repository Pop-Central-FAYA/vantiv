<?php

namespace Tests\Unit\Models\RateCard;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vanguard\Models\Company;
use Vanguard\Models\Ratecard\Ratecard;

class CreateRateCardTest extends TestCase
{
    public function test_it_belongs_to_a_company()
    {
        $company = factory(Company::class)->create();

        $rate_card = factory(Ratecard::class)->create([
            'company_id' => $company->id
        ]);

        $this->assertInstanceOf(Company::class, $rate_card->company->first());
    }
}
