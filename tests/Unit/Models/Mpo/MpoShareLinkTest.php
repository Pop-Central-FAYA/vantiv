<?php

namespace Tests\Unit\Models\Mpo;

use Tests\TestCase;
use Vanguard\Models\MpoShareLink;
use Vanguard\Models\MpoShareLinkActivity;
use Vanguard\Models\CampaignMpo;
use Vanguard\Libraries\Enum\Status;
use Carbon\Carbon;

class MpoShareLinkTest extends TestCase
{
    public function test_it_can_have_many_share_link_activities()
    {
        $share_link = factory(MpoShareLink::class)->create();
        factory(MpoShareLinkActivity::class)->create([
            'mpo_share_link_id' => $share_link->id
        ]);
        $this->assertInstanceOf(MpoShareLinkActivity::class, $share_link->share_link_activities->first());
    }

    public function test_it_belongs_to_an_mpo()
    {
        $share_link = factory(MpoShareLink::class)->create([
            'mpo_id' => factory(CampaignMpo::class)->create()->id
        ]);
        $this->assertInstanceOf(CampaignMpo::class, $share_link->campaign_mpo);
    }

    public function test_it_can_get_the_latest_active_link()
    {
        $mpo = factory(CampaignMpo::class)->create();
<<<<<<< HEAD
        factory(MpoShareLink::class)->create([
            'status' => Status::ACTIVE,
            'mpo_id' => $mpo->id
        ]);
        factory(MpoShareLink::class)->create([
            'status' => Status::EXPIRED,
            'mpo_id' => $mpo->id
        ]);
        $this->assertEquals(Status::ACTIVE, $mpo->active_link->status);
    }

    public function test_it_cast_the_expired_at_to_carbon_time_and_add_90_days()
    {
        $stop_date = '2019-01-01';
        $share_link = factory(MpoShareLink::class)->create([
            'mpo_id' => factory(CampaignMpo::class)->create()->id,
            'expired_at' => $stop_date
        ]);
        $this->assertEquals(Carbon::parse('2019-04-01'), $share_link->expired_at);
    }
}
=======
        $link1 = factory(MpoShareLink::class)->create([
            'mpo_id' => $mpo->id,
            'expired_at' => Carbon::parse('2019-07-01')->addDays(90)
        ]);
        $link2 = factory(MpoShareLink::class)->create([
            'mpo_id' => $mpo->id,
            'expired_at' => Carbon::parse('2019-08-19')->addDays(90)
        ]);
        $this->assertEquals($link2->url, $mpo->active_share_link->url);
    }
}
 
>>>>>>> 0ce911066d366c2eafdeaec55e25fa8e10e0ece7
