<?php

namespace Vanguard\Services\MediaPlan;
use Vanguard\Models\MediaPlan;
use Vanguard\Models\MediaPlanProgram;
use Vanguard\Models\MediaPlanSuggestion;
use Auth;
use Illuminate\Support\Collection;
/**
 * @todo Wrap these db writes in a transaction
 */
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
            'campaign_name' => $this->criteriaForm->campaign_name,
            'planner_id' => Auth::id(),
            'status' => 'Suggested',
            'state_list' => json_encode($this->suggestions['state_list'])
        ]);
        foreach ($this->suggestions['projected_counts'] as $key => $suggestion) {
            MediaPlanSuggestion::create([
                'media_plan_id' => $newMediaPlan->id,
                'media_type' => $suggestion['media_type'],
                'station' => $suggestion['station'],
                'program' => $this->updateProgram($suggestion['station'], $suggestion['day'],
                            $suggestion['start_time']) ? $this->updateProgram($suggestion['station'],
                            $suggestion['day'], $suggestion['start_time'])->program_name : $suggestion['program'],
                'day' => $suggestion['day'],
                'start_time' => $suggestion['start_time'],
                'end_time' => $suggestion['end_time'],
                'total_audience' => $suggestion['audience'],
                'state_counts' => json_encode($suggestion['state_counts'])
            ]);
        }
        return $newMediaPlan;   
    }

    private function updateProgram($station, $day, $start_time)
    {
        return MediaPlanProgram::where([
                                    ['station', $station],
                                    ['day', $day],
                                ])
                                ->whereTime('start_time', $start_time)
                                ->first();
    }
}
