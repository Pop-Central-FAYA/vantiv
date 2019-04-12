<?php

namespace Vanguard\Services\MediaPlan;

use Vanguard\Services\Traits\MediaSuggestionQueryTrait;

class GetSuggestionWithProgramRating
{
    use MediaSuggestionQueryTrait;

    protected $media_plan_suggestion_id;

    public function __construct($media_plan_suggestion_id)
    {
        $this->media_plan_suggestion_id = $media_plan_suggestion_id;
    }

    public function getMediaPlanSuggestion()
    {
        return $this->mediaSuggestionQuery()
            ->addSelect('media_plan_programs.actual_time_slot')
            ->where([
                ['media_plan_suggestions.id', $this->media_plan_suggestion_id],
                ['media_plan_suggestions.status', 1]
            ])
            ->groupBy('media_plan_suggestions.id')
            ->get();
    }
}
