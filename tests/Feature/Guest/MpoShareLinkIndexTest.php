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

    public function test_it_is_not_protected_by_auth()
    {
        $share_link = factory(MpoShareLink::class)->create();
        $this->get($share_link->url)->assertSee($share_link->id); 
    }

    public function test_the_user_gets_the_right_error_message_when_the_link_expires()
    {
        $mpo = factory(CampaignMpo::class)->create();
        $share_link = factory(MpoShareLink::class)->create([
            'id' => $id = uniqid(),
            'mpo_id' => $mpo->id,
            'expired_at' => Carbon::parse('2019-01-01')->addDays(90),
            'url' => URL::signedRoute('guest.mpo_share_link', ['id' => $id]),
        ]);
        $this->get($share_link->url)->assertSee('link expired');
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
}