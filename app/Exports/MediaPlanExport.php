<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MediaPlanExport implements WithMultipleSheets
{
    protected $formated_plan;

    public function __construct($formated_plan)
    {
        $this->formated_plan = $formated_plan;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new MediaPlanSummaryExport($this->formated_plan['summary'], $this->formated_plan['plan']);

        foreach ($this->formated_plan['summary_by_medium'] as $medium => $durations) {
            foreach ($durations as $duration => $data) {
                if ($duration == "summary") {
                    $sheets[] = new MediaPlanMediaTypeSummaryExport($medium, $data);
                } else {
                    $sheets[] = new MediaPlanMediaLengthExport($medium, $duration, $data, $this->formated_plan);
                }
            }
        }
        return $sheets;
    }
}