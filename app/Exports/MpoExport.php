<?php

namespace Vanguard\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MpoExport implements FromView
{
    protected $exportable_mpos;
    protected $day_numbers;
    protected $mpo_details;
    protected $summary;

    public function __construct($exportable_mpos, $day_numbers, $mpo_details, $summary)
    {
        $this->exportable_mpos = $exportable_mpos;
        $this->day_numbers = $day_numbers;
        $this->mpo_details = $mpo_details;
        $this->summary = $summary;
    }

    public function view(): View
    {
        return view('agency.campaigns.export_mpo', [
            'mpos' => $this->exportable_mpos,
            'day_numbers' =>$this->day_numbers,
            'mpo_details' => $this->mpo_details,
            'time_belt_summary' => $this->summary
        ]);
    }
}