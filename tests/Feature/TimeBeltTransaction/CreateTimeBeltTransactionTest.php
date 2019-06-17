<?php

namespace Tests\Feature\TimeBeltTransaction;

use Tests\TestCase;
use Vanguard\Models\Company;
use Vanguard\Models\MediaProgram;
use Vanguard\Models\Publisher;
use Vanguard\Models\TimeBelt;
use Vanguard\Services\Inventory\CreateTimeBeltTransaction;

class CreateTimeBeltTransactionTest extends TestCase
{
    public function test_it_create_time_belt_transaction()
    {
        factory(Publisher::class)->create([
            'company_id' => $company_id = factory(Company::class)->create()->id
        ]);
        $time_belt_transaction = new CreateTimeBeltTransaction($this->getPreselectedTimeBelt($company_id));
        $this->assertEquals('success', $time_belt_transaction->createTimeBeltTransaction());
    }

    public function getPreselectedTimeBelt($company_id)
    {
        $preselected_time_belt = [];
        $preselected_time_belt_1 = (object)[
            'time_belt_id' => factory(TimeBelt::class)->create()->id,
            'media_program_id' => factory(MediaProgram::class)->create()->id,
            'campaign_details_id' => 'hfcagsrjdmzgxsd',
            'duration' => 30,
            'file_name' => 'ridwan.mp4',
            'file_url' => 'http://ridwan.com/ridwan.mp4',
            'file_format' => 'mp4',
            'amount_paid' => 6500000,
            'playout_date' => '2019-06-11',
            'playout_hour' => '11:00:00',
            'approval_status' => 'approved',
            'payment_status' => 'approved',
            'company_id' => $company_id
        ];
        $preselected_time_belt_2 = (object)[
            'time_belt_id' => factory(TimeBelt::class)->create()->id,
            'media_program_id' => factory(MediaProgram::class)->create()->id,
            'campaign_details_id' => 'hfcagsrjdmzgx654',
            'duration' => 45,
            'file_name' => 'ridwan_busari.mp4',
            'file_url' => 'http://ridwan.com/ridwan_busari.mp4',
            'file_format' => 'mp4',
            'amount_paid' => 9000000,
            'playout_date' => '2019-06-11',
            'playout_hour' => '11:00:00',
            'approval_status' => 'approved',
            'payment_status' => 'approved',
            'company_id' => $company_id
        ];

        array_push($preselected_time_belt, $preselected_time_belt_1, $preselected_time_belt_2);
        return $preselected_time_belt;
    }
}
