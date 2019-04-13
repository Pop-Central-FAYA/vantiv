<?php

namespace Vanguard\Services\Traits;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;

trait MediaSuggestionQueryTrait
{
    use CompanyIdTrait;
    public function mediaSuggestionQuery()
    {
        return \DB::table('media_plan_suggestions')
                ->leftJoin('media_plan_programs', function ($query) {
                    return $query->on('media_plan_programs.station', '=', 'media_plan_suggestions.station')
                        ->on('media_plan_programs.program_name', '=', 'media_plan_suggestions.program')
                        ->on('media_plan_programs.day', '=', 'media_plan_suggestions.day')
                        ->on('media_plan_programs.start_time', '=', 'media_plan_suggestions.start_time');
                })
                ->leftJoin('media_plan_program_ratings', function ($query) {
                    return $query->on('media_plan_program_ratings.program_name', '=', 'media_plan_programs.program_name')
                        ->on('media_plan_program_ratings.station', '=', 'media_plan_programs.station');
                })
                ->leftJoin('media_plan_volume_discounts', function ($query) {
                    return $query->on('media_plan_suggestions.station', '=', 'media_plan_volume_discounts.station')
                                ->where('media_plan_volume_discounts.agency_id', $this->companyId());
                })
                ->select('media_plan_suggestions.*',
                    'media_plan_programs.program_name AS name_of_program',
                    'media_plan_programs.station AS station_of_program',
                    'media_plan_programs.day AS day_of_program',
                    'media_plan_program_ratings.duration',
                    'media_plan_program_ratings.price',
                    'media_plan_volume_discounts.discount AS volume_discount');
    }
}
