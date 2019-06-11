<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Services\Schedule\AdPatternSchedule;
use Vanguard\Services\Schedule\GetPublisherSettings;

class CreateTimeBeltTransaction
{
    protected $preselected_time_belt;

    public function __construct($preselected_time_belt)
    {
        $this->preselected_time_belt = $preselected_time_belt;
    }

    public function createTimeBeltTransaction()
    {
        \DB::transaction(function () {
            foreach ($this->preselected_time_belt as $time_belt){
                $ad_pattern = (new GetPublisherSettings($time_belt->broadcaster_id))->run()['ad_pattern']['length'];
                $time_belt_transaction = new TimeBeltTransaction();
                $time_belt_transaction->time_belt_id = $time_belt->time_belt_id;
                $time_belt_transaction->media_program_id = $time_belt->media_program_id;
                $time_belt_transaction->campaign_details_id = $time_belt->campaign_details_id;
                $time_belt_transaction->duration = $time_belt->duration;
                $time_belt_transaction->file_name = $time_belt->file_name;
                $time_belt_transaction->file_url = $time_belt->file_url;
                $time_belt_transaction->file_format = $time_belt->file_format;
                $time_belt_transaction->amount_paid = $time_belt->amount_paid;
                $time_belt_transaction->playout_date = $time_belt->playout_date;
                $time_belt_transaction->playout_hour = $time_belt->playout_hour;
                $time_belt_transaction->approval_status = $time_belt->approval_status;
                $time_belt_transaction->payment_status = $time_belt->payment_status;
                $time_belt_transaction->company_id = $time_belt->broadcaster_id;
                $time_belt_transaction->save();

                $ad_schedule_service = new AdPatternSchedule($time_belt, $ad_pattern, $time_belt_transaction->id);
                $ad_schedule_service->run();
            }
        });
        return 'success';
    }
}
