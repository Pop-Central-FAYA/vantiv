<?php

namespace Tests\Feature\TimeBeltServiceTest;

use Faker\Factory;
use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\TimeBelt;
use Vanguard\Services\Inventory\GetMediaInventory;
use Vanguard\Services\User\CreateUser;

class GetMediaInventoryTest extends TestCase
{
    public function test_it_can_fetch_media_inventory_for_publisher()
    {
        $company = factory(Company::class)->create();
        $this->createMediaInventory($company->id);
        $media_inventory_service = new GetMediaInventory($company->id);
        $media_inventory = $media_inventory_service->getMediaInventory();
        $this->assertEquals('11h00', $media_inventory[0]['start_time']);
    }

    public function test_a_logged_in_user_can_access_the_media_inventory_page()
    {
        $fakeer = Factory::create();
        $user = $this->createUser($fakeer);

        $this->actingAs($user)
            ->get('/program-management')
            ->assertSee('Program Management');
    }

    public function test_it_sees_the_necessary_data_in_the_page()
    {
        $fakeer = Factory::create();
        $user = $this->createUser($fakeer);
        $company = factory(Company::class)->create();
        $this->createMediaInventory($company->id);
        $media_inventory_service = new GetMediaInventory($company->id);
        $media_inventory_service->getMediaInventory();
        $this->actingAs($user)
            ->get('/program-management')
            ->assertSee('Monday');

    }

    public function test_a_logged_in_user_can_visit_the_create_media_inventory_page()
    {
        $fakeer = Factory::create();
        $user = $this->createUser($fakeer);
        $this->actingAs($user)
            ->get('/program-management/create')
            ->assertSee('Create Program');
    }

    public function test_a_logged_in_user_can_create_a_media_inventory()
    {
        \Session::start();
        $fakeer = Factory::create();
        $user = $this->createUser($fakeer);

       $this->actingAs($user)->call('POST', '/program-management/store', [
            '_token' => csrf_token(),
            'day' => 'monday',
            'program_name' => 'test program'
        ])->assertSee('Monday');
    }

    public function createUser($faker)
    {
        $create_user_service = new CreateUser($faker->name, $faker->name, $faker->email, null,
            $faker->phoneNumber, 'testUserPassword', null);
        return $create_user_service->createUser();
    }

    public function createMediaInventory($company_id)
    {
        return factory(TimeBelt::class)->create([
                    'station_id' => $company_id,
                    'start_time' => '11:00:00',
                    'end_time' => '11:15:00',
                    'day' => 'Monday'
                ]);
    }
}
