<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MediaPlanSummaryExport implements FromView, ShouldAutoSize, WithTitle
{
	protected $summary;
    
    public function __construct($summary)
    {
        $this->summary = $summary;
    }

    public function view(): View
    {
        return view('agency.mediaPlan.export.summary', [
            'summary' => collect($this->summary)
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Media Plan Summary";
    }
}