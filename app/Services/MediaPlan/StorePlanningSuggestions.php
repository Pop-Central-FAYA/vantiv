<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanSuggestion;
use Auth;

class StorePlanningSuggestions
{
    protected $criteriaForm;
    protected $suggestions;

    public function __construct($criteriaForm, $suggestions)
    {
        $this->criteriaForm = $criteriaForm;
        $this->suggestions = $suggestions;
    }

    public function storePlanningSuggestions()
    {
        $newMediaPlan = MediaPlan::create([
            'criteria_gender' => $this->criteriaForm->gender,
            'criteria_lsm' => json_encode($this->criteriaForm->lsm),
            'criteria_social_class' => json_encode($this->criteriaForm->social_class),
            'criteria_region' => json_encode($this->criteriaForm->region),
            'criteria_state' => json_encode($this->criteriaForm->state),
            'criteria_age_groups' => json_encode($this->criteriaForm->age_groups),
            'agency_commission' => $this->criteriaForm->agency_commission,
            'start_date' => $this->criteriaForm->start_date,
            'end_date' => $this->criteriaForm->end_date,
            'media_type' => $this->criteriaForm->media_type,
            'planner_id' => Auth::id(),
            'status' => 'Initialized'
        ]);

        foreach ($this->suggestions as $key => $suggestion) {
            MediaPlanSuggestion::create([
                'media_plan_id' => $newMediaPlan->id,
                'media_type' => $suggestion->media_type,
                'station' => $suggestion->station,
                'program' => $suggestion->program,
                'day' => $suggestion->day,
                'start_time' => $suggestion->start_time,
                'end_time' => $suggestion->end_time,
                'total_audience' => $suggestion->audience
            ]);
        }

        return $newMediaPlan;   
    }
}