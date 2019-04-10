<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MediaPlanExport implements FromView, ShouldAutoSize
{
	protected $summary;
	protected $station_data;
	protected $monthly_weeks;
    
    public function __construct($summary, $station_data, $monthly_weeks)
    {
        $this->summary = $summary;
        $this->station_data = $station_data;
        $this->monthly_weeks = $monthly_weeks;
    }

    public function view(): View
    {
        return view('agency.mediaPlan.export.summary', [
            'summary' => collect($this->summary)
        ]);
    }
}