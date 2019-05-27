<?php

namespace Tests\Feature\RateCard;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\Ratecard\Ratecard;
use Vanguard\Services\RateCard\UpdateRateCardService;
use Vanguard\User;

class RateCardUpdateTest extends TestCase
{
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->ajaxPost('/rate-card-management/update/1');
        $result->assertRedirect('/login');
    }

    public function test_user_with_wrong_role_get_redirected()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $this->actingAs($user)
            ->get('/rate-card-management/edit/4')
            ->assertSeeText('Forbidden!');
    }

    public function test_it_requires_a_name()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $rate_card = \factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id)
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
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id,[
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
        $rate_card = \factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id)
            ->assertSessionHasErrors('price');
    }

    public function test_it_requires_duration_and_must_be_array()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $rate_card = \factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id)
            ->assertSessionHasErrors('duration');
    }

    public function test_it_takes_is_base_to_be_boolean()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $rate_card = \factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id, [
                'is_base' => 'hjdfk'
            ])
            ->assertSessionHasErrors('is_base');
    }

    public function test_it_can_update_a_rate_card()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $rate_card = factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id, [
                'title' => $title = 'New Title',
                'duration' => [30],
                'price' => [2000],
                'company' => $company_id
            ]);
        $this->assertDatabaseHas('rate_cards', [
            'id' => $rate_card->id,
            'title' => $title
        ]);
    }

    public function test_it_can_update_a_rate_card_duration()
    {
        $user = factory(User::class)->create();
        $user->companies()->attach(factory(Company::class)->create()->id);
        $user->assignRole(factory(Role::class)->create()->id);
        $rate_card = factory(Ratecard::class)->create([
            'company_id' => $company_id = $user->companies->first()->id
        ]);
        $this->actingAs($user)
            ->ajaxPost('/rate-card-management/update/'.$rate_card->id, [
                'title' => $title = 'New Title',
                'duration' => $duration = [30],
                'price' => $price =  [2000],
                'company' => $company_id
            ]);
        $this->assertDatabaseHas('rate_card_durations', [
            'rate_card_id' => $rate_card->id,
            'price' => $price[0],
            'duration' => $duration[0]
        ]);
    }

    public function test_it_update_rate_card_from_the_service()
    {
        $company = \factory(Company::class)->create();
        $rate_card = factory(Ratecard::class)->create([
                        'company_id' => $company->id
                    ]);
        $update_rate_card_service = new UpdateRateCardService($rate_card->id,$this->rateCardData($company->id,'Updated Title',null));
        $update_rate_card_service->run();
        $this->assertDatabaseHas('rate_cards', [
            'company_id' => $company->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_it_update_rate_card_from_the_service_which_includes_base()
    {
        $company = \factory(Company::class)->create();
        $rate_card = factory(Ratecard::class)->create([
            'company_id' => $company->id
        ]);
        $update_rate_card_service = new UpdateRateCardService($rate_card->id,$this->rateCardData($company->id,'Updated Title',true));
        $update_rate_card_service->run();
        $this->assertDatabaseHas('rate_cards', [
            'company_id' => $company->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_it_update_rate_card_from_the_service_and_also_toggles_the_base()
    {
        $company = \factory(Company::class)->create();
        $existing_rate_card = factory(Ratecard::class)->create([
                                    'company_id' => $company->id,
                                    'is_base' => true
                                ]);
        $rate_card = factory(Ratecard::class)->create([
            'title' => $title = 'Initial Rate card',
            'company_id' => $company->id,
            'is_base' => true,
            'slug' => str_slug($title)
        ]);
        $update_rate_card_service = new UpdateRateCardService($rate_card->id,$this->rateCardData($company->id,'Updated Title',true));
        $update_rate_card_service->run();
        $this->assertDatabaseHas('rate_cards', [
            'id' => $existing_rate_card->id,
            'is_base' => false,
        ]);
    }

    public function rateCardData($company_id, $name, $is_base)
    {
        return [
            'company_id' => $company_id,
            'duration' => [15],
            'price' => [1000],
            'name' => $name,
            'is_base' => $is_base,
        ];
    }
}
