<?php

namespace Tests\Feature\Feature\Guest\MpoShareLinkIndex;

use Tests\TestCase;
use Vanguard\Models\CampaignMpo;
use Vanguard\Models\MpoShareLink;
use Illuminate\Support\Facades\URL;
use Vanguard\Models\MpoShareLinkActivity;
use Carbon\Carbon;

class MpoShareLinkIndex extends TestCase
{
    protected $route_name = 'guest.mpo_share_link';
    protected $mpo_route_name = 'public.mpo.export';
    protected $get_temporary_url_route = 'public.mpo.export.temporary_url';

    public function test_it_is_not_protected_by_auth()
    {
        $mpo = factory(CampaignMpo::class)->create();
        $share_link = factory(MpoShareLink::class)->create([
            'mpo_id' => $mpo->id
        ]);
        $company = $mpo->campaign->company;
        $this->get($share_link->url)->assertSee($company->logo); 
    }

    public function test_the_user_gets_the_right_status_when_the_link_expires()
    {
        $mpo = factory(CampaignMpo::class)->create();
        $share_link = factory(MpoShareLink::class)->create([
            'id' => $id = uniqid(),
            'mpo_id' => $mpo->id,
            'expired_at' => Carbon::parse('2019-01-01')->addDays(90),
            'url' => URL::signedRoute('guest.mpo_share_link', ['id' => $id]),
        ]);
        $company = $mpo->campaign->company;
        $this->get($share_link->url)->assertSee($company->name);
    }

    public function test_it_create_log_when_user_access_the_share_link()
    {
        $share_link = factory(MpoShareLink::class)->create();
        $log = factory(MpoShareLinkActivity::class)->create([
                    'mpo_share_link_id' => $share_link->id
                ]);
        $this->get($share_link->url);
        $this->assertInstanceOf(MpoShareLinkActivity::class, $log); 
    }

    public function test_it_get_the_right_status_if_link_is_invalid()
    {
        $mpo = factory(CampaignMpo::class)->create();
        $temporary_route = URL::temporarySignedRoute(
            $this->mpo_route_name, now()->subDays(2), ['id' => $mpo->id]
        );
        $this->get($temporary_route)->assertJson([
            'error' => 'invalid'
        ]);
    }

    public function test_it_returns_temporary_url()
    {
        $mpo = factory(CampaignMpo::class)->create();
        $this->get(route($this->get_temporary_url_route, ['id' => $mpo->id]))
            ->assertStatus(200);
    }
}