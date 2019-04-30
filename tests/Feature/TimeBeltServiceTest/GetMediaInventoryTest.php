<?php

namespace Tests\Feature\TimeBeltServiceTest;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Services\Inventory\CreateInventoryService;
use Vanguard\Services\Inventory\GetMediaInventory;
use Vanguard\Services\Inventory\GetProgramList;
use Vanguard\Services\Inventory\PopulateTimeBelt;
use Vanguard\Services\User\CreateUser;
use Vanguard\Models\Ratecard\Ratecard;

class GetMediaInventoryTest extends TestCase
{
    public function test_it_can_fetch_media_inventory_for_publisher()
    {
        $company = factory(Company::class)->create();
        $time_belt = new PopulateTimeBelt($company->id);
        $time_belt->populateTimeBelt();
        $this->createMediaInventory($company->id);
        $media_inventory_service = new GetMediaInventory($company->id);
        $media_inventory_service->getMediaInventory();
        $this->assertDatabaseHas('time_belts', [
            'actual_time_picked' => '11:00:00-12:00:00'
        ]);
    }

    public function test_a_logged_in_user_can_access_the_media_inventory_page()
    {
        $fakeer = Factory::create();
        $user = $this->createUser($fakeer);

        $this->actingAs($user)
            ->get('/program-management')
            ->assertSee('Programs');
    }

    public function test_it_sees_the_necessary_data_in_the_page()
    {
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $time_belt = new PopulateTimeBelt($user->companies->first()->id);
        $time_belt->populateTimeBelt();
        $this->createMediaInventory($user->companies->first()->id);
        $get_program_list_service = new GetProgramList($user->companies->first()->id);
        $get_program_list_service->getActivePrograms();
        $this->actingAs($user)
            ->get('/program-management/data-table')
            ->assertSee('Hello Program');

    }

    public function test_a_logged_in_user_can_visit_the_create_media_inventory_page()
    {
        $faker = Factory::create();
        $user = $this->createUser($faker);
        $this->actingAs($user)
            ->get('/program-management/create')
            ->assertSee('Create Program');
    }

    public function createUser($faker)
    {
        $create_user_service = new CreateUser($faker->name, $faker->name, $faker->email, null,
            $faker->phoneNumber, 'testUserPassword', null);
        $user = $create_user_service->createUser();
        $company = \factory(Company::class)->create();
        $user->companies()->sync($company->id);
        return $user;
    }

    public function createMediaInventory($company_id)
    {
        $rate_card = \factory(Ratecard::class)->create([
            'company_id' => $company_id
        ]);
        $media_inventory_service = new CreateInventoryService($this->getDayList(), 'Hello Program', $company_id,
                        null, $rate_card->id, '2019-04-03', '2019-05-05',
                                        $this->getStartTimeList(),$this->getEndTimeList());
        return $media_inventory_service->createTimeBelt();
    }

    private function getDayList()
    {
        return [
            'Monday'
        ];
    }

    private function getStartTimeList()
    {
        return [
            '11:00:00'
        ];
    }

    private function getEndTimeList()
    {
        return [
            '12:00:00'
        ];
    }
}
