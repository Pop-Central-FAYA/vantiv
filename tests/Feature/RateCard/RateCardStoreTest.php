<?php

namespace Tests\Feature\RateCard;

use Faker\Factory;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\Services\RateCard\StoreBaseRateCard;
use Vanguard\User;

class RateCardStoreTest extends TestCase
{
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->ajaxPost('/rate-card-management/store');
        $result->assertRedirect('/login');
    }

    public function test_user_with_wrong_role_get_redirected()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store')
            ->assertSeeText('Forbidden!');
    }

    public function test_it_requires_a_name()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store')
            ->assertSessionHasErrors('title');
    }

    public function test_name_is_unique_for_each_publisher()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $rate_card = \factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store',[
                'company' => $company_id,
                'title' => $rate_card->title
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_it_requires_price_and_must_be_array()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store')
            ->assertSessionHasErrors('price');
    }

    public function test_it_requires_duration_and_must_be_array()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store')
            ->assertSessionHasErrors('duration');
    }

    public function test_it_takes_is_base_to_be_boolean()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store', [
                'is_base' => 'hjdfk'
            ])
            ->assertSessionHasErrors('is_base');
    }

    public function test_it_store_ratecard_through_the_service()
    {
        $faker = Factory::create();
        $company = \factory(Company::class)->create();
        $store_rate_card_service = new StoreBaseRateCard($this->rateCardData($company->id, $faker->name, ''));
        $store_rate_card_service->run();
        $this->assertDatabaseHas('rate_cards', [
            'company_id' => $company->id
        ]);
    }

    public function test_it_can_store_ratecard_from_form_request()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store', [
                'title' => 'Test',
                'company' => $user->companies->first()->id,
                'duration' => [
                    15
                ],
                'price' => [
                    20000
                ]
            ])
            ->assertSessionHas('success', 'Rate Card created successfully');
    }

    public function test_it_can_store_ratecard_from_form_request_when_base_is_checked()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/store', [
                'title' => 'Test',
                'company' => $user->companies->first()->id,
                'is_base' => '1',
                'duration' => [
                    15
                ],
                'price' => [
                    20000
                ]
            ])
            ->assertSessionHas('success', 'Rate Card created successfully');
    }

    public function test_it_create_stores_rate_card_duration_and_price_when_base_is_not_included()
    {
        $faker = Factory::create();
        $company = \factory(Company::class)->create();
        $store_rate_card_service = new StoreBaseRateCard($this->rateCardData($company->id, $faker->name, ''));
        $store_rate_card = $store_rate_card_service->run();
        $this->assertDatabaseHas('rate_card_durations', [
            'rate_card_id' => $store_rate_card['rate_card']->id
        ]);
    }

    public function test_it_create_stores_rate_card_duration_and_price_when_base_included()
    {
        $faker = Factory::create();
        $company = \factory(Company::class)->create();
        $store_rate_card_service = new StoreBaseRateCard($this->rateCardData($company->id, $faker->name, true));
        $store_rate_card = $store_rate_card_service->run();
        $this->assertDatabaseHas('rate_card_durations', [
            'rate_card_id' => $store_rate_card['rate_card']->id
        ]);
    }

    public function test_it_create_stores_rate_card_duration_and_also_toggles_the_base_if_it_exists()
    {
        $faker = Factory::create();
        $company = \factory(Company::class)->create();
        \factory(Ratecard::class)->create([
            'company_id' => $company->id,
            'is_base' => true
        ]);
        $store_rate_card_service = new StoreBaseRateCard($this->rateCardData($company->id, $faker->name, true));
        $store_rate_card = $store_rate_card_service->run();
        $this->assertTrue($store_rate_card['rate_card']->is_base);
    }

    public function rateCardData($company_id, $name, $is_base)
    {
        return [
            'company_id' => $company_id,
            'duration' => [15],
            'price' => [1000],
            'name' => $name,
            'is_base' => $is_base
        ];
    }

}
