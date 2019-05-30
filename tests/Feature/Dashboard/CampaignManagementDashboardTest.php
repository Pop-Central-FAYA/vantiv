<?php

namespace Tests\Feature\Dashboard;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tests\Traits\PermissionsTrait;
use Vanguard\Models\CampaignDetail;
use Vanguard\Models\Company;
use Vanguard\Models\Mpo;
use Vanguard\Models\MpoDetail;
use Vanguard\Models\Publisher;
use Vanguard\Models\TimeBelt;
use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Models\WalkIns;
use Vanguard\Services\Reports\Publisher\CampaignsByMediaType;
use Vanguard\Services\Reports\Publisher\ClientsAndBrandsByMediaType;
use Vanguard\Services\Reports\Publisher\MposByMediaType;
use Vanguard\Services\Reports\Publisher\TopRevenueByClient;
use Vanguard\Services\Reports\Publisher\TopRevenueByMediaType;
use Vanguard\User;

class CampaignManagementDashboardTest extends TestCase
{
    use PermissionsTrait;
    public function test_it_redirects_to_login_if_user_is_not_authenticated()
    {
        $result = $this->get('/campaign-management/dashboard');
        $result->assertRedirect('/login');
    }

    public function test_it_can_get_revenue_by_media_type()
    {
        //create user and company with admin role
        $user_attributes = $this->createUserAndAttributes();
        factory(Publisher::class)->create([
            'company_id' => $company_id = $user_attributes['company']->id
        ]);
        $time_belt = factory(TimeBelt::class)->create([
            'station_id' => $company_id
        ]);
        factory(TimeBeltTransaction::class)->create([
            'time_belt_id' => $time_belt->id
        ]);
        $top_revenue_service = new TopRevenueByMediaType(array($company_id));
        $top_revenue = $top_revenue_service->run();
        $this->assertObjectHasAttribute('revenue', $top_revenue[0]);
    }

    public function test_it_can_get_top_revenue_when_ther_is_multiple_record_by_media_type()
    {
        //create user and company with admin role
        $user_attributes = $this->createUserAndAttributes();
        $company_2 = factory(Company::class)->create(['name' => 'STV']);
        $user_attributes['user']->companies()->attach($company_2->id);
        factory(Publisher::class)->create([
            'company_id' => $user_attributes['company']->id
        ]);
        factory(Publisher::class)->create([
            'company_id' => $company_2->id
        ]);
        $time_belt = factory(TimeBelt::class)->create([
            'station_id' => $user_attributes['company']->id
        ]);
        $time_belt_2 = factory(TimeBelt::class)->create([
            'station_id' => $company_2->id
        ]);
        factory(TimeBeltTransaction::class)->create([
            'time_belt_id' => $time_belt->id,
            'amount_paid' => 1000
        ]);
        factory(TimeBeltTransaction::class)->create([
            'time_belt_id' => $time_belt_2->id,
            'amount_paid' => 70000
        ]);
        $top_revenue_service = new TopRevenueByMediaType(array($user_attributes['company']->id, $company_2->id));
        $top_revenue = $top_revenue_service->run();
        $this->assertEquals(70000, $top_revenue[0]->revenue);
    }

    public function test_it_get_client_and_brand_by_media_type()
    {
        $user_attributes = $this->createUserAndAttributes();
        $walkins = factory(WalkIns::class)->create([
            'company_id' => $user_attributes['company']->id
        ]);
        factory(CampaignDetail::class)->create([
            'launched_on' => $user_attributes['company']->id,
            'walkins_id' => $walkins->id
        ]);
        factory(Publisher::class)->create([
            'company_id' => $user_attributes['company']->id
        ]);

        $client_and_brand_by_media_type_service = new ClientsAndBrandsByMediaType(array($user_attributes['company']->id));
        $cliena_brand_by_media_type = $client_and_brand_by_media_type_service->run();
        $this->assertArrayHasKey('brands', $cliena_brand_by_media_type);
        $this->assertArrayHasKey('walkin_clients', $cliena_brand_by_media_type);
    }

    public function test_it_can_get_revenue_by_client()
    {
        $user_attributes = $this->createUserAndAttributes();
        $walkins = factory(WalkIns::class)->create([
            'company_id' => $user_attributes['company']->id
        ]);
        $campaign_details = factory(CampaignDetail::class)->create([
            'walkins_id' => $walkins->id,
            'launched_on' => $user_attributes['company']->id
        ]);
        factory(TimeBeltTransaction::class)->create([
            'campaign_details_id' => $campaign_details->id,
            'amount_paid' => 1000
        ]);
        $top_revenue_by_client_service = new TopRevenueByClient(array($user_attributes['company']->id));
        $reveune = $top_revenue_by_client_service->run();
        $this->assertEquals(1000, (integer)$reveune->actual_revenue);
    }

    public function test_it_can_get_top_revenue_by_client()
    {
        $user_attributes = $this->createUserAndAttributes();
        $walkins = factory(WalkIns::class)->create([
            'company_id' => $user_attributes['company']->id
        ]);

        $walkins_2 = factory(WalkIns::class)->create([
            'company_name' => 'Ridwan and Co',
            'company_id' => $user_attributes['company']->id
        ]);

        $campaign_details = factory(CampaignDetail::class)->create([
            'walkins_id' => $walkins->id,
            'launched_on' => $user_attributes['company']->id
        ]);

        $campaign_details_2 = factory(CampaignDetail::class)->create([
            'walkins_id' => $walkins_2->id,
            'launched_on' => $user_attributes['company']->id
        ]);

        factory(TimeBeltTransaction::class)->create([
            'campaign_details_id' => $campaign_details->id,
            'amount_paid' => 1000
        ]);

        factory(TimeBeltTransaction::class)->create([
            'campaign_details_id' => $campaign_details_2->id,
            'amount_paid' => 12000
        ]);

        $top_revenue_by_client_service = new TopRevenueByClient(array($user_attributes['company']->id));
        $reveune = $top_revenue_by_client_service->run();
        $this->assertEquals(12000, (integer)$reveune->actual_revenue);
    }

    public function test_it_can_get_campaigns_by_media_types()
    {
        $user_attributes = $this->createUserAndAttributes();
        factory(Publisher::class)->create([
            'company_id' => $company_id = $user_attributes['company']->id
        ]);
        factory(CampaignDetail::class)->create([
            'launched_on' => $company_id
        ]);

        $campaigns_by_media_type_service = new CampaignsByMediaType(array($company_id));
        $campaign_by_media_type = $campaigns_by_media_type_service->run();
        $this->assertArrayHasKey('total', $campaign_by_media_type);
        $this->assertArrayHasKey('detailed_counts', $campaign_by_media_type);
    }

    public function test_it_can_get_the_actual_total_from_campaigns_by_media_types()
    {
        $user_attributes = $this->createUserAndAttributes();
        factory(Publisher::class)->create([
            'company_id' => $company_id = $user_attributes['company']->id
        ]);
        factory(CampaignDetail::class)->create([
            'launched_on' => $company_id
        ]);

        $campaigns_by_media_type_service = new CampaignsByMediaType(array($company_id));
        $campaign_by_media_type = $campaigns_by_media_type_service->run();
        $this->assertEquals(1, $campaign_by_media_type['total']['tv']);
    }

    public function test_it_can_get_the_actual_detailed_count_from_campaigns_by_media_types()
    {
        $user_attributes = $this->createUserAndAttributes();
        factory(Publisher::class)->create([
            'company_id' => $company_id = $user_attributes['company']->id
        ]);
        factory(CampaignDetail::class)->create([
            'launched_on' => $company_id
        ]);

        $campaigns_by_media_type_service = new CampaignsByMediaType(array($company_id));
        $campaign_by_media_type = $campaigns_by_media_type_service->run();
        $this->assertEquals(1, $campaign_by_media_type['detailed_counts']['tv']['active']);
        $this->assertEquals(0, $campaign_by_media_type['detailed_counts']['tv']['on_hold']);
        $this->assertEquals(0, $campaign_by_media_type['detailed_counts']['tv']['pending']);
        $this->assertEquals(0, $campaign_by_media_type['detailed_counts']['tv']['expired']);
    }

    public function test_it_can_get_mpos_by_media_types()
    {
        $user_attributes = $this->createUserAndAttributes();
        $campaign_details = factory(CampaignDetail::class)->create([
            'launched_on' => $company_id = $user_attributes['company']->id
        ]);
        $mpo = factory(Mpo::class)->create([
                    'campaign_id' => $campaign_details->campaign_id
                ]);
        factory(Publisher::class)->create([
            'company_id' => $company_id
        ]);
        factory(MpoDetail::class)->create([
            'broadcaster_id' => $company_id,
            'mpo_id' => $mpo->id
        ]);
        $mpo_by_media_type_service = new MposByMediaType(array($company_id));
        $mpo_by_media_type = $mpo_by_media_type_service->run();
        $this->assertArrayHasKey('total', $mpo_by_media_type);
        $this->assertArrayHasKey('detailed_counts', $mpo_by_media_type);
    }

    public function test_it_can_get_total_from_mpos_by_media_types()
    {
        $user_attributes = $this->createUserAndAttributes();
        $campaign_details = factory(CampaignDetail::class)->create([
            'launched_on' => $company_id = $user_attributes['company']->id
        ]);
        $mpo = factory(Mpo::class)->create([
            'campaign_id' => $campaign_details->campaign_id
        ]);
        factory(Publisher::class)->create([
            'company_id' => $company_id
        ]);
        factory(MpoDetail::class)->create([
            'broadcaster_id' => $company_id,
            'mpo_id' => $mpo->id
        ]);
        $mpo_by_media_type_service = new MposByMediaType(array($company_id));
        $mpo_by_media_type = $mpo_by_media_type_service->run();
        $this->assertEquals(1, $mpo_by_media_type['total']['tv']);
    }

    public function test_it_can_get_detailed_count_from_mpos_by_media_types()
    {
        $user_attributes = $this->createUserAndAttributes();
        $campaign_details = factory(CampaignDetail::class)->create([
            'launched_on' => $company_id = $user_attributes['company']->id
        ]);
        $mpo = factory(Mpo::class)->create([
            'campaign_id' => $campaign_details->campaign_id
        ]);
        factory(Publisher::class)->create([
            'company_id' => $company_id
        ]);
        factory(MpoDetail::class)->create([
            'broadcaster_id' => $company_id,
            'mpo_id' => $mpo->id
        ]);
        $mpo_by_media_type_service = new MposByMediaType(array($company_id));
        $mpo_by_media_type = $mpo_by_media_type_service->run();
        $this->assertEquals(1, $mpo_by_media_type['detailed_counts']['tv']['accepted']);
        $this->assertEquals(0, $mpo_by_media_type['detailed_counts']['tv']['pending']);
    }

    public function createUserAndAttributes()
    {
        $user = factory(User::class)->create();
        $user->assignRole($this->createDefaultRole()->id);
        $company = factory(Company::class)->create(['name' => 'TVC']);
        $user->companies()->attach($company->id);
        return ['user' => $user, 'company' => $company];
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
