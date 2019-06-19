<?php

namespace Vanguard\Models;

class TimeBeltTransaction extends Base
{
    protected $fillable = ['time_belt_id', 'media_program_id', 'playout_date', 'duration', 'file_name',
                            'file_url', 'campaign_details_id', 'file_format', 'amount_paid', 'playout_hour',
                            'approval_status', 'payment_status', 'company_id'];

    public function time_belt()
    {
        return $this->belongsTo(TimeBelt::class);
    }

    public function campaign_detail()
    {
        return $this->belongsTo(CampaignDetail::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'company_id', 'company_id');
    }

    public function media_program()
    {
        return $this->belongsTo(MediaProgram::class);
    }

    public function getMediaProgramHours()
    {
        return $this->time_belt::selectRaw("hour(start_time) as program_hours, start_time")
                ->where('media_program_id', $this->media_program->id)
                ->groupBy(\DB::raw('hour(start_time)'))
                ->get()
                ->toArray();

    }

    public function getFormattedMediaProgramHoursAttribute()
    {
        $media_program_hours = $this->getMediaProgramHours();
        $result = [];
        foreach ($media_program_hours as $media_program_hour){
            $result[] = $media_program_hour['program_hours'];
        }
        return ['result' => $result, 'start_time' => $media_program_hours[0]['start_time']];
    }
}
