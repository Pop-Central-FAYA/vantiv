<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MediaPlanExport implements WithMultipleSheets
{
	protected $summary;
	protected $station_data;
	protected $monthly_weeks;
    protected $media_plan_data;

    public function __construct($summary, $station_data, $monthly_weeks, $media_plan_data)
    {
        $this->summary = $summary;
        $this->station_data = $station_data;
        $this->monthly_weeks = $monthly_weeks;
        $this->media_plan_data = $media_plan_data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new MediaPlanSummaryExport($this->summary);

        foreach ($this->station_data as $media_type => $material_lengths) {
            foreach ($material_lengths as $material_length => $station_programs) {
                $sheets[] = new MediaPlanMediaLengthExport($media_type, $material_length, $station_programs, $this->monthly_weeks, $this->summary, $this->media_plan_data);
            }
        }

        return $sheets;
    }
}