<?php

namespace Vanguard\Services\Traits;

trait YearTrait
{
    public function getYearFrom2018()
    {
        $year_begin = 2018;
        $current_year = date('Y');
        $year_array = [];
        for ($year = $year_begin; $year <= $current_year; $year++){
            $year_array[] = $year;
        }
        return $year_array;
    }
}
