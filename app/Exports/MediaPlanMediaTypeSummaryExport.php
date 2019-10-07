<?php

namespace Vanguard\Exports;

use Vanguard\Models\MediaPlan;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MediaPlanMediaTypeSummaryExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    protected $media_type;
    protected $summary_by_medium;

    
    public function __construct($media_type, $summary_by_medium)
    {
        $this->media_type = $media_type;
        $this->summary_by_medium = $summary_by_medium;
    }

    public function view(): View
    {
        return view('agency.mediaPlan.export.mediaTypeSummary', [
            'summary_by_length' => $this->summary_by_medium['summary_by_duration'],
            'summary_by_length_station_type' => $this->summary_by_medium['summary_by_station_type']
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return strtoupper($this->media_type.' Summary');
    }

    public function registerEvents(): array
    {
        return [ 
            AfterSheet::class => function(AfterSheet $event) {
                $styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        ],
                    ],
                ];

               $event->sheet->getStyle('A1:'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->getFont()->setSize(16);
               $event->sheet->getColumnDimension('A')->setAutoSize(false);
               $event->sheet->getColumnDimension('A')->setWidth(10);
               $event->sheet->getColumnDimension('B')->setAutoSize(false);
               $event->sheet->getColumnDimension('B')->setWidth(30);
               $event->sheet->getColumnDimension('D')->setAutoSize(false);
               $event->sheet->getColumnDimension('D')->setWidth(18);
               $event->sheet->getStyle('B3:'.$event->sheet->getHighestColumn().(count($this->summary_by_medium['summary_by_duration']['data'])+3+2))->applyFromArray($styleArray);
               $event->sheet->getStyle('B3:'.$event->sheet->getHighestColumn().(count($this->summary_by_medium['summary_by_duration']['data'])+3+1))->applyFromArray($styleArray);
               $event->sheet->getStyle('C'.(count($this->summary_by_medium['summary_by_duration']['data'])+3+2+4).':'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->applyFromArray($styleArray);
            },
        ];
    }
}