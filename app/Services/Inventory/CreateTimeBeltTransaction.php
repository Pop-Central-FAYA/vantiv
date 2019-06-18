<?php

namespace Vanguard\Services\Inventory;

use Vanguard\Models\TimeBeltTransaction;
use Vanguard\Services\Schedule\AdPatternSchedule;
use Vanguard\Services\Schedule\PlaceAdForSchedule;

class CreateTimeBeltTransaction
{
    protected $preselected_time_belt;

    public function __construct($preselected_time_belt)
    {
        $this->preselected_time_belt = $preselected_time_belt;
    }

    public function run()
    {
        \DB::transaction(function () {
            foreach ($this->preselected_time_belt as $time_belt){
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
                $time_belt_transaction->company_id = $time_belt->company_id;
                $time_belt_transaction->save();

                $place_ad_for_schedule = new PlaceAdForSchedule(
                                                $time_belt_transaction->publisher->decoded_settings['ad_pattern']['length'],
                                                $time_belt_transaction->id,$time_belt,
                                                $time_belt_transaction->formatted_media_program_hours['result'],
                                                $time_belt_transaction->formatted_media_program_hours['start_time']);
                $place_ad_for_schedule->run();
            }
        });
        return 'success';
    }

}
