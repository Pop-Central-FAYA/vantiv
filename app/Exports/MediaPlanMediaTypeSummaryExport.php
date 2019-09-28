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
    protected $material_lengths;
    protected $media_plan_period;
    protected $summaryByLength;
    protected $summaryByLengthStationType;
    
    public function __construct($media_type, $material_lengths, $media_plan_period)
    {
        $this->media_type = $media_type;
        $this->material_lengths = $material_lengths;
        $this->media_plan_period = $media_plan_period;
    }

    public function view(): View
    {
        foreach ($this->material_lengths as $length => $timebelts) {
            $this->summaryByLength[] = [
                'length' => $length,
                'total_spots' => $timebelts->sum('total_spots'),
                'gross_total' => $timebelts->sum('gross_value'),
                'net_total' => $timebelts->sum('net_value'),
                'duration' => $this->media_plan_period
            ];
        }

        foreach ($this->material_lengths as $length => $timebelts) {
            $national_stations = $this->groupByStationType($timebelts, ['network', 'Network']);
            $cable_stations = $this->groupByStationType($timebelts, ['cable', 'satellite', 'Satellite']);
            $regional_stations = $this->groupByRegions($timebelts);

            $this->summaryByLengthStationType[$length] = [
                'National' => [
                    'total_spots' => $national_stations->sum('total_spots'),
                    'net_total' => $national_stations->sum('net_value'),
                ],
                'Cable' => [
                    'total_spots' => $cable_stations->sum('total_spots'),
                    'net_total' => $cable_stations->sum('net_value'),
                ],
                'Regional' => [
                    'total_spots' => $regional_stations->sum('total_spots'),
                    'net_total' => $regional_stations->sum('net_value'),
                ]
            ];
        }

        return view('agency.mediaPlan.export.mediaTypeSummary', [
            'summary_by_length' => $this->summaryByLength,
            'summary_by_length_station_type' => $this->summaryByLengthStationType
        ]);
    }


    public function groupByStationType($suggestions, $station_type)
    {
        $suggestions = $suggestions->whereIn('station_type', $station_type);
        return $suggestions;
    }

    public function groupByRegions($suggestions)
    {
        $suggestions = $suggestions->whereIn('station_type', ['terrestrial', 'regional', 'Regional']);
        return $suggestions->groupBy(['station_region', 'station']);
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
               $event->sheet->getStyle('B3:'.$event->sheet->getHighestColumn().(count($this->summaryByLength)+3+2))->applyFromArray($styleArray);
               $event->sheet->getStyle('B3:'.$event->sheet->getHighestColumn().(count($this->summaryByLength)+3+1))->applyFromArray($styleArray);
               $event->sheet->getStyle('C'.(count($this->summaryByLength)+3+2+4).':'.$event->sheet->getHighestColumn(). $event->sheet->getHighestRow())->applyFromArray($styleArray);
            },
        ];
    }
}