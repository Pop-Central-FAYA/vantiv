<?php

namespace Vanguard\Services\MediaPlan;

use Vanguard\Services\Traits\MediaSuggestionQueryTrait;

class GetSuggestionListWithProgramRating
{
    protected $media_plan_id;
    use MediaSuggestionQueryTrait;

    public function __construct($media_plan_id)
    {
        $this->media_plan_id = $media_plan_id;
    }

    public function getMediaPlanSuggestionWithProgram()
    {
        return $this->mediaSuggestionQuery()
                    ->addSelect('media_plan_programs.actual_time_slot')
                    ->selectRaw("JSON_ARRAYAGG(media_plan_program_ratings.duration) AS duration_lists, 
                                JSON_ARRAYAGG(media_plan_program_ratings.price) AS rate_lists")
                    ->where([
                        ['media_plan_suggestions.media_plan_id', $this->media_plan_id],
                        ['media_plan_suggestions.status', 1]
                    ])
                    ->groupBy('media_plan_suggestions.id')
                    ->get();
    }
}
